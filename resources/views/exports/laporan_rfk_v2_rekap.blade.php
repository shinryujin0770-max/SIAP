@php
use Carbon\Carbon;
$bulanName = Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
@endphp
<table>
    <thead>
        <tr>
            <th colspan="10">REKAPITULASI LAPORAN REALISASI FISIK DAN KEUANGAN PELAKSANAAN PROGRAM/KEGIATAN APBD</th>
        </tr>
        <tr>
            <th colspan="10">KABUPATEN TULUNGAGUNG TAHUN ANGGARAN {{ $year }}</th>
        </tr>
        <tr>
            <th colspan="10">Bulan : s.d. {{ $bulanName }} {{ $year }}</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Organisasi</th>
            <th rowspan="2">Pagu Anggaran</th>
            <th colspan="3">Realisasi Keuangan</th>
            <th rowspan="2">Sisa Anggaran</th>
            <th colspan="2">Realisasi (%)</th>
            <th rowspan="2">Keterangan</th>
        </tr>
        <tr>
            <th>Bulan Lalu</th>
            <th>Bulan Ini</th>
            <th>s.d. Bulan Ini</th>
            <th>Keuangan</th>
            <th>Fisik</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['uraian'] }}</td>
            <td>{{ number_format($item['pagu_anggaran'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['bulan_lalu'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['sd_bulan_ini'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['sisa_anggaran'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['keuangan'], 2, ',', '.') }}</td>
            <td>{{ number_format($item['fisik'], 2, ',', '.') }}</td>
            <td>{{ $item['keterangan'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

