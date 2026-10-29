<?php

namespace App\Services;

use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use Illuminate\Support\Facades\DB;

class RfkV2HierarchyService
{
    /**
     * Generate code for new child based on parent
     *
     * @param string $parent_code
     * @param string $parent_type
     * @param int $parent_id
     * @return string
     */
    public static function generateCode($parent_code, $parent_type, $parent_id)
    {
        // Count existing siblings
        $siblingCount = RfkV2Detail::where('parent_id', $parent_id)->count();
        $nextNumber = $siblingCount + 1;

        switch ($parent_type) {
            case 'opd':
                // OPD → Program: {parent_code}0{count+1}
                // e.g., "1.01" → "1.01.01", "1.01.02"
                return $parent_code . '.' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            case 'program':
                // Program → Kegiatan: {parent_code}.{count+1}
                // e.g., "1.01.01" → "1.01.01.1", "1.01.01.2"
                return $parent_code . '.' . $nextNumber;

            case 'kegiatan':
                // Kegiatan → Sub Kegiatan: {parent_code}.{count+1}
                // e.g., "1.01.01.1" → "1.01.01.1.1", "1.01.01.1.2"
                return $parent_code . '.' . $nextNumber;

            default:
                return $parent_code . '.' . $nextNumber;
        }
    }

    /**
     * Aggregate financial data from direct children and update parent
     *
     * @param int $parent_id
     * @return RfkV2Detail|null
     */
    public static function aggregateFinancials($parent_id)
    {
        // Use fresh() to bypass model cache and get latest data from database
        $parent = RfkV2Detail::find($parent_id);

        if (!$parent) {
            return null;
        }

        // Reload children to ensure we have fresh data
        $parent->load('children');

        $aggregated = $parent->getAggregatedFinancials();

        // Always update parent, even if aggregated is all zeros (when no children)
        // This ensures parent is set to 0 when all children are deleted
        if ($aggregated !== null) {
            $parent->update($aggregated);
            // Refresh to get updated values
            $parent->refresh();
        }

        return $parent;
    }

    /**
     * Update parent aggregates recursively up the hierarchy
     *
     * @param int $id
     * @return void
     */
    public static function updateParentAggregatesRecursive($id)
    {
        // Use where()->first() to get fresh data, bypassing cache
        $record = RfkV2Detail::where('id', $id)->first();

        if (!$record || !$record->parent_id) {
            return;
        }

        // Aggregate current parent (this will update parent with aggregated values)
        self::aggregateFinancials($record->parent_id);

        // Recursively update grandparent
        self::updateParentAggregatesRecursive($record->parent_id);
    }

    /**
     * Copy entire hierarchy from previous month
     *
     * @param int $from_rfk_id
     * @param int $to_rfk_id
     * @param int|null $parent_id
     * @param int|null $new_parent_id
     * @return int Count of copied records
     */
    public static function copyMonthHierarchy($from_rfk_id, $to_rfk_id, $parent_id = null, $new_parent_id = null, $resetFinancials = false)
    {
        $count = 0;

        // Fetch records at this level
        $query = RfkV2Detail::where('id_rfk', $from_rfk_id);

        if ($parent_id === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parent_id);
        }

        $records = $query->get();

        foreach ($records as $record) {
            // Create new record
            $newRecord = new RfkV2Detail();
            $newRecord->id_rfk = $to_rfk_id;
            $newRecord->parent_id = $new_parent_id;
            $newRecord->type = $record->type;
            $newRecord->code = $record->code;
            $newRecord->uraian = in_array($record->type, ['opd', 'program']) ? strtoupper($record->uraian) : $record->uraian;
            $newRecord->year = $record->year;

            // Copy financial data with adjustments
            if ($resetFinancials) {
                $newRecord->pagu_anggaran = 0;
                $newRecord->bulan_lalu = 0;
                $newRecord->bulan_ini = 0;
                $newRecord->sd_bulan_ini = 0;
                $newRecord->sisa_anggaran = 0;
                $newRecord->angkas = 0;
                $newRecord->angkas_prosentase = 0;
                $newRecord->keuangan = 0;
                $newRecord->fisik = 0;
                $newRecord->fisik_rupiah = 0;
            } else {
                $newRecord->pagu_anggaran = $record->pagu_anggaran;
                $newRecord->bulan_lalu = $record->sd_bulan_ini; // Last month's cumulative becomes this month's "bulan_lalu"
                $newRecord->bulan_ini = 0; // Reset current month
                $newRecord->sd_bulan_ini = $record->sd_bulan_ini; // Keep cumulative
                $newRecord->sisa_anggaran = $record->pagu_anggaran - $record->sd_bulan_ini;
                $newRecord->angkas = $record->angkas;
                $newRecord->angkas_prosentase = $record->angkas_prosentase;
                $newRecord->keuangan = $record->keuangan;
                $newRecord->fisik = $record->fisik;
                $newRecord->fisik_rupiah = $record->fisik_rupiah;
            }
            $newRecord->keterangan = '';

            $newRecord->save();
            $count++;

            // Recursively copy children
            $count += self::copyMonthHierarchy($from_rfk_id, $to_rfk_id, $record->id, $newRecord->id, $resetFinancials);
        }

