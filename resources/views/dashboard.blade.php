@extends('adminlte::page')

@section('title', 'Sistem Informasi Administrasi Pembangunan')

@section('content_header')
<h1 class="m-0 text-dark">Sistem Informasi Administrasi Pembangunan</h1>
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
<div class="row justify-content-center">
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

                        @if (auth()->user()->hasRole('superadmin'))
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
                    <div class="card-header">
                        <h5 class="card-title">Nama OPD</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="rfk-opd-table" class="table table-bordered table-hover">
                                <thead class="text-center font-weight-bold">
                                    <tr>
                                        <th>Nama OPD</th>
                                        <th>Pagu Anggaran</th>
                                        <th>Bulan Ini</th>
                                        <th>Realisasi sd Bulan ini</th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('keu')">
                                            Keu % <i class="fa fa-sort"></i>
                                        </th>
                                        <th style="cursor: pointer;" onclick="sortRFKTable('fisik')">
                                            Fisik % <i class="fa fa-sort"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="font-weight-bold">
                                    <tr>
                                        <td>Total</td>
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
                    <div class="card-header">
                        <h5 class="card-title">Program OPD yang Dijalankan</h5>
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
</div>
@endsection

@section('js')
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
    var rfkOpdSortColumn = null;
    var rfkOpdSortDirection = 'asc';
    var rfkProgramSortColumn = null;
    var rfkProgramSortDirection = 'asc';

    function formatToCurrencyRFK(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    function loadRFKDashboard() {
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

                // Update summary cards
                $('#rfk-total-pagu').text(formatToCurrencyRFK(response.summary.total_pagu || 0));
                $('#rfk-total-fisik').text((response.summary.total_fisik || 0).toFixed(2) + '%');
                $('#rfk-total-keu').text((response.summary.total_keu || 0).toFixed(2) + '%');
                $('#rfk-total-realisasi').text(formatToCurrencyRFK(response.summary.total_realisasi || 0));

                // Update OPD table
                var opdTbody = $('#rfk-opd-table tbody');
                opdTbody.empty();
                if (response.opd_data && response.opd_data.length > 0) {
                    var totalBulanIni = 0;
                    response.opd_data.forEach(function(item) {
                        var keuangan = (item.keuangan != null && !isNaN(item.keuangan)) ? Number(item.keuangan).toFixed(2) : '0.00';
                        var fisik = (item.fisik != null && !isNaN(item.fisik)) ? Number(item.fisik).toFixed(2) : '0.00';
                        totalBulanIni += parseFloat(item.bulan_ini || 0);

                        var row = '<tr>' +
                            '<td>' + (item.opd_name || '') + '</td>' +
                            '<td class="text-right">' + formatToCurrencyRFK(item.pagu_anggaran || 0) + '</td>' +
                            '<td class="text-right">' + formatToCurrencyRFK(item.bulan_ini || 0) + '</td>' +
                            '<td class="text-right">' + formatToCurrencyRFK(item.sd_bulan_ini || 0) + '</td>' +
                            '<td class="text-center">' + keuangan + '%</td>' +
                            '<td class="text-center">' + fisik + '%</td>' +
                            '</tr>';
                        opdTbody.append(row);
                    });
                    $('#rfk-opd-total-bulan-ini').text(formatToCurrencyRFK(totalBulanIni));
                }

                // Update OPD totals
                $('#rfk-opd-total-pagu').text(formatToCurrencyRFK(response.summary.total_pagu || 0));
                $('#rfk-opd-total-realisasi').text(formatToCurrencyRFK(response.summary.total_realisasi || 0));
                $('#rfk-opd-total-keu').text((response.summary.total_keu || 0).toFixed(2) + '%');
                $('#rfk-opd-total-fisik').text((response.summary.total_fisik || 0).toFixed(2) + '%');

                // Update Program table
                var programTbody = $('#rfk-program-table tbody');
                programTbody.empty();
                if (response.program_data && response.program_data.length > 0) {
                    response.program_data.forEach(function(item) {
                        var keuangan = (item.keuangan != null && !isNaN(item.keuangan)) ? Number(item.keuangan).toFixed(2) : '0.00';
                        var fisik = (item.fisik != null && !isNaN(item.fisik)) ? Number(item.fisik).toFixed(2) : '0.00';

                        var row = '<tr>' +
                            '<td>' + (item.program_name || '') + '</td>' +
                            '<td class="text-right">' + formatToCurrencyRFK(item.pagu_anggaran || 0) + '</td>' +
                            '<td class="text-right">' + formatToCurrencyRFK(item.sd_bulan_ini || 0) + '</td>' +
                            '<td class="text-center">' + keuangan + '%</td>' +
                            '<td class="text-center">' + fisik + '%</td>' +
                            '</tr>';
                        programTbody.append(row);
                    });
                }

                // Update Program totals
                $('#rfk-program-total-pagu').text(formatToCurrencyRFK(response.program_summary.total_pagu || 0));
                $('#rfk-program-total-realisasi').text(formatToCurrencyRFK(response.program_summary.total_realisasi || 0));
                $('#rfk-program-total-keu').text((response.program_summary.total_keu || 0).toFixed(2) + '%');
                $('#rfk-program-total-fisik').text((response.program_summary.total_fisik || 0).toFixed(2) + '%');

                // === Dynamic OPD Ranking Update ===
                updateRankingUI(response.rank_info);

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

    // Reload RFK data when filter changes (for RFK tab only)
    $('#filter-opd, #filter-month, #filter-year').on('change', function() {
        if ($('#rfk-tab').hasClass('active')) {
            loadRFKDashboard();
        }
    });

    function sortRFKTable(column) {
        // Simple client-side sorting for OPD table
        var table = $('#rfk-opd-table');
        var tbody = table.find('tbody');
        var rows = tbody.find('tr').toArray();

        rows.sort(function(a, b) {
            var aVal, bVal;
            if (column === 'keu') {
                aVal = parseFloat($(a).find('td').eq(4).text().replace('%', ''));
                bVal = parseFloat($(b).find('td').eq(4).text().replace('%', ''));
            } else if (column === 'fisik') {
                aVal = parseFloat($(a).find('td').eq(5).text().replace('%', ''));
                bVal = parseFloat($(b).find('td').eq(5).text().replace('%', ''));
            }

            if (rfkOpdSortColumn === column && rfkOpdSortDirection === 'asc') {
                return bVal - aVal;
            } else {
                return aVal - bVal;
            }
        });

        if (rfkOpdSortColumn === column) {
            rfkOpdSortDirection = rfkOpdSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            rfkOpdSortColumn = column;
            rfkOpdSortDirection = 'asc';
        }

        tbody.empty().append(rows);
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
</script>
@endsection