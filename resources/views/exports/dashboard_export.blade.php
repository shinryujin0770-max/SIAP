@php
use Carbon\Carbon;
$bulanName = Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
@endphp
<table>
    <thead>
        <tr>
            <th colspan="7">Ringkasan Dashboard Utama - Periode: {{ $bulanName }} {{ $year }}</th>
        </tr>
        <tr>
            <th colspan="7">KABUPATEN TULUNGAGUNG TAHUN ANGGARAN {{ $year }}</th>
        </tr>
        <tr>
            <th colspan="2">Total Pagu</th>
            <th>Total Realisasi Bulan Ini</th>
            <th>Total Realisasi s.d. Bulan Ini</th>
            <th>% Keuangan</th>
            <th>% Fisik</th>
            <th>Sisa Anggaran</th>
        </tr>
        <tr>
            <td colspan="2">{{ number_format($data['summary']['total_pagu'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_realisasi'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_keu'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_fisik'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_pagu'] - $data['summary']['total_realisasi'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama OPD</th>
            <th>Pagu Anggaran</th>
            <th>Realisasi Bulan Ini</th>
            <th>Realisasi s.d. Bulan Ini</th>
            <th>% Keuangan</th>
            <th>% Fisik</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['opd_data'] as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['opd_name'] }}</td>
            <td>{{ number_format($item['bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['sd_bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['keuangan'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['fisik'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
