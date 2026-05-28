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
    public static function copyMonthHierarchy($from_rfk_id, $to_rfk_id, $parent_id = null, $new_parent_id = null)
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
            $newRecord->keterangan = '';

            $newRecord->save();
            $count++;

            // Recursively copy children
            $count += self::copyMonthHierarchy($from_rfk_id, $to_rfk_id, $record->id, $newRecord->id);
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
        $previousDate = $currentDate->subMonth();

        return RfkV2::where([
            'id_opd' => $opd_id,
            'month' => $previousDate->format('m'),
            'year' => $previousDate->format('Y'),
        ])->first();
    }
}

