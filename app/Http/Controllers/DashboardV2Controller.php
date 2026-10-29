<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use App\Models\User;
use App\Exports\DashboardV2Export;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DashboardV2Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Dashboard V2 executive dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data['filter_year'] = request('filter_year');
        $data['filter_month'] = request('filter_month');

        // For admin_opd: auto-filter to their OPD
        // For superadmin: default to all OPD, allow filter
        if ($user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_opd') && !$user->hasRole('superadmin')) {
            $data['filter_opd'] = request('filter_opd') ?? $user->id_opd;
        } else {
            $data['filter_opd'] = request('filter_opd') ?? '';
        }

        $data['opds'] = Opd::all();

        return view('dashboard-v2', $data);
    }

    /**
     * Get Dashboard V2 API data for AJAX calls.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApiData(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');

        $bulan = Carbon::parse($month)->format('m');
        $tahun = Carbon::parse($month)->format('Y');

        Log::info('Dashboard V2 API Request', [
            'month' => $month,
            'opd_filter' => $opdFilter,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);

        $user = Auth::user();
        if ($opdFilter) {
            $opdIds = [$opdFilter];
        } elseif ($user && $user->hasRole('admin_opd') && !$user->hasRole('superadmin') && !$user->hasRole('eksekutif') && $user->id_opd) {
            $opdIds = [$user->id_opd];
        } else {
            $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
        }

        Log::info('Dashboard V2 OPD IDs', ['opd_ids' => $opdIds]);

        // ── SUMMARY ──────────────────────────────────────────────────
        $opdData = [];
        $summary = [
            'total_pagu' => 0,
            'total_realisasi' => 0,
            'pct_keuangan' => 0,
            'pct_fisik' => 0,
            'sisa_anggaran' => 0,
        ];

        foreach ($opdIds as $opdId) {
            $opd = Opd::find($opdId);
            if (!$opd) continue;

            $rfkOpd = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
                ->where('rfk_v2.id_opd', $opdId)
                ->where('rfk_v2.month', $bulan)
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2_detail.type', 'opd')
                ->whereNull('rfk_v2_detail.parent_id')
                ->first();

            if ($rfkOpd) {
                $opdData[] = [
                    'opd_name' => $opd->name,
                    'pagu_anggaran' => $rfkOpd->pagu_anggaran ?? 0,
                    'sd_bulan_ini' => $rfkOpd->sd_bulan_ini ?? 0,
                    'keuangan' => $rfkOpd->keuangan ?? 0,
                    'fisik' => $rfkOpd->fisik ?? 0,
                ];

                $summary['total_pagu'] += $rfkOpd->pagu_anggaran ?? 0;
                $summary['total_realisasi'] += $rfkOpd->sd_bulan_ini ?? 0;
            }
        }

        // Calculate summary percentages
        if ($summary['total_pagu'] > 0) {
            $summary['pct_keuangan'] = round(($summary['total_realisasi'] / $summary['total_pagu']) * 100, 2);

            // Fisik: weighted average
            $totalFisikRupiah = 0;
            foreach ($opdData as $opd) {
                $totalFisikRupiah += ($opd['fisik'] / 100) * $opd['pagu_anggaran'];
            }
            $summary['pct_fisik'] = round(($totalFisikRupiah / $summary['total_pagu']) * 100, 2);
        }
        $summary['sisa_anggaran'] = $summary['total_pagu'] - $summary['total_realisasi'];

        $monthInt = (int)$bulan;
        $expectedTarget = round(($monthInt / 12) * 100, 2);
        $deviasi = round($summary['pct_keuangan'] - $expectedTarget, 2);
        $summary['expected_target'] = $expectedTarget;
        $summary['deviasi'] = $deviasi;
        $summary['triwulan'] = 'Triwulan ' . ceil($monthInt / 3);

        // ── RANK INFO ────────────────────────────────────────────────
        $rankInfo = null;
        $activeOpdId = $opdFilter ?: (Auth::user() ? Auth::user()->id_opd : null);

        // Only show rank when a specific OPD is selected or user is admin_opd
        $isSingleOpd = !empty($opdFilter) || (User::find(Auth::id())->hasRole('admin_opd') && !User::find(Auth::id())->hasRole('superadmin'));
        if ($isSingleOpd && $activeOpdId) {
            $rankInfo = Opd::getRank($activeOpdId, $bulan, $tahun);
            if ($rankInfo) {
                $opdModel = Opd::find($activeOpdId);
                $rankInfo['opd_name'] = $opdModel ? $opdModel->name : '';
            }
        }

        // ── MONTHLY TREND (12 months) ────────────────────────────────
        $monthLabels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthlyTrend = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);

            // All OPDs average
            $allOpdRfk = RfkV2Detail::select(
                    DB::raw('SUM(pagu_anggaran) as total_pagu'),
                    DB::raw('SUM(sd_bulan_ini) as total_realisasi'),
                    DB::raw('SUM((fisik / 100) * pagu_anggaran) as total_fisik_rupiah')
                )
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2.month', $monthStr)
                ->where('rfk_v2_detail.type', 'opd')
                ->whereNull('rfk_v2_detail.parent_id')
                ->first();

            $allTotalPagu = $allOpdRfk->total_pagu ?? 0;
            $allKeu = $allTotalPagu > 0 ? (($allOpdRfk->total_realisasi ?? 0) / $allTotalPagu) * 100 : 0;
            $allFisik = $allTotalPagu > 0 ? (($allOpdRfk->total_fisik_rupiah ?? 0) / $allTotalPagu) * 100 : 0;

            // Selected OPD(s)
            $selectedKeu = 0;
            $selectedFisik = 0;
            if (!empty($opdIds)) {
                $selectedOpdRfk = RfkV2Detail::select(
                        DB::raw('SUM(pagu_anggaran) as total_pagu'),
                        DB::raw('SUM(sd_bulan_ini) as total_realisasi'),
                        DB::raw('SUM((fisik / 100) * pagu_anggaran) as total_fisik_rupiah')
                    )
                    ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                    ->whereIn('rfk_v2.id_opd', $opdIds)
                    ->where('rfk_v2.year', $tahun)
                    ->where('rfk_v2.month', $monthStr)
                    ->where('rfk_v2_detail.type', 'opd')
                    ->whereNull('rfk_v2_detail.parent_id')
                    ->first();

                $selectedTotalPagu = $selectedOpdRfk->total_pagu ?? 0;
                $selectedKeu = $selectedTotalPagu > 0 ? (($selectedOpdRfk->total_realisasi ?? 0) / $selectedTotalPagu) * 100 : 0;
                $selectedFisik = $selectedTotalPagu > 0 ? (($selectedOpdRfk->total_fisik_rupiah ?? 0) / $selectedTotalPagu) * 100 : 0;
            }

            $monthlyTrend[] = [
                'month' => $monthLabels[$m - 1],
                'all_keuangan' => round($allKeu, 2),
                'all_fisik' => round($allFisik, 2),
                'selected_keuangan' => round($selectedKeu, 2),
                'selected_fisik' => round($selectedFisik, 2),
            ];
        }

        // ── OPD RANKING ──────────────────────────────────────────────
        $opdRanking = RfkV2Detail::select(
                'rfk_v2_detail.pagu_anggaran',
                'rfk_v2_detail.sd_bulan_ini',
                'rfk_v2_detail.keuangan',
                'rfk_v2_detail.fisik',
                'opd.name as opd_name',
                'rfk_v2.id_opd'
            )
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->get()
            ->map(function ($item) {
                return [
                    'opd_id' => $item->id_opd,
                    'opd_name' => $item->opd_name ?? '',
                    'pagu_anggaran' => (float)($item->pagu_anggaran ?? 0),
                    'sd_bulan_ini' => (float)($item->sd_bulan_ini ?? 0),
                    'keuangan' => (float)($item->keuangan ?? 0),
                    'fisik' => (float)($item->fisik ?? 0),
                ];
            })->toArray();

        // Apply custom OPD sort order
        $opdSortOrder = config('opd_sort_order');
        usort($opdRanking, function($a, $b) use ($opdSortOrder) {
            $orderA = $opdSortOrder[$a['opd_id']] ?? 999;
            $orderB = $opdSortOrder[$b['opd_id']] ?? 999;
            return $orderA - $orderB;
        });

        // ── PROGRAM DATA ─────────────────────────────────────────────
        $programData = [];
        $programSummary = [
            'total_pagu' => 0,
            'total_realisasi' => 0,
            'total_keu' => 0,
            'total_fisik' => 0,
        ];

        foreach ($opdIds as $opdId) {
            $rfkPrograms = RfkV2Detail::select('rfk_v2_detail.*')
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->where('rfk_v2.id_opd', $opdId)
                ->where('rfk_v2.month', $bulan)
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2_detail.type', 'program')
                ->get();

            $rfkPrograms = $rfkPrograms->map(function ($program) {
                $programName = $program->uraian ?? '';
                $programNameShort = strlen($programName) > 35 ? substr($programName, 0, 32) . '...' : $programName;

                return (object)[
                    'code' => $program->code,
                    'uraian' => $programName,
                    'uraian_short' => $programNameShort,
                    'pagu_anggaran' => $program->pagu_anggaran ?? 0,
                    'sd_bulan_ini' => $program->sd_bulan_ini ?? 0,
                    'keuangan' => $program->keuangan ?? 0,
                    'fisik' => $program->fisik ?? 0,
                ];
            })->filter(function ($program) {
                return !empty($program->uraian) && $program->pagu_anggaran > 0;
            })->values();

            foreach ($rfkPrograms as $program) {
                $programData[] = [
                    'opd_id' => $opdId,
                    'program_name' => $program->uraian ?? '',
                    'pagu_anggaran' => $program->pagu_anggaran ?? 0,
                    'sd_bulan_ini' => $program->sd_bulan_ini ?? 0,
                    'keuangan' => $program->keuangan ?? 0,
                    'fisik' => $program->fisik ?? 0,
                ];

                $programSummary['total_pagu'] += $program->pagu_anggaran ?? 0;
                $programSummary['total_realisasi'] += $program->sd_bulan_ini ?? 0;
            }
        }

        // Calculate program summary percentages
        if ($programSummary['total_pagu'] > 0) {
            $programSummary['total_keu'] = ($programSummary['total_realisasi'] / $programSummary['total_pagu']) * 100;

            $totalFisikRupiah = 0;
            foreach ($programData as $program) {
                $totalFisikRupiah += ($program['fisik'] / 100) * $program['pagu_anggaran'];
            }
            $programSummary['total_fisik'] = ($totalFisikRupiah / $programSummary['total_pagu']) * 100;
        }

        // ── FORECASTING & PROYEKSI SiLPA ───────────────────────────────
        $currentMonthNum = (int)$bulan;
        $monthlyRate = $currentMonthNum > 0 ? ($summary['total_realisasi'] / $currentMonthNum) : 0;
        $projectedRealisasi = min($summary['total_pagu'], $monthlyRate * 12);
        $projectedPctKeuangan = $summary['total_pagu'] > 0 ? round(($projectedRealisasi / $summary['total_pagu']) * 100, 2) : 0;
        $projectedSilpa = max(0, $summary['total_pagu'] - $projectedRealisasi);

        $forecastingData = [
            'monthly_rate' => $monthlyRate,
            'projected_realisasi' => $projectedRealisasi,
            'projected_pct_keuangan' => $projectedPctKeuangan,
            'projected_silpa' => $projectedSilpa,
            'current_month_num' => $currentMonthNum,
            'target_expected_pct' => round(($currentMonthNum / 12) * 100, 1),
        ];

        // ── WATCHLIST KRITIS (EARLY WARNING SYSTEM / EWS) ──────────────
        $watchlistKritis = [];
        $targetExpectedPct = round(($currentMonthNum / 12) * 100, 1);

        foreach ($opdRanking as $item) {
            $keuDiff = $item['keuangan'] - $targetExpectedPct;
            $fisikDiff = $item['fisik'] - $targetExpectedPct;

            if ($item['pagu_anggaran'] > 0 && ($keuDiff < 0 || $fisikDiff < 0)) {
                $status = 'Perhatian';
                if ($keuDiff < -10 || $fisikDiff < -10) {
                    $status = 'Deviasi Kritis';
                }
                $watchlistKritis[] = [
                    'opd_id' => $item['opd_id'],
                    'opd_name' => $item['opd_name'],
                    'pagu_anggaran' => $item['pagu_anggaran'],
                    'keuangan' => $item['keuangan'],
                    'fisik' => $item['fisik'],
                    'keu_diff' => round($keuDiff, 1),
                    'fisik_diff' => round($fisikDiff, 1),
                    'status' => $status,
                ];
            }
        }
        usort($watchlistKritis, function ($a, $b) {
            return $a['keu_diff'] <=> $b['keu_diff'];
        });

        // ── GIS MAP PROJECTS ──────────────────────────────────────────
        $kecamatanCoords = [
            1 => [-8.0664, 111.9015], // Tulungagung
            2 => [-8.1189, 111.9042], // Boyolangu
            3 => [-8.0381, 111.9167], // Kedungwaru
            4 => [-8.0053, 111.9367], // Ngantru
            5 => [-8.0531, 111.8542], // Kauman
            6 => [-8.0847, 111.8325], // Gondang
            7 => [-7.9942, 111.8792], // Karangrejo
            8 => [-7.9542, 111.8125], // Sendang
            9 => [-8.0167, 111.7583], // Pagerwojo
            10 => [-8.2333, 111.7833], // Besuki
            11 => [-8.1833, 111.7667], // Bandung
            12 => [-8.1667, 111.8333], // Pakel
            13 => [-8.1500, 111.8667], // Campurdarat
            14 => [-8.1833, 111.9667], // Kalidawir
            15 => [-8.1333, 112.0167], // Rejotangan
            16 => [-8.1000, 111.9833], // Ngunut
            17 => [-8.0833, 111.9500], // Sumbergempol
            18 => [-8.2333, 111.9000], // Tanggunggunung
            19 => [-8.2333, 112.0167], // Pucanglaban
        ];

        $projectsQuery = DB::table('projects')
            ->select(
                'projects.id',
                'projects.name',
                'projects.spk_amount',
                'projects.pagu_anggaran',
                'projects.id_kecamatan',
                'projects.lokasi',
                'opd.name as opd_name',
                'kecamatan.nama as kecamatan_nama',
                'partners.name as partner_name'
            )
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id')
            ->leftJoin('kecamatan', 'projects.id_kecamatan', '=', 'kecamatan.id')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id');

        if (!empty($opdIds)) {
            $projectsQuery->whereIn('projects.id_opd', $opdIds);
        }

        $rawProjects = $projectsQuery->limit(100)->get();

        $gisProjects = [];
        foreach ($rawProjects as $proj) {
            $kecId = $proj->id_kecamatan ?: 1;
            $coords = $kecamatanCoords[$kecId] ?? [-8.0664, 111.9015];

            $latJitter = (sin($proj->id * 1.5) * 0.006);
            $lngJitter = (cos($proj->id * 1.5) * 0.006);

            $docCount = DB::table('project_details')
                ->where('id_project', $proj->id)
                ->count(DB::raw('DISTINCT type'));

            $docPct = min(100, round(($docCount / 5) * 100));

            $statusBg = 'success';
            if ($docPct < 50) {
                $statusBg = 'danger';
            } elseif ($docPct < 80) {
                $statusBg = 'warning';
            }

            $gisProjects[] = [
                'id' => $proj->id,
                'name' => $proj->name,
                'opd_name' => $proj->opd_name ?? '-',
                'kecamatan_nama' => $proj->kecamatan_nama ?? 'Tulungagung',
                'lokasi' => $proj->lokasi ?? '-',
                'partner_name' => $proj->partner_name ?? '-',
                'amount' => (float)($proj->spk_amount ?: $proj->pagu_anggaran ?: 0),
                'lat' => $coords[0] + $latJitter,
                'lng' => $coords[1] + $lngJitter,
                'doc_pct' => $docPct,
                'doc_count' => $docCount,
                'status_color' => $statusBg,
            ];
        }

        // ── DOCUMENT MATRIX PER OPD ───────────────────────────────────
        $documentMatrix = [];
        foreach ($opdIds as $oId) {
            $opdModel = Opd::find($oId);
            if (!$opdModel) continue;

            $opdProjects = DB::table('projects')->where('id_opd', $oId)->pluck('id')->toArray();
            if (empty($opdProjects)) continue;

            $totalProj = count($opdProjects);
            $totalDocsFilled = DB::table('project_details')
                ->whereIn('id_project', $opdProjects)
                ->count(DB::raw('DISTINCT id_project, type'));

            $divider = ($totalProj * 5);
            $avgPct = $divider > 0 ? round(($totalDocsFilled / $divider) * 100, 1) : 0;

            $documentMatrix[] = [
                'opd_id' => $oId,
                'opd_name' => $opdModel->name,
                'total_projects' => $totalProj,
                'doc_filled' => $totalDocsFilled,
                'doc_completeness_pct' => min(100, $avgPct),
            ];
        }

        Log::info('Dashboard V2 API Response', [
            'summary' => $summary,
            'opd_ranking_count' => count($opdRanking),
            'program_data_count' => count($programData),
        ]);

        $categoryDesc = $summary['pct_keuangan'] >= 70 ? 'Tinggi' : ($summary['pct_keuangan'] >= 40 ? 'Sedang' : 'Rendah');
        $watchlistCount = count($watchlistKritis);
        $silpaTriliun = number_format($forecastingData['projected_silpa'] / 1000000000000, 2, ',', '.');
        $narrativeSnapshot = "Realisasi keuangan daerah mencapai {$summary['pct_keuangan']}% (Kategori {$categoryDesc}). Terdapat {$watchlistCount} OPD yang perlu akselerasi serapan, dengan proyeksi SiLPA akhir tahun sebesar Rp {$silpaTriliun} Triliun.";

        return response()->json([
            'summary' => $summary,
            'narrative_snapshot' => $narrativeSnapshot,
            'rank_info' => $rankInfo,
            'monthly_trend' => $monthlyTrend,
            'opd_ranking' => $opdRanking,
            'program_data' => $programData,
            'program_summary' => $programSummary,
            'watchlist_kritis' => $watchlistKritis,
            'forecasting' => $forecastingData,
            'gis_projects' => $gisProjects,
            'document_matrix' => $documentMatrix,
        ]);
    }

    /**
     * Export Dashboard V2 summary to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');

        $bulan = Carbon::parse($month)->format('m');
        $tahun = Carbon::parse($month)->format('Y');

        // Get OPD filter - same logic as getApiData
        $user = Auth::user();
        if ($opdFilter) {
            $opdIds = [$opdFilter];
        } elseif ($user && $user->hasRole('admin_opd') && !$user->hasRole('superadmin') && !$user->hasRole('eksekutif') && $user->id_opd) {
            $opdIds = [$user->id_opd];
        } else {
            $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
        }

        // Build summary
        $summary = [
            'total_pagu' => 0,
            'total_realisasi' => 0,
            'pct_keuangan' => 0,
            'pct_fisik' => 0,
        ];

        $opdData = [];
        foreach ($opdIds as $opdId) {
            $opd = Opd::find($opdId);
            if (!$opd) continue;

            $rfkOpd = RfkV2Detail::select('rfk_v2_detail.*')
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->where('rfk_v2.id_opd', $opdId)
                ->where('rfk_v2.month', $bulan)
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2_detail.type', 'opd')
                ->whereNull('rfk_v2_detail.parent_id')
                ->first();

            if ($rfkOpd) {
                $opdData[] = [
                    'pagu_anggaran' => $rfkOpd->pagu_anggaran ?? 0,
                    'fisik' => $rfkOpd->fisik ?? 0,
                ];
                $summary['total_pagu'] += $rfkOpd->pagu_anggaran ?? 0;
                $summary['total_realisasi'] += $rfkOpd->sd_bulan_ini ?? 0;
            }
        }

        if ($summary['total_pagu'] > 0) {
            $summary['pct_keuangan'] = round(($summary['total_realisasi'] / $summary['total_pagu']) * 100, 2);

            $totalFisikRupiah = 0;
            foreach ($opdData as $opd) {
                $totalFisikRupiah += ($opd['fisik'] / 100) * $opd['pagu_anggaran'];
            }
            $summary['pct_fisik'] = round(($totalFisikRupiah / $summary['total_pagu']) * 100, 2);
        }

        // OPD Ranking (all OPDs)
        $opdRanking = RfkV2Detail::select(
                'rfk_v2_detail.pagu_anggaran',
                'rfk_v2_detail.sd_bulan_ini',
                'rfk_v2_detail.keuangan',
                'rfk_v2_detail.fisik',
                'opd.name as opd_name',
                'rfk_v2.id_opd'
            )
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->get()
            ->map(function ($item) {
                return [
                    'opd_id' => $item->id_opd,
                    'opd_name' => $item->opd_name ?? '',
                    'pagu_anggaran' => (float)($item->pagu_anggaran ?? 0),
                    'sd_bulan_ini' => (float)($item->sd_bulan_ini ?? 0),
                    'keuangan' => (float)($item->keuangan ?? 0),
                    'fisik' => (float)($item->fisik ?? 0),
                ];
            })->toArray();

        // Apply custom OPD sort order
        $opdSortOrder = config('opd_sort_order');
        usort($opdRanking, function($a, $b) use ($opdSortOrder) {
            $orderA = $opdSortOrder[$a['opd_id']] ?? 999;
            $orderB = $opdSortOrder[$b['opd_id']] ?? 999;
            return $orderA - $orderB;
        });

        $data = [
            'summary' => $summary,
            'opd_ranking' => $opdRanking,
        ];

        $bulanName = Carbon::createFromFormat('m', $bulan)->locale('id')->translatedFormat('F');
        $filename = 'Dashboard_Executive_' . $bulanName . '_' . $tahun . '.xlsx';

        return Excel::download(new DashboardV2Export($data, $bulan, $tahun), $filename);
    }
}
