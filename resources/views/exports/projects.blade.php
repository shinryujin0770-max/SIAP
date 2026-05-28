@php
    $numberToWords = new \App\Utils\NumberToRoman();
    $lastCount = 0;
    foreach ($projects as $project) {
        $count = 0;
        foreach ($project['payments'] as $payment) {
            $count += 1;
        }
        if($lastCount < $count) $lastCount = $count;
    }
@endphp
<table>
    <thead>
        <tr>
            <th rowspan="3">Nama Pekerjaan</th>
            <th rowspan="3">Pemenang</th>
            <th rowspan="3">Pagu Anggaran</th>
            <th rowspan="3">HPS</th>
            <th rowspan="3">Kontrak</th>
            <th rowspan="3">Tanggal SPK</th>
            <th rowspan="3">Tanggal Mulai Kontrak</th>
            <th rowspan="3">Tanggal Selesai Kontrak</th>
            <th rowspan="3">Nama OPD</th>
            <th rowspan="3">Realisasi Kontrak</th>
            <th colspan="{{3 * $lastCount}}">Checklist</th>
            <th rowspan="3">Jumlah</th>
        </tr>
        <tr>
            @for ($i = 0; $i < $lastCount; $i++)
                <th colspan="3">{{$numberToWords->numberToRomanRepresentation($i + 1)}}</th>
            @endfor
        </tr>
        <tr>
            @for ($i = 0; $i < $lastCount; $i++)
                <th>No</th>
                <th>Tanggal</th>
                <th>Rp.</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($projects as $project)
        <tr>
            <th>{{$project['name']}}</th>
            <th>{{$project['partner_name']}}</th>
            <th>{{number_format($project['pagu_anggaran'], 2, ',', '.')}}</th>
            <th>{{number_format($project['hps'], 2, ',', '.')}}</th>
            <th>{{number_format($project['spk_amount'], 2, ',', '.')}}</th>
            <th>{{\Carbon\Carbon::parse($project['spk_start'])->isoFormat('DD-MM-YYYY')}}</th>
            <th>{{\Carbon\Carbon::parse($project['spk_start'])->isoFormat('DD-MM-YYYY')}}</th>
            <th>{{$project['adendum_end'] ? \Carbon\Carbon::parse($project['adendum_end'])->isoFormat('DD-MM-YYYY') : \Carbon\Carbon::parse($project['spk_end'])->isoFormat('DD-MM-YYYY')}}</th>
            <th>{{$project['opd_name']}}</th>
            <th>{{number_format($project['jumlah'], 2, ',', '.')}}</th>
            @for ($i = 0; $i < $lastCount; $i++)
                @if (isset($project['payments'][$i]))
                    <th>{{$project['payments'][$i]['id']}}</th>
                    <th>{{\Carbon\Carbon::parse($project['payments'][$i]['tanggal'])->isoFormat('DD-MM-YYYY')}}</th>
                    <th>{{number_format($project['payments'][$i]['total'], 2, ',', '.')}}</th>
                @else
                    <th></th>
                    <th></th>
                    <th></th>
                @endif
            @endfor
            <th>{{number_format($project['jumlah'], 2, ',', '.')}}</th>
        </tr>
        @endforeach
    </tbody>
</table>