@extends('adminlte::page')

@section('title', 'Sistem Informasi Administrasi Pembangunan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="m-0 text-dark">Sistem Informasi Administrasi Pembangunan</h1>
    <div class="d-flex align-items-center mt-2 mt-md-0">
        <button class="btn btn-sm btn-danger mr-2" id="rfk-export-pdf" title="Export PDF" style="border-radius: 8px; font-weight: 600; padding: 6px 14px;">
            <i class="fas fa-file-pdf mr-1"></i> PDF
        </button>
        <a href="#" class="btn btn-sm btn-success" id="rfk-export-excel" title="Export Excel" style="border-radius: 8px; font-weight: 600; padding: 6px 14px;">
            <i class="fas fa-file-excel mr-1"></i> Excel
        </a>
    </div>
</div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    .card-header {
        font-weight: 600;
    }
    .card-body h3 {
        font-size: 1.5rem;
        word-break: break-word;
    }
    @media (max-width: 768px) {
        .card-body h3 {
            font-size: 1.2rem;
        }
    }
    /* === OPD Ranking Styles === */
    .rank-banner {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
        border-radius: 12px;
        color: #fff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.35);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .rank-banner:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(30, 60, 114, 0.45);
    }
    .rank-banner::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .rank-banner::after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .rank-number-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(253, 160, 133, 0.5);
        animation: pulseRank 2s infinite;
    }
    .rank-number-circle .rank-num {
        font-size: 2rem;
        font-weight: 800;
        color: #1e3c72;
        line-height: 1;
    }
    .rank-trophy-icon {
        font-size: 1.3rem;
        color: #f6d365;
        margin-right: 8px;
        filter: drop-shadow(0 2px 4px rgba(246,211,101,0.5));
    }
    @keyframes pulseRank {
        0% { box-shadow: 0 4px 15px rgba(253,160,133,0.5); }
        50% { box-shadow: 0 4px 25px rgba(253,160,133,0.8); }
        100% { box-shadow: 0 4px 15px rgba(253,160,133,0.5); }
    }
    .rank-badge-pill {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }
</style>
@stop

