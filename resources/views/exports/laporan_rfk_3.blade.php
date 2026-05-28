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
            <th colspan="12">Anggaran Kas Bulan</th>
        </tr>
        <tr>
            <th>Januari</th>
            <th>Februari</th>
            <th>Maret</th>
            <th>April</th>
            <th>Mei</th>
            <th>Juni</th>
            <th>Juli</th>
            <th>Agustus</th>
            <th>September</th>
            <th>Oktober</th>
            <th>November</th>
            <th>Desember</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            <th>{{$item['code']}}</th>
            <th>{{$item['uraian']}}</th>
            <th>{{isset($item['angkas'][1]) ? number_format($item['angkas'][1]['angkas'], 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][2]) ? number_format($item['angkas'][2]['angkas'] - (isset($item['angkas'][1])  ? $item['angkas'][1]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][3]) ? number_format($item['angkas'][3]['angkas'] - (isset($item['angkas'][2])  ? $item['angkas'][2]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][4]) ? number_format($item['angkas'][4]['angkas'] - (isset($item['angkas'][3])  ? $item['angkas'][3]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][5]) ? number_format($item['angkas'][5]['angkas'] - (isset($item['angkas'][4])  ? $item['angkas'][4]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][6]) ? number_format($item['angkas'][6]['angkas'] - (isset($item['angkas'][5])  ? $item['angkas'][5]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][7]) ? number_format($item['angkas'][7]['angkas'] - (isset($item['angkas'][6])  ? $item['angkas'][6]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][8]) ? number_format($item['angkas'][8]['angkas'] - (isset($item['angkas'][7])  ? $item['angkas'][7]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][9]) ? number_format($item['angkas'][9]['angkas'] - (isset($item['angkas'][8])  ? $item['angkas'][8]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][10]) ? number_format($item['angkas'][10]['angkas'] - (isset($item['angkas'][9])  ? $item['angkas'][9]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][11]) ? number_format($item['angkas'][11]['angkas'] - (isset($item['angkas'][10]) ? $item['angkas'][10]['angkas'] : 0), 2, ',', '.') : 0}}</th>
            <th>{{isset($item['angkas'][12]) ? number_format($item['angkas'][12]['angkas'] - (isset($item['angkas'][11]) ? $item['angkas'][11]['angkas'] : 0), 2, ',', '.') : 0}}</th>
        </tr>
        @endforeach
    </tbody>
</table>