@extends('adminlte::page')

@section('title', 'Dashboard V3 - Spatial GIS Mapping & Executive Briefing')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="m-0 font-weight-bold" style="color: #0f172a; font-size: 1.65rem;">
            <i class="fas fa-map-marked-alt text-primary mr-2"></i>Dashboard V3 &bull; Spatial GIS Mapping
        </h1>
        <small class="text-muted" style="font-size: 0.9rem;">
            Monitoring Geospasial Sebaran Proyek Fisik & Executive Briefing Laporan Rakor Pembangunan Daerah
        </small>
    </div>
    <div class="d-flex align-items-center mt-2 mt-md-0">
        <a href="#" id="btn-open-executive-sheet" target="_blank" class="btn btn-danger font-weight-bold mr-2 shadow-sm" style="border-radius: 8px; padding: 7px 16px;">
            <i class="fas fa-file-pdf mr-1"></i> Cetak 1 Lembar (Executive Sheet)
        </a>
        <button type="button" class="btn btn-warning font-weight-bold shadow-sm" data-toggle="modal" data-target="#v3BriefingModal" style="border-radius: 8px; padding: 7px 16px;">
            <i class="fas fa-chalkboard-teacher mr-1"></i> Slide Briefing Rakor
        </button>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    :root {
        --v3-primary: #1e3a8a;
        --v3-success: #10b981;
        --v3-warning: #f59e0b;
        --v3-danger: #ef4444;
        --v3-dark: #0f172a;
    }
    body {
        background-color: #f8fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .v3-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .v3-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    }
    .v3-stat-card {
        padding: 16px 20px;
        border-radius: 12px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .v3-stat-card .stat-icon {
        position: absolute;
        right: 15px;
        bottom: 12px;
        font-size: 3rem;
        opacity: 0.18;
    }
    .v3-stat-pagu { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
    .v3-stat-realisasi { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); }
    .v3-stat-fisik { background: linear-gradient(135deg, #5b21b6 0%, #8b5cf6 100%); }
    .v3-stat-silpa { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }

    /* Map Styles */
    #gis-v3-map {
        width: 100%;
        height: 600px;
        border-radius: 10px;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        padding: 4px;
    }
    .leaflet-popup-content {
        margin: 12px 14px;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    .map-filter-pill {
        cursor: pointer;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        transition: all 0.2s;
    }
    .map-filter-pill:hover, .map-filter-pill.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .map-filter-pill.pill-green.active { background: #10b981; border-color: #10b981; }
    .map-filter-pill.pill-yellow.active { background: #f59e0b; border-color: #f59e0b; }
    .map-filter-pill.pill-red.active { background: #ef4444; border-color: #ef4444; }

    /* Project Drawer Item */
    .project-list-scroll {
        max-height: 540px;
        overflow-y: auto;
    }
    .project-list-item {
        border-left: 4px solid #cbd5e1;
        padding: 10px 14px;
        margin-bottom: 8px;
        background: #ffffff;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        transition: all 0.2s;
        border-top: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    .project-list-item:hover {
        background: #f8fafc;
        transform: translateX(3px);
    }
    .project-list-item.border-green { border-left-color: #10b981; }
    .project-list-item.border-yellow { border-left-color: #f59e0b; }
    .project-list-item.border-red { border-left-color: #ef4444; }

    .pulse-danger {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse-red 1.6s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>
@stop

@section('content')
<!-- FILTER CONTROL BAR -->
<div class="v3-card p-3 mb-4">
    <div class="row align-items-center">
        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
            <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-calendar-alt mr-1"></i>Periode Bulan & Tahun</label>
            <input type="month" id="v3-filter-month" class="form-control form-control-sm font-weight-bold" value="{{ date('Y-m') }}">
        </div>
        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
            <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-building mr-1"></i>Organisasi Perangkat Daerah (OPD)</label>
            <select id="v3-filter-opd" class="form-control form-control-sm select2">
                <option value="">Semua OPD (Pemerintah Daerah)</option>
                @foreach($opds as $opd)
                <option value="{{ $opd->id }}" {{ ($filter_opd == $opd->id) ? 'selected' : '' }}>{{ $opd->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
            <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-map-marker-alt mr-1"></i>Wilayah Kecamatan</label>
            <select id="v3-filter-kecamatan" class="form-control form-control-sm">
                <option value="">Semua Kecamatan (19 Wilayah)</option>
                @foreach($kecamatans as $kec)
                <option value="{{ $kec->id }}">Kec. {{ $kec->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
            <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-tools mr-1"></i>Kategori Proyek</label>
            <div class="d-flex">
                <select id="v3-filter-category" class="form-control form-control-sm mr-2">
                    <option value="">Semua Kategori</option>
                    <option value="konstruksi">Konstruksi</option>
                    <option value="pengawasan">Pengawasan</option>
                    <option value="perencanaan">Perencanaan</option>
                </select>
                <button type="button" id="v3-btn-refresh" class="btn btn-sm btn-primary px-3 shadow-sm font-weight-bold" title="Segarkan Data">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- EXECUTIVE MACRO KPI CARDS -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="v3-stat-card v3-stat-pagu shadow-sm">
            <i class="fas fa-wallet stat-icon"></i>
            <small class="text-uppercase font-weight-bold" style="letter-spacing: 0.5px; opacity: 0.85;">Total Pagu Anggaran</small>
            <h3 class="font-weight-bold mt-1 mb-1" id="v3-val-pagu">Rp 0</h3>
            <div class="small" style="opacity: 0.9;">
                <span id="v3-val-total-projects">0</span> Paket Pekerjaan Fisik
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="v3-stat-card v3-stat-realisasi shadow-sm">
            <i class="fas fa-chart-line stat-icon"></i>
            <small class="text-uppercase font-weight-bold" style="letter-spacing: 0.5px; opacity: 0.85;">Realisasi Keuangan</small>
            <h3 class="font-weight-bold mt-1 mb-1" id="v3-val-realisasi">Rp 0</h3>
            <div class="small d-flex justify-content-between" style="opacity: 0.95;">
                <span id="v3-val-keu-pct" class="font-weight-bold">0.00%</span>
                <span id="v3-val-target-label">Target: 0%</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="v3-stat-card v3-stat-fisik shadow-sm">
            <i class="fas fa-hard-hat stat-icon"></i>
            <small class="text-uppercase font-weight-bold" style="letter-spacing: 0.5px; opacity: 0.85;">Progres Fisik Rata-rata</small>
            <h3 class="font-weight-bold mt-1 mb-1" id="v3-val-fisik-pct">0.00%</h3>
            <div class="small" style="opacity: 0.9;">
                Deviasi: <span id="v3-val-deviasi" class="font-weight-bold">0.00%</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="v3-stat-card v3-stat-silpa shadow-sm">
            <i class="fas fa-calculator stat-icon"></i>
            <small class="text-uppercase font-weight-bold" style="letter-spacing: 0.5px; opacity: 0.85;">Proyeksi SiLPA Akhir Tahun</small>
            <h3 class="font-weight-bold mt-1 mb-1" id="v3-val-silpa">Rp 0</h3>
            <div class="small" style="opacity: 0.9;">
                Laju Serapan: <span id="v3-val-run-rate">Rp 0</span> / bln
            </div>
        </div>
    </div>
</div>

<!-- MAIN WORKSPACE: SPATIAL GIS MAP + PROJECT DRAWER & EWS -->
<div class="row">
    <!-- LEFT: SPATIAL GIS MAP -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="v3-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark">
                        <i class="fas fa-map-pin text-danger mr-1"></i>Peta Sebaran Proyek Pembangunan Fisik
                    </h5>
                    <small class="text-muted">Klik titik pin pada peta untuk melihat detail kontrak, kontraktor pelaksana, & navigasi sidak lapangan</small>
                </div>
                <!-- FILTER PILLS BY REALIZATION STATUS -->
                <div class="d-flex flex-wrap mt-2 mt-md-0" id="v3-status-pills">
                    <button type="button" class="map-filter-pill active mr-1 mb-1" data-status="">
                        Semua (<span id="pill-count-all">0</span>)
                    </button>
                    <button type="button" class="map-filter-pill pill-green mr-1 mb-1" data-status="green">
                        🟢 Sesuai Target (<span id="pill-count-green">0</span>)
                    </button>
                    <button type="button" class="map-filter-pill pill-yellow mr-1 mb-1" data-status="yellow">
                        🟡 Deviasi Minor (<span id="pill-count-yellow">0</span>)
                    </button>
                    <button type="button" class="map-filter-pill pill-red mb-1" data-status="red">
                        🔴 Kritis (<span id="pill-count-red">0</span>)
                    </button>
                </div>
            </div>

            <!-- LEAFLET MAP CONTAINER -->
            <div id="gis-v3-map" class="shadow-sm border"></div>

            <!-- MAP FOOTER LEGEND -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-2 px-1">
                <div class="small text-muted">
                    <span class="mr-3"><i class="fas fa-circle text-success mr-1"></i><strong>Hijau:</strong> Deviasi &le; 5% / Tepat Waktu</span>
                    <span class="mr-3"><i class="fas fa-circle text-warning mr-1"></i><strong>Kuning:</strong> Deviasi 5% - 10%</span>
                    <span><i class="fas fa-circle text-danger mr-1"></i><strong>Merah:</strong> Deviasi &gt; 10% (Kritis)</span>
                </div>
                <div class="small text-muted">
                    <i class="fas fa-info-circle mr-1"></i>Tile: OpenStreetMap &copy; Pemkab Tulungagung
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: PROJECT DRAWER & EXECUTIVE PERFORMANCE -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <!-- PROJECT DRAWER -->
        <div class="v3-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="font-weight-bold m-0 text-dark">
                    <i class="fas fa-list-ul text-primary mr-1"></i>Daftar Titik Sidak Lapangan
                </h6>
                <span class="badge badge-primary px-2 py-1" id="project-list-total-badge">0 Paket</span>
            </div>
            <div class="input-group input-group-sm mb-2">
                <input type="text" id="v3-search-project" class="form-control" placeholder="Cari nama paket/lokasi/rekanan...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <div class="project-list-scroll pr-1" id="v3-project-list-container">
                <!-- Dynamically populated -->
                <div class="text-center py-4 text-muted small">
                    <i class="fas fa-spinner fa-spin mr-1"></i>Memuat daftar proyek...
                </div>
            </div>
        </div>

        <!-- ZONA MERAH / EWS WATCHLIST -->
        <div class="v3-card p-3 mb-3" style="border-left: 4px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="font-weight-bold m-0 text-danger">
                    <span class="pulse-danger mr-1"></span>OPD Zona Merah (Perlu Perhatian)
                </h6>
                <span class="badge badge-danger px-2 py-1" id="zona-merah-count-badge">0 OPD</span>
            </div>
            <small class="text-muted d-block mb-2">Realisasi serapan keuangan jauh di bawah target triwulan berjalan</small>
            <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                <table class="table table-sm table-striped mb-0" style="font-size: 0.8rem;" id="v3-zona-merah-table">
                    <thead>
                        <tr class="bg-light">
                            <th>Nama OPD</th>
                            <th class="text-right">Realisasi</th>
                            <th class="text-center">Deviasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada OPD di zona merah.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP 5 & BOTTOM 5 OPD TABS -->
        <div class="v3-card p-3">
            <ul class="nav nav-pills nav-justified mb-2" id="v3-ranking-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active py-1 font-weight-bold" id="tab-top5" data-toggle="pill" href="#pane-top5" role="tab" style="font-size: 0.82rem;">
                        <i class="fas fa-trophy text-warning mr-1"></i>Top 5 Kinerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1 font-weight-bold" id="tab-bottom5" data-toggle="pill" href="#pane-bottom5" role="tab" style="font-size: 0.82rem;">
                        <i class="fas fa-exclamation-triangle text-danger mr-1"></i>Bottom 5 Terendah
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="pane-top5" role="tabpanel">
                    <ul class="list-group list-group-flush" id="v3-top5-list" style="font-size: 0.82rem;">
                        <li class="list-group-item text-center text-muted py-3">Memuat data...</li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="pane-bottom5" role="tabpanel">
                    <ul class="list-group list-group-flush" id="v3-bottom5-list" style="font-size: 0.82rem;">
                        <li class="list-group-item text-center text-muted py-3">Memuat data...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SLIDE EXECUTIVE BRIEFING RAKOR -->
<div class="modal fade" id="v3BriefingModal" tabindex="-1" role="dialog" aria-labelledby="v3BriefingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <div>
                    <h5 class="modal-title font-weight-bold" id="v3BriefingModalLabel">
                        <i class="fas fa-chalkboard-teacher text-warning mr-2"></i>Executive Summary &bull; Rapat Koordinasi Pembangunan
                    </h5>
                    <small class="text-white-50" id="briefing-period-subtitle">Periode Evaluasi Pembangunan Daerah</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- MACRO METRICS -->
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <div class="bg-white p-3 rounded shadow-sm border-left border-primary" style="border-left-width: 4px !important;">
                            <small class="text-muted font-weight-bold">TOTAL PAGU</small>
                            <h5 class="font-weight-bold mb-0 text-dark" id="brief-pagu">Rp 0</h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="bg-white p-3 rounded shadow-sm border-left border-success" style="border-left-width: 4px !important;">
                            <small class="text-muted font-weight-bold">REALISASI KEUANGAN</small>
                            <h5 class="font-weight-bold mb-0 text-success" id="brief-keu">0.00%</h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="bg-white p-3 rounded shadow-sm border-left border-info" style="border-left-width: 4px !important;">
                            <small class="text-muted font-weight-bold">PROGRES FISIK</small>
                            <h5 class="font-weight-bold mb-0 text-info" id="brief-fisik">0.00%</h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="bg-white p-3 rounded shadow-sm border-left border-danger" style="border-left-width: 4px !important;">
                            <small class="text-muted font-weight-bold">ESTIMASI SiLPA</small>
                            <h5 class="font-weight-bold mb-0 text-danger" id="brief-silpa">Rp 0</h5>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- LEFT: TOP & BOTTOM OPD -->
                    <div class="col-md-6 mb-3">
                        <div class="bg-white p-3 rounded shadow-sm h-100 border">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-trophy text-warning mr-1"></i>Top 3 & Bottom 3 OPD
                            </h6>
                            <div class="mb-3">
                                <span class="badge badge-success mb-2 px-2 py-1 font-weight-bold">Top 3 Realisasi Tertinggi</span>
                                <ul class="list-group list-group-flush small" id="brief-top3"></ul>
                            </div>
                            <div>
                                <span class="badge badge-danger mb-2 px-2 py-1 font-weight-bold">3 OPD Perlu Akselerasi</span>
                                <ul class="list-group list-group-flush small" id="brief-bottom3"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: STRATEGIC ACTION RECOMMENDATIONS -->
                    <div class="col-md-6 mb-3">
                        <div class="bg-white p-3 rounded shadow-sm h-100 border">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check text-primary mr-1"></i>Rekomendasi Tindakan Strategis Pimpinan
                            </h6>
                            <div id="brief-recommendations-list"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <div>
                    <a href="#" id="modal-btn-print-sheet" target="_blank" class="btn btn-danger font-weight-bold shadow-sm">
                        <i class="fas fa-print mr-1"></i> Cetak Dokumen Laporan (1 Lembar)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
$(document).ready(function() {
    var map = null;
    var markersGroup = null;
    var allProjects = [];
    var currentFilterStatus = '';
    var currentData = null;

    // Initialize Select2 if exists
    if ($('.select2').length > 0) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }

    // Number & Currency Formatters
    function fmtMoney(num) {
        if (!num) return 'Rp 0';
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    function fmtPct(num) {
        if (num === null || num === undefined) return '0.00%';
        return parseFloat(num).toFixed(2) + '%';
    }

    // Initialize Leaflet Map
    function initMap() {
        if (map) return;
        // Tulungagung Center: [-8.0664, 111.9015]
        map = L.map('gis-v3-map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([-8.0664, 111.9015], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors &bull; SIAP Kab. Tulungagung'
        }).addTo(map);

        markersGroup = L.layerGroup().addTo(map);
    }

    // Fetch and render data
    function loadData() {
        var month = $('#v3-filter-month').val() || '{{ date("Y-m") }}';
        var opd = $('#v3-filter-opd').val() || '';
        var kecamatan = $('#v3-filter-kecamatan').val() || '';
        var category = $('#v3-filter-category').val() || '';

        // Update Executive Sheet Link
        var sheetUrl = "{{ url('/dashboard-v3/executive-report') }}?month=" + month + "&opd=" + opd + "&kecamatan=" + kecamatan + "&category=" + category;
        $('#btn-open-executive-sheet').attr('href', sheetUrl);
        $('#modal-btn-print-sheet').attr('href', sheetUrl);

        $.ajax({
            url: "{{ url('/dashboard-v3/api-data') }}",
            type: "GET",
            data: {
                month: month,
                opd: opd,
                kecamatan: kecamatan,
                category: category,
                status: currentFilterStatus
            },
            beforeSend: function() {
                $('#v3-project-list-container').html('<div class="text-center py-4 text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat data geospasial...</div>');
            },
            success: function(resp) {
                currentData = resp;
                renderSummary(resp.summary, resp.forecasting, resp.period);
                renderPills(resp.summary);
                renderGisProjects(resp.gis_projects || []);
                renderZonaMerah(resp.zona_merah_opd || []);
                renderRankingLists(resp.top_5_opd || [], resp.bottom_5_opd || []);
                updateBriefingModal(resp);
            },
            error: function() {
                toastr.error('Gagal mengambil data Dashboard V3.');
            }
        });
    }

    // Render KPI Cards
    function renderSummary(summary, fc, period) {
        if (!summary) return;
        $('#v3-val-pagu').text(fmtMoney(summary.total_pagu));
        $('#v3-val-realisasi').text(fmtMoney(summary.total_realisasi));
        $('#v3-val-keu-pct').text(fmtPct(summary.pct_keuangan));
        $('#v3-val-target-label').text('Target ' + (period.triwulan || '') + ': ' + (period.target_triwulan || 0) + '%');
        $('#v3-val-fisik-pct').text(fmtPct(summary.pct_fisik));
        
        var dev = summary.deviasi || 0;
        var devText = (dev > 0 ? '+' : '') + fmtPct(dev);
        $('#v3-val-deviasi').text(devText).removeClass('text-success text-danger text-warning')
            .addClass(dev >= 0 ? 'text-success' : (dev >= -5 ? 'text-warning' : 'text-danger'));

        $('#v3-val-total-projects').text(summary.total_projects || 0);
        $('#v3-val-silpa').text(fmtMoney(fc ? fc.projected_silpa : 0));
        $('#v3-val-run-rate').text(fmtMoney(fc ? fc.monthly_rate : 0));
    }

    // Render Filter Pills Count
    function renderPills(summary) {
        if (!summary) return;
        $('#pill-count-all').text(summary.total_projects || 0);
        $('#pill-count-green').text(summary.count_hijau || 0);
        $('#pill-count-yellow').text(summary.count_kuning || 0);
        $('#pill-count-red').text(summary.count_merah || 0);
    }

    // Render Markers on Map & Project Drawer List
    var markerMapById = {};

    function renderGisProjects(projects) {
        allProjects = projects;
        initMap();
        markersGroup.clearLayers();
        markerMapById = {};

        var container = $('#v3-project-list-container');
        container.empty();
        $('#project-list-total-badge').text(projects.length + ' Paket');

        if (projects.length === 0) {
            container.html('<div class="text-center py-4 text-muted small"><i class="fas fa-box-open mr-1"></i>Tidak ada paket pekerjaan sesuai filter.</div>');
            return;
        }

        var bounds = [];

        projects.forEach(function(proj) {
            // Colors
            var colorHex = '#10b981'; // green
            var borderClass = 'border-green';
            var badgeClass = 'badge-success';

            if (proj.status_color === 'danger') {
                colorHex = '#ef4444';
                borderClass = 'border-red';
                badgeClass = 'badge-danger';
            } else if (proj.status_color === 'warning') {
                colorHex = '#f59e0b';
                borderClass = 'border-yellow';
                badgeClass = 'badge-warning';
            }

            // Circle marker on Leaflet
            var marker = L.circleMarker([proj.lat, proj.lng], {
                radius: 8,
                fillColor: colorHex,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.92
            });

            // Interactive Popup (Sidak Lapangan Ready)
            var popupHtml = '<div style="min-width: 250px; font-family: Inter, sans-serif;">' +
                '<span class="badge ' + badgeClass + ' mb-1 px-2 py-1 font-weight-bold">' + proj.status_label + '</span>' +
                '<h6 class="font-weight-bold mb-1 text-dark" style="font-size: 0.95rem;">' + proj.name + '</h6>' +
                '<div class="small text-muted mb-2">' +
                    '<i class="fas fa-building mr-1"></i><strong>' + proj.opd_name + '</strong><br/>' +
                    '<i class="fas fa-map-marker-alt mr-1"></i>' + proj.lokasi + '<br/>' +
                    '<i class="fas fa-hard-hat mr-1"></i>Rekanan: <strong>' + proj.partner_name + '</strong>' +
                '</div>' +
                '<div class="bg-light p-2 rounded mb-2 border" style="font-size: 0.8rem;">' +
                    '<div class="d-flex justify-content-between"><span>Nilai SPK:</span><strong class="text-primary">' + fmtMoney(proj.spk_amount) + '</strong></div>' +
                    '<div class="d-flex justify-content-between"><span>Waktu Pelaksanaan:</span><strong>' + proj.spk_days + ' Hari</strong></div>' +
                    '<div class="d-flex justify-content-between"><span>Kelengkapan Berkas:</span><strong>' + proj.doc_pct + '% (' + proj.doc_count + '/5)</strong></div>' +
                '</div>' +
                '<div class="d-flex">' +
                    '<a href="' + proj.gmaps_url + '" target="_blank" class="btn btn-xs btn-danger flex-grow-1 font-weight-bold py-1 mr-1 text-center" style="font-size: 0.75rem;">' +
                        '<i class="fas fa-directions mr-1"></i>Sidak Lapangan (Rute Maps)' +
                    '</a>' +
                '</div>' +
            '</div>';

            marker.bindPopup(popupHtml);
            markersGroup.addLayer(marker);
            markerMapById[proj.id] = marker;
            bounds.push([proj.lat, proj.lng]);

            // Drawer List Item
            var itemHtml = '<div class="project-list-item ' + borderClass + '" data-id="' + proj.id + '">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                    '<strong class="text-dark" style="font-size: 0.85rem;">' + proj.name + '</strong>' +
                    '<span class="badge ' + badgeClass + ' ml-1" style="font-size: 0.7rem;">' + proj.doc_pct + '%</span>' +
                '</div>' +
                '<div class="small text-muted mt-1">' +
                    '<i class="fas fa-map-marker-alt text-danger mr-1"></i>' + proj.kecamatan_nama + ' &bull; ' + fmtMoney(proj.spk_amount) +
                '</div>' +
            '</div>';

            container.append(itemHtml);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }

    // Click Drawer Item to zoom and open popup
    $(document).on('click', '.project-list-item', function() {
        var id = $(this).data('id');
        var marker = markerMapById[id];
        if (marker && map) {
            map.setView(marker.getLatLng(), 15, { animate: true });
            marker.openPopup();
        }
    });

    // Filter project list search box
    $('#v3-search-project').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('.project-list-item').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    });

    // Pill Status Filter Click
    $('#v3-status-pills button').on('click', function() {
        $('#v3-status-pills button').removeClass('active');
        $(this).addClass('active');
        currentFilterStatus = $(this).data('status');
        loadData();
    });

    // Render Zona Merah
    function renderZonaMerah(opds) {
        var tbody = $('#v3-zona-merah-table tbody');
        tbody.empty();
        $('#zona-merah-count-badge').text(opds.length + ' OPD');

        if (opds.length === 0) {
            tbody.html('<tr><td colspan="3" class="text-center text-success py-3"><i class="fas fa-check-circle mr-1"></i>Semua OPD dalam performa aman.</td></tr>');
            return;
        }

        opds.forEach(function(opd) {
            var tr = $('<tr>');
            tr.append('<td class="font-weight-bold text-truncate" style="max-width: 160px;" title="' + opd.opd_name + '">' + opd.opd_name + '</td>');
            tr.append('<td class="text-right font-weight-bold text-danger">' + fmtPct(opd.keuangan) + '</td>');
            tr.append('<td class="text-center"><span class="badge badge-danger px-2">' + opd.deviasi + '%</span></td>');
            tbody.append(tr);
        });
    }

    // Render Ranking Lists
    function renderRankingLists(top5, bottom5) {
        var topContainer = $('#v3-top5-list').empty();
        if (top5.length === 0) {
            topContainer.html('<li class="list-group-item text-center text-muted py-2">Tidak ada data.</li>');
        } else {
            top5.forEach(function(opd, idx) {
                topContainer.append(
                    '<li class="list-group-item d-flex justify-content-between align-items-center py-2">' +
                        '<span class="font-weight-bold text-truncate" style="max-width: 220px;">' + (idx+1) + '. ' + opd.opd_name + '</span>' +
                        '<span class="badge badge-success font-weight-bold">' + fmtPct(opd.keuangan) + '</span>' +
                    '</li>'
                );
            });
        }

        var bottomContainer = $('#v3-bottom5-list').empty();
        if (bottom5.length === 0) {
            bottomContainer.html('<li class="list-group-item text-center text-muted py-2">Tidak ada data.</li>');
        } else {
            bottom5.forEach(function(opd, idx) {
                bottomContainer.append(
                    '<li class="list-group-item d-flex justify-content-between align-items-center py-2">' +
                        '<span class="font-weight-bold text-truncate text-danger" style="max-width: 220px;">' + (idx+1) + '. ' + opd.opd_name + '</span>' +
                        '<span class="badge badge-danger font-weight-bold">' + fmtPct(opd.keuangan) + '</span>' +
                    '</li>'
                );
            });
        }
    }

    // Update Briefing Modal
    function updateBriefingModal(data) {
        if (!data) return;
        $('#briefing-period-subtitle').text('Periode ' + data.period.month_name + ' ' + data.period.year + ' &bull; ' + data.period.triwulan);
        $('#brief-pagu').text(fmtMoney(data.summary.total_pagu));
        $('#brief-keu').text(fmtPct(data.summary.pct_keuangan));
        $('#brief-fisik').text(fmtPct(data.summary.pct_fisik));
        $('#brief-silpa').text(fmtMoney(data.forecasting.projected_silpa));

        // Top 3
        var top3List = $('#brief-top3').empty();
        (data.top_5_opd || []).slice(0, 3).forEach(function(opd, i) {
            top3List.append('<li class="list-group-item d-flex justify-content-between py-1"><span>' + (i+1) + '. ' + opd.opd_name + '</span><strong>' + fmtPct(opd.keuangan) + '</strong></li>');
        });

        // Bottom 3
        var bottom3List = $('#brief-bottom3').empty();
        (data.bottom_5_opd || []).slice(0, 3).forEach(function(opd, i) {
            bottom3List.append('<li class="list-group-item d-flex justify-content-between py-1 text-danger"><span>' + (i+1) + '. ' + opd.opd_name + '</span><strong>' + fmtPct(opd.keuangan) + '</strong></li>');
        });

        // Recommendations
        var recList = $('#brief-recommendations-list').empty();
        (data.recommendations || []).forEach(function(rec, i) {
            recList.append(
                '<div class="d-flex align-items-start mb-2 p-2 bg-light rounded border">' +
                    '<span class="badge badge-primary mr-2 mt-1">' + (i+1) + '</span>' +
                    '<span class="small font-weight-bold text-dark">' + rec + '</span>' +
                '</div>'
            );
        });
    }

    // Filter Change Listeners
    $('#v3-filter-month, #v3-filter-opd, #v3-filter-kecamatan, #v3-filter-category').on('change', function() {
        loadData();
    });
    $('#v3-btn-refresh').on('click', function() {
        loadData();
    });

    // Initial Load
    loadData();
});
</script>
@stop
