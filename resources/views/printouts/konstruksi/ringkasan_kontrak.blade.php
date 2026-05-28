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
    <div class="container">
        <div
            style="position: absolute; right: 100px; top: 50px; background-color: #000; color: #fff; display: inline; padding: 0.1rem 1rem; font-weight: bold; font-size: 14pt;">
            FPK-10</div>
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
                    <table border="1" cellspacing="0" cellpadding="2" style="width: 100%; margin: 15px 0;">
                        <tr>
                            <td colspan="3">
                                <div class="text-center">
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        RINGKASAN KONTRAK
                                    </div>
                                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px">
                                        PEKERJAAN KONSTRUKSI
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
                                <div style="font-size: 12pt; margin-left: 5px;">Kode Rekening Belanja </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['rekening']}}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Uraian Sub Kegiatan </div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['uraian']}}</div>
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
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['director_partner'] ??
                                    $project_detail['partner_director']}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">NPWP</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{isset($contents['npwp']) ?
                                    $contents['npwp'] : '-'}}</div>
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
                                <div style="font-size: 12pt; margin-left: 5px;">Nomor Adendum</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{$project_detail['adendum_no'] ?? '-'}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Tanggal Adendum</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{$project_detail['adendum_date'] ?
                                    \Carbon\Carbon::parse($project_detail['adendum_date'])->isoFormat('DD MMMM YYYY') :
                                    '-'}}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;">Alamat</div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;">:</td>
                            <td style="border-left: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px">{{$project_detail['address_partner'] ??
                                    $project_detail['partner_address']}}
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
                                <div style="font-size: 12pt; margin-left: 5px">
                                    {{\Carbon\Carbon::parse($contents['penyelesaian_pekerjaan'])->isoFormat('DD MMMM
                                    YYYY')}}</div>
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
                                        <td style="width: 35%">
                                            <div style="font-size: 12pt; margin-left: 5px">1.
                                                {{$project_detail->payment_type != 'paid' ? 'Uang Muka' : 'Langsung
                                                100%'}} </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($project_payments['data'][$project_detail->payment_type
                                            != 'paid' ? 'Uang Muka' : 'Langsung 100%']['amount'])
                                            ? $project_payments['data'][$project_detail->payment_type != 'paid'
                                            ? 'Uang Muka' : 'Langsung 100%']['amount'] : 0, 2, ',', '.')}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @php
                        $i = 1;
                        @endphp
                        @foreach ($project_payments['payments'] as $item)
                        {{-- @if ($i > 0) --}}
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 35%">
                                            <div style="font-size: 12pt; margin-left: 5px">{{$i + 1}}.
                                                {{str_replace("Termin", "Angsuran", $item['type'])}} </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format(isset($item['amount']) ?
                                            $item['amount'] : 0, 2, ',', '.')}}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        @php
                        $i++;
                        @endphp
                        {{-- @endif --}}
                        @endforeach
                        <tr>
                            <td style="border-right: 0px;">
                                <div style="font-size: 12pt; margin-left: 5px;"></div>
                            </td>
                            <td style="text-align: center; border-left: 0px; border-right: 0px;"></td>
                            <td style="border-left: 0px;">
                                <table style="width: 100%; font-weight: bold;">
                                    <tr>
                                        <td style="width: 35%">
                                            <div
                                                style="font-size: 12pt; margin-left: 5px; text-align: right; margin-right: 15px;">
                                                Jumlah </div>
                                        </td>
                                        <td style="width: 5%">Rp</td>
                                        <td>{{number_format($project_payments['total'], 2, ',', '.')}}</td>
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
            <tr>
                <td>
                    <div style="margin-top: 10px;" class="text-left">
                        Menyatakan bahwa ringkasan kontrak di atas sesuai dengan dokumen kontrak yang ada, jika terjadi
                        kesalahan maka menjadi tanggungjawab kami sepenuhnya. Demikian ringkasan kontrak ini dibuat
                        untuk dipergunakan sebagaimana mestinya.
                    </div>
                </td>
            </tr>
            <tr style="font-size: 12pt;">
                <table style="width: 100%; margin-top: 10px;">
                    <tr>
                        <td style="width: 50%" class="ttd text-center">
                        </td>
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
                    </tr>
                </table>
            </tr>
        </table>
    </div>
    @include('printouts.footer')

</body>

</html>