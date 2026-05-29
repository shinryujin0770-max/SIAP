@extends('adminlte::page')

@section('title', 'Dashboard Executive - SIAP')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="m-0 dv2-page-title">
            <i class="fas fa-chart-area mr-2" style="color: var(--dv2-accent);"></i>Dashboard Executive
        </h1>
        <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Ringkasan eksekutif realisasi fisik &amp; keuangan</p>
    </div>
    <div class="d-flex align-items-center mt-2 mt-md-0">
        <button class="btn dv2-btn-export mr-2" id="dv2-export-pdf" title="Export PDF">
            <i class="fas fa-file-pdf mr-1"></i> PDF
        </button>
        <a href="#" class="btn dv2-btn-export" id="dv2-export-excel" title="Export Excel">
            <i class="fas fa-file-excel mr-1"></i> Excel
        </a>
    </div>
</div>
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* ===== CSS VARIABLES ===== */
    :root {
        --dv2-bg: #f0f2f5;
        --dv2-card-bg: #ffffff;
        --dv2-navy: #0f172a;
        --dv2-navy-light: #1e3a5f;
        --dv2-accent: #f59e0b;
        --dv2-accent-glow: rgba(245, 158, 11, 0.3);
        --dv2-emerald: #10b981;
        --dv2-emerald-light: #34d399;
        --dv2-coral: #f43f5e;
        --dv2-blue: #3b82f6;
        --dv2-purple: #8b5cf6;
        --dv2-cyan: #06b6d4;
        --dv2-text: #1e293b;
        --dv2-text-muted: #64748b;
        --dv2-border: rgba(0,0,0,0.06);
        --dv2-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --dv2-shadow-lg: 0 10px 25px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.04);
        --dv2-radius: 16px;
        --dv2-radius-sm: 10px;
        --dv2-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== BASE ===== */
    .content-wrapper {
        background: var(--dv2-bg) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }
    .dv2-page-title {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        font-size: 1.6rem;
        color: var(--dv2-navy);
        letter-spacing: -0.02em;
    }

    /* ===== FILTER BAR ===== */
    .dv2-filter-bar {
        background: var(--dv2-card-bg);
        border-radius: var(--dv2-radius);
        box-shadow: var(--dv2-shadow);
        padding: 20px 24px;
        margin-bottom: 24px;
        border: 1px solid var(--dv2-border);
    }
    .dv2-filter-bar label {
        font-weight: 600;
        font-size: 0.78rem;
        color: var(--dv2-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }
    .dv2-filter-bar .form-control,
    .dv2-filter-bar .select2-container--default .select2-selection--single {
        border-radius: var(--dv2-radius-sm);
        border: 1.5px solid #e2e8f0;
        padding: 8px 14px;
        font-size: 0.9rem;
        height: auto;
        transition: var(--dv2-transition);
    }
    .dv2-filter-bar .form-control:focus {
        border-color: var(--dv2-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .dv2-filter-btn {
        background: linear-gradient(135deg, var(--dv2-navy), var(--dv2-navy-light));
        color: #fff;
        border: none;
        border-radius: var(--dv2-radius-sm);
        padding: 10px 24px;
        font-weight: 600;
        font-size: 0.88rem;
        transition: var(--dv2-transition);
        letter-spacing: 0.01em;
    }
    .dv2-filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
        color: #fff;
    }

    /* ===== SUMMARY CARDS ===== */
    .dv2-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    @media (max-width: 1200px) {
        .dv2-summary-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .dv2-summary-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .dv2-summary-grid { grid-template-columns: 1fr; }
    }
    .dv2-summary-card {
        background: var(--dv2-card-bg);
        border-radius: var(--dv2-radius);
        padding: 22px 24px;
        box-shadow: var(--dv2-shadow);
        border: 1px solid var(--dv2-border);
        position: relative;
        overflow: hidden;
        transition: var(--dv2-transition);
    }
    .dv2-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--dv2-shadow-lg);
    }
    .dv2-summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: var(--dv2-radius) var(--dv2-radius) 0 0;
    }
    .dv2-summary-card.card-pagu::before { background: linear-gradient(90deg, var(--dv2-blue), var(--dv2-cyan)); }
    .dv2-summary-card.card-realisasi::before { background: linear-gradient(90deg, var(--dv2-emerald), var(--dv2-emerald-light)); }
    .dv2-summary-card.card-keuangan::before { background: linear-gradient(90deg, var(--dv2-accent), #fbbf24); }
    .dv2-summary-card.card-fisik::before { background: linear-gradient(90deg, var(--dv2-purple), #a78bfa); }
    .dv2-summary-card.card-sisa::before { background: linear-gradient(90deg, var(--dv2-coral), #fb7185); }
    .dv2-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 14px;
    }
    .card-pagu .dv2-card-icon { background: rgba(59, 130, 246, 0.1); color: var(--dv2-blue); }
    .card-realisasi .dv2-card-icon { background: rgba(16, 185, 129, 0.1); color: var(--dv2-emerald); }
    .card-keuangan .dv2-card-icon { background: rgba(245, 158, 11, 0.1); color: var(--dv2-accent); }
    .card-fisik .dv2-card-icon { background: rgba(139, 92, 246, 0.1); color: var(--dv2-purple); }
    .card-sisa .dv2-card-icon { background: rgba(244, 63, 94, 0.1); color: var(--dv2-coral); }
    .dv2-card-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--dv2-text-muted);
        margin-bottom: 6px;
    }
    .dv2-card-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--dv2-navy);
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    .dv2-card-value.dv2-animate-number {
        transition: opacity 0.3s ease;
    }

    /* ===== RANKING BANNER ===== */
    .dv2-rank-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
        border-radius: var(--dv2-radius);
        color: #fff;
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.3);
        display: none;
    }
    .dv2-rank-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.15), transparent 70%);
        border-radius: 50%;
    }
    .dv2-rank-banner::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -40px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
        border-radius: 50%;
    }
    .dv2-rank-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--dv2-accent), #fbbf24);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px var(--dv2-accent-glow);
        animation: dv2PulseRank 2.5s infinite ease-in-out;
        flex-shrink: 0;
    }
    .dv2-rank-num {
        font-size: 2rem;
        font-weight: 900;
        color: var(--dv2-navy);
        line-height: 1;
    }
    @keyframes dv2PulseRank {
        0%, 100% { box-shadow: 0 4px 20px var(--dv2-accent-glow); }
        50% { box-shadow: 0 4px 35px rgba(245, 158, 11, 0.6); }
    }
    .dv2-rank-badge {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    /* ===== CHART CARDS ===== */
    .dv2-chart-card {
        background: var(--dv2-card-bg);
        border-radius: var(--dv2-radius);
        box-shadow: var(--dv2-shadow);
        border: 1px solid var(--dv2-border);
        padding: 0;
        margin-bottom: 24px;
        transition: var(--dv2-transition);
        overflow: hidden;
    }
    .dv2-chart-card:hover {
        box-shadow: var(--dv2-shadow-lg);
    }
    .dv2-chart-header {
        padding: 20px 24px 0 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .dv2-chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dv2-navy);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .dv2-chart-subtitle {
        font-size: 0.78rem;
        color: var(--dv2-text-muted);
        margin-top: 2px;
    }
    .dv2-chart-body {
        padding: 16px 24px 24px 24px;
    }
    .dv2-chart-legend {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        padding: 0 24px 16px 24px;
    }
    .dv2-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--dv2-text-muted);
    }
    .dv2-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    /* ===== PROGRESS RINGS ===== */
    .dv2-progress-ring-container {
        display: flex;
        justify-content: center;
        gap: 48px;
        padding: 16px 0;
    }
    .dv2-ring-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .dv2-ring-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--dv2-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 10px;
    }
    .dv2-ring-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dv2-navy);
        margin-top: 2px;
    }

    /* ===== TABLE ===== */
    .dv2-table-card {
        background: var(--dv2-card-bg);
        border-radius: var(--dv2-radius);
        box-shadow: var(--dv2-shadow);
        border: 1px solid var(--dv2-border);
        padding: 0;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .dv2-table-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--dv2-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dv2-table-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dv2-navy);
        margin: 0;
    }
    .dv2-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .dv2-table thead th {
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--dv2-text-muted);
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transition: var(--dv2-transition);
    }
    .dv2-table thead th:hover {
        background: #f1f5f9;
        color: var(--dv2-navy);
    }
    .dv2-table tbody td {
        padding: 12px 16px;
        font-size: 0.87rem;
        color: var(--dv2-text);
        border-bottom: 1px solid #f1f5f9;
        transition: var(--dv2-transition);
    }
    .dv2-table tbody tr:hover td {
        background: #f8fafc;
    }
    #dv2-opd-tbody tr {
        cursor: pointer;
    }
    #dv2-opd-tbody tr.table-primary td {
        background-color: rgba(59, 130, 246, 0.15) !important;
        font-weight: 600;
        color: var(--dv2-navy);
    }
    .dv2-table tbody tr.dv2-highlight td {
        background: rgba(245, 158, 11, 0.08);
        font-weight: 600;
    }
    .dv2-table tfoot td {
        padding: 14px 16px;
        font-size: 0.87rem;
        font-weight: 700;
        color: var(--dv2-navy);
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
    }
    .dv2-rank-cell {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .dv2-rank-gold { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #78350f; }
    .dv2-rank-silver { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: #374151; }
    .dv2-rank-bronze { background: linear-gradient(135deg, #fbbf24, #d97706); color: #78350f; }
    .dv2-rank-default { background: #f1f5f9; color: var(--dv2-text-muted); }

    /* ===== PERCENT BAR ===== */
    .dv2-pct-bar {
        width: 100%;
        max-width: 120px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }
    .dv2-pct-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dv2-pct-bar-fill.pct-keu { background: linear-gradient(90deg, var(--dv2-accent), #fbbf24); }
    .dv2-pct-bar-fill.pct-fisik { background: linear-gradient(90deg, var(--dv2-purple), #a78bfa); }

    /* ===== EXPORT BUTTONS ===== */
    .dv2-btn-export {
        background: var(--dv2-card-bg);
        border: 1.5px solid #e2e8f0;
        color: var(--dv2-text);
        border-radius: var(--dv2-radius-sm);
        padding: 8px 18px;
        font-weight: 600;
        font-size: 0.82rem;
        transition: var(--dv2-transition);
    }
    .dv2-btn-export:hover {
        border-color: var(--dv2-navy);
        color: var(--dv2-navy);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-decoration: none;
    }

    /* ===== LOADING SKELETON ===== */
    .dv2-skeleton {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: dv2Shimmer 1.5s ease-in-out infinite;
        border-radius: 6px;
    }
    @keyframes dv2Shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ===== FADE IN ANIMATION ===== */
    .dv2-fade-in {
        opacity: 0;
        transform: translateY(12px);
        animation: dv2FadeIn 0.5s ease forwards;
    }
    @keyframes dv2FadeIn {
        to { opacity: 1; transform: translateY(0); }
    }
    .dv2-delay-1 { animation-delay: 0.1s; }
    .dv2-delay-2 { animation-delay: 0.2s; }
    .dv2-delay-3 { animation-delay: 0.3s; }
    .dv2-delay-4 { animation-delay: 0.4s; }
    .dv2-delay-5 { animation-delay: 0.5s; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .dv2-page-title { font-size: 1.2rem; }
        .dv2-card-value { font-size: 1.1rem; }
        .dv2-rank-banner { padding: 20px; }
        .dv2-rank-circle { width: 60px; height: 60px; }
        .dv2-rank-num { font-size: 1.5rem; }
        .dv2-progress-ring-container { gap: 24px; }
        .dv2-chart-body { padding: 12px 16px 16px 16px; }
    }

    /* ===== PRINT STYLES ===== */
    @media print {
        .dv2-filter-bar, .dv2-btn-export, .content-header,
        .main-sidebar, .main-header, .main-footer { display: none !important; }
        .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .dv2-summary-card, .dv2-chart-card, .dv2-table-card { break-inside: avoid; }
    }
</style>
@stop

@section('content')
<div id="dv2-app">
    {{-- ===== FILTER BAR ===== --}}
    <div class="dv2-filter-bar dv2-fade-in">
        <form id="dv2-filter-form" class="row align-items-end">
            <div class="col-sm-6 col-lg-3 mb-2 mb-lg-0">
                <label for="dv2-filter-year">Tahun</label>
                <input type="number" name="filter_year" class="form-control" id="dv2-filter-year"
                       min="2020" max="2099" step="1" value="{{ $filter_year ?? date('Y') }}">
            </div>
            <div class="col-sm-6 col-lg-3 mb-2 mb-lg-0">
                <label for="dv2-filter-month">Bulan</label>
                <select name="filter_month" class="form-control" id="dv2-filter-month">
                    @php
                        $months = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        $currentMonth = $filter_month ?? date('m');
                        $currentMonth = str_pad($currentMonth, 2, '0', STR_PAD_LEFT);
                    @endphp
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @if (auth()->user()->hasRole('superadmin'))
            <div class="col-sm-6 col-lg-3 mb-2 mb-lg-0">
                <label for="dv2-filter-opd">OPD</label>
                <select name="filter_opd" class="form-control select2" id="dv2-filter-opd" style="width:100%">
                    <option value="">Semua OPD</option>
                    @foreach ($opds as $opd)
                        <option value="{{ $opd['id'] }}" {{ ($filter_opd ?? '') == $opd['id'] ? 'selected' : '' }}>
                            {{ $opd['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-sm-6 col-lg-3 mb-2 mb-lg-0">
                <label>&nbsp;</label>
                <button type="button" class="btn dv2-filter-btn d-block w-100" id="dv2-filter-btn">
                    <i class="fas fa-search mr-1"></i> Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    <div class="dv2-summary-grid" id="dv2-summary-cards">
        <div class="dv2-summary-card card-pagu dv2-fade-in dv2-delay-1">
            <div class="dv2-card-icon"><i class="fas fa-wallet"></i></div>
            <div class="dv2-card-label">Total Pagu Anggaran</div>
            <div class="dv2-card-value" id="dv2-val-pagu">
                <span class="dv2-skeleton" style="display:inline-block;width:140px;height:22px;"></span>
            </div>
        </div>
        <div class="dv2-summary-card card-realisasi dv2-fade-in dv2-delay-2">
            <div class="dv2-card-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="dv2-card-label">Realisasi sd Bulan Ini</div>
            <div class="dv2-card-value" id="dv2-val-realisasi">
                <span class="dv2-skeleton" style="display:inline-block;width:140px;height:22px;"></span>
            </div>
        </div>
        <div class="dv2-summary-card card-keuangan dv2-fade-in dv2-delay-3">
            <div class="dv2-card-icon"><i class="fas fa-percentage"></i></div>
            <div class="dv2-card-label">Realisasi Keuangan</div>
            <div class="dv2-card-value" id="dv2-val-keuangan">
                <span class="dv2-skeleton" style="display:inline-block;width:80px;height:22px;"></span>
            </div>
        </div>
        <div class="dv2-summary-card card-fisik dv2-fade-in dv2-delay-4">
            <div class="dv2-card-icon"><i class="fas fa-hard-hat"></i></div>
            <div class="dv2-card-label">Realisasi Fisik</div>
            <div class="dv2-card-value" id="dv2-val-fisik">
                <span class="dv2-skeleton" style="display:inline-block;width:80px;height:22px;"></span>
            </div>
        </div>
        <div class="dv2-summary-card card-sisa dv2-fade-in dv2-delay-5">
            <div class="dv2-card-icon"><i class="fas fa-coins"></i></div>
            <div class="dv2-card-label">Sisa Anggaran</div>
            <div class="dv2-card-value" id="dv2-val-sisa">
                <span class="dv2-skeleton" style="display:inline-block;width:140px;height:22px;"></span>
            </div>
        </div>
    </div>

    {{-- ===== RANKING BANNER (OPD) ===== --}}
    <div class="dv2-rank-banner dv2-fade-in" id="dv2-rank-banner">
        <div class="d-flex align-items-center flex-wrap" style="position:relative;z-index:1;">
            <div class="dv2-rank-circle mr-4">
                <span class="dv2-rank-num" id="dv2-rank-num">#-</span>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-1 font-weight-bold" style="color: var(--dv2-accent); font-family: 'Inter', sans-serif;">
                    <i class="fas fa-trophy mr-2"></i>Peringkat Realisasi Keuangan OPD
                </h5>
                <p class="mb-1" style="color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                    <strong id="dv2-rank-opd-name">-</strong> berada pada peringkat
                    <span class="dv2-rank-badge mx-1" id="dv2-rank-badge">#-</span>
                    dari <strong id="dv2-rank-total">-</strong> OPD
                </p>
                <small style="color: rgba(255,255,255,0.5);" id="dv2-rank-period">-</small>
            </div>
        </div>
    </div>

    {{-- ===== PROGRESS RINGS (Keuangan & Fisik) ===== --}}
    <div class="row mb-4">
        <div class="col-md-4 dv2-fade-in dv2-delay-2">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title">Capaian Realisasi</h6>
                        <div class="dv2-chart-subtitle">Keuangan &amp; Fisik bulan ini</div>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <div class="dv2-progress-ring-container">
                        <div class="dv2-ring-wrap">
                            <canvas id="dv2-ring-keu" width="110" height="110"></canvas>
                            <div class="dv2-ring-label">Keuangan</div>
                            <div class="dv2-ring-value" id="dv2-ring-keu-val">0%</div>
                        </div>
                        <div class="dv2-ring-wrap">
                            <canvas id="dv2-ring-fisik" width="110" height="110"></canvas>
                            <div class="dv2-ring-label">Fisik</div>
                            <div class="dv2-ring-value" id="dv2-ring-fisik-val">0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TREND LINE CHART ===== --}}
        <div class="col-md-8 dv2-fade-in dv2-delay-3">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title">
                            Tren Progres Bulanan
                            <span id="dv2-trend-drilldown-badge" class="badge badge-info ml-2" style="display:none; font-size: 0.8rem; font-weight: 500; background-color: var(--dv2-blue);">
                                Filter: <span id="dv2-trend-drilldown-opd-name"></span>
                            </span>
                        </h6>
                        <div class="dv2-chart-subtitle">%Keuangan &amp; %Fisik Januari - Desember</div>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <canvas id="dv2-trend-chart" height="220"></canvas>
                </div>
                <div class="dv2-chart-legend">
                    <div class="dv2-legend-item">
                        <span class="dv2-legend-dot" style="background:var(--dv2-accent);"></span> %Keu OPD Terpilih
                    </div>
                    <div class="dv2-legend-item">
                        <span class="dv2-legend-dot" style="background:var(--dv2-purple);"></span> %Fisik OPD Terpilih
                    </div>
                    <div class="dv2-legend-item">
                        <span class="dv2-legend-dot" style="background:rgba(245,158,11,0.3);"></span> %Keu Rata-rata
                    </div>
                    <div class="dv2-legend-item">
                        <span class="dv2-legend-dot" style="background:rgba(139,92,246,0.3);"></span> %Fisik Rata-rata
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RANKING CHART (Horizontal Bar - All OPDs) ===== --}}
    <div class="dv2-chart-card dv2-fade-in dv2-delay-3" id="dv2-ranking-section">
        <div class="dv2-chart-header">
            <div>
                <h6 class="dv2-chart-title">
                    <i class="fas fa-sort-amount-down mr-2" style="color:var(--dv2-accent);"></i>Ranking OPD — Realisasi Keuangan
                    <span id="dv2-ranking-drilldown-badge" class="badge badge-info ml-2" style="display:none; font-size: 0.8rem; font-weight: 500; background-color: var(--dv2-blue);">
                        Filter: <span id="dv2-ranking-drilldown-opd-name"></span>
                        <i class="fas fa-times ml-1" style="cursor:pointer;" onclick="resetDrillDown(event)" title="Reset Drill Down"></i>
                    </span>
                </h6>
                <div class="dv2-chart-subtitle">Seluruh OPD diurutkan berdasarkan %Keuangan (tertinggi → terendah)</div>
            </div>
        </div>
        <div class="dv2-chart-body">
            <div style="position:relative; min-height:300px;" id="dv2-ranking-chart-wrapper">
                <canvas id="dv2-ranking-chart"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== TOP/BOTTOM 5 CHARTS ===== --}}
    <div class="row">
        <div class="col-md-6 dv2-fade-in dv2-delay-2">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title"><i class="fas fa-medal mr-2" style="color:#f59e0b;"></i>5 OPD Keuangan Tertinggi</h6>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <canvas id="dv2-top5-keu-chart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 dv2-fade-in dv2-delay-3">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title"><i class="fas fa-exclamation-triangle mr-2" style="color:var(--dv2-coral);"></i>5 OPD Keuangan Terendah</h6>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <canvas id="dv2-bottom5-keu-chart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 dv2-fade-in dv2-delay-2">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title"><i class="fas fa-medal mr-2" style="color:#8b5cf6;"></i>5 OPD Fisik Tertinggi</h6>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <canvas id="dv2-top5-fisik-chart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 dv2-fade-in dv2-delay-3">
            <div class="dv2-chart-card">
                <div class="dv2-chart-header">
                    <div>
                        <h6 class="dv2-chart-title"><i class="fas fa-exclamation-triangle mr-2" style="color:var(--dv2-coral);"></i>5 OPD Fisik Terendah</h6>
                    </div>
                </div>
                <div class="dv2-chart-body">
                    <canvas id="dv2-bottom5-fisik-chart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== OPD TABLE ===== --}}
    <div class="dv2-table-card dv2-fade-in dv2-delay-3">
        <div class="dv2-table-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <h6 class="dv2-table-title">
                <i class="fas fa-list-ol mr-2" style="color:var(--dv2-blue);"></i>Detail Realisasi per OPD
                <span id="dv2-opd-drilldown-badge" class="badge badge-info ml-2" style="display:none; font-size: 0.8rem; font-weight: 500; background-color: var(--dv2-blue);">
                    Filter: <span id="dv2-opd-drilldown-opd-name"></span>
                    <i class="fas fa-times ml-1" style="cursor:pointer;" onclick="resetDrillDown(event)" title="Reset Drill Down"></i>
                </span>
            </h6>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="text" id="dv2-opd-search" class="form-control form-control-sm" placeholder="Cari OPD..." style="width: 200px; border-radius: 8px;">
                <span class="badge badge-light" id="dv2-opd-count" style="font-size:0.8rem;">0 OPD</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="dv2-table" id="dv2-opd-table">
                <thead>
                    <tr>
                        <th style="width:40px;" onclick="dv2SortOpdTable('rank')">#</th>
                        <th onclick="dv2SortOpdTable('name')">Nama OPD <i class="fas fa-sort ml-1" style="opacity:0.4;"></i></th>
                        <th class="text-right" onclick="dv2SortOpdTable('pagu')">Pagu Anggaran <i class="fas fa-sort ml-1" style="opacity:0.4;"></i></th>
                        <th class="text-right" style="cursor: pointer;" onclick="dv2SortOpdTable('realisasi')">
                            <div class="d-flex align-items-center justify-content-end" style="gap: 5px;">
                                <span>Realisasi</span>
                                <i class="fas fa-sort ml-1" style="opacity:0.4;"></i>
                                <a href="#" class="text-muted ml-1" id="dv2-opd-filter-btn-realisasi" title="Tampilkan OPD belum input realisasi" onclick="event.stopPropagation(); showBelumInputModalV2();">
                                    <i class="fas fa-filter fa-xs"></i>
                                </a>
                            </div>
                        </th>
                        <th class="text-center" style="width:180px; cursor: pointer;" onclick="dv2SortOpdTable('keu')">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                <span>%Keu</span>
                                <i class="fas fa-sort ml-1" style="opacity:0.4;"></i>
                                <div class="dropdown d-inline-block" onclick="event.stopPropagation();">
                                    <a href="#" class="text-muted" id="dv2-opd-filter-btn-keu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-filter fa-xs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px; padding: 8px;">
                                        <h6 class="dropdown-header px-2 py-1" style="font-size: 0.78rem;">Filter Progres Keu</h6>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val active" href="#" data-filter-type="keu" data-filter-val="all" style="font-size: 0.82rem;">Semua</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="zero" style="font-size: 0.82rem;">Masih 0%</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="low" style="font-size: 0.82rem;">Rendah (&lt; 50%)</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="high" style="font-size: 0.82rem;">Tinggi (&gt;= 50%)</a>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="text-center" style="width:180px; cursor: pointer;" onclick="dv2SortOpdTable('fisik')">
                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                <span>%Fisik</span>
                                <i class="fas fa-sort ml-1" style="opacity:0.4;"></i>
                                <div class="dropdown d-inline-block" onclick="event.stopPropagation();">
                                    <a href="#" class="text-muted" id="dv2-opd-filter-btn-fisik" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-filter fa-xs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px; padding: 8px;">
                                        <h6 class="dropdown-header px-2 py-1" style="font-size: 0.78rem;">Filter Progres Fisik</h6>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val active" href="#" data-filter-type="fisik" data-filter-val="all" style="font-size: 0.82rem;">Semua</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="zero" style="font-size: 0.82rem;">Masih 0%</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="low" style="font-size: 0.82rem;">Rendah (&lt; 50%)</a>
                                        <a class="dropdown-item px-2 py-1 dv2-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="high" style="font-size: 0.82rem;">Tinggi (&gt;= 50%)</a>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th class="text-center" style="width:140px; cursor: pointer;" onclick="dv2SortOpdTable('deviasi')">
                            <span>Deviasi</span> <i class="fas fa-sort ml-1" style="opacity:0.4;"></i>
                        </th>
                    </tr>
                </thead>
                <tbody id="dv2-opd-tbody">
                    <tr><td colspan="7" class="text-center py-4">
                        <span class="dv2-skeleton" style="display:inline-block;width:200px;height:16px;"></span>
                    </td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Total</td>
                        <td class="text-right" id="dv2-opd-total-pagu">-</td>
                        <td class="text-right" id="dv2-opd-total-realisasi">-</td>
                        <td class="text-center" id="dv2-opd-total-keu">-</td>
                        <td class="text-center" id="dv2-opd-total-fisik">-</td>
                        <td class="text-center" id="dv2-opd-total-deviasi" style="font-weight:bold;">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
 
    {{-- ===== PROGRAM TABLE ===== --}}
    <div class="dv2-table-card dv2-fade-in dv2-delay-4">
        <div class="dv2-table-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <h6 class="dv2-table-title">
                <i class="fas fa-project-diagram mr-2" style="color:var(--dv2-emerald);"></i>Detail Realisasi per Program
                <span id="dv2-program-drilldown-badge" class="badge badge-info ml-2" style="display:none; font-size: 0.8rem; font-weight: 500; background-color: var(--dv2-blue);">
                    Filter: <span id="dv2-program-drilldown-opd-name"></span> 
                    <i class="fas fa-times ml-1" style="cursor:pointer;" onclick="resetDrillDown(event)" title="Reset Drill Down"></i>
                </span>
            </h6>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <input type="text" id="dv2-program-search" class="form-control form-control-sm" placeholder="Cari Program..." style="width: 200px; border-radius: 8px;">
                <span class="badge badge-light" id="dv2-program-count" style="font-size:0.8rem;">0 Program</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="dv2-table" id="dv2-program-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Program</th>
                        <th class="text-right">Pagu Anggaran</th>
                        <th class="text-right">Realisasi</th>
                        <th class="text-center" style="width:180px;">%Keu</th>
                        <th class="text-center" style="width:180px;">%Fisik</th>
                        <th class="text-center" style="width:140px;">Deviasi</th>
                    </tr>
                </thead>
                <tbody id="dv2-program-tbody">
                    <tr><td colspan="7" class="text-center py-4">
                        <span class="dv2-skeleton" style="display:inline-block;width:200px;height:16px;"></span>
                    </td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Total</td>
                        <td class="text-right" id="dv2-program-total-pagu">-</td>
                        <td class="text-right" id="dv2-program-total-realisasi">-</td>
                        <td class="text-center" id="dv2-program-total-keu">-</td>
                        <td class="text-center" id="dv2-program-total-fisik">-</td>
                        <td class="text-center" id="dv2-program-total-deviasi" style="font-weight:bold;">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ===== MODAL BELUM INPUT REALISASI V2 ===== --}}
    <div class="modal fade" id="dv2-modal-belum-input-realisasi" tabindex="-1" role="dialog" aria-labelledby="dv2ModalBelumInputLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="dv2ModalBelumInputLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Daftar OPD Belum Input Realisasi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Berikut adalah daftar OPD yang belum menginput realisasi anggaran pada periode <strong id="dv2-modal-belum-input-periode">-</strong> (realisasi Bulan Ini = Rp 0):</p>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped" id="dv2-table-belum-input-detail">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Nama OPD</th>
                                    <th class="text-right" style="width: 220px;">Pagu Anggaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dinamis -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="dv2-btn-copy-belum-input">
                        <i class="fas fa-copy mr-1"></i>Salin Daftar OPD
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
{{-- html2canvas & jsPDF for PDF export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function() {
    'use strict';

    // ===== UTILITIES =====
    var fmtCurrency = new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR',
        minimumFractionDigits: 0, maximumFractionDigits: 0
    });
    var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function fmtPct(val) { return (val || 0).toFixed(2) + '%'; }
    function fmtMoney(val) { return fmtCurrency.format(val || 0); }

    // Animated counter
    function animateValue(el, start, end, duration, formatter) {
        if (!el) return;
        var startTime = null;
        var fn = formatter || function(v) { return v; };
        function step(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
            var current = start + (end - start) * eased;
            el.textContent = fn(current);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ===== CHART INSTANCES =====
    var chartInstances = {};
    function destroyChart(key) {
        if (chartInstances[key]) { chartInstances[key].destroy(); chartInstances[key] = null; }
    }

    // ===== DONUT RING =====
    function drawRing(canvasId, pct, color) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var size = canvas.width;
        var center = size / 2;
        var radius = (size / 2) - 10;
        var lineWidth = 10;
        var startAngle = -Math.PI / 2;
        var endAngle = startAngle + (2 * Math.PI * (Math.min(pct, 100) / 100));

        ctx.clearRect(0, 0, size, size);

        // Background ring
        ctx.beginPath();
        ctx.arc(center, center, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Value ring
        if (pct > 0) {
            ctx.beginPath();
            ctx.arc(center, center, radius, startAngle, endAngle);
            ctx.strokeStyle = color;
            ctx.lineWidth = lineWidth;
            ctx.lineCap = 'round';
            ctx.stroke();
        }

        // Center text
        ctx.font = '700 18px Inter, sans-serif';
        ctx.fillStyle = '#0f172a';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(pct.toFixed(1) + '%', center, center);
    }

    // ===== DATA STORAGE =====
    var currentData = null;
    var opdSortCol = 'keu';
    var opdSortDir = 'desc';
    var dv2OpdFilters = {
        realisasi: 'all', // 'all', 'zero', 'active'
        keu: 'all',       // 'all', 'zero', 'low', 'high'
        fisik: 'all'      // 'all', 'zero', 'low', 'high'
    };

    // ===== DRILL DOWN STATE =====
    var selectedDrillDownOpdId = null;
    var selectedDrillDownOpdName = null;

    // ===== LOAD DATA =====
    function loadDashboardData() {
        // Reset drill down state when main filter is loaded
        selectedDrillDownOpdId = null;
        selectedDrillDownOpdName = null;
        $('#dv2-trend-drilldown-badge, #dv2-program-drilldown-badge, #dv2-opd-drilldown-badge, #dv2-ranking-drilldown-badge').hide();

        var year = $('#dv2-filter-year').val() || '{{ date("Y") }}';
        var month = $('#dv2-filter-month').val() || '{{ date("m") }}';
        var opd = $('#dv2-filter-opd').val() || '';
        var fullMonth = year + '-' + month;

        $.ajax({
            url: '{{ url("/dashboard-v2/api-data") }}',
            method: 'GET',
            data: { month: fullMonth, opd: opd },
            beforeSend: function() {
                // Show skeletons
                ['dv2-val-pagu','dv2-val-realisasi','dv2-val-keuangan','dv2-val-fisik','dv2-val-sisa'].forEach(function(id) {
                    $('#' + id).html('<span class="dv2-skeleton" style="display:inline-block;width:120px;height:22px;"></span>');
                });
            },
            success: function(resp) {
                currentData = resp;
                updateSummaryCards(resp.summary);
                updateRanking(resp.rank_info);
                updateProgressRings(resp.summary);
                renderTrendChart(resp.monthly_trend);
                renderRankingChart(resp.opd_ranking);
                renderTop5Bottom5Charts(resp.opd_ranking);
                updateOpdTable(resp.opd_ranking || resp.opd_data || []);
                updateProgramTable(resp.program_data || [], resp.program_summary || {});
                updateExcelLink();
            },
            error: function() {
                toastr.error('Gagal memuat data dashboard');
            }
        });
    }

    // ===== UPDATE SUMMARY CARDS =====
    function updateSummaryCards(summary) {
        if (!summary) return;
        animateValue(document.getElementById('dv2-val-pagu'), 0, summary.total_pagu || 0, 800, fmtMoney);
        animateValue(document.getElementById('dv2-val-realisasi'), 0, summary.total_realisasi || 0, 800, fmtMoney);
        animateValue(document.getElementById('dv2-val-keuangan'), 0, summary.pct_keuangan || summary.total_keu || 0, 600, fmtPct);
        animateValue(document.getElementById('dv2-val-fisik'), 0, summary.pct_fisik || summary.total_fisik || 0, 600, fmtPct);
        animateValue(document.getElementById('dv2-val-sisa'), 0, summary.sisa_anggaran || 0, 800, fmtMoney);
    }

    // ===== UPDATE RANKING =====
    function updateRanking(rankInfo) {
        var banner = $('#dv2-rank-banner');
        if (rankInfo && rankInfo.rank) {
            var m = parseInt(rankInfo.month, 10);
            var periodText = (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + rankInfo.year;
            $('#dv2-rank-num').text('#' + rankInfo.rank);
            $('#dv2-rank-opd-name').text(rankInfo.opd_name || '');
            $('#dv2-rank-badge').text('#' + rankInfo.rank);
            $('#dv2-rank-total').text(rankInfo.total);
            $('#dv2-rank-period').text('Periode: ' + periodText);
            banner.slideDown(400);
        } else {
            banner.slideUp(200);
        }
    }

    // ===== PROGRESS RINGS =====
    function updateProgressRings(summary) {
        var keuPct = summary.pct_keuangan || summary.total_keu || 0;
        var fisikPct = summary.pct_fisik || summary.total_fisik || 0;
        drawRing('dv2-ring-keu', keuPct, '#f59e0b');
        drawRing('dv2-ring-fisik', fisikPct, '#8b5cf6');
        $('#dv2-ring-keu-val').text(fmtPct(keuPct));
        $('#dv2-ring-fisik-val').text(fmtPct(fisikPct));
    }

    // ===== TREND CHART =====
    function renderTrendChart(trend) {
        destroyChart('trend');
        var canvas = document.getElementById('dv2-trend-chart');
        if (!canvas || !trend) return;

        var labels = Array.isArray(trend) ? trend.map(function(d) { return d.month; }) : (trend.month_labels || monthNames);
        var keuSelected = Array.isArray(trend) ? trend.map(function(d) { return d.selected_keuangan; }) : (trend.keu_selected || []);
        var fisikSelected = Array.isArray(trend) ? trend.map(function(d) { return d.selected_fisik; }) : (trend.fisik_selected || []);
        var keuAll = Array.isArray(trend) ? trend.map(function(d) { return d.all_keuangan; }) : (trend.keu_all || []);
        var fisikAll = Array.isArray(trend) ? trend.map(function(d) { return d.all_fisik; }) : (trend.fisik_all || []);

        chartInstances['trend'] = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '%Keu OPD Terpilih',
                        data: keuSelected,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#f59e0b',
                        fill: true,
                        lineTension: 0.35
                    },
                    {
                        label: '%Fisik OPD Terpilih',
                        data: fisikSelected,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,0.08)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#8b5cf6',
                        fill: true,
                        lineTension: 0.35
                    },
                    {
                        label: '%Keu Rata-rata',
                        data: keuAll,
                        borderColor: 'rgba(245,158,11,0.3)',
                        borderWidth: 1.5,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        fill: false,
                        lineTension: 0.35
                    },
                    {
                        label: '%Fisik Rata-rata',
                        data: fisikAll,
                        borderColor: 'rgba(139,92,246,0.3)',
                        borderWidth: 1.5,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        fill: false,
                        lineTension: 0.35
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(15,23,42,0.9)',
                    titleFontFamily: 'Inter',
                    bodyFontFamily: 'Inter',
                    cornerRadius: 8,
                    xPadding: 12,
                    yPadding: 10,
                    callbacks: {
                        label: function(t, d) {
                            return d.datasets[t.datasetIndex].label + ': ' + t.yLabel.toFixed(2) + '%';
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        gridLines: { color: 'rgba(0,0,0,0.04)', zeroLineColor: 'rgba(0,0,0,0.08)' },
                        ticks: {
                            beginAtZero: true, max: 100,
                            fontColor: '#94a3b8', fontFamily: 'Inter', fontSize: 11,
                            callback: function(v) { return v + '%'; }
                        }
                    }],
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: '#94a3b8', fontFamily: 'Inter', fontSize: 11 }
                    }]
                }
            }
        });
    }

    // ===== RANKING CHART (Horizontal Bar) =====
    function renderRankingChart(opdRanking) {
        destroyChart('ranking');
        var canvas = document.getElementById('dv2-ranking-chart');
        if (!canvas) return;

        var data = (opdRanking || []).slice().sort(function(a, b) {
            return (b.keuangan || b.keuangan_pct || 0) - (a.keuangan || a.keuangan_pct || 0);
        });

        // If drill-down active, filter ranking to only selected OPD
        if (selectedDrillDownOpdId) {
            data = data.filter(function(d) { return d.opd_id == selectedDrillDownOpdId; });
        }

        // Dynamically size the chart based on number of OPDs
        var chartHeight = Math.max(300, data.length * 32);
        var wrapper = document.getElementById('dv2-ranking-chart-wrapper');
        if (wrapper) wrapper.style.minHeight = chartHeight + 'px';
        canvas.height = chartHeight;

        var labels = data.map(function(d) { return d.opd_name || ''; });
        var values = data.map(function(d) { return d.keuangan || d.keuangan_pct || 0; });

        // Color gradient: top = gold, middle = blue, bottom = gray
        var colors = values.map(function(v, i) {
            var ratio = data.length > 1 ? i / (data.length - 1) : 0;
            if (ratio < 0.2) return '#f59e0b';
            if (ratio < 0.6) return '#3b82f6';
            return '#94a3b8';
        });

        // Highlight current OPD or drilldown OPD
        var currentOpdId = selectedDrillDownOpdId || $('#dv2-filter-opd').val();
        @if(!auth()->user()->hasRole('superadmin'))
            if (!currentOpdId) currentOpdId = '{{ auth()->user()->id_opd }}';
        @endif
        if (currentOpdId) {
            data.forEach(function(d, i) {
                if (d.opd_id == currentOpdId) {
                    colors[i] = '#10b981'; // emerald highlight for selected OPD
                }
            });
        }

        chartInstances['ranking'] = new Chart(canvas, {
            type: 'horizontalBar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0,
                    barPercentage: 0.7,
                    categoryPercentage: 0.85
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: 'rgba(15,23,42,0.9)',
                    cornerRadius: 8,
                    titleFontFamily: 'Inter',
                    bodyFontFamily: 'Inter',
                    callbacks: {
                        label: function(t) { return 'Keuangan: ' + t.xLabel.toFixed(2) + '%'; }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            beginAtZero: true, max: 100,
                            fontColor: '#94a3b8', fontFamily: 'Inter', fontSize: 11,
                            callback: function(v) { return v + '%'; }
                        }
                    }],
                    yAxes: [{
                        gridLines: { display: false },
                        ticks: {
                            fontColor: '#475569', fontFamily: 'Inter', fontSize: 11,
                            padding: 8
                        }
                    }]
                },
                animation: {
                    onComplete: function() {
                        var chartInstance = this.chart;
                        var ctx = chartInstance.ctx;
                        ctx.font = '600 11px Inter, sans-serif';
                        ctx.fillStyle = '#fff';
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';
                        chartInstance.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var val = dataset.data[index];
                                var barWidth = bar._model.x - bar._model.base;
                                if (barWidth > 50) {
                                    ctx.fillText(val.toFixed(1) + '%', bar._model.base + 8, bar._model.y);
                                }
                            });
                        });
                    }
                }
            }
        });
    }

    // ===== TOP/BOTTOM 5 CHARTS =====
    function renderTop5Bottom5Charts(opdRanking) {
        var data = (opdRanking || []).slice();

        // Sort by keuangan desc
        var sortedKeu = data.slice().sort(function(a, b) {
            return (b.keuangan || b.keuangan_pct || 0) - (a.keuangan || a.keuangan_pct || 0);
        });
        var top5Keu = sortedKeu.slice(0, 5);
        var bottom5Keu = sortedKeu.slice(-5).reverse();

        // Sort by fisik desc
        var sortedFisik = data.slice().sort(function(a, b) {
            return (b.fisik || b.fisik_pct || 0) - (a.fisik || a.fisik_pct || 0);
        });
        var top5Fisik = sortedFisik.slice(0, 5);
        var bottom5Fisik = sortedFisik.slice(-5).reverse();

        renderSmallBarChart('top5keu', 'dv2-top5-keu-chart', top5Keu, 'keuangan', ['#f59e0b','#fbbf24','#fcd34d','#fde68a','#fef3c7']);
        renderSmallBarChart('bottom5keu', 'dv2-bottom5-keu-chart', bottom5Keu, 'keuangan', ['#f43f5e','#fb7185','#fda4af','#fecdd3','#ffe4e6']);
        renderSmallBarChart('top5fisik', 'dv2-top5-fisik-chart', top5Fisik, 'fisik', ['#8b5cf6','#a78bfa','#c4b5fd','#ddd6fe','#ede9fe']);
        renderSmallBarChart('bottom5fisik', 'dv2-bottom5-fisik-chart', bottom5Fisik, 'fisik', ['#f43f5e','#fb7185','#fda4af','#fecdd3','#ffe4e6']);
    }

    function renderSmallBarChart(key, canvasId, data, field, colors) {
        destroyChart(key);
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;

        var labels = data.map(function(d) { return d.opd_name || ''; });
        var values = data.map(function(d) { return d[field] || d[field + '_pct'] || 0; });

        chartInstances[key] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0,
                    barPercentage: 0.6
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: 'rgba(15,23,42,0.9)',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(t) { return t.yLabel.toFixed(2) + '%'; }
                    }
                },
                scales: {
                    yAxes: [{
                        gridLines: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            beginAtZero: true, max: 100,
                            fontColor: '#94a3b8', fontSize: 10,
                            callback: function(v) { return v + '%'; }
                        }
                    }],
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: {
                            fontColor: '#475569', fontSize: 9,
                            maxRotation: 25, minRotation: 0
                        }
                    }]
                },
                animation: {
                    onComplete: function() {
                        var ci = this.chart, ctx = ci.ctx;
                        ctx.font = '700 11px Inter, sans-serif';
                        ctx.fillStyle = '#0f172a';
                        ctx.textAlign = 'center';
                        ci.data.datasets.forEach(function(ds, i) {
                            ci.controller.getDatasetMeta(i).data.forEach(function(bar, idx) {
                                ctx.fillText(ds.data[idx].toFixed(1) + '%', bar._model.x, bar._model.y - 6);
                            });
                        });
                    }
                }
            }
        });
    }

    // ===== OPD TABLE =====
    function updateOpdTable(opdData) {
        var tbody = $('#dv2-opd-tbody');
        tbody.empty();
        if (!opdData || opdData.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data</td></tr>');
            $('#dv2-opd-count').text('0 OPD');
            return;
        }

        // Sort
        var sorted = opdData.slice().sort(function(a, b) {
            var kA = a.keuangan || a.keuangan_pct || 0;
            var kB = b.keuangan || b.keuangan_pct || 0;
            return kB - kA;
        });

        // Apply Drill-down filter: only show the selected OPD
        if (selectedDrillDownOpdId) {
            sorted = sorted.filter(function(item) {
                return item.opd_id == selectedDrillDownOpdId;
            });
        }

        // Search filter
        var searchQuery = $('#dv2-opd-search').val() || '';
        searchQuery = searchQuery.toLowerCase().trim();
        if (searchQuery !== '') {
            sorted = sorted.filter(function(item) {
                return (item.opd_name || '').toLowerCase().indexOf(searchQuery) !== -1;
            });
        }

        // Apply "Realisasi" column filter
        if (dv2OpdFilters.realisasi === 'zero') {
            sorted = sorted.filter(function(item) {
                var real = item.sd_bulan_ini || item.realisasi || 0;
                return parseFloat(real) === 0;
            });
        } else if (dv2OpdFilters.realisasi === 'active') {
            sorted = sorted.filter(function(item) {
                var real = item.sd_bulan_ini || item.realisasi || 0;
                return parseFloat(real) > 0;
            });
        }

        // Apply "%Keu" column filter
        if (dv2OpdFilters.keu === 'zero') {
            sorted = sorted.filter(function(item) {
                var keu = item.keuangan || item.keuangan_pct || 0;
                return parseFloat(keu) === 0;
            });
        } else if (dv2OpdFilters.keu === 'low') {
            sorted = sorted.filter(function(item) {
                var keu = item.keuangan || item.keuangan_pct || 0;
                return parseFloat(keu) < 50;
            });
        } else if (dv2OpdFilters.keu === 'high') {
            sorted = sorted.filter(function(item) {
                var keu = item.keuangan || item.keuangan_pct || 0;
                return parseFloat(keu) >= 50;
            });
        }

        // Apply "%Fisik" column filter
        if (dv2OpdFilters.fisik === 'zero') {
            sorted = sorted.filter(function(item) {
                var fis = item.fisik || item.fisik_pct || 0;
                return parseFloat(fis) === 0;
            });
        } else if (dv2OpdFilters.fisik === 'low') {
            sorted = sorted.filter(function(item) {
                var fis = item.fisik || item.fisik_pct || 0;
                return parseFloat(fis) < 50;
            });
        } else if (dv2OpdFilters.fisik === 'high') {
            sorted = sorted.filter(function(item) {
                var fis = item.fisik || item.fisik_pct || 0;
                return parseFloat(fis) >= 50;
            });
        }

        if (sorted.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data OPD yang cocok</td></tr>');
            $('#dv2-opd-count').text('0 OPD');
            $('#dv2-opd-total-pagu').text('-');
            $('#dv2-opd-total-realisasi').text('-');
            $('#dv2-opd-total-keu').text('-');
            $('#dv2-opd-total-fisik').text('-');
            $('#dv2-opd-total-deviasi').text('-');
            return;
        }
 
        var currentOpdId = '{{ auth()->user()->id_opd ?? "" }}';
        var totalPagu = 0, totalRealisasi = 0;
        var totalFisikRupiah = 0;
 
        sorted.forEach(function(item, idx) {
            var rank = idx + 1;
            var keu = item.keuangan || item.keuangan_pct || 0;
            var fisik = item.fisik || item.fisik_pct || 0;
            var pagu = item.pagu_anggaran || item.pagu || 0;
            var realisasi = item.sd_bulan_ini || item.realisasi || 0;
            totalPagu += pagu;
            totalRealisasi += realisasi;
            totalFisikRupiah += (fisik / 100) * pagu;
 
            var deviasi = fisik - keu;
            var deviasiHtml = '';
            if (deviasi > 0) {
                deviasiHtml = '<span class="text-success font-weight-bold"><i class="fas fa-caret-up mr-1"></i>+' + deviasi.toFixed(2) + '%</span>';
            } else if (deviasi < 0) {
                deviasiHtml = '<span class="text-danger font-weight-bold"><i class="fas fa-caret-down mr-1"></i>' + deviasi.toFixed(2) + '%</span>';
            } else {
                deviasiHtml = '<span class="text-success font-weight-bold">+0.00%</span>';
            }
 
            var rankClass = 'dv2-rank-default';
            if (rank === 1) rankClass = 'dv2-rank-gold';
            else if (rank === 2) rankClass = 'dv2-rank-silver';
            else if (rank === 3) rankClass = 'dv2-rank-bronze';
 
            var isHighlight = '';
            if (selectedDrillDownOpdId && item.opd_id == selectedDrillDownOpdId) {
                isHighlight = ' table-primary';
            } else if (item.opd_id == currentOpdId) {
                isHighlight = ' dv2-highlight';
            }
 
            var row = '<tr class="' + isHighlight + '" onclick="handleOpdRowClick(' + item.opd_id + ', \'' + (item.opd_name || '').replace(/'/g, "\\'") + '\', this)">' +
                '<td><span class="dv2-rank-cell ' + rankClass + '">' + rank + '</span></td>' +
                '<td>' + (item.opd_name || '') + '</td>' +
                '<td class="text-right">' + fmtMoney(pagu) + '</td>' +
                '<td class="text-right">' + fmtMoney(realisasi) + '</td>' +
                '<td class="text-center"><div class="d-flex align-items-center justify-content-center"><div class="dv2-pct-bar"><div class="dv2-pct-bar-fill pct-keu" style="width:' + Math.min(keu, 100) + '%"></div></div><span style="min-width:50px;font-weight:600;">' + keu.toFixed(2) + '%</span></div></td>' +
                '<td class="text-center"><div class="d-flex align-items-center justify-content-center"><div class="dv2-pct-bar"><div class="dv2-pct-bar-fill pct-fisik" style="width:' + Math.min(fisik, 100) + '%"></div></div><span style="min-width:50px;font-weight:600;">' + fisik.toFixed(2) + '%</span></div></td>' +
                '<td class="text-center">' + deviasiHtml + '</td>' +
                '</tr>';
            tbody.append(row);
        });
 
        var totalKeu = totalPagu > 0 ? (totalRealisasi / totalPagu) * 100 : 0;
        var totalFisik = totalPagu > 0 ? (totalFisikRupiah / totalPagu) * 100 : 0;
        var totalDeviasi = totalFisik - totalKeu;
        var totalDeviasiHtml = '';
        if (totalDeviasi > 0) {
            totalDeviasiHtml = '<span class="text-success font-weight-bold">+' + totalDeviasi.toFixed(2) + '%</span>';
        } else if (totalDeviasi < 0) {
            totalDeviasiHtml = '<span class="text-danger font-weight-bold">' + totalDeviasi.toFixed(2) + '%</span>';
        } else {
            totalDeviasiHtml = '<span class="text-success font-weight-bold">+0.00%</span>';
        }
 
        $('#dv2-opd-total-pagu').text(fmtMoney(totalPagu));
        $('#dv2-opd-total-realisasi').text(fmtMoney(totalRealisasi));
        $('#dv2-opd-total-keu').text(fmtPct(totalKeu));
        $('#dv2-opd-total-fisik').text(fmtPct(totalFisik));
        $('#dv2-opd-total-deviasi').html(totalDeviasiHtml);
        $('#dv2-opd-count').text(sorted.length + ' OPD');
    }
 
    // ===== OPD TABLE SORT =====
    function dv2SortOpdTable(col) {
        // Re-sort the current data
        if (!currentData) return;
        var data = currentData.opd_ranking || currentData.opd_data || [];
        if (!data.length) return;
 
        if (opdSortCol === col) {
            opdSortDir = opdSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            opdSortCol = col;
            opdSortDir = 'desc';
        }
 
        data.sort(function(a, b) {
            var vA, vB;
            switch(col) {
                case 'name': vA = (a.opd_name || '').toLowerCase(); vB = (b.opd_name || '').toLowerCase();
                    return opdSortDir === 'asc' ? vA.localeCompare(vB) : vB.localeCompare(vA);
                case 'pagu': vA = a.pagu_anggaran || a.pagu || 0; vB = b.pagu_anggaran || b.pagu || 0; break;
                case 'realisasi': vA = a.sd_bulan_ini || a.realisasi || 0; vB = b.sd_bulan_ini || b.realisasi || 0; break;
                case 'keu': vA = a.keuangan || a.keuangan_pct || 0; vB = b.keuangan || b.keuangan_pct || 0; break;
                case 'fisik': vA = a.fisik || a.fisik_pct || 0; vB = b.fisik || b.fisik_pct || 0; break;
                case 'deviasi':
                    vA = (a.fisik || a.fisik_pct || 0) - (a.keuangan || a.keuangan_pct || 0);
                    vB = (b.fisik || b.fisik_pct || 0) - (b.keuangan || b.keuangan_pct || 0);
                    break;
                default: vA = a.keuangan || 0; vB = b.keuangan || 0;
            }
            return opdSortDir === 'asc' ? vA - vB : vB - vA;
        });
 
        updateOpdTable(data);
    }
    window.dv2SortOpdTable = dv2SortOpdTable;

    // ===== PROGRAM TABLE =====
    function updateProgramTable(programData, summary) {
        var tbody = $('#dv2-program-tbody');
        tbody.empty();
        if (!programData || programData.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data program</td></tr>');
            $('#dv2-program-count').text('0 Program');
            $('#dv2-program-total-pagu').text('-');
            $('#dv2-program-total-realisasi').text('-');
            $('#dv2-program-total-keu').text('-');
            $('#dv2-program-total-fisik').text('-');
            $('#dv2-program-total-deviasi').text('-');
            return;
        }
 
        var filtered = programData || [];
        
        // Apply Drill-down filter client-side
        if (selectedDrillDownOpdId) {
            filtered = filtered.filter(function(item) {
                return item.opd_id == selectedDrillDownOpdId;
            });
        }

        // Search filter
        var searchQuery = $('#dv2-program-search').val() || '';
        searchQuery = searchQuery.toLowerCase().trim();
        if (searchQuery !== '') {
            filtered = filtered.filter(function(item) {
                return (item.program_name || '').toLowerCase().indexOf(searchQuery) !== -1;
            });
        }
 
        if (filtered.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data program yang cocok</td></tr>');
            $('#dv2-program-count').text('0 Program');
            $('#dv2-program-total-pagu').text('-');
            $('#dv2-program-total-realisasi').text('-');
            $('#dv2-program-total-keu').text('-');
            $('#dv2-program-total-fisik').text('-');
            $('#dv2-program-total-deviasi').text('-');
            return;
        }
 
        var totalPagu = 0, totalRealisasi = 0;
        var totalFisikRupiah = 0;
 
        filtered.forEach(function(item, idx) {
            var keu = item.keuangan || 0;
            var fisik = item.fisik || 0;
            var pagu = item.pagu_anggaran || 0;
            var realisasi = item.sd_bulan_ini || 0;
            totalPagu += pagu;
            totalRealisasi += realisasi;
            totalFisikRupiah += (fisik / 100) * pagu;
 
            var deviasi = fisik - keu;
            var deviasiHtml = '';
            if (deviasi > 0) {
                deviasiHtml = '<span class="text-success font-weight-bold"><i class="fas fa-caret-up mr-1"></i>+' + deviasi.toFixed(2) + '%</span>';
            } else if (deviasi < 0) {
                deviasiHtml = '<span class="text-danger font-weight-bold"><i class="fas fa-caret-down mr-1"></i>' + deviasi.toFixed(2) + '%</span>';
            } else {
                deviasiHtml = '<span class="text-success font-weight-bold">+0.00%</span>';
            }
 
            var row = '<tr>' +
                '<td>' + (idx + 1) + '</td>' +
                '<td>' + (item.program_name || '') + '</td>' +
                '<td class="text-right">' + fmtMoney(pagu) + '</td>' +
                '<td class="text-right">' + fmtMoney(realisasi) + '</td>' +
                '<td class="text-center"><div class="d-flex align-items-center justify-content-center"><div class="dv2-pct-bar"><div class="dv2-pct-bar-fill pct-keu" style="width:' + Math.min(keu, 100) + '%"></div></div><span style="min-width:50px;font-weight:600;">' + keu.toFixed(2) + '%</span></div></td>' +
                '<td class="text-center"><div class="d-flex align-items-center justify-content-center"><div class="dv2-pct-bar"><div class="dv2-pct-bar-fill pct-fisik" style="width:' + Math.min(fisik, 100) + '%"></div></div><span style="min-width:50px;font-weight:600;">' + fisik.toFixed(2) + '%</span></div></td>' +
                '<td class="text-center">' + deviasiHtml + '</td>' +
                '</tr>';
            tbody.append(row);
        });
 
        var totalKeu = totalPagu > 0 ? (totalRealisasi / totalPagu) * 100 : 0;
        var totalFisik = totalPagu > 0 ? (totalFisikRupiah / totalPagu) * 100 : 0;
        var totalDeviasi = totalFisik - totalKeu;
        var totalDeviasiHtml = '';
        if (totalDeviasi > 0) {
            totalDeviasiHtml = '<span class="text-success font-weight-bold">+' + totalDeviasi.toFixed(2) + '%</span>';
        } else if (totalDeviasi < 0) {
            totalDeviasiHtml = '<span class="text-danger font-weight-bold">' + totalDeviasi.toFixed(2) + '%</span>';
        } else {
            totalDeviasiHtml = '<span class="text-success font-weight-bold">+0.00%</span>';
        }
 
        $('#dv2-program-total-pagu').text(fmtMoney(totalPagu));
        $('#dv2-program-total-realisasi').text(fmtMoney(totalRealisasi));
        $('#dv2-program-total-keu').text(fmtPct(totalKeu));
        $('#dv2-program-total-fisik').text(fmtPct(totalFisik));
        $('#dv2-program-total-deviasi').html(totalDeviasiHtml);
        $('#dv2-program-count').text(filtered.length + ' Program');
    }

    // ===== DRILL DOWN HANDLERS =====
    function handleOpdRowClick(opdId, opdName, rowElement) {
        if (!opdId) return;

        // Prevent drill down if there is already a filter locked to a single OPD from the top filter
        var topFilterOpd = $('#dv2-filter-opd').val();
        if (topFilterOpd && topFilterOpd != '') {
            return;
        }

        // Toggle state
        if (selectedDrillDownOpdId == opdId) {
            resetDrillDown();
        } else {
            selectedDrillDownOpdId = opdId;
            selectedDrillDownOpdName = opdName;

            // Show and set ALL drill-down badges
            $('#dv2-trend-drilldown-opd-name').text(opdName);
            $('#dv2-program-drilldown-opd-name').text(opdName);
            $('#dv2-opd-drilldown-opd-name').text(opdName);
            $('#dv2-ranking-drilldown-opd-name').text(opdName);
            $('#dv2-trend-drilldown-badge, #dv2-program-drilldown-badge, #dv2-opd-drilldown-badge, #dv2-ranking-drilldown-badge').fadeIn(200);

            // Fetch dynamic trend for this OPD via AJAX
            var year = $('#dv2-filter-year').val() || '{{ date("Y") }}';
            var month = $('#dv2-filter-month').val() || '{{ date("m") }}';
            var fullMonth = year + '-' + month;

            $.ajax({
                url: '{{ url("/dashboard-v2/api-data") }}',
                method: 'GET',
                data: { month: fullMonth, opd: opdId },
                success: function(resp) {
                    // Update Trend Chart with the selected OPD's trend
                    renderTrendChart(resp.monthly_trend);
                    // Update Summary Cards for this OPD
                    updateSummaryCards(resp.summary);
                    updateProgressRings(resp.summary);
                },
                error: function() {
                    toastr.error('Gagal memuat data tren OPD');
                }
            });

            // Filter OPD Table, Program Table, and Ranking Chart (client-side)
            if (currentData) {
                updateOpdTable(currentData.opd_ranking || currentData.opd_data || []);
                updateProgramTable(currentData.program_data || [], currentData.program_summary || {});
                renderRankingChart(currentData.opd_ranking);
            }
        }
    }

    function resetDrillDown(e) {
        if (e) {
            e.stopPropagation(); // Stop event propagation if clicked on Reset icon
        }
        
        selectedDrillDownOpdId = null;
        selectedDrillDownOpdName = null;

        // Remove highlight on rows
        $('#dv2-opd-tbody tr').removeClass('table-primary');

        // Hide ALL drill-down badges
        $('#dv2-trend-drilldown-badge, #dv2-program-drilldown-badge, #dv2-opd-drilldown-badge, #dv2-ranking-drilldown-badge').fadeOut(200);

        // Restore ALL sections: trend, program table, OPD table, ranking chart, summary
        if (currentData) {
            renderTrendChart(currentData.monthly_trend);
            updateProgramTable(currentData.program_data || [], currentData.program_summary || {});
            updateOpdTable(currentData.opd_ranking || currentData.opd_data || []);
            renderRankingChart(currentData.opd_ranking);
            updateSummaryCards(currentData.summary);
            updateProgressRings(currentData.summary);
        }
    }

    // Expose functions globally for row onclick and badge click
    window.handleOpdRowClick = handleOpdRowClick;
    window.resetDrillDown = resetDrillDown;

    function showBelumInputModalV2() {
        if (!currentData || !currentData.opd_ranking) {
            toastr.warning('Data belum dimuat atau tidak tersedia');
            return;
        }

        var year = $('#dv2-filter-year').val() || '{{ date("Y") }}';
        var month = $('#dv2-filter-month').val() || '{{ date("m") }}';
        var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var m = parseInt(month, 10);
        var periodText = (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + year;
        
        $('#dv2-modal-belum-input-periode').text(periodText);

        var tableBody = $('#dv2-table-belum-input-detail tbody');
        tableBody.empty();

        var belumInputOpds = currentData.opd_ranking.filter(function(item) {
            var real = item.sd_bulan_ini || item.realisasi || 0;
            return parseFloat(real) === 0;
        });

        if (belumInputOpds.length > 0) {
            belumInputOpds.forEach(function(item, index) {
                var pagu = parseFloat(item.pagu_anggaran || item.pagu || 0);
                var row = '<tr>' +
                    '<td class="text-center">' + (index + 1) + '</td>' +
                    '<td>' + (item.opd_name || '') + '</td>' +
                    '<td class="text-right">' + fmtMoney(pagu) + '</td>' +
                    '</tr>';
                tableBody.append(row);
            });
        } else {
            tableBody.append('<tr><td colspan="3" class="text-center text-muted">Semua OPD sudah menginput realisasi bulan ini.</td></tr>');
        }

        $('#dv2-modal-belum-input-realisasi').modal('show');
    }
    window.showBelumInputModalV2 = showBelumInputModalV2;

    // ===== EXCEL EXPORT LINK =====
    function updateExcelLink() {
        var year = $('#dv2-filter-year').val() || '{{ date("Y") }}';
        var month = $('#dv2-filter-month').val() || '{{ date("m") }}';
        var opd = $('#dv2-filter-opd').val() || '';
        var url = '{{ url("/dashboard-v2/export/excel") }}?month=' + year + '-' + month + '&opd=' + opd;
        $('#dv2-export-excel').attr('href', url);
    }

    // ===== PDF EXPORT (Client-side with html2canvas + jsPDF) =====
    function exportPDF() {
        var btn = $('#dv2-export-pdf');
        var origText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating...').prop('disabled', true);

        var element = document.getElementById('dv2-app');

        html2canvas(element, {
            scale: 1.5,
            useCORS: true,
            logging: false,
            backgroundColor: '#f0f2f5',
            scrollY: -window.scrollY,
            windowHeight: element.scrollHeight
        }).then(function(canvas) {
            var imgData = canvas.toDataURL('image/jpeg', 0.85);
            var pdf = new jspdf.jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            var pageWidth = pdf.internal.pageSize.getWidth();
            var pageHeight = pdf.internal.pageSize.getHeight();
            var margin = 10;
            var contentWidth = pageWidth - (margin * 2);

            var imgWidth = canvas.width;
            var imgHeight = canvas.height;
            var ratio = contentWidth / imgWidth;
            var scaledHeight = imgHeight * ratio;

            // Multi-page support
            var position = 0;
            var remainingHeight = scaledHeight;

            while (remainingHeight > 0) {
                if (position > 0) pdf.addPage();
                pdf.addImage(imgData, 'JPEG', margin, margin - (position * (pageHeight - margin * 2)),
                    contentWidth, scaledHeight);
                remainingHeight -= (pageHeight - margin * 2);
                position++;
            }

            var year = $('#dv2-filter-year').val();
            var month = $('#dv2-filter-month').val();
            pdf.save('Dashboard_Executive_' + year + '_' + month + '.pdf');

            btn.html(origText).prop('disabled', false);
            toastr.success('PDF berhasil diunduh');
        }).catch(function(err) {
            console.error('PDF export error:', err);
            btn.html(origText).prop('disabled', false);
            toastr.error('Gagal menghasilkan PDF');
        });
    }

    // ===== EVENT BINDINGS =====
    $(document).ready(function() {
        // Init select2
        if ($.fn.select2) {
            $('#dv2-filter-opd').select2({ placeholder: 'Pilih OPD', allowClear: true });
        }

        // Load data on page load
        loadDashboardData();

        // Filter button
        $('#dv2-filter-btn').on('click', function() {
            loadDashboardData();
        });

        // Auto-refresh on filter change
        $('#dv2-filter-opd, #dv2-filter-month, #dv2-filter-year').on('change', function() {
            loadDashboardData();
        });

        // PDF export
        $('#dv2-export-pdf').on('click', function(e) {
            e.preventDefault();
            exportPDF();
        });

        // Search event bindings
        $('#dv2-opd-search').on('keyup input', function() {
            if (currentData) {
                updateOpdTable(currentData.opd_ranking || currentData.opd_data || []);
            }
        });

        $('#dv2-program-search').on('keyup input', function() {
            if (currentData) {
                updateProgramTable(currentData.program_data || [], currentData.program_summary || {});
            }
        });

        // Column Filter event bindings
        $('.dv2-opd-filter-val').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $this.data('filter-type');
            var val = $this.data('filter-val');

            // Update active state in dropdown UI
            $this.siblings('.dv2-opd-filter-val').removeClass('active');
            $this.addClass('active');

            // Save filter state
            dv2OpdFilters[type] = val;

            // Highlight filter icon if filter is active (not 'all')
            var icon = $('#dv2-opd-filter-btn-' + type);
            if (val !== 'all') {
                icon.removeClass('text-muted').addClass('text-warning');
            } else {
                icon.removeClass('text-warning').addClass('text-muted');
            }

            // Re-populate table
            if (currentData) {
                updateOpdTable(currentData.opd_ranking || currentData.opd_data || []);
            }
        });

        // Copy button inside Belum Input modal for V2
        $('#dv2-btn-copy-belum-input').on('click', function() {
            if (!currentData || !currentData.opd_ranking) return;

            var year = $('#dv2-filter-year').val() || '{{ date("Y") }}';
            var month = $('#dv2-filter-month').val() || '{{ date("m") }}';
            var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            var m = parseInt(month, 10);
            var periodText = (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + year;

            var belumInputOpds = currentData.opd_ranking.filter(function(item) {
                var real = item.sd_bulan_ini || item.realisasi || 0;
                return parseFloat(real) === 0;
            });

            if (belumInputOpds.length === 0) {
                toastr.info('Tidak ada OPD yang belum menginput realisasi.');
                return;
            }

            var textToCopy = 'Daftar OPD belum menginput realisasi anggaran periode: ' + periodText + '\n\n';
            belumInputOpds.forEach(function(item, index) {
                textToCopy += (index + 1) + '. ' + item.opd_name + '\n';
            });

            // Copy to clipboard
            var tempTextarea = $('<textarea>');
            $('body').append(tempTextarea);
            tempTextarea.val(textToCopy).select();
            document.execCommand('copy');
            tempTextarea.remove();

            toastr.success('Daftar OPD belum input realisasi disalin ke clipboard');
        });
    });

})();
</script>
@endsection