        return $count;
    }

    /**
     * Get total count of all descendants
     *
     * @param int $id
     * @return int
     */
    public static function getDescendantsCount($id)
    {
        $children = RfkV2Detail::where('parent_id', $id)->get();
        $count = $children->count();

        foreach ($children as $child) {
            $count += self::getDescendantsCount($child->id);
        }

        return $count;
    }

    /**
     * Determine child type based on parent type
     *
     * @param string $parent_type
     * @return string
     */
    public static function getChildType($parent_type)
    {
        $typeMap = [
            'opd' => 'program',
            'program' => 'kegiatan',
            'kegiatan' => 'sub_kegiatan',
        ];

        return $typeMap[$parent_type] ?? 'sub_kegiatan';
    }

    /**
     * Get type label in Indonesian
     *
     * @param string $type
     * @return string
     */
    public static function getTypeLabel($type)
    {
        $labels = [
            'opd' => 'OPD',
            'program' => 'Program',
            'kegiatan' => 'Kegiatan',
            'sub_kegiatan' => 'Sub Kegiatan',
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Delete record and all its descendants
     *
     * @param int $id
     * @return int Count of deleted records
     */
    public static function cascadeDelete($id)
    {
        $record = RfkV2Detail::find($id);

        if (!$record) {
            return 0;
        }

        $count = 0;
        $parent_id = $record->parent_id;

        // Get all children
        $children = RfkV2Detail::where('parent_id', $id)->get();

        // Recursively delete children first
        foreach ($children as $child) {
            $count += self::cascadeDelete($child->id);
        }

        // Delete the record itself
        $record->delete();
        $count++;

        // Update parent aggregates if exists
        if ($parent_id) {
            self::updateParentAggregatesRecursive($parent_id);
        }

        return $count;
    }

    /**
     * Check if data exists for given month/year/opd
     *
     * @param string $month
     * @param string $year
     * @param int $opd_id
     * @return bool
     */
    public static function dataExistsForMonth($month, $year, $opd_id)
    {
        $rfk = RfkV2::where([
            'id_opd' => $opd_id,
            'month' => $month,
            'year' => $year,
        ])->first();

        if (!$rfk) {
            return false;
        }

        return RfkV2Detail::where('id_rfk', $rfk->id)->exists();
    }

    /**
     * Get previous month's RFK
     *
     * @param string $month
     * @param string $year
     * @param int $opd_id
     * @return RfkV2|null
     */
    public static function getPreviousMonthRfk($month, $year, $opd_id)
    {
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        
        // If current month is January (01), there is no previous month in the same year.
        // Also do not carry over realization from December of the previous year.
        if ($currentDate->format('m') === '01') {
            return null;
        }

        $previousDate = $currentDate->subMonth();

        return RfkV2::where([
            'id_opd' => $opd_id,
            'month' => $previousDate->format('m'),
            'year' => $previousDate->format('Y'),
        ])->first();
    }

    /**
     * Automatically ensure month data is populated from previous month or synchronized
     *
     * @param int $current_rfk_id
     * @param int $opd_id
     * @param string $month
     * @param string $year
     * @return void
     */
    public static function ensureAndAutoSyncMonthData($current_rfk_id, $opd_id, $month, $year)
    {
        if ($month === '01') {
            // Ensure January structure exists from Dec prev year for THIS specific OPD
            $prevYear = (int)$year - 1;
            $decRfk = RfkV2::where([
                'id_opd' => $opd_id,
                'month' => '12',
                'year' => (string)$prevYear,
            ])->first();

            if (!$decRfk) {
                return;
            }

            $currentDetailCount = RfkV2Detail::where('id_rfk', $current_rfk_id)->count();
            $hasRealData = false;
            if ($currentDetailCount > 0) {
                $records = RfkV2Detail::where('id_rfk', $current_rfk_id)->get();
                foreach ($records as $r) {
                    if ((float)$r->pagu_anggaran > 0 || (float)$r->bulan_ini > 0 || (float)$r->angkas > 0) {
                        $hasRealData = true;
                        break;
                    }
                }
            }

            // If no real data exists for this OPD, copy structure from its December previous year with reset
            if (!$hasRealData) {
                RfkV2Detail::where('id_rfk', $current_rfk_id)->delete();
                self::copyMonthHierarchy($decRfk->id, $current_rfk_id, null, null, true);
            }
            return;
        }

        $previousRfk = self::getPreviousMonthRfk($month, $year, $opd_id);
        if (!$previousRfk) {
            return;
        }

        $prevDetailCount = RfkV2Detail::where('id_rfk', $previousRfk->id)->count();
        if ($prevDetailCount === 0) {
            return;
        }

        $currentDetailCount = RfkV2Detail::where('id_rfk', $current_rfk_id)->count();
        $isOnlyEmptyOpd = false;

        if ($currentDetailCount <= 1) {
            $onlyRecord = RfkV2Detail::where('id_rfk', $current_rfk_id)->first();
            if (!$onlyRecord || ($onlyRecord->type === 'opd' && (float)$onlyRecord->pagu_anggaran == 0 && !$onlyRecord->children()->exists())) {
                $isOnlyEmptyOpd = true;
            }
        }

        if ($currentDetailCount === 0 || $isOnlyEmptyOpd) {
            // Auto copy entire hierarchy from previous month
            RfkV2Detail::where('id_rfk', $current_rfk_id)->delete();
            self::copyMonthHierarchy($previousRfk->id, $current_rfk_id);
        } else {
            // Auto sync bulan_lalu from previous month's sd_bulan_ini
            self::syncRfkDataWithPrevious($current_rfk_id, $previousRfk->id);
        }
    }

    /**
     * Sync a month's detail records with the previous month's cumulative realization
     *
     * @param int $current_rfk_id
     * @param int $previous_rfk_id
     * @return void
     */
    public static function syncRfkDataWithPrevious($current_rfk_id, $previous_rfk_id)
    {
        $currentDetails = RfkV2Detail::where('id_rfk', $current_rfk_id)->get();
        if ($currentDetails->isEmpty()) {
            return;
        }

        $prevDetails = RfkV2Detail::where('id_rfk', $previous_rfk_id)->get()->keyBy('code');
        if ($prevDetails->isEmpty()) {
            return;
        }

        $changed = false;

        foreach ($currentDetails as $detail) {
            if ($detail->code && isset($prevDetails[$detail->code])) {
                $prev = $prevDetails[$detail->code];
                $newBulanLalu = (float)$prev->sd_bulan_ini;

                // Check if bulan_lalu differs from previous month's sd_bulan_ini
                // OR pagu_anggaran differs
                if (abs((float)$detail->bulan_lalu - $newBulanLalu) > 0.001 || abs((float)$detail->pagu_anggaran - (float)$prev->pagu_anggaran) > 0.001) {
                    $bulanIni = (float)$detail->bulan_ini;
                    $pagu = (float)$prev->pagu_anggaran;
                    $newSdBulanIni = $newBulanLalu + $bulanIni;
                    $newSisa = $pagu - $newSdBulanIni;
                    $newKeu = $pagu > 0 ? ($newSdBulanIni / $pagu * 100) : 0;

                    $detail->update([
                        'pagu_anggaran' => $pagu,
                        'bulan_lalu' => $newBulanLalu,
                        'sd_bulan_ini' => $newSdBulanIni,
                        'sisa_anggaran' => $newSisa,
                        'keuangan' => $newKeu,
                    ]);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            // Re-aggregate parent levels (sub_kegiatan -> kegiatan -> program -> opd)
            $subKegiatans = RfkV2Detail::where('id_rfk', $current_rfk_id)
                ->where('type', 'sub_kegiatan')
                ->whereNotNull('parent_id')
                ->get();

            $parentIds = [];
            foreach ($subKegiatans as $sub) {
                $parentIds[$sub->parent_id] = true;
            }

            foreach (array_keys($parentIds) as $pid) {
                self::updateParentAggregatesRecursive($pid);
            }
        }
    }

    /**
     * Cascade update subsequent months (e.g. Sep, Oct, Nov, Dec) when an earlier month is edited
     *
     * @param int $opd_id
     * @param string $year
     * @param int $fromMonthNum
     * @return void
     */
    public static function cascadeUpdateSubsequentMonths($opd_id, $year, $fromMonthNum)
    {
        \Log::info("Cascade update triggered", ['opd' => $opd_id, 'year' => $year, 'month' => $fromMonthNum]);
        for ($m = $fromMonthNum + 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $currentRfk = RfkV2::where([
                'id_opd' => $opd_id,
                'month' => $monthStr,
                'year' => $year,
            ])->first();

            if (!$currentRfk) {
                continue;
            }

            $prevMonthStr = str_pad($m - 1, 2, '0', STR_PAD_LEFT);
            $prevRfk = RfkV2::where([
                'id_opd' => $opd_id,
                'month' => $prevMonthStr,
                'year' => $year,
            ])->first();

            if (!$prevRfk) {
                continue;
            }

            \Log::info("Syncing month {$monthStr} with prev {$prevMonthStr}");
            self::syncRfkDataWithPrevious($currentRfk->id, $prevRfk->id);
        }
    }
}

