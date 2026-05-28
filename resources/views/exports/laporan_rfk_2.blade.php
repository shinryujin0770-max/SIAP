@php
$numberToWords = new \App\Utils\NumberToRoman();
@endphp
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">SKPD</th>
            <th rowspan="2">Pagu Anggaran</th>
            <th colspan="3">Realisasi Keuangan</th>
            <th rowspan="2">Sisa Anggaran</th>
            {{-- <th colspan="2">Anggaran Kas</th> --}}
            <th colspan="2">Prosentase Realisasi</th>
            <th rowspan="2">Keterangan</th>
        </tr>
        <tr>
            <th>Bulan Lalu</th>
            <th>Bulan Ini</th>
            <th>s.d. Bulan Ini</th>
            {{-- <th>Sampai dengan Bulan Ini</th>
            <th>Prosentase</th> --}}
            <th>Keuangan</th>
            <th>Fisik</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
        <tr>
            <th>{{$i + 1}}</th>
            <th>{{$item['opd_name']}}</th>
            <th>{{number_format($item['pagu_anggaran'], 2, ',', '.')}}</th>
            <th>{{number_format($item['bulan_lalu'], 2, ',', '.')}}</th>
            <th>{{number_format($item['bulan_ini'], 2, ',', '.')}}</th>
            <th>{{number_format($item['sd_bulan_ini'], 2, ',', '.')}}</th>
            <th>{{number_format($item['sisa_anggaran'], 2, ',', '.')}}</th>
            {{-- <th>{{number_format($item['angkas'], 2, ',', '.')}}</th>
            <th>{{$item['angkas_prosentase']}}%</th> --}}
            <th>{{$item['keuangan']}}%</th>
            <th>{{$item['fisik']}}%</th>
            <th>{{$item['keterangan']}}</th>
        </tr>
        @endforeach
    </tbody>
</table>