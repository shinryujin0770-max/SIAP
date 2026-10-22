<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\Project;
use App\Models\Project_details;
use App\Models\Rfk;
use App\Models\Rfk_detail;
use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use App\Models\User;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data['filter_year'] = request('filter_year');
        $data['filter_month'] = request('filter_month');
        // For RFK dashboard, default to "Semua OPD" (empty) instead of user's OPD
        // Only set user's OPD as default for admin_opd role who can only see their own data
        if ($user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_opd') && !$user->hasRole('superadmin')) {
            $data['filter_opd'] = request('filter_opd') ?? $user->id_opd;
        } else {
            $data['filter_opd'] = request('filter_opd') ?? '';
        }
        $this_year = $data['filter_year'] ?? date('Y');

        $data['opds'] = Opd::all();
        $projects = Project::selectRaw('category, count(id) as count')
            ->groupBy('category')
            ->whereYear('spk_date', $this_year);
        if ($data['filter_opd']) {
            $projects = $projects->where('id_opd', $data['filter_opd']);
        }

        $data['projects'] = json_encode($projects->get()->pluck('count'));

        $hps_sum = Project::select(DB::raw("DATE_FORMAT(spk_date, '%Y-%m') AS month_year, SUM(spk_amount) AS spk_sum, SUM(hps) AS hps_sum, SUM(pagu_anggaran) AS pagu_anggaran_sum"))
            ->groupBy("month_year")
            ->orderBy("month_year", 'ASC');
        if ($data['filter_opd']) {
            $hps_sum = $hps_sum->where('id_opd', $data['filter_opd']);
        }
        $hps_sum = $hps_sum->get();
        // dd($hps_sum);
        $checklist_sum = Project_details::select(DB::raw("DATE_FORMAT(spk_date, '%Y-%m') AS month_year, SUM(project_details.amount) AS amount_sum"))
            ->leftJoin('projects', 'project_details.id_project', '=', 'projects.id')
            ->where('project_details.amount', '>', 0)
            ->where('project_details.paid', 1)
            ->groupBy("month_year")
            ->orderBy("month_year", 'ASC');

        if ($data['filter_opd']) {
            $checklist_sum = $checklist_sum->where('projects.id_opd', $data['filter_opd']);
        }
        $checklist_sum = $checklist_sum->get();


        for ($month = 1; $month <= 12; $month++) {
            $year_month = new DateTime("$this_year-$month");
            $year_month = $year_month->format('Y-m');

            $find = $hps_sum->filter(fn ($item) => $item['month_year'] == $year_month)->values();
            $find_checlist = $checklist_sum->filter(fn ($item) => $item['month_year'] == $year_month)->values();

            $chart_hps[] =  count($find) && $find[0]['hps_sum'] != 0 ? ($find[0]['spk_sum'] / $find[0]['hps_sum'] * 100) : 0;
            $chart_avarage_hps[] =  count($find) ? $find[0]['hps_sum'] : 0;
            $chart_avarage_spk[] =  count($find) ? $find[0]['spk_sum'] : 0;
            $chart_avarage_pagu_anggaran[] =  count($find) ? $find[0]['pagu_anggaran_sum'] : 0;
            $chart_avarage_realisasi[] =  count($find_checlist) ? $find_checlist[0]['amount_sum'] : 0;
        }

        $data['chart_avarage_hps'] = json_encode($chart_avarage_hps);
        $data['chart_avarage_spk'] = json_encode($chart_avarage_spk);
        $data['chart_avarage_pagu_anggaran'] = json_encode($chart_avarage_pagu_anggaran);
        $data['chart_avarage_realisasi'] = json_encode($chart_avarage_realisasi);

        $data['hps'] = json_encode($chart_hps);

        // Calculate totals per category
        $categories = ['konstruksi', 'perencanaan', 'pengawasan'];
        foreach ($categories as $category) {
            $categoryQuery = Project::select(
                DB::raw('SUM(pagu_anggaran) as total_pagu'),
                DB::raw('SUM(hps) as total_hps'),
                DB::raw('SUM(spk_amount) as total_kontrak')
            )
            ->whereYear('spk_date', $this_year)
            ->where('category', $category);

            if ($data['filter_opd']) {
                $categoryQuery = $categoryQuery->where('id_opd', $data['filter_opd']);
            }

            $categoryTotals = $categoryQuery->first();

            $data['category_totals'][$category] = [
                'pagu' => $categoryTotals->total_pagu ?? 0,
                'hps' => $categoryTotals->total_hps ?? 0,
                'kontrak' => $categoryTotals->total_kontrak ?? 0,
                'sisa_pagu' => ($categoryTotals->total_pagu ?? 0) - ($categoryTotals->total_kontrak ?? 0)
            ];
        }

        return view('dashboard', $data);
    }

    /**
     * Get RFK dashboard data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRFKData(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');

        $bulan = Carbon::parse($month)->format('m');
        $tahun = Carbon::parse($month)->format('Y');

        Log::info('RFK Dashboard Request', [
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

        Log::info('OPD IDs to query', ['opd_ids' => $opdIds]);

        $opdData = [];
        $programData = [];
        $summary = [
            'total_pagu' => 0,
            'total_realisasi' => 0,
            'total_keu' => 0,
            'total_fisik' => 0,
        ];
        $programSummary = [
            'total_pagu' => 0,
            'total_realisasi' => 0,
            'total_keu' => 0,
            'total_fisik' => 0,
        ];

        foreach ($opdIds as $opdId) {
            $opd = Opd::find($opdId);
            if (!$opd) continue;

            // Get OPD-level RFK data from rfk_v2_detail
            $rfkOpdQuery = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
                ->where('rfk_v2.id_opd', $opdId)
                ->where('rfk_v2.month', $bulan)
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2_detail.type', 'opd')
                ->whereNull('rfk_v2_detail.parent_id');

            $rfkOpd = $rfkOpdQuery->first();

            Log::info('RFK OPD Query Result', [
                'opd_id' => $opdId,
                'opd_name' => $opd->name,
                'found' => $rfkOpd ? 'yes' : 'no',
                'data' => $rfkOpd ? $rfkOpd->toArray() : null
            ]);

            if ($rfkOpd) {
                $opdData[] = [
                    'opd_id' => $opdId,
                    'opd_name' => $opd->name,
                    'pagu_anggaran' => $rfkOpd->pagu_anggaran ?? 0,
                    'bulan_ini' => $rfkOpd->bulan_ini ?? 0,
                    'sd_bulan_ini' => $rfkOpd->sd_bulan_ini ?? 0,
                    'keuangan' => $rfkOpd->keuangan ?? 0,
                    'fisik' => $rfkOpd->fisik ?? 0,
                ];

                $summary['total_pagu'] += $rfkOpd->pagu_anggaran ?? 0;
                $summary['total_realisasi'] += $rfkOpd->sd_bulan_ini ?? 0;
            }

            // Get Program-level RFK data from rfk_v2_detail
            $rfkPrograms = RfkV2Detail::select('rfk_v2_detail.*')
                ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->where('rfk_v2.id_opd', $opdId)
                ->where('rfk_v2.month', $bulan)
                ->where('rfk_v2.year', $tahun)
                ->where('rfk_v2_detail.type', 'program')
                ->get();

            // Transform to expected format
            $rfkPrograms = $rfkPrograms->map(function($program) {
                // Truncate long program names for display (keep full name in uraian)
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
            })->filter(function($program) {
                return !empty($program->uraian) && $program->pagu_anggaran > 0;
            })->values();

            foreach ($rfkPrograms as $program) {
                $programData[] = [
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

        // Apply custom OPD sort order
        $opdSortOrder = config('opd_sort_order');
        usort($opdData, function($a, $b) use ($opdSortOrder) {
            $orderA = $opdSortOrder[$a['opd_id']] ?? 999;
            $orderB = $opdSortOrder[$b['opd_id']] ?? 999;
            return $orderA - $orderB;
        });

        // Calculate percentages for summary
        if ($summary['total_pagu'] > 0) {
            $summary['total_keu'] = ($summary['total_realisasi'] / $summary['total_pagu']) * 100;

            // For fisik, we need to calculate weighted average
            $totalFisikRupiah = 0;
            foreach ($opdData as $opd) {
                $totalFisikRupiah += ($opd['fisik'] / 100) * $opd['pagu_anggaran'];
            }
            $summary['total_fisik'] = ($totalFisikRupiah / $summary['total_pagu']) * 100;
        }

        // Calculate percentages for program summary
        if ($programSummary['total_pagu'] > 0) {
            $programSummary['total_keu'] = ($programSummary['total_realisasi'] / $programSummary['total_pagu']) * 100;

            // For fisik, calculate weighted average
            $totalFisikRupiah = 0;
            foreach ($programData as $program) {
                $totalFisikRupiah += ($program['fisik'] / 100) * $program['pagu_anggaran'];
            }
            $programSummary['total_fisik'] = ($totalFisikRupiah / $programSummary['total_pagu']) * 100;
        }

        // Prepare chart data - pass program_data that we already collected
        $chartData = $this->prepareRFKChartData($bulan, $tahun, $opdIds, $programData);

        $activeOpdId = $opdFilter ?: (Auth::user() ? Auth::user()->id_opd : null);
        $rankInfo = null;
        if ($activeOpdId) {
            $rankInfo = \App\Models\Opd::getRank($activeOpdId, $bulan, $tahun);
            if ($rankInfo) {
                $opdModel = \App\Models\Opd::find($activeOpdId);
                $rankInfo['opd_name'] = $opdModel ? $opdModel->name : '';
            }
        }

        Log::info('RFK Dashboard Response', [
            'summary' => $summary,
            'opd_data_count' => count($opdData),
            'program_data_count' => count($programData),
        ]);

        return response()->json([
            'summary' => $summary,
            'opd_data' => $opdData,
            'program_data' => $programData,
            'program_summary' => $programSummary,
            'chart_data' => $chartData,
            'rank_info' => $rankInfo,
        ]);
    }

    /**
     * Prepare RFK chart data
     *
     * @param string $bulan
     * @param string $tahun
     * @param array $opdIds
     * @param array $programData - Already collected program data from getRFKData
     * @return array
     */
    private function prepareRFKChartData($bulan, $tahun, $opdIds, $programData)
    {
        // Chart 1: Program chart data (for current month's programs)
        // Use the program_data that we already collected in getRFKData
        $programChartLabels = [];
        $programKeuData = [];
        $programFisikData = [];

        if (!empty($programData)) {
            foreach ($programData as $program) {
                // Truncate long program names for display
                $programName = $program['program_name'] ?? '';
                if (strlen($programName) > 50) {
                    $programName = substr($programName, 0, 47) . '...';
                }
                if (!empty($programName)) {
                    $programChartLabels[] = $programName;
                    $programKeuData[] = (float)($program['keuangan'] ?? 0);
                    $programFisikData[] = (float)($program['fisik'] ?? 0);
                }
            }
        }

        // Chart 2 & 3: Comparison charts (monthly data for current year)
        $monthLabels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Get data for all OPDs (for comparison)
        $allOpdKeuData = [];
        $allOpdFisikData = [];

        // Get data for selected OPD(s)
        $selectedOpdKeuData = [];
        $selectedOpdFisikData = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);

            // Weighted average for all OPDs from rfk_v2_detail
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

            $allOpdKeuData[] = round($allKeu, 2);
            $allOpdFisikData[] = round($allFisik, 2);

            // Weighted average for selected OPD(s)
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

                $selectedOpdKeuData[] = round($selectedKeu, 2);
                $selectedOpdFisikData[] = round($selectedFisik, 2);
            } else {
                $selectedOpdKeuData[] = 0;
                $selectedOpdFisikData[] = 0;
            }
        }

        // Get Top 5 and Bottom 5 OPDs by Keuangan and Fisik for current month from rfk_v2_detail
        // Top 5 OPDs by Keuangan
        $top5Keu = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->orderBy('rfk_v2_detail.keuangan', 'DESC')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'opd_name' => $item->opd_name ?? '',
                    'keuangan' => (float)($item->keuangan ?? 0),
                ];
            })->toArray();

        // Bottom 5 OPDs by Keuangan
        $bottom5Keu = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->orderBy('rfk_v2_detail.keuangan', 'ASC')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'opd_name' => $item->opd_name ?? '',
                    'keuangan' => (float)($item->keuangan ?? 0),
                ];
            })->toArray();

        // Top 5 OPDs by Fisik
        $top5Fisik = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->orderBy('rfk_v2_detail.fisik', 'DESC')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'opd_name' => $item->opd_name ?? '',
                    'fisik' => (float)($item->fisik ?? 0),
                ];
            })->toArray();

        // Bottom 5 OPDs by Fisik
        $bottom5Fisik = RfkV2Detail::select('rfk_v2_detail.*', 'opd.name as opd_name')
            ->leftJoin('rfk_v2', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->leftJoin('opd', 'rfk_v2.id_opd', '=', 'opd.id')
            ->where('rfk_v2.month', $bulan)
            ->where('rfk_v2.year', $tahun)
            ->where('rfk_v2_detail.type', 'opd')
            ->whereNull('rfk_v2_detail.parent_id')
            ->orderBy('rfk_v2_detail.fisik', 'ASC')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'opd_name' => $item->opd_name ?? '',
                    'fisik' => (float)($item->fisik ?? 0),
                ];
            })->toArray();

        return [
            'program_chart' => [
                'labels' => $programChartLabels,
                'keu_data' => $programKeuData,
                'fisik_data' => $programFisikData,
            ],
            'comparison_chart' => [
                'month_labels' => $monthLabels,
                'keu_all_opd' => $allOpdKeuData,
                'keu_selected_opd' => $selectedOpdKeuData,
                'fisik_all_opd' => $allOpdFisikData,
                'fisik_selected_opd' => $selectedOpdFisikData,
            ],
            'top5_keu' => $top5Keu,
            'bottom5_keu' => $bottom5Keu,
            'top5_fisik' => $top5Fisik,
            'bottom5_fisik' => $bottom5Fisik,
        ];
    }

    /**
     * Export Dashboard V1 summary to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');

        $bulan = Carbon::parse($month)->format('m');
        $tahun = Carbon::parse($month)->format('Y');

        if ($user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_opd') && !$user->hasRole('superadmin')) {
            $opdIds = [$user->id_opd];
        } else {
            if ($opdFilter) {
                $opdIds = [$opdFilter];
            } else {
                $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
            }
        }

        $opdData = [];
        $summary = [
            'total_pagu' => 0,
            'total_bulan_ini' => 0,
            'total_realisasi' => 0,
            'total_keu' => 0,
            'total_fisik' => 0,
        ];

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
                    'opd_id' => $opdId,
                    'opd_name' => $opd->name,
                    'pagu_anggaran' => (float)($rfkOpd->pagu_anggaran ?? 0),
                    'bulan_ini' => (float)($rfkOpd->bulan_ini ?? 0),
                    'sd_bulan_ini' => (float)($rfkOpd->sd_bulan_ini ?? 0),
                    'keuangan' => (float)($rfkOpd->keuangan ?? 0),
                    'fisik' => (float)($rfkOpd->fisik ?? 0),
                ];

                $summary['total_pagu'] += $rfkOpd->pagu_anggaran ?? 0;
                $summary['total_bulan_ini'] += $rfkOpd->bulan_ini ?? 0;
                $summary['total_realisasi'] += $rfkOpd->sd_bulan_ini ?? 0;
            }
        }

        // Apply custom OPD sort order
        $opdSortOrder = config('opd_sort_order');
        usort($opdData, function($a, $b) use ($opdSortOrder) {
            $orderA = $opdSortOrder[$a['opd_id']] ?? 999;
            $orderB = $opdSortOrder[$b['opd_id']] ?? 999;
            return $orderA - $orderB;
        });

        if ($summary['total_pagu'] > 0) {
            $summary['total_keu'] = ($summary['total_realisasi'] / $summary['total_pagu']) * 100;

            $totalFisikRupiah = 0;
            foreach ($opdData as $opd) {
                $totalFisikRupiah += ($opd['fisik'] / 100) * $opd['pagu_anggaran'];
            }
            $summary['total_fisik'] = ($totalFisikRupiah / $summary['total_pagu']) * 100;
        }

        $data = [
            'summary' => $summary,
            'opd_data' => $opdData,
        ];

        $bulanName = Carbon::createFromFormat('m', $bulan)->locale('id')->translatedFormat('F');
        $filename = 'Laporan_Dashboard_' . $bulanName . '_' . $tahun . '.xlsx';

        return Excel::download(new DashboardExport($data, $bulan, $tahun), $filename);
    }
}