@section('content')
<div class="row justify-content-center" id="dashboard-app-content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="/dashboard" method="get" id="dashboard-filter-form">
                    <div class="row mb-3">
                        <div class="col-sm-12 col-lg-3">
                            <label for="filter-year">Tahun</label>
                            <input type="number" name="filter_year" class="form-control" id="filter-year" min="1900"
                                max="2099" step="1" value="{{$filter_year ?? date('Y')}}" />
                        </div>

                        <div class="col-sm-12 col-lg-3">
                            <label for="filter-month">Bulan</label>
                            <select name="filter_month" class="form-control" id="filter-month" style="width: 100%">
                                @php
                                    $months = [
                                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                    ];
                                    // Handle filter_month: if it's YYYY-MM format, extract month; if it's already MM, use as is
                                    if ($filter_month) {
                                        if (strlen($filter_month) > 2) {
                                            $currentMonth = \Carbon\Carbon::parse($filter_month)->format('m');
                                        } else {
                                            $currentMonth = str_pad($filter_month, 2, '0', STR_PAD_LEFT);
                                        }
                                    } else {
                                        $currentMonth = date('m');
                                    }
                                @endphp
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin_rfk'))
                        <div class="col-sm-12 col-lg-3">
                            <label>OPD</label>
                            <select name="filter_opd" class="form-control select2" id="filter-opd" style="width: 100%">
                                <option value="">Semua OPD</option>
                                @foreach ($opds as $opd)
                                <option value="{{$opd['id']}}" {{$filter_opd==$opd['id'] ? 'selected' : '' }}>
                                    {{$opd['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-12 col-lg-3">
                            <label></label>
                            <button type="button" class="btn btn-sm btn-primary" id="dashboard-filter-btn">
                                <i class="fa fa-search mr-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        @php
            $user = Auth::user();
            $isSuperadmin = $user->hasRole('superadmin');
            $isAdminOpd = $user->hasRole('admin_opd');
            $isAdminRfk = $user->hasRole('admin_rfk');

            // Determine which tabs to show
            $showKonstruksi = $isSuperadmin || $isAdminOpd;
            $showRfk = $isSuperadmin || $isAdminRfk;

            // Determine default active tab
            if ($isAdminRfk && !$isSuperadmin && !$isAdminOpd) {
                $defaultActiveTab = 'rfk';
            } else {
                $defaultActiveTab = 'konstruksi';
            }
        @endphp
        <ul class="nav nav-tabs" id="dashboardTab" role="tablist">
            @if($showKonstruksi)
            <li class="nav-item">
                <a class="nav-link {{ $defaultActiveTab == 'konstruksi' ? 'active' : '' }}" id="konstruksi-tab" data-toggle="tab" href="#konstruksi" role="tab" aria-controls="konstruksi" aria-selected="{{ $defaultActiveTab == 'konstruksi' ? 'true' : 'false' }}">Pekerjaan Konstruksi</a>
            </li>
            @endif
            @if($showRfk)
            <li class="nav-item">
                <a class="nav-link {{ $defaultActiveTab == 'rfk' ? 'active' : '' }}" id="rfk-tab" data-toggle="tab" href="#rfk" role="tab" aria-controls="rfk" aria-selected="{{ $defaultActiveTab == 'rfk' ? 'true' : 'false' }}">RFK</a>
            </li>
            @endif
        </ul>
    </div>
</div>
<div class="tab-content" id="dashboardTabContent">
    <!-- Pekerjaan Konstruksi Tab -->
    @if($showKonstruksi)
    <div class="tab-pane fade {{ $defaultActiveTab == 'konstruksi' ? 'show active' : '' }}" id="konstruksi" role="tabpanel" aria-labelledby="konstruksi-tab">
        <div class="row justify-content-center mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Jenis Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <div class="position-relative mb-4">
                                        <canvas id="project-chart" height="223"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Jumlah Nilai Realisasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <div class="position-relative mb-4">
                                        <canvas id="avarage-chart" height="223"></canvas>
                                    </div>
                                    <div class="d-flex flex-row justify-content-end">
                                        <span class="mr-2">
                                            <i class="fas fa-square" style="color: #007bff"></i> Nilai Kontrak
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-square" style="color: #FF5733"></i> Realisasi Anggaran
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Summary Cards - Total Keseluruhan -->
            <div class="col-12 mb-3">
                <h4 class="font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Ringkasan Keseluruhan
                </h4>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-wallet mr-2"></i>PAGU
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-primary mb-0" id="total-pagu">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Total Pagu Anggaran</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>HPS
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-info mb-0" id="total-hps">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Harga Perkiraan Sendiri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-handshake mr-2"></i>KONTRAK
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-success mb-0" id="total-kontrak">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Nilai Kontrak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-coins mr-2"></i>SISA PAGU
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-warning mb-0" id="total-sisa-pagu">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Sisa Pagu Anggaran</p>
                    </div>
                </div>
            </div>

            <!-- Konstruksi Cards -->
            <div class="col-12 mb-3 mt-4">
                <h4 class="font-weight-bold" style="color: #ff6b35;">
                    <i class="fas fa-hard-hat mr-2"></i>Kategori Konstruksi
                </h4>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-danger" style="border-left-width: 4px;">
                    <div class="card-header" style="background-color: #ff6b35; color: white;">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-wallet mr-2"></i>PAGU KONSTRUKSI
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold mb-0" style="color: #ff6b35;" id="total-pagu-konstruksi">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Total Pagu Anggaran</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-danger" style="border-left-width: 4px;">
                    <div class="card-header" style="background-color: #ff6b35; color: white;">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>HPS KONSTRUKSI
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold mb-0" style="color: #ff6b35;" id="total-hps-konstruksi">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Harga Perkiraan Sendiri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-danger" style="border-left-width: 4px;">
                    <div class="card-header" style="background-color: #ff6b35; color: white;">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-handshake mr-2"></i>KONTRAK KONSTRUKSI
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold mb-0" style="color: #ff6b35;" id="total-kontrak-konstruksi">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Nilai Kontrak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-danger" style="border-left-width: 4px;">
                    <div class="card-header" style="background-color: #ff6b35; color: white;">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-coins mr-2"></i>SISA PAGU KONSTRUKSI
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold mb-0" style="color: #ff6b35;" id="total-sisa-pagu-konstruksi">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Sisa Pagu Anggaran</p>
                    </div>
                </div>
            </div>

            <!-- Perencanaan Cards -->
            <div class="col-12 mb-3 mt-4">
                <h4 class="font-weight-bold" style="color: #007bff;">
                    <i class="fas fa-drafting-compass mr-2"></i>Kategori Perencanaan
                </h4>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-primary" style="border-left-width: 4px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-wallet mr-2"></i>PAGU PERENCANAAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-primary mb-0" id="total-pagu-perencanaan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Total Pagu Anggaran</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-primary" style="border-left-width: 4px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>HPS PERENCANAAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-primary mb-0" id="total-hps-perencanaan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Harga Perkiraan Sendiri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-primary" style="border-left-width: 4px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-handshake mr-2"></i>KONTRAK PERENCANAAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-primary mb-0" id="total-kontrak-perencanaan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Nilai Kontrak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-primary" style="border-left-width: 4px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-coins mr-2"></i>SISA PAGU PERENCANAAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-primary mb-0" id="total-sisa-pagu-perencanaan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Sisa Pagu Anggaran</p>
                    </div>
                </div>
            </div>

            <!-- Pengawasan Cards -->
            <div class="col-12 mb-3 mt-4">
                <h4 class="font-weight-bold" style="color: #28a745;">
                    <i class="fas fa-clipboard-check mr-2"></i>Kategori Pengawasan
                </h4>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-success" style="border-left-width: 4px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-wallet mr-2"></i>PAGU PENGAWASAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-success mb-0" id="total-pagu-pengawasan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Total Pagu Anggaran</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-success" style="border-left-width: 4px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>HPS PENGAWASAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-success mb-0" id="total-hps-pengawasan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Harga Perkiraan Sendiri</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-success" style="border-left-width: 4px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-handshake mr-2"></i>KONTRAK PENGAWASAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-success mb-0" id="total-kontrak-pengawasan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Nilai Kontrak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-left-success" style="border-left-width: 4px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-coins mr-2"></i>SISA PAGU PENGAWASAN
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center font-weight-bold text-success mb-0" id="total-sisa-pagu-pengawasan">Rp...........</h3>
                        <p class="text-center text-muted mb-0" style="font-size: 0.85rem;">Sisa Pagu Anggaran</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- RFK Tab -->
    @if($showRfk)
    <div class="tab-pane fade {{ $defaultActiveTab == 'rfk' ? 'show active' : '' }}" id="rfk" role="tabpanel" aria-labelledby="rfk-tab">
        <div class="row justify-content-center mt-3">
            <!-- Dynamic OPD Rank inside RFK Tab -->
            <div class="col-md-12 mb-3" id="rfk-tab-rank-banner" style="display: none;">
                <div class="rank-banner p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="rank-number-circle mr-4">
                                <span class="rank-num" id="rfk-rank-badge-large">#-</span>
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bold" style="color: #f6d365;">
                                    <i class="fas fa-trophy rank-trophy-icon"></i>
                                    Peringkat Realisasi Keuangan OPD
                                </h5>
                                <p class="mb-1" style="color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                                    <strong id="rfk-rank-opd-name">-</strong> berada pada peringkat
                                    <span class="rank-badge-pill mx-1" id="rfk-rank-badge">#-</span>
                                    dari <strong id="rfk-rank-total-opd">-</strong> OPD
                                </p>
                                <small style="color: rgba(255,255,255,0.6);" id="rfk-rank-period">-</small>
                            </div>
                        </div>
                        <div class="text-right d-none d-md-block">
                            <small class="rank-badge-pill" id="rfk-rank-total-large">- OPD</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Summary Cards -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Pagu Anggaran</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center" id="rfk-total-pagu">Rp...........</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Fisik %</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center" id="rfk-total-fisik">...........</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Keu %</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center" id="rfk-total-keu">...........</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Realisasi sd Bulan ini</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center" id="rfk-total-realisasi">Rp...........</h3>
                    </div>
                </div>
            </div>
            <!-- Nama OPD Table -->
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <h5 class="card-title m-0">Nama OPD</h5>
                        <input type="text" id="rfk-opd-search" class="form-control form-control-sm" placeholder="Cari OPD..." style="width: 200px; display: inline-block;">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="rfk-opd-table" class="table table-bordered table-hover">
                                <thead class="text-center font-weight-bold">
                                    <tr>
                                        <th style="width: 50px; cursor: pointer;" onclick="sortRFKTable('rank')"># <i class="fa fa-sort ml-1" style="opacity:0.4;"></i></th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('name')">Nama OPD <i class="fa fa-sort ml-1" style="opacity:0.4;"></i></th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('pagu')">Pagu Anggaran <i class="fa fa-sort ml-1" style="opacity:0.4;"></i></th>
                                        <th class="position-relative" style="cursor: pointer;" onclick="sortRFKTable('bulan_ini')">
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                                <span>Bulan Ini</span>
                                                <i class="fa fa-sort mr-1" style="opacity:0.4;"></i>
                                                <div class="dropdown d-inline-block" onclick="event.stopPropagation();">
                                                    <a href="#" class="text-muted" id="rfk-opd-filter-btn-bulan_ini" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-filter fa-xs"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px; padding: 8px;">
                                                        <h6 class="dropdown-header px-2 py-1" style="font-size: 0.78rem;">Filter Realisasi Bulan Ini</h6>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val active" href="#" data-filter-type="bulan_ini" data-filter-val="all" style="font-size: 0.82rem;">Semua</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="bulan_ini" data-filter-val="zero" style="font-size: 0.82rem;">Belum Input (Rp 0)</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="bulan_ini" data-filter-val="active" style="font-size: 0.82rem;">Sudah Input (&gt; Rp 0)</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item px-2 py-1 text-primary text-center font-weight-bold" href="#" onclick="event.preventDefault(); showBelumInputModal();" style="font-size: 0.82rem;">
                                                            <i class="fas fa-list mr-1"></i> Detail Belum Input
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('realisasi')">Realisasi sd Bulan ini <i class="fa fa-sort ml-1" style="opacity:0.4;"></i></th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('keu')" class="position-relative">
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                                <span>Keu %</span>
                                                <i class="fa fa-sort mr-1" style="opacity:0.4;"></i>
                                                <div class="dropdown d-inline-block" onclick="event.stopPropagation();">
                                                    <a href="#" class="text-muted" id="rfk-opd-filter-btn-keu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-filter fa-xs"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px; padding: 8px;">
                                                        <h6 class="dropdown-header px-2 py-1" style="font-size: 0.78rem;">Filter Progres Keu</h6>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val active" href="#" data-filter-type="keu" data-filter-val="all" style="font-size: 0.82rem;">Semua</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="zero" style="font-size: 0.82rem;">Masih 0%</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="low" style="font-size: 0.82rem;">Rendah (&lt; 50%)</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="keu" data-filter-val="high" style="font-size: 0.82rem;">Tinggi (&gt;= 50%)</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('fisik')" class="position-relative">
                                            <div class="d-flex align-items-center justify-content-center" style="gap: 5px;">
                                                <span>Fisik %</span>
                                                <i class="fa fa-sort mr-1" style="opacity:0.4;"></i>
                                                <div class="dropdown d-inline-block" onclick="event.stopPropagation();">
                                                    <a href="#" class="text-muted" id="rfk-opd-filter-btn-fisik" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-filter fa-xs"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px; padding: 8px;">
                                                        <h6 class="dropdown-header px-2 py-1" style="font-size: 0.78rem;">Filter Progres Fisik</h6>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val active" href="#" data-filter-type="fisik" data-filter-val="all" style="font-size: 0.82rem;">Semua</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="zero" style="font-size: 0.82rem;">Masih 0%</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="low" style="font-size: 0.82rem;">Rendah (&lt; 50%)</a>
                                                        <a class="dropdown-item px-2 py-1 rfk-opd-filter-val" href="#" data-filter-type="fisik" data-filter-val="high" style="font-size: 0.82rem;">Tinggi (&gt;= 50%)</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="font-weight-bold">
                                    <tr>
                                        <td colspan="2">Total</td>
                                        <td class="text-right" id="rfk-opd-total-pagu">Rp0</td>
                                        <td class="text-right" id="rfk-opd-total-bulan-ini">Rp0</td>
                                        <td class="text-right" id="rfk-opd-total-realisasi">Rp0</td>
                                        <td class="text-center" id="rfk-opd-total-keu">0%</td>
                                        <td class="text-center" id="rfk-opd-total-fisik">0%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program OPD Table -->
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <h5 class="card-title m-0">Program OPD yang Dijalankan</h5>
                        <input type="text" id="rfk-program-search" class="form-control form-control-sm" placeholder="Cari Program..." style="width: 200px; display: inline-block;">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="rfk-program-table" class="table table-bordered table-hover">
                                <thead class="text-center font-weight-bold">
                                    <tr>
                                        <th>Nama Program</th>
                                        <th>Pagu Anggaran</th>
                                        <th>Realisasi sd Bulan ini</th>
                                        <th style="cursor: pointer;" onclick="sortRFKProgramTable('keu')">
                                            Keu % <i class="fa fa-sort"></i>
                                        </th>
                                        <th style="cursor: pointer;" onclick="sortRFKProgramTable('fisik')">
                                            Fisik % <i class="fa fa-sort"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="font-weight-bold">
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-right" id="rfk-program-total-pagu">Rp0</td>
                                        <td class="text-right" id="rfk-program-total-realisasi">Rp0</td>
                                        <td class="text-center" id="rfk-program-total-keu">0%</td>
                                        <td class="text-center" id="rfk-program-total-fisik">0%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chart: Realisasi Keuangan dan Fisik Setiap Program -->
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Realisasi Keuangan dan Fisik Setiap Program</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-program-chart" height="300"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #FF5733"></i> Keuangan %
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #007bff"></i> Fisik %
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chart: Realisasi %Keu Seluruh OPD vs %Keu per OPD -->
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Realisasi %Keu Seluruh OPD vs %Keu per OPD</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-keu-comparison-chart" height="250"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #FF5733"></i> Rata-Rata keu Seluruh OPD
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #007bff"></i> Rata-Rata keu OPD Terpilih
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Chart: Realisasi %Fisik Seluruh OPD vs %Fisik per OPD -->
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Realisasi %Fisik Seluruh OPD vs %Fisik per OPD</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-fisik-comparison-chart" height="250"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #FF5733"></i> Rata-Rata fisik Seluruh OPD
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #007bff"></i> Rata-Rata fisik OPD Terpilih
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Top and Bottom 5 OPD Charts -->
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">5 OPD Realisasi Keuangan Tertinggi</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-top5-keu-chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">5 OPD Realisasi Keuangan Terendah</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-bottom5-keu-chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">5 OPD Realisasi Fisik Tertinggi</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-top5-fisik-chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">5 OPD Realisasi Fisik Terendah</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="rfk-bottom5-fisik-chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== MODAL BELUM INPUT REALISASI ===== --}}
    <div class="modal fade" id="modal-belum-input-realisasi" tabindex="-1" role="dialog" aria-labelledby="modalBelumInputLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalBelumInputLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Daftar OPD Belum Input Realisasi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Berikut adalah daftar OPD yang belum menginput realisasi anggaran pada periode <strong id="modal-belum-input-periode">-</strong> (realisasi Bulan Ini = Rp 0):</p>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped" id="table-belum-input-detail">
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
                    <button type="button" class="btn btn-primary" id="btn-copy-belum-input">
                        <i class="fas fa-copy mr-1"></i>Salin Daftar OPD
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    var projectChart = new Chart(
        $('#project-chart'),
        {
            type:'bar',
            data: {
                labels:['Perencanaan','Konstruksi','Pengawasan'],
                datasets:[
                    {
                        backgroundColor:['#FF5733', '#007bff', '#33FFA2'],
                        borderColor:['#FF5733', '#007bff', '#33FFA2'],
                        data: {{$projects}},
                        fill: false,
                    },
                ]
            },
            options:{
                maintainAspectRatio:false,
                tooltips:{
                    mode:'index',
                    intersect:true
                },
                hover:{
                    mode:'index',
                    intersect:true
                },
                legend:{
                    display:false
                },
                scales:{
                    yAxes:[
                        {
                            gridLines:{
                                display:true,
                                lineWidth:'4px',
                                color:'rgba(0, 0, 0, .2)',
                                zeroLineColor:'transparent'
                            },
                            ticks:$.extend({
                                beginAtZero:true,
                                callback:function(value){
                                    if(value>=1000){
                                        value/=1000
                                        value+='k'
                                    }
                                    return value;
                                }
                            }, {fontColor:'#495057',fontStyle:'bold'} )
                        }
                    ],
                    xAxes:[{display:true,gridLines:{display:false},ticks:{fontColor:'#495057',fontStyle:'bold'}}]
                },
                animation: {
                    onComplete: function() {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                        ctx.font = Chart.helpers.fontString(18, 'bold', Chart.defaults.global.defaultFontFamily);
                        ctx.fillStyle = 'white';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var data = dataset.data[index] + ' Paket';
                                // Calculate center position of the bar
                                var barHeight = bar._model.base - bar._model.y;
                                var centerY = bar._model.y + (barHeight / 2);
                                ctx.fillText(data, bar._model.x, centerY);
                            });
                        });
                    }
                }
            }
        }
    )
    var avarageChart = new Chart(
        $('#avarage-chart'),
        {
            type:'line',
            data: {
                labels:['Januari','Februari','Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets:[
                    {
                        label: 'Nilai Kontrak',
                        backgroundColor:'#007bff',
                        borderColor:'#007bff',
                        data: {{$chart_avarage_spk}},
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Realisasi Anggaran',
                        backgroundColor:'#FF5733',
                        borderColor:'#FF5733',
                        data: {{$chart_avarage_realisasi}},
                        fill: false,
                        tension: 0.4
                    },
                ]
            },
            options:{
                maintainAspectRatio:false,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let label = data.datasets[tooltipItem.datasetIndex].label || '';
                            let value = tooltipItem.yLabel;
                            return label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
                        }
                    }
                },
                hover:{
                    mode:'index',
                    intersect:false
                },
                legend:{
                    display:false
                },
                scales:{
                    yAxes:[
                        {
                            gridLines:{
                                display:true,
                                lineWidth:'4px',
                                color:'rgba(0, 0, 0, .2)',
                                zeroLineColor:'transparent'
                            },
                            ticks:$.extend({
                                beginAtZero:true,
                                callback:function(value){
                                    if(value>=1000000000){
                                        value/=1000000000
                                        value+='M'
                                    }
                                    else if(value>=1000000){
                                        value/=1000000
                                        value+=' juta'
                                    }
                                    else if(value>=1000){
                                        value/=1000
                                        value+='k'
                                    }
                                    return value;
                                }
                            }, {fontColor:'#495057',fontStyle:'bold'} )
                        }
                    ],
                    xAxes:[{display:true,gridLines:{display:false},ticks:{fontColor:'#495057',fontStyle:'bold'}}]
                }
            }
        }
    )
    // Calculate totals for the 4 cards
    var paguData = {{$chart_avarage_pagu_anggaran ?? '[0,0,0,0,0,0,0,0,0,0,0,0]'}};
    var hpsData = {{$chart_avarage_hps ?? '[0,0,0,0,0,0,0,0,0,0,0,0]'}};
    var kontrakData = {{$chart_avarage_spk ?? '[0,0,0,0,0,0,0,0,0,0,0,0]'}};

    // Sum up all values
    var totalPagu = paguData.reduce((a, b) => a + b, 0);
    var totalHps = hpsData.reduce((a, b) => a + b, 0);
    var totalKontrak = kontrakData.reduce((a, b) => a + b, 0);
    var totalSisaPagu = totalPagu - totalKontrak;

    // Format as Indonesian Rupiah
    var formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    // Update the card values
    $('#total-pagu').text(formatter.format(totalPagu));
    $('#total-hps').text(formatter.format(totalHps));
    $('#total-kontrak').text(formatter.format(totalKontrak));
    $('#total-sisa-pagu').text(formatter.format(totalSisaPagu));

    // Update category totals
    @if(isset($category_totals))
        var categoryTotals = @json($category_totals);

        // Konstruksi
        $('#total-pagu-konstruksi').text(formatter.format((categoryTotals.konstruksi && categoryTotals.konstruksi.pagu) || 0));
        $('#total-hps-konstruksi').text(formatter.format((categoryTotals.konstruksi && categoryTotals.konstruksi.hps) || 0));
        $('#total-kontrak-konstruksi').text(formatter.format((categoryTotals.konstruksi && categoryTotals.konstruksi.kontrak) || 0));
        $('#total-sisa-pagu-konstruksi').text(formatter.format((categoryTotals.konstruksi && categoryTotals.konstruksi.sisa_pagu) || 0));

        // Perencanaan
        $('#total-pagu-perencanaan').text(formatter.format((categoryTotals.perencanaan && categoryTotals.perencanaan.pagu) || 0));
        $('#total-hps-perencanaan').text(formatter.format((categoryTotals.perencanaan && categoryTotals.perencanaan.hps) || 0));
        $('#total-kontrak-perencanaan').text(formatter.format((categoryTotals.perencanaan && categoryTotals.perencanaan.kontrak) || 0));
        $('#total-sisa-pagu-perencanaan').text(formatter.format((categoryTotals.perencanaan && categoryTotals.perencanaan.sisa_pagu) || 0));

        // Pengawasan
        $('#total-pagu-pengawasan').text(formatter.format((categoryTotals.pengawasan && categoryTotals.pengawasan.pagu) || 0));
        $('#total-hps-pengawasan').text(formatter.format((categoryTotals.pengawasan && categoryTotals.pengawasan.hps) || 0));
        $('#total-kontrak-pengawasan').text(formatter.format((categoryTotals.pengawasan && categoryTotals.pengawasan.kontrak) || 0));
        $('#total-sisa-pagu-pengawasan').text(formatter.format((categoryTotals.pengawasan && categoryTotals.pengawasan.sisa_pagu) || 0));
    @else
        // Default values if category_totals is not set
        $('#total-pagu-konstruksi').text(formatter.format(0));
        $('#total-hps-konstruksi').text(formatter.format(0));
        $('#total-kontrak-konstruksi').text(formatter.format(0));
        $('#total-sisa-pagu-konstruksi').text(formatter.format(0));

        $('#total-pagu-perencanaan').text(formatter.format(0));
        $('#total-hps-perencanaan').text(formatter.format(0));
        $('#total-kontrak-perencanaan').text(formatter.format(0));
        $('#total-sisa-pagu-perencanaan').text(formatter.format(0));

        $('#total-pagu-pengawasan').text(formatter.format(0));
        $('#total-hps-pengawasan').text(formatter.format(0));
        $('#total-kontrak-pengawasan').text(formatter.format(0));
        $('#total-sisa-pagu-pengawasan').text(formatter.format(0));
    @endif

    // RFK Dashboard Functions
    var rfkDashboardData = null;
    var rfkOpdSortColumn = null;
    var rfkOpdSortDirection = 'asc';
    var rfkProgramSortColumn = null;
    var rfkProgramSortDirection = 'asc';
    var rfkOpdFilters = {
        bulan_ini: 'all', // 'all', 'zero', 'active'
        keu: 'all',       // 'all', 'zero', 'low', 'high'
        fisik: 'all'      // 'all', 'zero', 'low', 'high'
    };

    function formatToCurrencyRFK(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    function populateRFKOpdTable(opdData) {
        var opdTbody = $('#rfk-opd-table tbody');
        opdTbody.empty();
        
        var searchQuery = $('#rfk-opd-search').val() || '';
        searchQuery = searchQuery.toLowerCase().trim();
        
        var filteredData = opdData;
        if (searchQuery !== '') {
            filteredData = opdData.filter(function(item) {
                return (item.opd_name || '').toLowerCase().indexOf(searchQuery) !== -1;
            });
        }

        // Apply "Bulan Ini" filter
        if (rfkOpdFilters.bulan_ini === 'zero') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.bulan_ini || 0) === 0;
            });
        } else if (rfkOpdFilters.bulan_ini === 'active') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.bulan_ini || 0) > 0;
            });
        }

        // Apply "Keu %" filter
        if (rfkOpdFilters.keu === 'zero') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.keuangan || 0) === 0;
            });
        } else if (rfkOpdFilters.keu === 'low') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.keuangan || 0) < 50;
            });
        } else if (rfkOpdFilters.keu === 'high') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.keuangan || 0) >= 50;
            });
        }

        // Apply "Fisik %" filter
        if (rfkOpdFilters.fisik === 'zero') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.fisik || 0) === 0;
            });
        } else if (rfkOpdFilters.fisik === 'low') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.fisik || 0) < 50;
            });
        } else if (rfkOpdFilters.fisik === 'high') {
            filteredData = filteredData.filter(function(item) {
                return parseFloat(item.fisik || 0) >= 50;
            });
        }
        
        if (filteredData && filteredData.length > 0) {
            // Sort according to rfkOpdSortColumn
            var col = rfkOpdSortColumn || 'custom';
            var dir = rfkOpdSortDirection || 'desc';
            var vA, vB;

            if (col !== 'custom') {
                filteredData.sort(function(a, b) {
                    if (col === 'name') {
                        vA = (a.opd_name || '').toLowerCase();
                        vB = (b.opd_name || '').toLowerCase();
                        return dir === 'asc' ? vA.localeCompare(vB) : vB.localeCompare(vA);
                    } else if (col === 'pagu') {
                        vA = parseFloat(a.pagu_anggaran || 0);
                        vB = parseFloat(b.pagu_anggaran || 0);
                    } else if (col === 'bulan_ini') {
                        vA = parseFloat(a.bulan_ini || 0);
                        vB = parseFloat(b.bulan_ini || 0);
                    } else if (col === 'realisasi') {
                        vA = parseFloat(a.sd_bulan_ini || 0);
                        vB = parseFloat(b.sd_bulan_ini || 0);
                    } else if (col === 'keu') {
                        vA = parseFloat(a.keuangan || 0);
                        vB = parseFloat(b.keuangan || 0);
                    } else if (col === 'fisik') {
                        vA = parseFloat(a.fisik || 0);
                        vB = parseFloat(b.fisik || 0);
                    } else if (col === 'rank') {
                        vA = parseFloat(a.keuangan || 0);
                        vB = parseFloat(b.keuangan || 0);
                        return dir === 'asc' ? vB - vA : vA - vB;
                    } else {
                        vA = parseFloat(a.keuangan || 0);
                        vB = parseFloat(b.keuangan || 0);
                    }

                    return dir === 'asc' ? vA - vB : vB - vA;
                });
            }

            var totalBulanIni = 0;
            var totalPagu = 0;
            var totalRealisasi = 0;
            var totalFisikRupiah = 0;
            
            filteredData.forEach(function(item, index) {
                var rank = index + 1;
                var rankClass = 'badge badge-secondary';
                if (rank === 1) rankClass = 'badge badge-warning'; // Gold
                else if (rank === 2) rankClass = 'badge badge-light'; // Silver
                else if (rank === 3) rankClass = 'badge-danger'; // Bronze (using dark badge/custom style)

                var rankStyle = rank <= 3 ? 'font-size: 0.85rem; padding: 4px 8px; font-weight: 800; border: 1px solid rgba(0,0,0,0.1);' : 'font-size: 0.8rem; padding: 3px 6px;';
                var rankHtml = '<span class="' + rankClass + '" style="' + rankStyle + '">' + rank + '</span>';

                var keuangan = (item.keuangan != null && !isNaN(item.keuangan)) ? Number(item.keuangan).toFixed(2) : '0.00';
                var fisik = (item.fisik != null && !isNaN(item.fisik)) ? Number(item.fisik).toFixed(2) : '0.00';
                var pagu = parseFloat(item.pagu_anggaran || 0);
                var realisasi = parseFloat(item.sd_bulan_ini || 0);
                
                totalBulanIni += parseFloat(item.bulan_ini || 0);
                totalPagu += pagu;
                totalRealisasi += realisasi;
                totalFisikRupiah += (parseFloat(fisik) / 100) * pagu;

                 var keuanganNum = parseFloat(keuangan);
                var fisikNum = parseFloat(fisik);
                var fisikSpanClass = fisikNum < keuanganNum ? 'text-danger font-weight-bold' : '';

                var row = '<tr>' +
                    '<td class="text-center">' + rankHtml + '</td>' +
                    '<td>' + (item.opd_name || '') + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(pagu) + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(item.bulan_ini || 0) + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(realisasi) + '</td>' +
                    '<td class="text-center">' + keuangan + '%</td>' +
                    '<td class="text-center"><span class="' + fisikSpanClass + '">' + fisik + '%</span></td>' +
                    '</tr>';
                opdTbody.append(row);
            });
            
            $('#rfk-opd-total-bulan-ini').text(formatToCurrencyRFK(totalBulanIni));
            $('#rfk-opd-total-pagu').text(formatToCurrencyRFK(totalPagu));
            $('#rfk-opd-total-realisasi').text(formatToCurrencyRFK(totalRealisasi));
            
            var totalKeu = totalPagu > 0 ? (totalRealisasi / totalPagu) * 100 : 0;
            var totalFisik = totalPagu > 0 ? (totalFisikRupiah / totalPagu) * 100 : 0;
            
            var totalFisikSpanClass = totalFisik < totalKeu ? 'text-danger font-weight-bold' : '';

            $('#rfk-opd-total-keu').text(totalKeu.toFixed(2) + '%');
            $('#rfk-opd-total-fisik').html('<span class="' + totalFisikSpanClass + '">' + totalFisik.toFixed(2) + '%</span>');
        } else {
            opdTbody.append('<tr><td colspan="7" class="text-center text-muted">Tidak ada data OPD yang cocok</td></tr>');
            $('#rfk-opd-total-bulan-ini').text('Rp0');
            $('#rfk-opd-total-pagu').text('Rp0');
            $('#rfk-opd-total-realisasi').text('Rp0');
            $('#rfk-opd-total-keu').text('0.00%');
            $('#rfk-opd-total-fisik').text('0.00%');
        }
    }

    function populateRFKProgramTable(programData, programSummary) {
        var programTbody = $('#rfk-program-table tbody');
        programTbody.empty();
        
        var searchQuery = $('#rfk-program-search').val() || '';
        searchQuery = searchQuery.toLowerCase().trim();
        
        var filteredData = programData;
        if (searchQuery !== '') {
            filteredData = programData.filter(function(item) {
                return (item.program_name || '').toLowerCase().indexOf(searchQuery) !== -1;
            });
        }
        
        if (filteredData && filteredData.length > 0) {
            var totalPagu = 0;
            var totalRealisasi = 0;
            var totalFisikRupiah = 0;
            
            filteredData.forEach(function(item) {
                var keuangan = (item.keuangan != null && !isNaN(item.keuangan)) ? Number(item.keuangan).toFixed(2) : '0.00';
                var fisik = (item.fisik != null && !isNaN(item.fisik)) ? Number(item.fisik).toFixed(2) : '0.00';
                var pagu = parseFloat(item.pagu_anggaran || 0);
                var realisasi = parseFloat(item.sd_bulan_ini || 0);
                
                totalPagu += pagu;
                totalRealisasi += realisasi;
                totalFisikRupiah += (parseFloat(fisik) / 100) * pagu;

                var row = '<tr>' +
                    '<td>' + (item.program_name || '') + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(pagu) + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(realisasi) + '</td>' +
                    '<td class="text-center">' + keuangan + '%</td>' +
                    '<td class="text-center">' + fisik + '%</td>' +
                    '</tr>';
                programTbody.append(row);
            });
            
            $('#rfk-program-total-pagu').text(formatToCurrencyRFK(totalPagu));
            $('#rfk-program-total-realisasi').text(formatToCurrencyRFK(totalRealisasi));
            
            var totalKeu = totalPagu > 0 ? (totalRealisasi / totalPagu) * 100 : 0;
            var totalFisik = totalPagu > 0 ? (totalFisikRupiah / totalPagu) * 100 : 0;
            
            $('#rfk-program-total-keu').text(totalKeu.toFixed(2) + '%');
            $('#rfk-program-total-fisik').text(totalFisik.toFixed(2) + '%');
        } else {
            programTbody.append('<tr><td colspan="5" class="text-center text-muted">Tidak ada data program yang cocok</td></tr>');
            $('#rfk-program-total-pagu').text('Rp0');
            $('#rfk-program-total-realisasi').text('Rp0');
            $('#rfk-program-total-keu').text('0.00%');
            $('#rfk-program-total-fisik').text('0.00%');
        }
    }

    function showBelumInputModal() {
        if (!rfkDashboardData || !rfkDashboardData.opd_data) {
            toastr.warning('Data belum dimuat atau tidak tersedia');
            return;
        }

        var month = $('#filter-month').val() || '{{date("m")}}';
        var year = $('#filter-year').val() || '{{date("Y")}}';
        var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var m = parseInt(month, 10);
        var periodText = (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + year;
        
        $('#modal-belum-input-periode').text(periodText);

        var tableBody = $('#table-belum-input-detail tbody');
        tableBody.empty();

        var belumInputOpds = rfkDashboardData.opd_data.filter(function(item) {
            return parseFloat(item.bulan_ini || 0) === 0;
        });

        if (belumInputOpds.length > 0) {
            belumInputOpds.forEach(function(item, index) {
                var pagu = parseFloat(item.pagu_anggaran || 0);
                var row = '<tr>' +
                    '<td class="text-center">' + (index + 1) + '</td>' +
                    '<td>' + (item.opd_name || '') + '</td>' +
                    '<td class="text-right">' + formatToCurrencyRFK(pagu) + '</td>' +
                    '</tr>';
                tableBody.append(row);
            });
        } else {
            tableBody.append('<tr><td colspan="3" class="text-center text-muted">Semua OPD sudah menginput realisasi bulan ini.</td></tr>');
        }

        $('#modal-belum-input-realisasi').modal('show');
    }
    window.showBelumInputModal = showBelumInputModal;

    function loadRFKDashboard() {
        // Reset sort column to default Keuangan DESC on new data load
        rfkOpdSortColumn = 'keu';
        rfkOpdSortDirection = 'desc';

        var opd = $('#filter-opd').val() || '';
        var month = $('#filter-month').val() || '{{date('m')}}';
        var year = $('#filter-year').val() || '{{date('Y')}}';
        // Combine year and month for API call (format: YYYY-MM)
        var fullMonth = year + '-' + month;

        $.ajax({
            url: '{{ url('/dashboard/rfk-data') }}',
            method: 'GET',
            data: {
                opd: opd,
                month: fullMonth
            },
            beforeSend: function() {
                $('.loader').show();
            },
            success: function(response) {
                $('.loader').hide();
                rfkDashboardData = response;

                // Update summary cards
                $('#rfk-total-pagu').text(formatToCurrencyRFK(response.summary.total_pagu || 0));
                $('#rfk-total-fisik').text((response.summary.total_fisik || 0).toFixed(2) + '%');
                $('#rfk-total-keu').text((response.summary.total_keu || 0).toFixed(2) + '%');
                $('#rfk-total-realisasi').text(formatToCurrencyRFK(response.summary.total_realisasi || 0));

                populateRFKOpdTable(response.opd_data || []);
                populateRFKProgramTable(response.program_data || [], response.program_summary || {});

                // === Dynamic OPD Ranking Update ===
                updateRankingUI(response.rank_info);

                // Update Excel download link
                updateRFKExcelLink();

                // Render charts
                if (response.chart_data) {
                    console.log('Rendering charts with data:', response.chart_data);
                    renderRFKCharts(response.chart_data, response.program_data || []);
                } else {
                    console.warn('No chart_data in response:', response);
                }
            },
            error: function(xhr, status, error) {
                $('.loader').hide();
                console.error('Error loading RFK data:', {xhr: xhr, status: status, error: error});
                toastr.error('Gagal memuat data RFK');
            }
        });
    }

    var rfkProgramChart = null;
    var rfkKeuComparisonChart = null;
    var rfkFisikComparisonChart = null;
    var rfkTop5KeuChart = null;
    var rfkBottom5KeuChart = null;
    var rfkTop5FisikChart = null;
    var rfkBottom5FisikChart = null;

    function renderRFKCharts(chartData, programData) {
        console.log('Chart data received:', chartData);
        programData = programData || [];

        // Destroy existing charts if they exist
        if (rfkProgramChart) {
            rfkProgramChart.destroy();
        }
        if (rfkKeuComparisonChart) {
            rfkKeuComparisonChart.destroy();
        }
        if (rfkFisikComparisonChart) {
            rfkFisikComparisonChart.destroy();
        }
        if (rfkTop5KeuChart) {
            rfkTop5KeuChart.destroy();
        }
        if (rfkBottom5KeuChart) {
            rfkBottom5KeuChart.destroy();
        }
        if (rfkTop5FisikChart) {
            rfkTop5FisikChart.destroy();
        }
        if (rfkBottom5FisikChart) {
            rfkBottom5FisikChart.destroy();
        }

        // Chart 1: Realisasi Keuangan dan Fisik Setiap Program (Bar Chart)
        // Use short labels for display, full labels in tooltip
        var programLabels = programData.map(function(p) {
            return p.program_name;
        });
        var programLabelsFull = programData.map(function(p) {
            return p.program_name;
        });
        var programKeuData = programData.map(function(p) { return p.keuangan || 0; });
        var programFisikData = programData.map(function(p) { return p.fisik || 0; });

        console.log('Program chart data:', {labels: programLabels, keu: programKeuData, fisik: programFisikData});

        var programChartCanvas = document.getElementById('rfk-program-chart');
        if (!programChartCanvas) {
            console.error('Chart canvas not found: rfk-program-chart');
            return;
        }

        rfkProgramChart = new Chart(programChartCanvas, {
            type: 'bar',
            data: {
                labels: programLabels,
                datasets: [
                    {
                        label: 'Keuangan %',
                        backgroundColor: '#FF5733',
                        borderColor: '#FF5733',
                        data: programKeuData,
                    },
                    {
                        label: 'Fisik %',
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        data: programFisikData,
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        bottom: 30,
                        left: 10,
                        right: 10
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItems, data) {
                            // Show full program name in tooltip title
                            var index = tooltipItems[0].index;
                            return programLabelsFull[index] || programLabels[index] || '';
                        },
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.yLabel.toFixed(2) + '%';
                        }
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            max: 100,
                            callback: function(value) {
                                return value + '%';
                            }
                        }, {fontColor: '#495057', fontStyle: 'bold'})
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#495057',
                            fontStyle: 'bold',
                            fontSize: 10,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: false,
                            padding: 10,
                            callback: function(value, index, values) {
                                // Truncate label if too long
                                var label = programLabels[index] || '';
                                return label || '';
                            }
                        }
                    }]
                }
            }
        });

        // Chart 2: Realisasi %Keu Seluruh OPD vs %Keu per OPD (Line Chart)
        var monthLabels = chartData.comparison_chart ? chartData.comparison_chart.month_labels : [];
        var keuAllOpd = chartData.comparison_chart ? chartData.comparison_chart.keu_all_opd : [];
        var keuSelectedOpd = chartData.comparison_chart ? chartData.comparison_chart.keu_selected_opd : [];

        console.log('Comparison chart data:', {labels: monthLabels, keuAll: keuAllOpd, keuSelected: keuSelectedOpd});

        var keuChartCanvas = document.getElementById('rfk-keu-comparison-chart');
        if (!keuChartCanvas) {
            console.error('Chart canvas not found: rfk-keu-comparison-chart');
            return;
        }

        rfkKeuComparisonChart = new Chart(keuChartCanvas, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Rata-Rata keu Seluruh OPD',
                        backgroundColor: '#FF5733',
                        borderColor: '#FF5733',
                        data: keuAllOpd,
                        fill: false,
                        lineTension: 0.4
                    },
                    {
                        label: 'Rata-Rata keu OPD Terpilih',
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        data: keuSelectedOpd,
                        fill: false,
                        lineTension: 0.4
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.yLabel.toFixed(2) + '%';
                        }
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            max: 100,
                            callback: function(value) {
                                return value + '%';
                            }
                        }, {fontColor: '#495057', fontStyle: 'bold'})
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#495057',
                            fontStyle: 'bold'
                        }
                    }]
                }
            }
        });

        // Chart 3: Realisasi %Fisik Seluruh OPD vs %Fisik per OPD (Line Chart)
        var fisikAllOpd = chartData.comparison_chart ? chartData.comparison_chart.fisik_all_opd : [];
        var fisikSelectedOpd = chartData.comparison_chart ? chartData.comparison_chart.fisik_selected_opd : [];

        console.log('Fisik comparison chart data:', {fisikAll: fisikAllOpd, fisikSelected: fisikSelectedOpd});

        var fisikChartCanvas = document.getElementById('rfk-fisik-comparison-chart');
        if (!fisikChartCanvas) {
            console.error('Chart canvas not found: rfk-fisik-comparison-chart');
            return;
        }

        rfkFisikComparisonChart = new Chart(fisikChartCanvas, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Rata-Rata fisik Seluruh OPD',
                        backgroundColor: '#FF5733',
                        borderColor: '#FF5733',
                        data: fisikAllOpd,
                        fill: false,
                        lineTension: 0.4
                    },
                    {
                        label: 'Rata-Rata fisik OPD Terpilih',
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        data: fisikSelectedOpd,
                        fill: false,
                        lineTension: 0.4
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.yLabel.toFixed(2) + '%';
                        }
                    }
                },
                hover: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            max: 100,
                            callback: function(value) {
                                return value + '%';
                            }
                        }, {fontColor: '#495057', fontStyle: 'bold'})
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: '#495057',
                            fontStyle: 'bold'
                        }
                    }]
                }
            }
        });

        // Chart 4: Top 5 OPD by Keuangan
        var top5KeuData = chartData.top5_keu || [];
        var top5KeuLabels = top5KeuData.map(function(item) { return item.opd_name || ''; });
        var top5KeuValues = top5KeuData.map(function(item) { return item.keuangan || 0; });

        var top5KeuCanvas = document.getElementById('rfk-top5-keu-chart');
        if (top5KeuCanvas) {
            rfkTop5KeuChart = new Chart(top5KeuCanvas, {
                type: 'bar',
                data: {
                    labels: top5KeuLabels,
                    datasets: [{
                        label: '% Keu',
                        backgroundColor: '#1e3a8a',
                        borderColor: '#1e3a8a',
                        data: top5KeuValues
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return tooltipItem.yLabel.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }]
                    },
                    animation: {
                        onComplete: function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                            ctx.fillStyle = 'white';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    var barHeight = bar._model.base - bar._model.y;
                                    var centerY = bar._model.y + (barHeight / 2);
                                    ctx.fillText(data.toFixed(2) + '%', bar._model.x, centerY);
                                });
                            });
                        }
                    }
                }
            });
        }

        // Chart 5: Bottom 5 OPD by Keuangan
        var bottom5KeuData = chartData.bottom5_keu || [];
        var bottom5KeuLabels = bottom5KeuData.map(function(item) { return item.opd_name || ''; });
        var bottom5KeuValues = bottom5KeuData.map(function(item) { return item.keuangan || 0; });

        var bottom5KeuCanvas = document.getElementById('rfk-bottom5-keu-chart');
        if (bottom5KeuCanvas) {
            rfkBottom5KeuChart = new Chart(bottom5KeuCanvas, {
                type: 'bar',
                data: {
                    labels: bottom5KeuLabels,
                    datasets: [{
                        label: '% Keu',
                        backgroundColor: '#1e3a8a',
                        borderColor: '#1e3a8a',
                        data: bottom5KeuValues
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return tooltipItem.yLabel.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }]
                    },
                    animation: {
                        onComplete: function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                            ctx.fillStyle = 'white';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    var barHeight = bar._model.base - bar._model.y;
                                    var centerY = bar._model.y + (barHeight / 2);
                                    ctx.fillText(data.toFixed(2) + '%', bar._model.x, centerY);
                                });
                            });
                        }
                    }
                }
            });
        }

        // Chart 6: Top 5 OPD by Fisik
        var top5FisikData = chartData.top5_fisik || [];
        var top5FisikLabels = top5FisikData.map(function(item) { return item.opd_name || ''; });
        var top5FisikValues = top5FisikData.map(function(item) { return item.fisik || 0; });

        var top5FisikCanvas = document.getElementById('rfk-top5-fisik-chart');
        if (top5FisikCanvas) {
            rfkTop5FisikChart = new Chart(top5FisikCanvas, {
                type: 'bar',
                data: {
                    labels: top5FisikLabels,
                    datasets: [{
                        label: '% Fisik',
                        backgroundColor: '#1e3a8a',
                        borderColor: '#1e3a8a',
                        data: top5FisikValues
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return tooltipItem.yLabel.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }]
                    },
                    animation: {
                        onComplete: function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                            ctx.fillStyle = 'white';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    var barHeight = bar._model.base - bar._model.y;
                                    var centerY = bar._model.y + (barHeight / 2);
                                    ctx.fillText(data.toFixed(2) + '%', bar._model.x, centerY);
                                });
                            });
                        }
                    }
                }
            });
        }

        // Chart 7: Bottom 5 OPD by Fisik
        var bottom5FisikData = chartData.bottom5_fisik || [];
        var bottom5FisikLabels = bottom5FisikData.map(function(item) { return item.opd_name || ''; });
        var bottom5FisikValues = bottom5FisikData.map(function(item) { return item.fisik || 0; });

        var bottom5FisikCanvas = document.getElementById('rfk-bottom5-fisik-chart');
        if (bottom5FisikCanvas) {
            rfkBottom5FisikChart = new Chart(bottom5FisikCanvas, {
                type: 'bar',
                data: {
                    labels: bottom5FisikLabels,
                    datasets: [{
                        label: '% Fisik',
                        backgroundColor: '#1e3a8a',
                        borderColor: '#1e3a8a',
                        data: bottom5FisikValues
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return tooltipItem.yLabel.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }]
                    },
                    animation: {
                        onComplete: function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;
                            ctx.font = Chart.helpers.fontString(12, 'bold', Chart.defaults.global.defaultFontFamily);
                            ctx.fillStyle = 'white';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    var barHeight = bar._model.base - bar._model.y;
                                    var centerY = bar._model.y + (barHeight / 2);
                                    ctx.fillText(data.toFixed(2) + '%', bar._model.x, centerY);
                                });
                            });
                        }
                    }
                }
            });
        }
    }

    // === Ranking UI Updater (shared by dashboard and rfk tab) ===
    function formatMonthYear(month, year) {
        var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var m = parseInt(month, 10);
        return (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + year;
    }

    function updateRankingUI(rankInfo) {
        // Elements: top banner, rfk-tab banner, navbar
        var topBanner = $('#dashboard-opd-rank-banner');
        var rfkBanner = $('#rfk-tab-rank-banner');
        var navbarRank = $('#navbar-opd-rank');
        var navbarDropdown = $('#navbar-dropdown-opd-rank');

        if (rankInfo && rankInfo.rank) {
            var rank = rankInfo.rank;
            var total = rankInfo.total;
            var opdName = rankInfo.opd_name || '';
            var periodText = formatMonthYear(rankInfo.month, rankInfo.year);

            // Top banner
            $('#rank-badge-large').text('#' + rank);
            $('#rank-opd-name').text(opdName);
            $('#rank-badge').text('#' + rank);
            $('#rank-total-opd').text(total);
            $('#rank-period').text('Periode: ' + periodText);
            $('#rank-total-large').text(total + ' OPD');
            topBanner.slideDown(300);

            // RFK tab banner
            $('#rfk-rank-badge-large').text('#' + rank);
            $('#rfk-rank-opd-name').text(opdName);
            $('#rfk-rank-badge').text('#' + rank);
            $('#rfk-rank-total-opd').text(total);
            $('#rfk-rank-period').text('Periode: ' + periodText);
            $('#rfk-rank-total-large').text(total + ' OPD');
            rfkBanner.slideDown(300);

            // Navbar compact rank
            navbarRank.find('.rank-val').text(rank);
            navbarRank.css('display', 'block');

            // Navbar dropdown rank
            navbarDropdown.find('.rank-val').text(rank);
            navbarDropdown.find('.total-val').text(total);
            navbarDropdown.css('display', 'block');
        } else {
            topBanner.slideUp(200);
            rfkBanner.slideUp(200);
            navbarRank.css('display', 'none');
            navbarDropdown.css('display', 'none');
        }
    }

    // Load RFK data when tab is clicked
    $('#rfk-tab').on('shown.bs.tab', function() {
        loadRFKDashboard();
    });

    // Load RFK data on page load if RFK tab is active by default
    $(document).ready(function() {
        if ($('#rfk-tab').hasClass('active')) {
            loadRFKDashboard();
        }

        // Search event bindings
        $('#rfk-opd-search').on('keyup input', function() {
            if (rfkDashboardData) {
                populateRFKOpdTable(rfkDashboardData.opd_data || []);
            }
        });

        $('#rfk-program-search').on('keyup input', function() {
            if (rfkDashboardData) {
                populateRFKProgramTable(rfkDashboardData.program_data || [], rfkDashboardData.program_summary || {});
            }
        });

        // Column Filter event bindings
        $('.rfk-opd-filter-val').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $this.data('filter-type');
            var val = $this.data('filter-val');

            // Update active state in dropdown UI
            $this.siblings('.rfk-opd-filter-val').removeClass('active');
            $this.addClass('active');

            // Save filter state
            rfkOpdFilters[type] = val;

            // Highlight filter icon if filter is active (not 'all')
            var icon = $('#rfk-opd-filter-btn-' + type);
            if (val !== 'all') {
                icon.removeClass('text-muted').addClass('text-warning');
            } else {
                icon.removeClass('text-warning').addClass('text-muted');
            }

            // Re-populate table
            if (rfkDashboardData) {
                populateRFKOpdTable(rfkDashboardData.opd_data || []);
            }
        });

        // Copy button inside Belum Input modal
        $('#btn-copy-belum-input').on('click', function() {
            if (!rfkDashboardData || !rfkDashboardData.opd_data) return;

            var month = $('#filter-month').val() || '{{date("m")}}';
            var year = $('#filter-year').val() || '{{date("Y")}}';
            var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            var m = parseInt(month, 10);
            var periodText = (m >= 1 && m <= 12 ? monthNames[m - 1] : '') + ' ' + year;

            var belumInputOpds = rfkDashboardData.opd_data.filter(function(item) {
                return parseFloat(item.bulan_ini || 0) === 0;
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

        // Inisialisasi tautan unduhan Excel saat halaman dimuat
        updateRFKExcelLink();

        // Handler tombol ekspor PDF
        $('#rfk-export-pdf').on('click', function(e) {
            e.preventDefault();
            exportRFKPDF();
        });
    });

    // Filter button for dashboard
    $('#dashboard-filter-btn').click(function(e) {
        e.preventDefault();
        // If RFK tab is active, just reload RFK data via AJAX
        if ($('#rfk-tab').hasClass('active')) {
            loadRFKDashboard();
        } else {
            // Otherwise, submit form to reload page with filters for construction tab
            $('#dashboard-filter-form').submit();
        }
    });

    // Reload RFK data ketika filter berubah
    $('#filter-opd, #filter-month, #filter-year').on('change', function() {
        if ($('#rfk-tab').hasClass('active')) {
            loadRFKDashboard();
        } else {
            updateRFKExcelLink();
        }
    });

    function sortRFKTable(column) {
        if (!rfkDashboardData || !rfkDashboardData.opd_data) return;

        if (rfkOpdSortColumn === column) {
            rfkOpdSortDirection = rfkOpdSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            rfkOpdSortColumn = column;
            rfkOpdSortDirection = (column === 'name') ? 'asc' : 'desc'; // Default text to asc, numbers to desc
        }

        populateRFKOpdTable(rfkDashboardData.opd_data);
    }

    function sortRFKProgramTable(column) {
        // Simple client-side sorting for Program table
        var table = $('#rfk-program-table');
        var tbody = table.find('tbody');
        var rows = tbody.find('tr').toArray();

        rows.sort(function(a, b) {
            var aVal, bVal;
            if (column === 'keu') {
                aVal = parseFloat($(a).find('td').eq(3).text().replace('%', ''));
                bVal = parseFloat($(b).find('td').eq(3).text().replace('%', ''));
            } else if (column === 'fisik') {
                aVal = parseFloat($(a).find('td').eq(4).text().replace('%', ''));
                bVal = parseFloat($(b).find('td').eq(4).text().replace('%', ''));
            }

            if (rfkProgramSortColumn === column && rfkProgramSortDirection === 'asc') {
                return bVal - aVal;
            } else {
                return aVal - bVal;
            }
        });

        if (rfkProgramSortColumn === column) {
            rfkProgramSortDirection = rfkProgramSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            rfkProgramSortColumn = column;
            rfkProgramSortDirection = 'asc';
        }

        tbody.empty().append(rows);
    }

    // ===== EXCEL EXPORT LINK =====
    function updateRFKExcelLink() {
        var year = $('#filter-year').val() || '{{ date("Y") }}';
        var month = $('#filter-month').val() || '{{ date("m") }}';
        var opd = $('#filter-opd').val() || '';
        var url = '{{ url("/dashboard/export/excel") }}?month=' + year + '-' + month + '&opd=' + opd;
        $('#rfk-export-excel').attr('href', url);
    }

    // ===== PDF EXPORT (Client-side with html2canvas + jsPDF) =====
    function exportRFKPDF() {
        var btn = $('#rfk-export-pdf');
        var origText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating...').prop('disabled', true);

        var element = document.getElementById('dashboard-app-content');

        html2canvas(element, {
            scale: 1.5,
            useCORS: true,
            logging: false,
            backgroundColor: '#f4f6f9', // Default AdminLTE body bg
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

            var position = 0;
            var remainingHeight = scaledHeight;

            while (remainingHeight > 0) {
                if (position > 0) pdf.addPage();
                pdf.addImage(imgData, 'JPEG', margin, margin - (position * (pageHeight - margin * 2)),
                    contentWidth, scaledHeight);
                remainingHeight -= (pageHeight - margin * 2);
                position++;
            }

            var year = $('#filter-year').val() || '{{ date("Y") }}';
            var month = $('#filter-month').val() || '{{ date("m") }}';
            pdf.save('Laporan_Dashboard_' + year + '_' + month + '.pdf');

            btn.html(origText).prop('disabled', false);
            toastr.success('PDF berhasil diunduh');
        }).catch(function(err) {
            console.error('PDF export error:', err);
            btn.html(origText).prop('disabled', false);
            toastr.error('Gagal menghasilkan PDF');
        });
    }
</script>
@endsection