<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        .container {
            margin-top: 1rem;
            padding: 5rem 4rem;
        }

        @page {
            margin: 0px;
            size: 21cm 33cm portrait;
        }

        body {
            margin: 0px;
        }

        .table {
            border: none;
            width: 100%;
        }

        .table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .table-border,
        .table-border th.border,
        .table-border td.border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .col {
            border: 1px solid black;
        }

        p {
            margin: 12px 0px
        }

        .isi p {
            margin: 0px 0px;
        }

        .no-margin p {
            margin: 0px 0px;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
            line-height: 1.6;
        }

        .float-right {
            float: right;
        }

        .text-top {
            vertical-align: text-top;
        }

        .text-bottom {
            vertical-align: text-bottom;
        }

        .kop-surat p {
            /* font-size: 12.1px; */
            color: rgb(0, 0, 0);
            /* font-weight: bold; */
            font-style: normal;
            text-decoration: none
        }

        .paragraph {
            padding-left: 40px;
        }

        .isi {
            /* padding-left: 85px; */
            margin-top: 1rem;
        }

        .ttd {
            width: 8.15cm;
        }

        .catatan {
            width: 5.15cm;
            margin-top: 230px;
        }

        .d-inline {
            display: inline;
        }

        .keep-together {
            page-break-inside: avoid;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        ol {
            padding-left: 20px;
        }

        ol li::before {
            text-align: justify;
            line-height: 1.6;
            display: block;
            float: left;
            margin-top: -0.25em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div
            style="position: absolute; right: 100px; top: 50px; background-color: #000; color: #fff; display: inline; padding: 0.1rem 1rem; font-weight: bold; font-size: 14pt;">
            FPK-07</div>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
            <tr>
                <td style="width: 100%">
                    <div class="text-center">
                        <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                            ANGGARAN PENDAPATAN DAN BELANJA DAERAH (APBD)
                        </div>
                        <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                            KABUPATEN TULUNGAGUNG TAHUN ANGGARAN
                            {{\Carbon\Carbon::parse($project_detail['created_at'])->isoFormat('YYYY')}}
                        </div>
                        <hr style="width: 60%; margin-top: 0px;">
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%">
                    <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; margin-top: 15px;">
                        <tr>
                            <td colspan="3">
                                <div class="text-center">
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        LAPORAN PENYELESAIAN
                                    </div>
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        PELAKSANAAN PEKERJAAN KONSTRUKSI
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px">OPD</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['opd_nama']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px">Pekerjaan</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%">
                                <div style="font-size: 11pt; margin-left: 5px">Nomor Laporan Penyelesaian</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px"></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px">Tanggal Laporan Penyelesaian</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($contents['tanggal'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 1rem;">
                    <table border="0" cellspacing="3" cellpadding="0" style="width: 100%; font-size: 11pt;">
                        <tr>
                            <td rowspan="8" style="width: 3%;" class="text-top">A.</td>
                            <td colspan="4">
                                <div style="font-size: 11pt;">
                                    Telah menyelesaikan Pelaksanaan Konstruksi untuk :
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 1%; text-align: center;">1.</td>
                            <td style="width: 30%">
                                <div style="font-size: 11pt; margin-left: 5px">Pekerjaan</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">2.</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Lokasi (Desa/Kel - Instansi)</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['lokasi']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">3.</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Organisasi Perangkat Daerah</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['opd_nama']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">4.</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Pelaksana Pekerjaan Konstruksi</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['partner_name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-top" style="text-align: center;">5.</td>
                            <td class="text-top">
                                <div style="font-size: 11pt; margin-left: 5px">Surat Perintah Kerja (SPK)/Surat
                                    Perjanjian (Kontrak)</div>
                            </td>
                            <td class="text-top" style="text-align: center;">:</td>
                            <td class="text-top">
                                <table border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Nomor</div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">
                                                {{$project_detail['spk_number']}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Tanggal</div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">
                                                {{\Carbon\Carbon::parse($project_detail['spk_date'])->isoFormat('DD MMMM
                                                YYYY')}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">6.</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Biaya Konstruksi</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Rp
                                    {{number_format($project_detail['spk_amount'], 2, ',', '.')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-top" style="text-align: center;">7.</td>
                            <td class="text-top">
                                <div style="font-size: 11pt; margin-left: 5px">Waktu Pelaksanaan</div>
                            </td>
                            <td class="text-top" style="text-align: center;">:</td>
                            <td class="text-top">
                                <table border="0" cellspacing="3" cellpadding="0">
                                    <tr>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Mulai</div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">
                                                {{\Carbon\Carbon::parse($project_detail['spk_start'])->isoFormat('DD
                                                MMMM YYYY')}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Selesai</div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">
                                                {{\Carbon\Carbon::parse($project_detail['spk_end'])->isoFormat('DD MMMM
                                                YYYY')}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Durasi</div>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">
                                                {{$project_detail['spk_days'] + 1}} hari</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 1rem;">
                    <table border="0" cellspacing="3" cellpadding="0"
                        style="width: 100%; font-size: 11pt; margin-top: -10px;">
                        <tr>
                            <td rowspan="7" style="width: 3%;" class="text-top">B.</td>
                            <td>
                                <div style="font-size: 11pt;">
                                    Penyelesaian Pekerjaan Konstruksi tersebut meliputi :
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="2" style="width: 100%; font-size: 11pt;">
                                    <tr style="text-align: center; font-weight: bold;">
                                        <td style="width: 5%;">No</td>
                                        <td style="width: 45%">
                                            <div style="font-size: 11pt; margin-left: 5px">JENIS PEKERJAAN</div>
                                        </td>
                                        <td style="width: 15%;">BOBOT (%)</td>
                                        <td style="width: 18%;">PRESTASI (%)</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">PROGRES (%)</div>
                                        </td>
                                    </tr>
                                    @php
                                    $bobot = 0;
                                    $jumlah_prestasi = 0;
                                    $prestasi = 0;
                                    @endphp
                                    @foreach ($pekerjaan as $i => $item)
                                    @php
                                    $bobot += $item['bobot'];
                                    $prestasi += $item['prestasi'];
                                    $jumlah_prestasi += 1;
                                    @endphp
                                    <tr>
                                        <td style="text-align: center;">{{$i + 1}}</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">{{$item['jenis']}}</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px; text-align: center;">
                                                {{$item['bobot']}}%
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px; text-align: center;">
                                                {{$item['prestasi']}}%
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px; text-align: center;">
                                                {{$item['progress']}}%
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr style="font-weight: bold;">
                                        <td colspan="2" style="text-align: right;">
                                            <div style="font-size: 11pt; margin-left: 5px">Jumlah&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </td>
                                        <td style="text-align: center;">{{$bobot}}%</td>
                                        <td style="text-align: center;">{{$prestasi}}%</td>
                                        <td style="text-align: center;">{{number_format($prestasi /
                                            $bobot * 100, 2)}}%</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    (Laporan lengkap dari Pelaksana dan Konsultan pengawasan disampaikan kepada
                                    PA/KPA/PPKom)
                                </div>
                                @php
                                $total_prosentase = number_format($prestasi / $bobot * 100, 2);
                                $total_prosentase_word = str_replace(' ', ' ',
                                App\Utils\NumberToWords::ntw($total_prosentase, 2));
                                @endphp
                                <div>Progres pekerjaan telah mencapai prestasi : {{$total_prosentase}}%
                                    ({{$total_prosentase_word}} Persen)</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: -10px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Demikian laporan Penyelesaian Pekerjaan
                        Konstruksi
                        ini dibuat dengan sebenarnya untuk keperluan seperlunya.
                    </div>
                </td>
            </tr>
            <tr style="font-size: 11pt;">
                <td>
                    <table style="width: 100%; margin-top: 10px;">
                        <tr>
                            <td style="width: 50%" class="ttd text-center">
                                <div id="signature_jabatan">
                                    diperiksa oleh Konsultan Pengawas
                                </div>
                                <div>
                                    {{$contents['konsultan_pengawas_nama']}}
                                </div>
                                <div style="height: 20mm"></div>
                                <div>
                                    <u>{{$contents['konsultan_pengawas_direktur']}}</u>
                                </div>
                                <div>
                                    Direktur
                                </div>
                            </td>
                            <td style="width: 50%" class="ttd text-center">
                                <div id="signature_jabatan">
                                    dibuat oleh pelaksana :
                                </div>
                                <div>
                                    {{$project_detail['partner_name']}}
                                </div>
                                <div style="height: 20mm"></div>
                                <div>
                                    <u>{{$project_detail['director_partner'] ??
                                        $project_detail['partner_director']}}</u>
                                </div>
                                <div>
                                    Direktur
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-size: 11pt;">
                <td>
                    <div class="text-center" style="margin-top: 50px">
                        Mengetahui :
                    </div>
                    <table style="width: 100%; margin-top: 10px;">
                        <tr>
                            <td style="width: 50%" class="ttd text-center">
                                <div>
                                    {{$project_detail['user_jabatan']}}
                                </div>
                                <div style="height: 20mm"></div>
                                <div>
                                    <u>{{$project_detail['user_name']}}</u>
                                </div>
                                <div>
                                    NIP. {{$project_detail['user_nip']}}
                                </div>
                            </td>
                            <td style="width: 50%" class="ttd text-center">
                                <div>
                                    a.n. Pejabat Pengendali Pekerjaan Konstruksi
                                </div>
                                <div style="height: 20mm"></div>
                                <div>
                                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                                </div>
                                <div>
                                    NIP.
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    @include('printouts.footer')

</body>

</html>