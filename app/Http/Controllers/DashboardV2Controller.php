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

        // Get OPD filter - if superadmin and no filter, get all; otherwise use user's OPD
        if (User::find(Auth::id())->hasRole('superadmin')) {
            if ($opdFilter) {
                $opdIds = [$opdFilter];
            } else {
                $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
            }
        } else {
            $opdIds = [Auth::user()->id_opd];
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
            ->orderBy('rfk_v2_detail.keuangan', 'DESC')
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

        Log::info('Dashboard V2 API Response', [
            'summary' => $summary,
            'opd_ranking_count' => count($opdRanking),
            'program_data_count' => count($programData),
        ]);

        return response()->json([
            'summary' => $summary,
            'rank_info' => $rankInfo,
            'monthly_trend' => $monthlyTrend,
            'opd_ranking' => $opdRanking,
            'program_data' => $programData,
            'program_summary' => $programSummary,
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
        if (User::find(Auth::id())->hasRole('superadmin')) {
            if ($opdFilter) {
                $opdIds = [$opdFilter];
            } else {
                $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
            }
        } else {
            $opdIds = [Auth::user()->id_opd];
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
                'opd.name as opd_name'
            )
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->orderBy('rfk_v2_detail.keuangan', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'opd_name' => $item->opd_name ?? '',
                    'pagu_anggaran' => (float)($item->pagu_anggaran ?? 0),
                    'sd_bulan_ini' => (float)($item->sd_bulan_ini ?? 0),
                    'keuangan' => (float)($item->keuangan ?? 0),
                    'fisik' => (float)($item->fisik ?? 0),
                ];
            })->toArray();

        $data = [
            'summary' => $summary,
            'opd_ranking' => $opdRanking,
        ];

        $bulanName = Carbon::createFromFormat('m', $bulan)->locale('id')->translatedFormat('F');
        $filename = 'Dashboard_Executive_' . $bulanName . '_' . $tahun . '.xlsx';

        return Excel::download(new DashboardV2Export($data, $bulan, $tahun), $filename);
    }
}
