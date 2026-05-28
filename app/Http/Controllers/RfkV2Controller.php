<?php

namespace App\Http\Controllers;

use App\Exports\LaporanRFKV2Lengkap;
use App\Exports\LaporanRFKV2Rekap;
use App\Exports\LaporanRFKV2Program;
use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use App\Models\User;
use App\Services\RfkService;
use App\Services\RfkV2HierarchyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RfkV2Controller extends Controller
{
    protected $rfkService;

    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
        $this->rfkService = new RfkService();
    }

    protected function isOvertimePeriod(): bool
    {
        $now = Carbon::now();
        $cutoffDay = (int) env('OVERTIME_RFK_DATE', 15);

        $cutoff = Carbon::create($now->year, $now->month, $cutoffDay, 23, 59, 59);

        return $now->greaterThan($cutoff);
    }

    protected function calculatePeriodWindows(Carbon $period): array
    {
        $nextMonth = $period->copy()->addMonthNoOverflow();
        $overtimeDaySetting = (int) env('OVERTIME_RFK_DATE', 15);
        $overtimeDay = min($overtimeDaySetting, $nextMonth->daysInMonth);
        $windowEnd = $nextMonth->copy()->day($overtimeDay)->endOfDay();

        return [
            'window_end' => $windowEnd,
        ];
    }

    protected function buildAccessMeta(RfkV2 $rfk, Carbon $period, bool $isSuperadmin, bool $isAdminRfk): array
    {
        $now = Carbon::now();
        $windows = $this->calculatePeriodWindows($period);

        $openOvertime = (bool) $rfk->open_overtime;

        $windowEnd = $windows['window_end'];

        $isOvertime = $now->greaterThan($windowEnd);

        $allowAdminEdit = false;
        if (!$isOvertime) {
            $allowAdminEdit = true;
        } elseif ($openOvertime) {
            $allowAdminEdit = true;
        }

        $allowEdit = true;
        if ($isSuperadmin) {
            $allowEdit = true;
        } elseif ($isAdminRfk) {
            $allowEdit = $allowAdminEdit;
        } else {
            $allowEdit = true;
        }

        $canToggle = $isSuperadmin && $isOvertime;

        return $this->serializeMeta([
            'is_overtime' => $isOvertime,
            'open_overtime' => $openOvertime,
            'allow_edit' => $allowEdit,
            'toggle_enabled' => $canToggle,
            'window_end' => $windowEnd,
            'allow_admin_edit' => $allowAdminEdit,
        ]);
    }

    protected function buildMetaWithoutRfk(Carbon $period, bool $isSuperadmin, bool $isAdminRfk): array
    {
        $windows = $this->calculatePeriodWindows($period);
        $now = Carbon::now();

        $isOvertime = $now->greaterThan($windows['window_end']);

        $allowAdminEdit = !$isOvertime;

        $allowEdit = $isSuperadmin
            ? true
            : ($isAdminRfk ? $allowAdminEdit : true);

        return $this->serializeMeta([
            'is_overtime' => $isOvertime,
            'open_overtime' => false,
            'allow_edit' => $allowEdit,
            'window_end' => $windows['window_end'],
            'allow_admin_edit' => $allowAdminEdit,
        ]);
    }

    protected function serializeMeta(array $meta): array
    {
        if (isset($meta['window_end']) && $meta['window_end'] instanceof Carbon) {
            $meta['window_end'] = $meta['window_end']->toDateTimeString();
        }

        return $meta;
    }

    public function index()
    {
        $user = Auth::user();
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "";
        $data['opds'] = Opd::where('rfk_code', '!=', '')->get();
        $data['id_opd'] = $user ? $user->id_opd : null;
        $data['is_admin_rfk'] = false;
        if ($user instanceof User && method_exists($user, 'hasRole')) {
            $data['is_admin_rfk'] = $user->hasRole('admin_rfk');
        }
        $data['is_superadmin'] = $user instanceof User && method_exists($user, 'hasRole')
            ? $user->hasRole('superadmin')
            : false;

        $data['is_overtime'] = $this->isOvertimePeriod();

        return view('pages/rfk/v2/browse', $data);
    }

    public function modalAdd()
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Tambah RFK";

        return view('pages/rfk/v2/add', $data);
    }

    public function browse(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = false;
        $isAdminRfk = false;

        if ($user instanceof User && method_exists($user, 'hasRole')) {
            $isSuperadmin = $user->hasRole('superadmin');
            $isAdminRfk = $user->hasRole('admin_rfk');
        }

        $date = $request['month'];
        $periodDate = Carbon::parse($date)->startOfMonth();
        $bulan = $periodDate->format('m');
        $tahun = $periodDate->format('Y');

        $auth = $isSuperadmin ? $request->input('opd') : ($user ? $user->id_opd : null);

        $defaultMeta = $this->buildMetaWithoutRfk($periodDate, $isSuperadmin, $isAdminRfk);

        if (!$auth) {
            return response()->json([
                'data' => [],
                'meta' => $defaultMeta,
            ]);
        }

        $opd = Opd::find($auth);
        if (!$opd || !$opd['rfk_code']) {
            return response()->json([
                'data' => [],
                'meta' => $defaultMeta,
            ]);
        }

        // Get or create RFK record
        $rfk = RfkV2::firstOrCreate([
            'id_opd' => $auth,
            'month' => $bulan,
            'year' => $tahun,
        ], [
            'status' => 'draft',
        ]);

        // Check if we need to copy from previous month
        $hasData = RfkV2Detail::where('id_rfk', $rfk->id)->exists();
        if (!$hasData) {
            $previousRfk = RfkV2HierarchyService::getPreviousMonthRfk($bulan, $tahun, $auth);
            if ($previousRfk) {
                RfkV2HierarchyService::copyMonthHierarchy($previousRfk->id, $rfk->id);
            } else {
                // Create initial OPD-level entry
                RfkV2Detail::create([
                    'id_rfk' => $rfk->id,
                    'parent_id' => null,
                    'type' => 'opd',
                    'code' => $opd->rfk_code,
                    'uraian' => $opd->name,
                    'year' => $tahun,
                    'pagu_anggaran' => 0,
                    'bulan_lalu' => 0,
                    'bulan_ini' => 0,
                    'sd_bulan_ini' => 0,
                    'sisa_anggaran' => 0,
                    'angkas' => 0,
                    'angkas_prosentase' => 0,
                    'keuangan' => 0,
                    'fisik' => 0,
                    'fisik_rupiah' => 0,
                    'keterangan' => '',
                ]);
            }
        }

        $meta = $this->buildAccessMeta($rfk, $periodDate, $isSuperadmin, $isAdminRfk);

        // Fetch OPD-level records
        $records = RfkV2Detail::where('id_rfk', $rfk->id)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $data = [];
        foreach ($records as $record) {
            $item = $record->toArray();
            $item['has_children'] = $record->hasChildren();
            $item['status'] = $rfk->status;
            $item['open_overtime'] = $meta['open_overtime'];
            $item['allow_edit'] = $meta['allow_edit'];
            $data[] = $item;
        }
        $activeOpdId = $auth;
        $rankInfo = null;
        if ($activeOpdId) {
            $rankInfo = \App\Models\Opd::getRank($activeOpdId, $bulan, $tahun);
        }
        $meta['rank_info'] = $rankInfo;

        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    public function resyncFromPrevious(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = false;
        $isAdminRfk = false;

        if ($user instanceof User && method_exists($user, 'hasRole')) {
            $isSuperadmin = $user->hasRole('superadmin');
            $isAdminRfk = $user->hasRole('admin_rfk');
        }

        if (!$isSuperadmin && !$isAdminRfk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan sinkronisasi ulang.',
            ], 403);
        }

        $rules = [
            'month' => 'required|date_format:Y-m',
        ];

        if ($isSuperadmin) {
            $rules['opd'] = 'required|exists:opd,id';
        } else {
            $rules['opd'] = 'sometimes|nullable|exists:opd,id';
        }

        $validated = $request->validate($rules);

        $periodDate = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $previousDate = $periodDate->copy()->subMonthNoOverflow();

        $opdId = null;
        if ($isSuperadmin) {
            $opdId = (int) $validated['opd'];
        } elseif ($user && $user->id_opd) {
            $opdId = $user->id_opd;
        }

        if (!$opdId) {
            return response()->json([
                'success' => false,
                'message' => 'OPD tidak ditemukan atau belum dipilih.',
            ], 422);
        }

        $previousRfk = RfkV2::where('id_opd', $opdId)
            ->where('month', $previousDate->format('m'))
            ->where('year', $previousDate->format('Y'))
            ->first();

        if (!$previousRfk) {
            return response()->json([
                'success' => false,
                'message' => 'Data bulan sebelumnya tidak ditemukan.',
            ], 422);
        }

        $previousHasData = RfkV2Detail::where('id_rfk', $previousRfk->id)->exists();
        if (!$previousHasData) {
            return response()->json([
                'success' => false,
                'message' => 'Data bulan sebelumnya belum memiliki detail untuk disalin.',
            ], 422);
        }

        $currentRfk = RfkV2::firstOrCreate([
            'id_opd' => $opdId,
            'month' => $periodDate->format('m'),
            'year' => $periodDate->format('Y'),
        ], [
            'status' => 'draft',
        ]);

        try {
            $copiedCount = DB::transaction(function () use ($currentRfk, $previousRfk) {
                RfkV2Detail::where('id_rfk', $currentRfk->id)->delete();
                return RfkV2HierarchyService::copyMonthHierarchy($previousRfk->id, $currentRfk->id);
            });
        } catch (\Exception $exception) {
            Log::error('RFK resync failed: ' . $exception->getMessage(), [
                'current_rfk_id' => $currentRfk->id,
                'previous_rfk_id' => $previousRfk->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan sinkronisasi ulang.',
            ], 500);
        }

        $currentRfk->refresh();
        $meta = $this->buildAccessMeta($currentRfk, $periodDate, $isSuperadmin, $isAdminRfk);

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Data bulan %s berhasil diambil dari bulan %s.',
                $periodDate->translatedFormat('F Y'),
                $previousDate->translatedFormat('F Y')
            ),
            'copied' => $copiedCount,
            'meta' => $meta,
        ]);
    }

    public function toggleOvertime(Request $request)
    {
        $user = Auth::user();
        if (!($user instanceof User) || !method_exists($user, 'hasRole') || !$user->hasRole('superadmin')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk mengubah status overtime.',
            ], 403);
        }

        $validated = $request->validate([
            'opd' => 'required|exists:opd,id',
            'month' => 'required|date_format:Y-m',
            'open' => 'required|boolean',
        ]);

        $date = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $bulan = $date->format('m');
        $tahun = $date->format('Y');

        $windows = $this->calculatePeriodWindows($date);

        $rfk = RfkV2::firstOrCreate([
            'id_opd' => $validated['opd'],
            'month' => $bulan,
            'year' => $tahun,
        ], [
            'status' => 'draft',
        ]);

        $shouldOpen = $request->boolean('open');
        $rfk->open_overtime = $shouldOpen;
        $rfk->save();

        $isAdminRfk = method_exists($user, 'hasRole') && $user->hasRole('admin_rfk');
        $meta = $this->buildAccessMeta($rfk, $date, true, $isAdminRfk);

        return response()->json([
            'success' => true,
            'message' => $meta['open_overtime'] ? 'Akses overtime dibuka.' : 'Akses overtime ditutup.',
            'meta' => $meta,
        ]);
    }

    public function getChart(Request $request)
    {
        if (User::find(Auth::id())->hasRole('superadmin')) {
            $opd = $request['opd'];
        } else {
            $opd = Auth::user()->id_opd;
        }

        $rfk = RfkV2Detail::select('month', DB::raw("AVG(pagu_anggaran) as pagu_anggaran"), DB::raw("AVG(keuangan) as keuangan"), DB::raw("AVG(fisik) as fisik"))
            ->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')
            ->where('uraian', 'TOTAL')
            ->where('year', Carbon::parse()->format('Y'))
            ->groupBy('month');
        if ($opd) {
            $rfk = $rfk->where('id_opd', $opd);
        }
        $rfk = $rfk->get();
        $rfkTransformed = $rfk->keyBy('month');

        $charts = [];
        for ($i = 1; $i <= 12; $i++) {
            $charts['pagu_anggaran'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['pagu_anggaran'] : 0;
            $charts['keuangan'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['keuangan'] : 0;
            $charts['fisik'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['fisik'] : 0;
        }

        return response($charts);
    }

    public function store(Request $request)
    {
        $attr = $request->all();

        // Normalize parent_id: convert empty string, 0, or 'null' to null
        if (isset($attr['parent_id']) && ($attr['parent_id'] === '' || $attr['parent_id'] === '0' || $attr['parent_id'] === 0 || $attr['parent_id'] === 'null')) {
            $attr['parent_id'] = null;
        }

        // OPD level should always have null parent_id
        if (isset($attr['type']) && $attr['type'] === 'opd') {
            $attr['parent_id'] = null;
        }

        // Basic validation rules for all types
        $rules = [
            'code' => 'required',
            'uraian' => 'required',
            'type' => 'required|in:opd,program,kegiatan,sub_kegiatan',
        ];

        // Parent ID validation - conditional based on type
        if (isset($attr['type']) && $attr['type'] === 'opd') {
            // OPD must have null parent_id
            $rules['parent_id'] = 'nullable';
        } else {
            // Other types must have valid parent_id
            $rules['parent_id'] = 'required|exists:rfk_v2_detail,id';
        }

        // Add financial field validation for all types
        $rules = array_merge($rules, [
            'pagu_anggaran' => 'required',
            'bulan_ini' => 'required',
            'fisik' => 'required',
        ]);

        $validator = Validator::make($attr, $rules, [
            'required' => ':attribute harus diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'parent_id.exists' => 'Parent ID yang dipilih tidak valid atau tidak ditemukan.',
            'parent_id.required' => 'Parent ID harus diisi untuk tipe ini.',
        ]);

        if ($validator->fails()) {
            $errors = [];
            $i = 0;
            foreach ($validator->getMessageBag()->toArray() as $key => $value) {
                $errors[$i]['code'] = $key;
                $errors[$i]['message'] = $value[0];
                $i++;
            }
            return Response::json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }

        DB::beginTransaction();
        try {
            $date = $attr['month'];
            $bulan = Carbon::parse($date)->format('m');
            $tahun = Carbon::parse($date)->format('Y');

            // Determine OPD ID
            if (isset($attr['id']) && $attr['id']) {
                // When editing, get OPD from existing record
                $existing = RfkV2Detail::with('rfk')->find($attr['id']);
                if ($existing && $existing->rfk) {
                    $id_opd = $existing->rfk->id_opd;
                    Log::info('Edit mode: Using OPD from existing record', ['id_opd' => $id_opd, 'rfk_detail_id' => $attr['id']]);
                } else {
                    throw new \Exception('Data RFK tidak ditemukan atau tidak memiliki relasi RFK');
                }
            } else {
                // When creating, determine OPD based on user role
                if (User::find(Auth::id())->hasRole('superadmin')) {
                    $id_opd = $attr['opd'] ?? null;
                    if (!$id_opd) {
                        throw new \Exception('OPD harus dipilih untuk membuat data baru');
                    }
                } else {
                    $id_opd = Auth::user()->id_opd;
                }
                Log::info('Create mode: Determined OPD', ['id_opd' => $id_opd]);
            }

            // Get or create RFK record
            $rfk = RfkV2::firstOrCreate([
                'id_opd' => $id_opd,
                'month' => $bulan,
                'year' => $tahun,
            ], [
                'status' => 'draft',
            ]);

            Log::info('RFK record obtained', ['rfk_id' => $rfk->id, 'id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun]);

            // Process financial data
            $attr['pagu_anggaran'] = (float) str_replace(['.', ','], ['', '.'], $attr['pagu_anggaran']);
            $attr['bulan_ini'] = (float) str_replace(['.', ','], ['', '.'], $attr['bulan_ini']);

            // Calculate bulan_lalu from previous month's sd_bulan_ini (if exists)
            // This ensures bulan_lalu is always accurate based on previous month data
            $bulan_lalu_calculated = 0;
            if (isset($attr['id']) && $attr['id']) {
                // For existing record, find the same record in previous month
                $existingRecord = RfkV2Detail::find($attr['id']);
                if ($existingRecord && $existingRecord->code) {
                    $previousRfk = RfkV2HierarchyService::getPreviousMonthRfk($bulan, $tahun, $id_opd);
                    if ($previousRfk) {
                        $previousRecord = RfkV2Detail::where('id_rfk', $previousRfk->id)
                            ->where('code', $existingRecord->code)
                            ->first();
                        if ($previousRecord) {
                            $bulan_lalu_calculated = (float) $previousRecord->sd_bulan_ini;
                            Log::info('Calculated bulan_lalu from previous month', [
                                'code' => $existingRecord->code,
                                'previous_sd_bulan_ini' => $previousRecord->sd_bulan_ini,
                                'calculated_bulan_lalu' => $bulan_lalu_calculated
                            ]);
                        }
                    }
                }
            }

            $attr['bulan_lalu'] = $bulan_lalu_calculated;
            $attr['sd_bulan_ini'] = $attr['bulan_lalu'] + $attr['bulan_ini'];
            $attr['sisa_anggaran'] = $attr['pagu_anggaran'] - $attr['sd_bulan_ini'];
            $attr['angkas'] = isset($attr['angkas']) ? (float) str_replace(['.', ','], ['', '.'], $attr['angkas']) : 0;
            $attr['angkas_prosentase'] = $attr['pagu_anggaran'] > 0 ? ($attr['angkas'] / $attr['pagu_anggaran']) * 100 : 0;
            $attr['keuangan'] = $attr['pagu_anggaran'] > 0 ? ($attr['sd_bulan_ini'] / $attr['pagu_anggaran']) * 100 : 0;
            $attr['fisik'] = (float) $attr['fisik'];

            // Calculate fisik_rupiah (shadow value): fisik (%) * pagu_anggaran / 100
            // This is used for aggregation in parent levels
            $attr['fisik_rupiah'] = $attr['pagu_anggaran'] > 0 ? ($attr['fisik'] * $attr['pagu_anggaran'] / 100) : 0;

            $attr['keterangan'] = $attr['keterangan'] ?? '';
            $attr['year'] = $tahun;
            $attr['id_rfk'] = $rfk->id;

            // Auto-generate code if not provided or if creating new
            // Trim code and check if empty
            $code = isset($attr['code']) ? trim($attr['code']) : '';
            if (empty($code) && !empty($attr['parent_id'])) {
                $parent = RfkV2Detail::find($attr['parent_id']);
                if ($parent) {
                    $attr['code'] = RfkV2HierarchyService::generateCode($parent->code, $parent->type, $attr['parent_id']);
                } else {
                    throw new \Exception('Parent tidak ditemukan untuk generate code');
                }
            } else {
                $attr['code'] = $code;
            }

            unset($attr['month']);
            if (isset($attr['opd'])) {
                unset($attr['opd']);
            }

            // Create or update record
            if (isset($attr['id']) && $attr['id']) {
                $rfk_detail = RfkV2Detail::find($attr['id']);
                if (!$rfk_detail) {
                    throw new \Exception('Data tidak ditemukan dengan ID: ' . $attr['id']);
                }

                Log::info('Updating RFK detail', [
                    'id' => $attr['id'],
                    'code' => $attr['code'] ?? 'N/A',
                    'type' => $attr['type'] ?? 'N/A'
                ]);

                // Remove id from update data to prevent issues
                $updateData = $attr;
                unset($updateData['id']);
                $rfk_detail->update($updateData);

                Log::info('RFK detail updated successfully', ['id' => $rfk_detail->id]);
            } else {
                // Remove id from create data if it exists
                unset($attr['id']);

                Log::info('Creating new RFK detail', [
                    'code' => $attr['code'] ?? 'N/A',
                    'type' => $attr['type'] ?? 'N/A'
                ]);

                $rfk_detail = RfkV2Detail::create($attr);

                Log::info('RFK detail created successfully', ['id' => $rfk_detail->id]);
            }

            // Update parent aggregates recursively
            $updatedParents = [];
            if ($rfk_detail->parent_id) {
                // Collect all updated parents during recursive update
                $currentParentId = $rfk_detail->parent_id;
                while ($currentParentId) {
                    RfkV2HierarchyService::aggregateFinancials($currentParentId);
                    $parent = RfkV2Detail::find($currentParentId);
                    if ($parent) {
                        $updatedParents[] = $parent;
                        $currentParentId = $parent->parent_id;
                    } else {
                        break;
                    }
                }
            }

            DB::commit();

            // Reload the saved record with fresh data
            $rfk_detail->refresh();

            // Convert saved data to array and add has_children
            $savedDataArray = $rfk_detail->toArray();
            $savedDataArray['has_children'] = $rfk_detail->children()->exists();

            // Convert updated parents to arrays with has_children
            $updatedParentsArray = [];
            foreach ($updatedParents as $parent) {
                $parentArray = $parent->toArray();
                $parentArray['has_children'] = $parent->children()->exists();
                $updatedParentsArray[] = $parentArray;
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Menyimpan Data',
                'data' => $savedDataArray,
                'updated_parents' => $updatedParentsArray, // All parent rows that were updated
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing RFK detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal Menyimpan Data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function modalEdit($id)
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Edit RFK";
        $rfk_detail = RfkV2Detail::with('parent', 'rfk')->find($id);

        if (!$rfk_detail) {
            abort(404);
        }

        $data['rfk_detail'] = $rfk_detail;
        $data['type_label'] = RfkV2HierarchyService::getTypeLabel($rfk_detail->type);
        $data['breadcrumb'] = $rfk_detail->getBreadcrumb();
        $data['year_month'] = $rfk_detail->year . '-' . str_pad($rfk_detail->rfk->month, 2, '0', STR_PAD_LEFT);

        return view('pages/rfk/v2/edit', $data);
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Hapus RFK";
        $rfk_detail = RfkV2Detail::with('children')->find($id);

        if (!$rfk_detail) {
            abort(404);
        }

        $data['rfk_detail'] = $rfk_detail;
        $data['children_count'] = RfkV2HierarchyService::getDescendantsCount($id);
        $data['type_label'] = RfkV2HierarchyService::getTypeLabel($rfk_detail->type);

        return view('pages/rfk/v2/delete', $data);
    }

    public function fetchChildren(Request $request)
    {
        $parent_id = $request->input('parent_id');
        $month = $request->input('month');

        $date = Carbon::parse($month);
        $bulan = $date->format('m');
        $tahun = $date->format('Y');

        if (User::find(Auth::id())->hasRole('superadmin')) {
            $opd_id = $request->input('opd');
        } else {
            $opd_id = Auth::user()->id_opd;
        }

        $rfk = RfkV2::where([
            'id_opd' => $opd_id,
            'month' => $bulan,
            'year' => $tahun,
        ])->first();

        if (!$rfk) {
            return response([]);
        }

        $children = RfkV2Detail::where('parent_id', $parent_id)
            ->where('id_rfk', $rfk->id)
            ->with('children')
            ->orderBy('code', 'asc') // Sort by code to ensure correct order
            ->get();

        $data = [];
        foreach ($children as $child) {
            $item = $child->toArray();
            $item['has_children'] = $child->hasChildren();
            $item['status'] = $rfk->status;
            $data[] = $item;
        }

        return response($data);
    }

    public function getOpdAverage(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = false;

        if ($user instanceof User && method_exists($user, 'hasRole')) {
            $isSuperadmin = $user->hasRole('superadmin');
        }

        $date = $request->input('month');
        $periodDate = Carbon::parse($date)->startOfMonth();
        $bulan = $periodDate->format('m');
        $tahun = $periodDate->format('Y');

        // Calculate average from ALL OPDs in Kabupaten Tulungagung, not just selected OPD
        // Get all RFK records for the month/year
        $rfkRecords = RfkV2::where('month', $bulan)
            ->where('year', $tahun)
            ->get();

        if ($rfkRecords->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data RFK tidak ditemukan',
                'data' => [
                    'average_keuangan' => 0,
                    'count' => 0,
                ]
            ], 404);
        }

        // Get all sub_kegiatan from ALL OPDs to calculate overall average
        $rfkIds = $rfkRecords->pluck('id')->toArray();
        $subKegiatan = RfkV2Detail::whereIn('id_rfk', $rfkIds)
            ->where('type', 'sub_kegiatan')
            ->get();

        $totalKeuangan = 0;
        $count = $subKegiatan->count();

        foreach ($subKegiatan as $item) {
            $totalKeuangan += floatval($item->keuangan);
        }

        $averageKeuangan = $count > 0 ? $totalKeuangan / $count : 0;

        // Get overall OPD-level aggregate for reference (weighted average)
        $allOpdRecords = RfkV2Detail::whereIn('id_rfk', $rfkIds)
            ->where('type', 'opd')
            ->whereNull('parent_id')
            ->get();

        $totalPagu = 0;
        $totalRealisasi = 0;
        foreach ($allOpdRecords as $opdRecord) {
            $totalPagu += floatval($opdRecord->pagu_anggaran ?? 0);
            $totalRealisasi += floatval($opdRecord->sd_bulan_ini ?? 0);
        }
        $overallOpdKeuangan = $totalPagu > 0 ? ($totalRealisasi / $totalPagu) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'average_keuangan' => round($averageKeuangan, 2),
                'count' => $count,
                'opd_keuangan' => round($overallOpdKeuangan, 2), // Overall OPD level weighted average
                'opd_name' => 'Kabupaten Tulungagung (Semua OPD)',
            ]
        ]);
    }

    public function ensureMonthData(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $opd_id = $request->input('opd');

        if (RfkV2HierarchyService::dataExistsForMonth($month, $year, $opd_id)) {
            return response()->json(['success' => true, 'message' => 'Data already exists']);
        }

        $previousRfk = RfkV2HierarchyService::getPreviousMonthRfk($month, $year, $opd_id);

        if (!$previousRfk) {
            return response()->json(['success' => false, 'message' => 'No previous month data found']);
        }

        $rfk = RfkV2::create([
            'id_opd' => $opd_id,
            'month' => $month,
            'year' => $year,
            'status' => 'draft',
        ]);

        $count = RfkV2HierarchyService::copyMonthHierarchy($previousRfk->id, $rfk->id);

        return response()->json([
            'success' => true,
            'message' => "Successfully copied $count records from previous month",
            'count' => $count,
        ]);
    }

    public function modalAddChild($parent_id)
    {
        $data['page_title'] = "RFK";
        $parent = RfkV2Detail::with('parent', 'rfk')->find($parent_id);

        if (!$parent) {
            abort(404);
        }

        $childType = RfkV2HierarchyService::getChildType($parent->type);
        $suggestedCode = RfkV2HierarchyService::generateCode($parent->code, $parent->type, $parent_id);

        $data['parent'] = $parent;
        $data['child_type'] = $childType;
        $data['suggested_code'] = $suggestedCode;
        $data['type_label'] = RfkV2HierarchyService::getTypeLabel($childType);
        $data['parent_type_label'] = RfkV2HierarchyService::getTypeLabel($parent->type);
        $data['breadcrumb'] = $parent->getBreadcrumb();
        $data['year_month'] = $parent->year . '-' . str_pad($parent->rfk->month, 2, '0', STR_PAD_LEFT);

        $data['page_subtitle'] = "Buat " . $data['type_label'] . " untuk " . $data['parent_type_label'] . " " . $parent->uraian;

        return view('pages/rfk/v2/add', $data);
    }

    public function cascadeDelete($id)
    {
        DB::beginTransaction();
        try {
            $record = RfkV2Detail::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            $parent_id = $record->parent_id;

            // Collect all parent IDs BEFORE delete (so we know which ones to update)
            $parentIds = [];
            if ($parent_id) {
                $currentParentId = $parent_id;
                while ($currentParentId) {
                    $parentIds[] = $currentParentId;
                    $parent = RfkV2Detail::find($currentParentId);
                    if ($parent) {
                        $currentParentId = $parent->parent_id;
                    } else {
                        break;
                    }
                }
            }

            // Now delete (this will update parent aggregates)
            $count = RfkV2HierarchyService::cascadeDelete($id);

            // Get updated parents after delete
            // IMPORTANT: Wait a bit to ensure aggregates are fully updated, then get fresh data
            $updatedParents = [];
            if (!empty($parentIds)) {
                // Small delay to ensure database updates are committed
                usleep(100000); // 100ms delay

                // Refresh each parent from bottom to top to get updated aggregates
                foreach ($parentIds as $pid) {
                    // Use fresh query to bypass any cache and get latest data from database
                    $parent = RfkV2Detail::where('id', $pid)->first();
                    if ($parent) {
                        // Force refresh from database to get latest aggregates
                        $parent->refresh();

                        // Reload children relationship to ensure fresh data
                        $parent->load('children');

                        // CRITICAL: Re-aggregate to ensure values are correct
                        // This ensures that if aggregation didn't run properly, we do it again
                        $aggregated = $parent->getAggregatedFinancials();
                        if ($aggregated !== null) {
                            Log::info('Re-aggregating parent after delete', [
                                'parent_id' => $pid,
                                'parent_code' => $parent->code,
                                'children_count' => $parent->children()->count(),
                                'aggregated' => $aggregated
                            ]);
                            $parent->update($aggregated);
                            $parent->refresh();
                        } else {
                            Log::warning('Aggregated is null for parent', [
                                'parent_id' => $pid,
                                'parent_code' => $parent->code
                            ]);
                        }

                        // Check if parent has children
                        $hasChildren = $parent->children()->exists();

                        // Convert to array and add has_children flag
                        $parentArray = $parent->toArray();
                        $parentArray['has_children'] = $hasChildren;

                        $updatedParents[] = $parentArray;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted $count record(s)",
                'deleted_count' => $count,
                'updated_parents' => $updatedParents, // All parent rows that were updated
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $attr = $request->all();
    }

    public function export(Request $request, $type)
    {
        if ($type == 1) return $this->laporanRFKV2Lengkap($request);
        if ($type == 2) return $this->laporanRFKV2Rekap($request);
        if ($type == 3) return $this->laporanRFKV2Program($request);
    }

    private function laporanRFKV2Lengkap(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('superadmin');
        $isAdminRfk = $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_rfk');

        if (!$isSuperadmin && !$isAdminRfk) {
            abort(403, 'Tidak memiliki akses untuk mengekspor laporan.');
        }

        $date = $request->input('month');
        if (!$date) {
            abort(400, 'Parameter bulan harus diisi.');
        }

        $periodDate = Carbon::parse($date)->startOfMonth();
        $bulan = $periodDate->format('m');
        $tahun = $periodDate->format('Y');

        // Determine OPD based on role
        if ($isSuperadmin) {
            // Superadmin: All OPDs automatically
            $opds = Opd::where('rfk_code', '!=', '')->get();
        } else {
            // Admin RFK: Only their own OPD
            $opds = Opd::where('id', $user->id_opd)->get();
        }

        $data = [];

        foreach ($opds as $opd) {
            // Get RFK record for this OPD
            $rfk = RfkV2::where('id_opd', $opd->id)
                ->where('month', $bulan)
                ->where('year', $tahun)
                ->first();

            if (!$rfk) {
                continue;
            }

            // Fetch all details hierarchically
            $details = RfkV2Detail::where('id_rfk', $rfk->id)
                ->orderBy('code', 'asc')
                ->get();

            if ($details->isEmpty()) {
                continue;
            }

            $items = [];
            foreach ($details as $detail) {
                $items[] = [
                    'code' => $detail->code,
                    'uraian' => $detail->uraian,
                    'type' => $detail->type,
                    'pagu_anggaran' => $detail->pagu_anggaran,
                    'bulan_lalu' => $detail->bulan_lalu,
                    'bulan_ini' => $detail->bulan_ini,
                    'sd_bulan_ini' => $detail->sd_bulan_ini,
                    'sisa_anggaran' => $detail->sisa_anggaran,
                    'keuangan' => $detail->keuangan,
                    'fisik' => $detail->fisik,
                    'keterangan' => $detail->keterangan ?? '',
                ];
            }

            $data[] = [
                'opd_name' => $opd->name,
                'items' => $items,
            ];
        }

        return Excel::download(
            new LaporanRFKV2Lengkap($data, $bulan, $tahun),
            'laporan_rfk_v2_lengkap_' . $bulan . '_' . $tahun . '.xlsx'
        );
    }

    private function laporanRFKV2Rekap(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('superadmin');

        if (!$isSuperadmin) {
            abort(403, 'Hanya superadmin yang dapat mengakses Laporan Rekap.');
        }

        $date = $request->input('month');
        if (!$date) {
            abort(400, 'Parameter bulan harus diisi.');
        }

        $periodDate = Carbon::parse($date)->startOfMonth();
        $bulan = $periodDate->format('m');
        $tahun = $periodDate->format('Y');

        // Fetch all OPDs with RFK data for this period
        $opds = Opd::where('rfk_code', '!=', '')->get();

        $data = [];

        foreach ($opds as $opd) {
            // Get RFK record for this OPD
            $rfk = RfkV2::where('id_opd', $opd->id)
                ->where('month', $bulan)
                ->where('year', $tahun)
                ->first();

            if (!$rfk) {
                continue;
            }

            // Fetch only OPD-level record (type='opd', parent_id=null)
            $opdDetail = RfkV2Detail::where('id_rfk', $rfk->id)
                ->where('type', 'opd')
                ->whereNull('parent_id')
                ->first();

            if (!$opdDetail) {
                continue;
            }

            $data[] = [
                'uraian' => $opdDetail->uraian,
                'pagu_anggaran' => $opdDetail->pagu_anggaran,
                'bulan_lalu' => $opdDetail->bulan_lalu,
                'bulan_ini' => $opdDetail->bulan_ini,
                'sd_bulan_ini' => $opdDetail->sd_bulan_ini,
                'sisa_anggaran' => $opdDetail->sisa_anggaran,
                'keuangan' => $opdDetail->keuangan,
                'fisik' => $opdDetail->fisik,
                'keterangan' => $opdDetail->keterangan ?? '',
            ];
        }

        return Excel::download(
            new LaporanRFKV2Rekap($data, $bulan, $tahun),
            'laporan_rfk_v2_rekap_' . $bulan . '_' . $tahun . '.xlsx'
        );
    }

    private function laporanRFKV2Program(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('superadmin');
        $isAdminRfk = $user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_rfk');

        if (!$isSuperadmin && !$isAdminRfk) {
            abort(403, 'Tidak memiliki akses untuk mengekspor laporan.');
        }

        $date = $request->input('month');
        if (!$date) {
            abort(400, 'Parameter bulan harus diisi.');
        }

        $periodDate = Carbon::parse($date)->startOfMonth();
        $bulan = $periodDate->format('m');
        $tahun = $periodDate->format('Y');

        // Determine OPD based on role
        if ($isSuperadmin) {
            // Superadmin: All OPDs automatically
            $opds = Opd::where('rfk_code', '!=', '')->get();
        } else {
            // Admin RFK: Only their own OPD
            $opds = Opd::where('id', $user->id_opd)->get();
        }

        $data = [];

        foreach ($opds as $opd) {
            // Get RFK record for this OPD
            $rfk = RfkV2::where('id_opd', $opd->id)
                ->where('month', $bulan)
                ->where('year', $tahun)
                ->first();

            if (!$rfk) {
                continue;
            }

            // Fetch only OPD and Program level details
            $details = RfkV2Detail::where('id_rfk', $rfk->id)
                ->whereIn('type', ['opd', 'program'])
                ->orderBy('code', 'asc')
                ->get();

            if ($details->isEmpty()) {
                continue;
            }

            $items = [];
            foreach ($details as $detail) {
                $items[] = [
                    'code' => $detail->code,
                    'uraian' => $detail->uraian,
                    'pagu_anggaran' => $detail->pagu_anggaran,
                    'bulan_lalu' => $detail->bulan_lalu,
                    'bulan_ini' => $detail->bulan_ini,
                    'sd_bulan_ini' => $detail->sd_bulan_ini,
                    'sisa_anggaran' => $detail->sisa_anggaran,
                    'keuangan' => $detail->keuangan,
                    'fisik' => $detail->fisik,
                    'keterangan' => $detail->keterangan ?? '',
                ];
            }

            $data[] = [
                'opd_name' => $opd->name,
                'items' => $items,
            ];
        }

        return Excel::download(
            new LaporanRFKV2Program($data, $bulan, $tahun),
            'laporan_rfk_v2_program_' . $bulan . '_' . $tahun . '.xlsx'
        );
    }
}
