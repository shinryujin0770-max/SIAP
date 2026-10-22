<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Summary Sheet - SIAP Tulungagung</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 10mm 12mm;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #0f172a;
            background-color: #f1f5f9;
            font-size: 11px;
            line-height: 1.3;
        }
        .sheet-container {
            max-width: 1150px;
            margin: 20px auto;
            background: #ffffff;
            padding: 24px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .header-title {
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kpi-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #f8fafc;
        }
        .table-custom {
            width: 100%;
            margin-bottom: 0;
            font-size: 10px;
        }
        .table-custom th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            padding: 5px 8px;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-custom td {
            padding: 4px 8px;
            border-top: 1px solid #e2e8f0;
        }
        .badge-status {
            font-size: 9px;
            padding: 3px 6px;
            font-weight: 700;
            border-radius: 4px;
        }
        @media print {
            body {
                background: #ffffff !important;
                font-size: 9.5px !important;
            }
            .sheet-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            .no-print {
                display: none !important;
            }
            .kpi-box {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .table-custom th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-2 bg-dark text-white sticky-top shadow">
    <button onclick="window.print()" class="btn btn-sm btn-danger font-weight-bold px-4 mr-2">
        <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF Sekarang
    </button>
    <button onclick="window.close()" class="btn btn-sm btn-outline-light px-3">
        Tutup
    </button>
</div>

<div class="sheet-container">
    <!-- OFFICIAL HEADER -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-landmark text-primary mr-3" style="font-size: 2.2rem;"></i>
            <div>
                <h6 class="mb-0 text-muted font-weight-bold" style="font-size: 11px;">PEMERINTAH KABUPATEN TULUNGAGUNG</h6>
                <h5 class="header-title mb-0" style="font-size: 15px;">EXECUTIVE SUMMARY REPORT &bull; RAKOR PEMBANGUNAN DAERAH</h5>
                <small class="text-muted">Sistem Informasi Administrasi Pembangunan (SIAP) - Evaluasi Kinerja Realisasi Fisik & Keuangan</small>
            </div>
        </div>
        <div class="text-right">
            <div class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 11px;">
                {{ $period['month_name'] }} {{ $period['year'] }} &bull; {{ $period['triwulan'] }}
            </div>
            <div class="small text-muted mt-1">Dicetak pada: {{ date('d/m/Y H:i') }} WIB</div>
        </div>
    </div>

    <!-- MACRO KPIS -->
    <div class="row mb-3">
        <div class="col-3">
            <div class="kpi-box border-left border-primary" style="border-left-width: 4px !important;">
                <div class="text-muted text-uppercase" style="font-size: 9px; font-weight: 700;">TOTAL PAGU ANGGARAN</div>
                <div class="font-weight-bold text-dark" style="font-size: 13px;">Rp {{ number_format($summary['total_pagu'], 0, ',', '.') }}</div>
                <small class="text-muted">{{ $summary['total_projects'] }} Paket Pekerjaan Terdaftar</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box border-left border-success" style="border-left-width: 4px !important;">
                <div class="text-muted text-uppercase" style="font-size: 9px; font-weight: 700;">REALISASI KEUANGAN</div>
                <div class="font-weight-bold text-success" style="font-size: 13px;">
                    {{ number_format($summary['pct_keuangan'], 2) }}% 
                    <small class="text-dark font-weight-normal">(Rp {{ number_format($summary['total_realisasi'], 0, ',', '.') }})</small>
                </div>
                <small class="text-muted">Target {{ $period['triwulan'] }}: {{ $period['target_triwulan'] }}% &bull; Deviasi: {{ ($summary['deviasi'] > 0 ? '+' : '') . number_format($summary['deviasi'], 2) }}%</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box border-left border-info" style="border-left-width: 4px !important;">
                <div class="text-muted text-uppercase" style="font-size: 9px; font-weight: 700;">PROGRES FISIK RATA-RATA</div>
                <div class="font-weight-bold text-info" style="font-size: 13px;">{{ number_format($summary['pct_fisik'], 2) }}%</div>
                <small class="text-muted">Sisa Anggaran: Rp {{ number_format($summary['sisa_anggaran'], 0, ',', '.') }}</small>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-box border-left border-danger" style="border-left-width: 4px !important;">
                <div class="text-muted text-uppercase" style="font-size: 9px; font-weight: 700;">PROYEKSI SiLPA AKHIR TAHUN</div>
                <div class="font-weight-bold text-danger" style="font-size: 13px;">Rp {{ number_format($forecasting['projected_silpa'], 0, ',', '.') }}</div>
                <small class="text-muted">Proyeksi Serapan: {{ number_format($forecasting['projected_pct_keuangan'], 2) }}% (Desember)</small>
            </div>
        </div>
    </div>

    <!-- MAIN TABLES SPLIT GRID -->
    <div class="row mb-3">
        <!-- TOP 5 & BOTTOM 5 OPD -->
        <div class="col-6">
            <!-- TOP 5 -->
            <div class="border rounded p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-success"><i class="fas fa-trophy mr-1"></i>TOP 5 OPD (Realisasi Keuangan Tertinggi)</strong>
                </div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 25px;">#</th>
                            <th>Nama OPD</th>
                            <th class="text-right">Pagu (Rp)</th>
                            <th class="text-right">Realisasi (%)</th>
                            <th class="text-right">Fisik (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_5_opd as $idx => $opd)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="font-weight-bold text-truncate" style="max-width: 180px;">{{ $opd['opd_name'] }}</td>
                            <td class="text-right">{{ number_format($opd['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-success">{{ number_format($opd['keuangan'], 2) }}%</td>
                            <td class="text-right">{{ number_format($opd['fisik'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- BOTTOM 5 -->
            <div class="border rounded p-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>BOTTOM 5 OPD (Realisasi Keuangan Terendah)</strong>
                </div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 25px;">#</th>
                            <th>Nama OPD</th>
                            <th class="text-right">Pagu (Rp)</th>
                            <th class="text-right">Realisasi (%)</th>
                            <th class="text-right">Deviasi (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bottom_5_opd as $idx => $opd)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="font-weight-bold text-truncate text-danger" style="max-width: 180px;">{{ $opd['opd_name'] }}</td>
                            <td class="text-right">{{ number_format($opd['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold text-danger">{{ number_format($opd['keuangan'], 2) }}%</td>
                            <td class="text-right text-danger">{{ ($opd['deviasi'] > 0 ? '+' : '') . number_format($opd['deviasi'], 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ZONA MERAH & CRITICAL PACKETS -->
        <div class="col-6">
            <div class="border rounded p-2 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-danger"><i class="fas fa-fire mr-1"></i>OPD ZONA MERAH (Serapan di Bawah Target Triwulan)</strong>
                    <span class="badge badge-danger">{{ count($zona_merah_opd) }} OPD</span>
                </div>
                <table class="table-custom mb-2">
                    <thead>
                        <tr>
                            <th>Nama OPD</th>
                            <th class="text-right">Pagu</th>
                            <th class="text-right">Realisasi</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($zona_merah_opd, 0, 5) as $zm)
                        <tr>
                            <td class="font-weight-bold text-truncate" style="max-width: 180px;">{{ $zm['opd_name'] }}</td>
                            <td class="text-right">{{ number_format($zm['pagu_anggaran'], 0, ',', '.') }}</td>
                            <td class="text-right text-danger font-weight-bold">{{ number_format($zm['keuangan'], 2) }}%</td>
                            <td class="text-center"><span class="badge-status badge-danger">Kritis</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">Tidak ada OPD di zona merah.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <strong class="text-dark d-block mb-1"><i class="fas fa-map-marker-alt text-primary mr-1"></i>Sebaran Titik Proyek Geospasial:</strong>
                <div class="d-flex justify-content-between bg-light p-2 rounded mb-2 border" style="font-size: 10px;">
                    <div><i class="fas fa-circle text-success mr-1"></i>Sesuai Target: <strong>{{ $summary['count_hijau'] }} Paket</strong></div>
                    <div><i class="fas fa-circle text-warning mr-1"></i>Deviasi Minor: <strong>{{ $summary['count_kuning'] }} Paket</strong></div>
                    <div><i class="fas fa-circle text-danger mr-1"></i>Deviasi Kritis: <strong>{{ $summary['count_merah'] }} Paket</strong></div>
                </div>

                <strong class="text-dark d-block mb-1"><i class="fas fa-lightbulb text-warning mr-1"></i>Rekomendasi Tindakan Strategis:</strong>
                <div class="bg-light p-2 rounded border" style="font-size: 9.5px;">
                    @foreach($recommendations as $rec)
                    <div class="d-flex align-items-start mb-1">
                        <i class="fas fa-check-circle text-primary mr-2 mt-1"></i>
                        <span>{{ $rec }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- SIGNATURE BLOCK -->
    <div class="d-flex justify-content-between align-items-end pt-2 border-top">
        <div class="text-muted" style="font-size: 9px;">
            Dokumen ini dicetak secara otomatis melalui Modul Executive Sheet SIAP Tulungagung.<br/>
            Keabsahan data terintegrasi langsung dengan database RFK & SPK Kabupaten Tulungagung.
        </div>
        <div class="text-center" style="min-width: 220px;">
            <div style="font-size: 10px;">Tulungagung, {{ date('d F Y') }}</div>
            <div class="font-weight-bold" style="font-size: 10px; margin-bottom: 45px;">Sekretaris Daerah / Kepala Bappeda</div>
            <div class="font-weight-bold text-decoration-underline" style="font-size: 10px;">.........................................................</div>
            <div class="text-muted" style="font-size: 9px;">NIP. .................................................</div>
        </div>
    </div>
</div>

</body>
</html>
