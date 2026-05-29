@php
use Carbon\Carbon;
$bulanName = Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
@endphp
<table>
    <thead>
        <tr>
            <th colspan="7">Ringkasan Dashboard Executive - Periode: {{ $bulanName }} {{ $year }}</th>
        </tr>
        <tr>
            <th colspan="7">KABUPATEN TULUNGAGUNG TAHUN ANGGARAN {{ $year }}</th>
        </tr>
        <tr>
            <th colspan="2">Total Pagu</th>
            <th>Total Realisasi</th>
            <th>Sisa Anggaran</th>
            <th>% Keuangan</th>
            <th>% Fisik</th>
            <th>Deviasi</th>
        </tr>
        <tr>
            <td colspan="2">{{ number_format($data['summary']['total_pagu'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_realisasi'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['total_pagu'] - $data['summary']['total_realisasi'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['pct_keuangan'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['pct_fisik'], 2, ',', '.') }}</td>
            <td>{{ number_format($data['summary']['pct_fisik'] - $data['summary']['pct_keuangan'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama OPD</th>
            <th>Pagu Anggaran</th>
            <th>Realisasi s.d. Bulan Ini</th>
            <th>% Keuangan</th>
            <th>% Fisik</th>
            <th>Deviasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['opd_ranking'] as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['opd_name'] }}</td>
            <td>{{ number_format($item['pagu_anggaran'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['sd_bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['keuangan'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['fisik'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['fisik'] - $item['keuangan'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
