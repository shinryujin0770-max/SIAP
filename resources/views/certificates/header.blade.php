<table class="table" style="margin-top: 25px">
    <tr>
        <td style="width: 50%;">&nbsp;</td>
        <td colspan="2" class="text-left">
            <p class="d-inline">
                Tulungagung,
            </p>
            <p id="tanggal_surat" class="d-inline">
                {{isset($mail) ? $mail['date'] : ''}}
            </p>
        </td>
    </tr>
</table>
<table class="table" style="margin-bottom: 30px">
    <tr>
        <td></td>
        <td colspan="2">
            <p style="margin-bottom: -10px">Kepada :</p>
        </td>
    </tr>
    <tr>
        <td class="text-top" style="width: 50%;">
            <table class="table" style="margin-top: 12px">
                @if ($template == 'pengantar' || $template == 'npknd')
                <tr>
                    <td style="width: 22%">Nomor</td>
                    <td style="width: 2%">:</td>
                    <td>{{isset($mail) ? ($mail['no_surat'] ? $mail['no_surat'] : 'Belum diatur') : 'Belum diatur'}}
                    </td>
                </tr>
                @else
                <tr>
                    <td style="width: 22%">Nomor</td>
                    <td style="width: 2%">:</td>
                    <td>{{isset($mail) ? ($mail['no_surat'] ? $mail['no_surat'] : 'Belum diatur') : 'Belum diatur'}}
                    </td>
                </tr>
                <tr>
                    <td>Sifat</td>
                    <td>:</td>
                    <td>
                        @if ($input)
                        <select name="message[sifat]" class="form-control" required>
                            @foreach ($mail_trait as $sifat)
                            <option value="{{$sifat['id']}}"
                                {{ isset($mail) &&  isset($mail) && isset(json_decode($mail['message'])->sifat) ? (json_decode($mail['message'])->sifat == $sifat['id'] ? 'selected' : '') : ''}}>
                                {{$sifat['name']}}</option>
                            @endforeach
                        </select>
                        @else
                        {{isset($sifat) ? $sifat : ''}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>
                        {{isset($lampiran) ? $lampiran : '-'}}
                    </td>
                </tr>
                @if ($template == 'undangan')
                <tr>
                    <td>Hal</td>
                    <td>:</td>
                    <td><u>U N D A N G A N</u></td>
                </tr>
                @elseif($template == 'panggilan')
                <tr>
                    <td>Hal</td>
                    <td>:</td>
                    <td><u>Panggilan</u></td>
                </tr>
                @else
                <tr>
                    <td class="text-top">Perihal</td>
                    <td class="text-top">:</td>
                    <td class="text-top">
                        @if ($input)
                        <input type="text" class="form-control" name="message[perihal]"
                            value="{{isset($mail) && isset(json_decode($mail['message'])->perihal) ? json_decode($mail['message'])->perihal : ''}}"
                            required>
                        @else
                        @if($template == 'edaran')
                        {{isset($mail) && isset(json_decode($mail['message'])->perihal) ? json_decode($mail['message'])->perihal : ''}}
                        <hr>
                        @else
                        {{isset($mail) && isset(json_decode($mail['message'])->perihal) ? json_decode($mail['message'])->perihal : ''}}
                        @endif
                        @endif
                    </td>
                </tr>
                @endif
                @endif
            </table>
        </td>
        <td class="text-top" style="width: 7%;">
            <p>Yth.</p>
        </td>
        <td class="text-top text-capitalize" id="kepada">
            @if (isset($mail))
            @if ($mail['type_id'] == 2)
            <p>Sekretaris Daerah</p>
            @else
            @if ($jumlahTujuan > 4)
            <p>Dilampirkan</p>
            @else
            @php
            $no = 1
            @endphp
            @if (isset($kepadaSemua))
            @foreach ($kepadaSemua as $i => $semua)
            <p>{{count($kepadaSemua) > 0 ? $i+1 . '. ' : ''}} {{strtolower($semua)}}</p>
            @php
            $no++
            @endphp
            @endforeach
            @endif
            @foreach ($mail_to as $i => $to)
            <p>{{$no > 1 ? $no . '. ' : ''}} {{strtolower($to['nmjabstr'] ?? $to['nama_gelar'])}}</p>
            @php
            $no++
            @endphp
            @endforeach
            @foreach ($mail_to_outside as $i => $to_outsite)
            <p>{{$no > 1 ? $no . '. ' : ''}} {{strtolower($to_outsite['optional'] ?? $to_outsite['optional'])}}</p>
            @php
            $no++
            @endphp
            @endforeach
            @endif
            @endif
            @endif
            <p>Di </p>
            <p style="margin-left: 25px">
                <b><u>{{isset($mail) && $mail['type_id'] == 5 ? 'Tempat' : 'TULUNGAGUNG'}}</u></b></p>
        </td>
    </tr>
</table>
<script>
    var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    var today = new Date();
    var dd = today.getDate();
    var yyyy = today.getFullYear();
    $("#tanggal_surat").html(dd + ' ' + monthNames[today.getMonth()] + ' ' + yyyy);
</script>