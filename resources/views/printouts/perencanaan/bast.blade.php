<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<body>
    <style>
        .container {
            padding: 3rem 5rem;
            padding-top: 5rem;
        }

        @page {
            margin: 0px;
            size: 24.13cm 28.94cm portrait;
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

        .dot {
            height: 5px;
            width: 5px;
            background-color: rgb(0, 0, 0);
            border-radius: 20%;
            display: block;
            margin-top: 2px;
            margin-right: 6px;
            margin-left: 3px;
        }
    </style>
    <div class="container">
        <div
            style="position: absolute; right: 100px; top: 25px; background-color: #000; color: #fff; display: inline; padding: 0.1rem 1rem; font-weight: bold; font-size: 14pt;">
            FPK-02</div>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
            <tr>
                <td style="width: 100%">
                    <div class="text-center">
                        <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                            ANGGARAN PENDAPATAN DAN BELANJA DAERAH (APBD)
                        </div>
                        <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                            KABUPATEN TULUNGAGUNG TAHUN ANGGARAN 2022
                        </div>
                        <hr style="width: 60%; margin-top: 0px;">
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%">
                    <table border="1" cellspacing="0" cellpadding="2" style="width: 100%; margin: 15px 0;">
                        <tr>
                            <td colspan="3">
                                <div class="text-center">
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        BERITA ACARA SERAH TERIMA
                                    </div>
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        PERENCANAAN KONSTRUKSI
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
                                <div style="font-size: 11pt; margin-left: 5px">Nomor</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$contents['nomor']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px">Tanggal</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($contents['tanggal'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px">Lampiran</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">1. Berita Acara Pemeriksaan Perencanaan
                                    Pekerjaan Kontruksi (FPK-01) </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 11pt; margin-left: 5px"></div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">
                                    2. DED Pekerjaan Perencanaan Kontruksi
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        Kami yang bertanda tangan dibawah ini :
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 1rem;">
                    <table border="0" cellspacing="3" cellpadding="0"
                        style="width: 100%; font-size: 11pt; margin-top: -5px;">
                        <tr>
                            <td rowspan="8" style="width: 3%;" class="text-top">
                                <div style="margin-top: 7px">I.</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                <div style="font-size: 11pt; margin-left: 5px">Nama</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['user_name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Jabatan</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['user_jabatan']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="font-size: 11pt; margin-left: 5px">Berdasarkan SK. Kepala {{$project_detail['opd_name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Nomor</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['user_sk_number']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Tanggal</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{\Carbon\Carbon::parse($project_detail['user_sk_date'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Alamat Kantor</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['opd_address']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="font-size: 11pt; margin-left: 5px">
                                    Yang selanjutnya disebut sebagai <strong>PIHAK KESATU</strong>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 1rem;">
                    <table border="0" cellspacing="3" cellpadding="0"
                        style="width: 100%; font-size: 11pt; margin-top: -5px;">
                        <tr>
                            <td rowspan="8" style="width: 3%;" class="text-top">
                                <div style="margin-top: 7px">II.</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                <div style="font-size: 11pt; margin-left: 5px">Nama</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['partner_name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Jabatan</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Direktur</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">Alamat Kantor</div>
                            </td>
                            <td style="text-align: center;">:</td>
                            <td>
                                <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['partner_address']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="font-size: 11pt; margin-left: 5px">
                                    selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div style="font-size: 11pt; margin-left: 5px">
                                    Berdasarkan :
                                </div>
                                <table>
                                    <tr>
                                        <td style="width: 3%"><span class="dot"></span></td>
                                        <td style="width: 35%;">
                                            <div style="font-size: 11pt; margin-left: 5px">Nomor SPK/Kontrak
                                            </div>
                                        </td>
                                        <td style="width: 1%; text-align: center;">:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">{{$project_detail['spk_number']}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="dot"></span></td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">Tanggal</div>
                                        </td>
                                        <td style="text-align: center;">:</td>
                                        <td>
                                            <div style="font-size: 11pt; margin-left: 5px">{{\Carbon\Carbon::parse($project_detail['spk_date'])->isoFormat('DD MMMM YYYY')}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: 10px;" class="text-justify">
                        telah melakukan Serah Terima Perencanaan Konstruksi yang disampaikan oleh Pihak Kedua, dengan
                        kesimpulan : {{$contents['kesimpulan']}} sebagaimana tersebut dalam SPK/Kontrak.
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: 10px;" class="text-justify">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Demikian Berita Acara Serah Terima Perencanaan
                        Konstruksi ini dibuat untuk dipergunakan seperlunya.
                    </div>
                </td>
            </tr>
            <tr style="font-size: 11pt;">
                <table style="width: 100%; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%" class="ttd text-center">
                            <div id="signature_jabatan">
                                PIHAK KEDUA
                            </div>
                            <div>
                                {{$project_detail['partner_name']}}
                            </div>
                            <div style="height: 20mm"></div>
                            <div>
                                <u>{{$project_detail['partner_director']}}</u>
                            </div>
                            <div>
                                Direktur
                            </div>
                        </td>
                        <td style="width: 50%" class="ttd text-center">
                            <div id="signature_jabatan">
                                PIHAK KESATU
                            </div>
                            <div>
                                PEJABAT PEMBUAT KOMITMEN
                            </div>
                            <div style="height: 20mm"></div>
                            <div>
                                <u>{{$project_detail['user_name']}}</u>
                            </div>
                            <div>
                                NIP. {{$project_detail['user_nip']}}
                            </div>
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>

</body>

</html>