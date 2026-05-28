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
</head>

<body>
    <div class="container">
        <div
            style="position: absolute; right: 100px; top: 50px; background-color: #000; color: #fff; display: inline; padding: 0.1rem 1rem; font-weight: bold; font-size: 14pt;">
            FPK-16</div>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
            <tr>
                <td style="width: 100%">
                    <div class="text-center" style="margin-bottom: 50px">
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
                <td>
                    <div style="margin-bottom: 15px">
                        Tulungagung, {{\Carbon\Carbon::parse($contents['tanggal'])->isoFormat('DD MMMM YYYY')}}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div>Kepada</div>
                    <div>Yth. Sdr. Kepala Bagian Administrasi Pembangunan </div>
                    <div style="margin-left: 62px">selaku Pengendali Pengawasan Konstruksi </div>
                    <div style="margin-left: 62px">Kabupaten Tulungagung Tahun
                        {{\Carbon\Carbon::parse($project_detail['created_at'])->isoFormat('YYYY')}} </div>
                    <div style="margin-left: 62px">di</div>
                    <div style="margin-left: 82px"> <u>TULUNGAGUNG</u> </div>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; margin-top: 20px; margin-bottom: 20px;">
                        <tr>
                            <td style="width: 12%;">Nomor</td>
                            <td style="width: 3%;">:</td>
                            <td>{{$contents['nomor']}}</td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td>:</td>
                            <td><u>Pembayaran Pengawasan Konstruksi </u></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: 10px;" class="text-justify">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Menunjuk Berita Acara Serah Terima Pengawasan
                        Konstruksi dan pasal-pasal pembayaran pada Surat Perintah Kerja (SPK) / Surat Perjanjian
                        (Kontrak) -
                        terlampir, dapatnya dilaksanakan verifikasi terhadap berkas Pembayaran
                        @if ($project_detail->type == 'Uang Muka')
                        <span style="text-decoration: line-through;">Langsung 100%</span>,
                        Uang Muka dan
                        <span style="text-decoration: line-through;">Angsuran/Tahapan</span>
                        @elseif($project_detail->type == 'Langsung 100%' || ($project_detail->type == 'pengantar' &&
                        $project_detail->payment_type == 'paid'))
                        Langsung 100%,
                        <span style="text-decoration: line-through;">Uang Muka</span> dan
                        <span style="text-decoration: line-through;">Angsuran/Tahapan
                            @else
                            <span style="text-decoration: line-through;">Langsung 100% </span>,
                            <span style="text-decoration: line-through;">Uang Muka </span> dan
                            Angsuran/Tahapan
                            @endif
                            Pengawasan Konstruksi.
                    </div>
                    <div>Bersama ini kami lampirkan : </div>
                    <table width="100%" cellspacing="0">
                        <tr>
                            <td style="width: 5%;">1.</td>
                            <td style="width: 90%;">Surat Perintah Kerja (SPK) / Surat Perjanjian (Kontrak)</td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 5%;">2.</td>
                            <td style="width: 90%;">Laporan Periode / Akhir Pengawasan </td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    @if (isset($contents['laporan_akhir']) && $contents['laporan_akhir'])
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                    @else
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10006;
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 5%;">3.</td>
                            <td style="width: 90%;">Berita Acara Pemeriksaan Pengawasan Pekerjaan Konstruksi </td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    @if ($project_details['berita_acara'][0]['content'])
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                    @else
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10006;
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 5%;">4.</td>
                            <td style="width: 90%;">Berita Acara Serah Terima Pengawasan Pekerjaan Konstruksi </td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    @if ($project_details['bast'][0]['content'])
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                    @else
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10006;
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 5%;">5.</td>
                            <td style="width: 90%;">Ringkasan Kontrak Pengawasan Pekerjaan Konstruksi </td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    @if ($project_details['ringkasan_kontrak'][0]['content'])
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                    @else
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10006;
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 5%;">6.</td>
                            <td style="width: 90%;">Surat Tanda Setoran (STS) denda keterlambatan </td>
                            <td>
                                <div style="border: 1px solid #000; width: 50px; text-align: center;">
                                    @if (isset($contents['sts']) && $contents['sts'])
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10003;
                                    </span>
                                    @else
                                    <span
                                        style="font-family: 'DeJaVu Sans Mono', monospace; margin-right: -10px; font-weight: bold">
                                        &#10006;
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div>
                        <span style="font-style: italic">*) Beri tanda (</span>
                        <span style="font-family: 'DeJaVu Sans Mono', monospace; margin: 0 -10px">
                            &#10003;
                        </span>
                        / x )
                        <span style="font-style: italic"> pada Kolom Kanan</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="margin-top: 10px;" class="text-justify">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Demikian untuk menjadikan periksa.
                    </div>
                </td>
            </tr>
            <tr style="font-size: 12pt;">
                <table style="width: 100%; margin-top: 30px;">
                    <tr>
                        <td style="width: 50%" class="ttd text-center">
                            <div>
                                {{$project_detail['user_jabatan']}}
                            </div>
                            <div style="height: 30mm"></div>
                            <div>
                                <u>{{$project_detail['user_name']}}</u>
                            </div>
                            <div>
                                NIP.{{$project_detail['user_nip']}}
                            </div>
                        </td>
                        <td style="width: 50%" class="ttd text-center">
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>
    @include('printouts.footer')

</body>

</html>