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
            padding: 3rem 4rem;
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
            line-height: 1.3;
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
            FPK-20</div>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
            <tr>
                <td style="width: 100%">
                    <div class="text-center">
                        <div
                            style="font-size: 28pt; font-weight: bolder; margin-bottom: 25px; border: 1px solid #000; width: 250px;">
                            <i>CHECK-LIST</i>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="width: 15%">
                                <div style="font-size: 12pt;">Nomor</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{isset($checklist['nomor']) ? "600/" . $checklist['nomor'] . "/20.02.02/" .
                                    \Carbon\Carbon::parse($checklist->tanggal)->format('Y') : '-'}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%">
                                <div style="font-size: 12pt;">Tanggal</div>
                            </td>
                            <td style="width: 1%; text-align: center;">:</td>
                            <td>
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{$checklist['tanggal'] ?
                                    \Carbon\Carbon::parse($checklist['tanggal'])->isoFormat('DD MMMM YYYY') : '-'}}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="text-center" style="font-size: 14pt; font-weight: bold; margin: 10px 0px 0">
                        <u>PEMBAYARAN PENGAWASAN PEKERJAAN KONSTRUKSI </u>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 100%">
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 15px;">
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Surat Pengantar : {{$opd['name']}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                            </div>
                                        </td>
                                        <td colspan="4">
                                            <div style="font-size: 12pt; margin-left: 5px">Surat Perintah Kerja (SPK) /
                                                Surat Perjanjian (Kontrak) /SPMK dan SPMK *) </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">1.</div>
                                        </td>
                                        <td style="width: 25%">
                                            <div style="font-size: 12pt; margin-left: 5px">Nama pekerjaan </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">{{$project['name']}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">2.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Pelaksana </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">{{$project['partner_name']}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">3.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Tanggal SPK/Kontrak </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">
                                                {{\Carbon\Carbon::parse($project['spk_date'])->isoFormat('DD MMMM
                                                YYYY')}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">4.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Nomor SPK/Kontrak </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">{{$project['spk_number'] ?? '-'}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">5.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Tanggal Adendum </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">
                                                {{$project['adendum_date'] ? \Carbon\Carbon::parse($project['adendum_date'])->isoFormat('DD MMMM YYYY') : '-'}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">6.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Nomor Adendum </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">{{$project['adendum_no'] ?? '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">7.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Nilai SPK/Kontrak </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Rp
                                                {{number_format($project['spk_amount'], 2, ',', '.')}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%">
                                        </td>
                                        <td style="width: 4%">
                                            <div style="font-size: 12pt; margin-left: 5px">8.</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Jangka Waktu </div>
                                        </td>
                                        <td style="width: 1%">
                                            <div style="font-size: 12pt; margin-left: 5px">:</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">{{$project['spk_days'] + 1}} hari
                                                ({{\Carbon\Carbon::parse($project['spk_start'])->isoFormat('DD-MM-YYYY')}}
                                                s/d
                                                {{\Carbon\Carbon::parse($project['spk_end'])->isoFormat('DD-MM-YYYY')}})
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                @if (isset($contents['laporan_akhir']) && $contents['laporan_akhir'])
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                                @else
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10006;
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Laporan Akhir dari
                                                Pelaksanaan Konstruksi </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                @if ($project_details['berita_acara'][0]['content'])
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                                @else
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10006;
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Berita Acara Pemeriksaan
                                                Perencanaan Pekerjaan Konstruksi </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                @if ($project_details['bast'][0]['content'])
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                                @else
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10006;
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Berita Acara Serah Terima
                                                Perencanaan Pekerjaan Konstruksi </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                @if ($project_details['ringkasan_kontrak'][0]['content'])
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                                @else
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10006;
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Ringkasan Kontrak Pengawasan Pekerjaan Konstruksi </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table border="1" cellspacing="0" cellpadding="3"
                                    style="width: 100%; margin-bottom: 5px">
                                    <tr>
                                        <td style="width: 5%">
                                            <div style="font-size: 12pt; margin-left: 9px">
                                                @if (isset($contents['sts']) && $contents['sts'])
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10003;
                                                </span>
                                                @else
                                                <span
                                                    style="font-family: 'DeJaVu Sans Mono', monospace; font-weight: bold">
                                                    &#10006;
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 12pt; margin-left: 5px">Surat Tanda Setoran (STS)
                                                denda keterlambatan </div>
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
                    <div class="text-justify" style="margin-top: 10px; font-size: 12pt;">
                        Berdasarkan dokumen-dokumen yang disampaikan oleh Pejabat Pembuat Komitmen (PPK), untuk
                        pembayaran
                        @if ($type == 'Uang Muka')
                        <b>
                            <span style="text-decoration: line-through;">Langsung 100%</span>,
                            Uang Muka dan
                            <span style="text-decoration: line-through;">Angsuran/Tahapan</span>
                        </b>
                        @elseif($type == 'Langsung 100%')
                        <b>
                            Langsung 100%,
                            <span style="text-decoration: line-through;">Uang Muka</span> dan
                            <span style="text-decoration: line-through;">Angsuran/Tahapan</b>
                        @else
                        <b>
                            <span style="text-decoration: line-through;">Langsung 100% </span>,
                            <span style="text-decoration: line-through;">Uang Muka </span> dan
                            Angsuran/Tahapan
                        </b>
                        @endif
                        atas perencanaan konstruksi
                        sebesar Rp {{number_format($amount_value, 2, ',', '.')}} <i>({{$amount_word}})</i>,
                        <b>dapat
                            direalisasikan</b>.
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-justify" style="margin: 10px 0; font-size: 12pt;">
                        Demikian Check-List pembayaran ini untuk dipergunakan seperlunya.
                    </div>
                </td>
            </tr>
            <tr style="font-size: 12pt;">
                <table style="width: 100%; margin-top: 10px;" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 50%" class="ttd">
                            <div id="signature_jabatan">
                                Kepala Bagian Administrasi Pembangunan
                            </div>
                            <div>
                                Selaku Pengendali Pekerjaan Konstruksi
                            </div>
                            <div>Kabupaten Tulungagung</div>
                            <div style="height: 20mm"></div>
                            <div>
                                <u><b>{{$checklist['nama']}}</b></u>
                            </div>
                            <div>
                                {{$checklist['jabatan']}}
                            </div>
                            <div>
                                NIP. {{$checklist['nip']}}
                            </div>
                        </td>
                        <td style="width: 50%" class="ttd">
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>
    @include('printouts.footer')

</body>

</html>