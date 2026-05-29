@php
$numberToWords = new \App\Utils\NumberToRoman();
@endphp
<table>
    <thead>
        <tr>
            <th rowspan="2">Kode</th>
            <th rowspan="2">
                Uraian Urusan, Organisasi, Program, Kegiatan, dan Sub Kegiatan
            </th>
            <th rowspan="2">Pagu Anggaran</th>
            <th colspan="3">Realisasi Keuangan</th>
            <th rowspan="2">Sisa Anggaran</th>
            {{-- <th colspan="2">Anggaran Kas</th> --}}
            <th colspan="2">Realisasi (%)</th>
            <th rowspan="2">Keterangan</th>
            <th rowspan="2">Sisa</th>
            <th rowspan="2">Prosentase Fisik Kegiatan</th>
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
        @foreach ($data as $opd)
        @foreach ($opd as $item)
        <tr>
            <th>{{$item['code']}}</th>
            <th>{{$item['uraian']}}</th>
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
            <th>{{$item['sisa']}}</th>
            <th>{{$item['prosentase_fisik']}}%</th>
        </tr>
        @endforeach
        <tr>
            <td colspan="12"></td>
        </tr>
        @endforeach
        <tr>
            <th></th>
            <th>TOTAL</th>
            <th>{{number_format($total['pagu_anggaran'], 2, ',', '.')}}</th>
            <th>{{number_format($total['bulan_lalu'], 2, ',', '.')}}</th>
            <th>{{number_format($total['bulan_ini'], 2, ',', '.')}}</th>
            <th>{{number_format($total['sd_bulan_ini'], 2, ',', '.')}}</th>
            <th>{{number_format($total['sisa_anggaran'], 2, ',', '.')}}</th>
            {{-- <th>{{number_format($total['angkas'], 2, ',', '.')}}</th>
            <th>{{$total['angkas_prosentase']}}%</th> --}}
            <th>{{$total['keuangan']}}%</th>
            <th>{{$total['fisik']}}%</th>
            <th></th>
            <th>{{$total['sisa']}}</th>
            <th>{{$total['prosentase_fisik']}}%</th>
        </tr>
    </tbody>
</table>