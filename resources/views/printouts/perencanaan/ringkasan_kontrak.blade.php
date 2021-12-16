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
            padding: 0 5rem;
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
            height: 7px;
            width: 7px;
            background-color: rgb(0, 0, 0);
            border-radius: 20%;
            display: block;
            margin-top: 6px;
            margin-right: 10px;
        }
    </style>
    <div class="container">
        <div
            style="position: absolute; right: 100px; top: 25px; background-color: #000; color: #fff; display: inline; padding: 0.1rem 1rem; font-weight: bold; font-size: 14pt;">
            FPK-03</div>
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
                                        RINGKASAN KONTRAK
                                    </div>
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        PERENCANAAN KONSTRUKSI
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%; border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">OPD</div>
                            </td>
                            <td style="width: 1%; text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['opd_nama']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Tanggal</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($contents['tanggal'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nomor DPA/DPPA </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$contents['nomor_dpa']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Tanggal DPA/DPPA </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($contents['tanggal_dpa'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Kode Rekening </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['rekening']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Pekerjaan</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Pelaksana Kontrak </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['partner_name']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nama Direktur</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['partner_director']}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nomor Kontrak</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['spk_number']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Tanggal Kontrak</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($project_detail['spk_date'])->isoFormat('DD MMMM YYYY')}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nilai Kontrak</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">Rp
                                    {{number_format($project_detail['spk_amount'], 2, ',', '.')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Alamat</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['partner_address']}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Mulai Pelaksanaan</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($project_detail['spk_start'])->isoFormat('DD MMMM YYYY')}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Selesai Pelaksanaan</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($project_detail['spk_end'])->isoFormat('DD MMMM YYYY')}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Penyelesaian Pekerjaan </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{\Carbon\Carbon::parse($contents['penyelesaian_pekerjaan'])->isoFormat('DD MMMM YYYY')}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Cara Pembayaran </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">1. Langsung 100% </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Langsung 100%']['amount']) ? $project_payments['data']['Langsung 100%']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">2. Uang Muka </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Uang Muka']['amount']) ? $project_payments['data']['Uang Muka']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">3. Angsuran I </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Termin 1']['amount']) ? $project_payments['data']['Termin 1']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">4. Angsuran II </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Termin 2']['amount']) ? $project_payments['data']['Termin 2']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">5. Angsuran III </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Termin 3']['amount']) ? $project_payments['data']['Termin 3']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div style="font-size: 12pt; margin-left: 5px">6. Angsuran III </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data']['Termin 4']['amount']) ? $project_payments['data']['Termin 4']['amount'] : 0, 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%; font-weight: bold;">
                                    <tr>
                                        <td style="width: 30%">
                                            <div
                                                style="font-size: 12pt; margin-left: 5px; text-align: right; margin-right: 15px;">
                                                Jumlah </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format($project_payments['total'], 0, ',', '.')}},-</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Bank Referensi </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$contents['bank']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nomor Rekening Bank </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$contents['nomor_rekening']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Nama Pemilik Rekening </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$contents['nama_rekening']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Masa Pemeliharaan </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$contents['masa_pemeliharaan']}} hari
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-size: 12pt;">
                <table style="width: 100%; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%" class="ttd text-center">
                        </td>
                        <td style="width: 50%" class="ttd text-center">
                            <div>
                                Kuasa Pengguna Anggaran
                            </div>
                            <div style="height: 20mm"></div>
                            <div>
                                <u>{{$contents['nama_kuasa_pengguna_anggaran']}}</u>
                            </div>
                            <div>
                                NIP. {{$contents['nip_kuasa_pengguna_anggaran']}}
                            </div>
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>

</body>

</html>