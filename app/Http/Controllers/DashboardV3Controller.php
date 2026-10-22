<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\Project;
use App\Models\Project_details;
use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardV3Controller extends Controller
{
    /**
     * Centroid coordinates for 19 Kecamatans in Tulungagung Regency.
     */
    protected $kecamatanCoords = [
        1 => ['lat' => -8.0664, 'lng' => 111.9015, 'name' => 'Tulungagung'],
        2 => ['lat' => -8.1065, 'lng' => 111.9022, 'name' => 'Boyolangu'],
        3 => ['lat' => -8.0461, 'lng' => 111.9168, 'name' => 'Kedungwaru'],
        4 => ['lat' => -8.0167, 'lng' => 111.9500, 'name' => 'Ngantru'],
        5 => ['lat' => -8.0772, 'lng' => 111.8594, 'name' => 'Kauman'],
        6 => ['lat' => -7.9861, 'lng' => 111.8028, 'name' => 'Pagerwojo'],
        7 => ['lat' => -7.9542, 'lng' => 111.8389, 'name' => 'Sendang'],
        8 => ['lat' => -8.0194, 'lng' => 111.8750, 'name' => 'Karangrejo'],
        9 => ['lat' => -8.0833, 'lng' => 111.8167, 'name' => 'Gondang'],
        10 => ['lat' => -8.0889, 'lng' => 111.9611, 'name' => 'Sumbergempol'],
        11 => ['lat' => -8.1167, 'lng' => 112.0167, 'name' => 'Ngunut'],
        12 => ['lat' => -8.1333, 'lng' => 112.0667, 'name' => 'Rejotangan'],
        13 => ['lat' => -8.1667, 'lng' => 111.9833, 'name' => 'Kalidawir'],
        14 => ['lat' => -8.2333, 'lng' => 112.0000, 'name' => 'Pucanglaban'],
        15 => ['lat' => -8.2167, 'lng' => 111.8833, 'name' => 'Tanggunggunung'],
        16 => ['lat' => -8.1667, 'lng' => 111.8500, 'name' => 'Campurdarat'],
        17 => ['lat' => -8.1500, 'lng' => 111.8000, 'name' => 'Pakel'],
        18 => ['lat' => -8.2500, 'lng' => 111.7833, 'name' => 'Besuki'],
        19 => ['lat' => -8.1833, 'lng' => 111.7667, 'name' => 'Bandung'],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show Dashboard V3 view
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data['filter_year'] = request('filter_year', date('Y'));
        $data['filter_month'] = request('filter_month', date('Y-m'));

        if ($user instanceof User && method_exists($user, 'hasRole') && $user->hasRole('admin_opd') && !$user->hasRole('superadmin')) {
            $data['filter_opd'] = request('filter_opd') ?? $user->id_opd;
        } else {
            $data['filter_opd'] = request('filter_opd') ?? '';
        }

        $data['opds'] = Opd::orderBy('name', 'asc')->get();
        $data['kecamatans'] = DB::table('kecamatan')->orderBy('nama', 'asc')->get();

        return view('dashboard-v3', $data);
    }

    /**
     * API data endpoint for Dashboard V3
     */
    public function getApiData(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');
        $kecamatanFilter = $request->input('kecamatan', '');
        $categoryFilter = $request->input('category', '');
        $statusFilter = $request->input('status', ''); // all, green, yellow, red

        $bulan = Carbon::parse($month)->format('m');
        $tahun = Carbon::parse($month)->format('Y');
        $monthInt = (int)$bulan;

        // Triwulan target baseline
        // TW1 (Bulan 1-3): 20%, TW2 (Bulan 4-6): 50%, TW3 (Bulan 7-9): 75%, TW4 (Bulan 10-12): 100%
        $triwulanKe = ceil($monthInt / 3);
        $expectedMonthlyTarget = ($monthInt / 12) * 100;
        $targetTriwulan = [
            1 => 20,
            2 => 50,
            3 => 75,
            4 => 100
        ][$triwulanKe] ?? 75;

        // 1. Get OPD IDs
        if (User::find(Auth::id())->hasRole('superadmin')) {
            if ($opdFilter) {
                $opdIds = [$opdFilter];
            } else {
                $opdIds = Opd::where('rfk_code', '!=', '')->pluck('id')->toArray();
            }
        } else {
            $opdIds = [Auth::user()->id_opd];
        }

        // 2. Realisasi RFK per OPD
        $opdRanking = [];
        $totalPagu = 0;
        $totalRealisasi = 0;
        $totalPaguFisikRupiah = 0;

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

            $pagu = (float)($rfkOpd->pagu_anggaran ?? 0);
            $realisasi = (float)($rfkOpd->sd_bulan_ini ?? 0);
            $keuanganPct = $pagu > 0 ? ($realisasi / $pagu) * 100 : 0;
            $fisikPct = (float)($rfkOpd->fisik ?? 0);
            $sisaAnggaran = $pagu - $realisasi;
            $deviasi = $keuanganPct - $expectedMonthlyTarget;

            $status = 'green';
            if ($deviasi < -10) {
                $status = 'red';
            } elseif ($deviasi < -5) {
                $status = 'yellow';
            }

            $opdRanking[] = [
                'opd_id' => $opdId,
                'opd_name' => $opd->name,
                'pagu_anggaran' => $pagu,
                'realisasi' => $realisasi,
                'sisa_anggaran' => $sisaAnggaran,
                'keuangan' => round($keuanganPct, 2),
                'fisik' => round($fisikPct, 2),
                'deviasi' => round($deviasi, 2),
                'status' => $status,
            ];

            $totalPagu += $pagu;
            $totalRealisasi += $realisasi;
            $totalPaguFisikRupiah += ($fisikPct / 100) * $pagu;
        }

        // Summary calculations
        $overallKeuPct = $totalPagu > 0 ? ($totalRealisasi / $totalPagu) * 100 : 0;
        $overallFisikPct = $totalPagu > 0 ? ($totalPaguFisikRupiah / $totalPagu) * 100 : 0;
        $overallSisa = $totalPagu - $totalRealisasi;
        $overallDeviasi = $overallKeuPct - $expectedMonthlyTarget;

        // Sort ranking
        usort($opdRanking, function ($a, $b) {
            return $b['keuangan'] <=> $a['keuangan'];
        });

        $top5Opd = array_slice($opdRanking, 0, 5);
        $bottom5Opd = array_slice(array_reverse($opdRanking), 0, 5);

        // Zona Merah: OPDs with deviasi < -10% or below triwulan target
        $zonaMerahOpd = array_values(array_filter($opdRanking, function ($item) use ($targetTriwulan) {
            return $item['keuangan'] < ($targetTriwulan * 0.7) || $item['deviasi'] < -10;
        }));

        // 3. SPATIAL GIS PROJECTS QUERY
        $projectsQuery = DB::table('projects')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id')
            ->leftJoin('kecamatan', 'projects.id_kecamatan', '=', 'kecamatan.id')
            ->leftJoin('kelurahan', 'projects.id_kelurahan', '=', 'kelurahan.id')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id')
            ->select(
                'projects.id',
                'projects.name',
                'projects.category',
                'projects.spk_number',
                'projects.spk_amount',
                'projects.pagu_anggaran',
                'projects.spk_date',
                'projects.spk_days',
                'projects.spk_start',
                'projects.spk_end',
                'projects.lokasi',
                'projects.id_kecamatan',
                'projects.id_kelurahan',
                'projects.id_opd',
                'opd.name as opd_name',
                'kecamatan.nama as kecamatan_nama',
                'kelurahan.nama as kelurahan_nama',
                DB::raw('COALESCE(partners.name, projects.name_partner, "Belum Ditentukan") as partner_name')
            );

        // Year filter
        if ($tahun) {
            $projectsQuery->where(function ($q) use ($tahun) {
                $q->whereYear('projects.spk_date', $tahun)
                  ->orWhereNull('projects.spk_date');
            });
        }

        if ($opdFilter) {
            $projectsQuery->where('projects.id_opd', $opdFilter);
        }

        if ($kecamatanFilter) {
            $projectsQuery->where('projects.id_kecamatan', $kecamatanFilter);
        }

        if ($categoryFilter) {
            $projectsQuery->where('projects.category', $categoryFilter);
        }

        $rawProjects = $projectsQuery->get();

        // Fetch document checklist count per project
        $projectIds = $rawProjects->pluck('id')->toArray();
        $docCounts = [];
        if (!empty($projectIds)) {
            $docRows = DB::table('project_details')
                ->whereIn('id_project', $projectIds)
                ->where('paid', 1)
                ->select('id_project', DB::raw('COUNT(DISTINCT type) as verified_types'))
                ->groupBy('id_project')
                ->get();

            foreach ($docRows as $dr) {
                $docCounts[$dr->id_project] = (int)$dr->verified_types;
            }
        }

        $gisProjects = [];
        $totalHijau = 0;
        $totalKuning = 0;
        $totalMerah = 0;

        foreach ($rawProjects as $proj) {
            $kecId = (int)($proj->id_kecamatan ?? 1);
            if ($kecId < 1 || $kecId > 19) {
                $kecId = 1;
            }

            $center = $this->kecamatanCoords[$kecId] ?? $this->kecamatanCoords[1];

            // Deterministic jitter to disperse markers around the kecamatan center
            $jitterSeed = (int)$proj->id * 17;
            $latOffset = ((($jitterSeed * 13) % 100) - 50) * 0.00035;
            $lngOffset = ((($jitterSeed * 19) % 100) - 50) * 0.00035;

            $lat = round($center['lat'] + $latOffset, 6);
            $lng = round($center['lng'] + $lngOffset, 6);

            $verifiedDocs = $docCounts[$proj->id] ?? 0;
            $docPct = min(100, round(($verifiedDocs / 5) * 100));

            // Status Realisasi logic
            // 🟢 Green: On Track (Docs >= 80% or SPK completed)
            // 🟡 Yellow: Minor deviation (Docs 50% - 79%)
            // 🔴 Red: Critical deviation (Docs < 50% or over time)
            if ($docPct >= 80) {
                $statusColor = 'success';
                $statusLabel = 'Sesuai Target / Tepat Waktu';
                $totalHijau++;
            } elseif ($docPct >= 50) {
                $statusColor = 'warning';
                $statusLabel = 'Deviasi Minor (5% - 10%)';
                $totalKuning++;
            } else {
                $statusColor = 'danger';
                $statusLabel = 'Deviasi Kritis (> 10%)';
                $totalMerah++;
            }

            // Apply status filter if active
            if ($statusFilter) {
                if ($statusFilter === 'green' && $statusColor !== 'success') continue;
                if ($statusFilter === 'yellow' && $statusColor !== 'warning') continue;
                if ($statusFilter === 'red' && $statusColor !== 'danger') continue;
            }

            $gmapsUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}";

            $gisProjects[] = [
                'id' => $proj->id,
                'name' => $proj->name,
                'category' => ucfirst($proj->category ?? 'Konstruksi'),
                'opd_id' => $proj->id_opd,
                'opd_name' => $proj->opd_name ?? 'Pemerintah Kabupaten Tulungagung',
                'kecamatan_id' => $proj->id_kecamatan,
                'kecamatan_nama' => $proj->kecamatan_nama ?? $center['name'],
                'kelurahan_nama' => $proj->kelurahan_nama ?? '-',
                'lokasi' => $proj->lokasi ?: ($proj->kecamatan_nama ? 'Kec. ' . $proj->kecamatan_nama : 'Kab. Tulungagung'),
                'partner_name' => $proj->partner_name,
                'spk_number' => $proj->spk_number ?: '-',
                'pagu_anggaran' => (float)($proj->pagu_anggaran ?? 0),
                'spk_amount' => (float)($proj->spk_amount ?? 0),
                'spk_days' => $proj->spk_days ?: '-',
                'spk_start' => $proj->spk_start ? date('d/m/Y', strtotime($proj->spk_start)) : '-',
                'spk_end' => $proj->spk_end ? date('d/m/Y', strtotime($proj->spk_end)) : '-',
                'lat' => $lat,
                'lng' => $lng,
                'gmaps_url' => $gmapsUrl,
                'doc_count' => $verifiedDocs,
                'doc_pct' => $docPct,
                'status_color' => $statusColor,
                'status_label' => $statusLabel,
            ];
        }

        // 4. Forecasting SiLPA & Monthly Run Rate
        $monthlyRate = $monthInt > 0 ? ($totalRealisasi / $monthInt) : 0;
        $projectedRealisasi = min($totalPagu, $totalRealisasi + ($monthlyRate * (12 - $monthInt)));
        $projectedSilpa = max(0, $totalPagu - $projectedRealisasi);
        $projectedPct = $totalPagu > 0 ? ($projectedRealisasi / $totalPagu) * 100 : 0;

        return response()->json([
            'period' => [
                'month' => $month,
                'month_name' => Carbon::parse($month)->translatedFormat('F'),
                'year' => $tahun,
                'triwulan' => 'Triwulan ' . $triwulanKe,
                'target_triwulan' => $targetTriwulan,
            ],
            'summary' => [
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'sisa_anggaran' => $overallSisa,
                'pct_keuangan' => round($overallKeuPct, 2),
                'pct_fisik' => round($overallFisikPct, 2),
                'expected_target' => round($expectedMonthlyTarget, 2),
                'deviasi' => round($overallDeviasi, 2),
                'total_projects' => count($rawProjects),
                'count_hijau' => $totalHijau,
                'count_kuning' => $totalKuning,
                'count_merah' => $totalMerah,
            ],
            'gis_projects' => $gisProjects,
            'top_5_opd' => $top5Opd,
            'bottom_5_opd' => $bottom5Opd,
            'zona_merah_opd' => $zonaMerahOpd,
            'opd_ranking' => $opdRanking,
            'forecasting' => [
                'monthly_rate' => $monthlyRate,
                'projected_realisasi' => $projectedRealisasi,
                'projected_pct_keuangan' => round($projectedPct, 2),
                'projected_silpa' => $projectedSilpa,
            ],
            'recommendations' => [
                'Fokus asistensi teknis dan percepatan verifikasi BAST pada OPD di Zona Merah.',
                'Lakukan inspeksi mendadak (sidak) pada ' . $totalMerah . ' paket pekerjaan fisik yang berstatus Deviasi Kritis.',
                'Pastikan pencairan SPM termin akhir tepat waktu guna meminimalkan proyeksi SiLPA sebesar Rp ' . number_format($projectedSilpa, 0, ',', '.') . '.',
            ]
        ]);
    }

    /**
     * Executive Summary 1-Page Printable Report Sheet (A4 Landscape Print / PDF)
     */
    public function executiveReport(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $opdFilter = $request->input('opd', '');

        // Generate full data by calling getApiData internal logic
        $apiResponse = $this->getApiData($request);
        $data = json_decode($apiResponse->getContent(), true);

        return view('pages.dashboard.v3-executive-sheet', $data);
    }
}
