<style>
    .container-skrd {
        padding: 0.5rem;
    }

    @page {
        margin: 0px;
        size: 21.9cm 34.4cm portrait;
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
<div class="container-skrd">
    <table border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 45%">
                <table class="table">
                    <tr>
                        <td style="width: 5%">
                            <img src="{{ url('logo-tulungagung.png')}}" width="75" height="95">
                        </td>
                        <td>
                            <div class="text-center">
                                <p style="margin-bottom: -10px; font-size: 11pt;">PEMERINTAH KABUPATEN
                                    TULUNGAGUNG</p>
                                <p style="margin-bottom: -10px; font-size: 14pt; font-weight: bold">DINAS PERINDUSTRIAN
                                    DAN PERDAGANGAN</p>
                                <p style="font-size: 12pt">
                                    Jl. Ahmad Yani Timur No. 37 Tulungagung
                                    <br>
                                    Telp. (0355) 320098 Kode Pos 66217
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 35%">
                <div class="text-center kop-surat">
                    <p style="margin-bottom: -10px; font-size: 16pt; font-weight: bold">SKRD</p>
                    <p style="margin-bottom: -10px; font-size: 11pt;">(SURAT KETETAPAN RETRIBUSI
                        DAERAH) </p>
                    <p style="margin-bottom: -10px; font-size: 11pt">Masa Retribusi : NOVEMBER</p>
                    <p style="margin-bottom: -10px; font-size: 11pt">Tahun : 2021</p>
                </div>
            </td>
            <td class="text-center" style="width: 12%;">
                No. Urut
                <div style="border: 1px solid black; width: 50px; height: 50px; margin-left: 25px; margin-top: 5px;">
                    <div style="margin-top: 13px">675</div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="isi table" style="margin-bottom: 10px">
                    <tr>
                        <td style="width: 30%">Nama Pedagang</td>
                        <td style="width: 1%">:</td>
                        <td style="font-weight: 100">SUTIKNO, MM. DRS</td>
                    </tr>
                    <tr>
                        <td>Tempat/Tanggal Lahir (Usia)</td>
                        <td>:</td>
                        <td style="font-weight: bold">TULUNGAGUNG, 31 Maret 1956</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td style="font-weight: bold"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="table" border="1" cellspacing="0" cellpadding="0" style="margin-bottom: 10px">
                    <tr>
                        <td class="text-center" style="width: 5%;">No.</td>
                        <td class="text-center" style="width: 15%;">Rekening</td>
                        <td class="text-center" style="width: 55%;">Jenis Retribusi</td>
                        <td class="text-center">Besar Retribusi</td>
                    </tr>
                    <tr>
                        <td class="text-center">1.</td>
                        <td class="text-center">4.1.2.01.06</td>
                        <td style="padding: 1rem;">
                            <div>Retribusi Kios Pasar</div>
                            <div>Luas</div>
                            <div>Lokasi</div>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex;">
                                <div>Rp</div>
                                <div style="text-align: right;">58.500,00</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 0"></td>
                        <td style="padding: 1rem;">
                            <table>
                                <tr>
                                    <td>
                                        <div>Jumlah Ketetapan Pokok</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display: inline">Jumlah Sanksi : a. Bunga</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display: inline; margin-left: 105px">b. Kenaikan</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display: inline">Jumlah Keseluruhan</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding: 0 1rem;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 5%">
                                        Rp
                                    </td>
                                    <td style="text-align: right; width: 90%">
                                        58.500,00
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%">
                                        Rp
                                    </td>
                                    <td style="text-align: right">
                                        58.500,00
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%">
                                        Rp
                                    </td>
                                    <td style="text-align: right">
                                        58.500,00
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%">
                                        Rp
                                    </td>
                                    <td style="text-align: right">
                                        58.500,00
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 0; text-align: right; padding: 0.5rem 1rem">
                            Dengan huruf :
                        </td>
                        <td colspan="2" style="border: 0">
                            <div style="width: 100%; border: 1px solid #000; margin-top: 5px; padding: 0.5rem 1rem">
                                Lima Ratus Ribu Rupiah
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div style="text-decoration: underline; margin-left: 3px;">PERHATIAN :</div>
                <div style="margin-top: 5px; margin-left: 25px; margin-bottom: -25px;">
                    <div style="display: flex;">
                        <div>1.</div>
                        <div style="margin-left: 20px">
                            Harap penyetoran dilakukan melalui Bendahara Penerimaan (BP) Dinas Perindustrian dan
                            Perdagangan Kabupaten Tulungagung atau melalui Kas Daerah (Bank Jatim No. Rekening :
                            0151001300) dengan menggunakan SKRD ini.
                        </div>
                    </div>
                    <div style="display: flex; margin-top: 15px">
                        <div>2.</div>
                        <div style="margin-left: 20px">
                            Apabila SKRD ini tidak atau Kurang Dibayar setelah lewat waktu paling lama 30 hari sejak
                            SKRD ini diterima dikenakan sanksi administrasi berupa bunga sebesar 2% per bulan.
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="table">
                    <tr>
                        <td style="width: 50%" class="ttd text-center">&nbsp;</td>
                        <td style="width: 50%" class="ttd text-center">
                            <div id="signature_jabatan" class="text-uppercase" style="font-size: 12pt;">
                                a.n. Kepala Bidang Pengendalian dan Evaluasi Kabupaten Tulungagung
                            </div>
                            <div class="text-capitalize" style="font-size: 12pt;">
                                Kepala Bidang Pengelolaan Pasar
                            </div>
                            <div style="height: 10mm"></div>
                            <div style="font-size: 12pt;">
                                <u>NURWENI ASTUTI, S.H.,M.M.</u>
                            </div>
                            <div style="font-size: 12pt;">
                                Pembina
                            </div>
                            <div style="font-size: 12pt;">
                                NIP. 19660509 199003 2 010
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="1" cellspacing="0" cellpadding="0" style="width: 100%">
                    <tr>
                        <td style="width: 33%">
                            <table class="table">
                                <tr>
                                    <td style="width: 100%" class="text-center">
                                        <div class="text-capitalize" style="font-size: 12pt;">
                                            Ruang Teraan Bendahara Penerima
                                        </div>
                                        <div style="height: 10mm"></div>
                                        <div style="font-size: 12pt;">
                                            <u>SUSWANTI, S.Pd</u>
                                        </div>
                                        <div style="font-size: 12pt;">
                                            NIP. 19660509 199003 2 010
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 33%; padding: 0.1rem">
                            <div>Diterima oleh,</div>
                            <div>Petugas Tempat Pembayaran</div>
                            <div>Tanggal :</div>
                            <div>Tanda Tangan :</div>
                            <div>Nama Terang :</div>
                        </td>
                        <td style="width: 33%">
                            <table class="table">
                                <tr>
                                    <td style="width: 100%" class="text-center">
                                        <div class="text-capitalize" style="font-size: 12pt;">
                                            Penyetor
                                        </div>
                                        <div style="height: 10mm"></div>
                                        <div style="font-size: 12pt;">
                                            (.............................)
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div>
        {{-- MODEL DPD-10G <br> --}}
        ………………………………………………………….................................................................................................................
    </div>
    <table class="table">
        <tr style="border: 0px;">
            <td colspan="3" style="border: 0px;">
                <table border="1" style="width: 100%; margin-top: 15px">
                    <tr>
                        <td style="border: 0px">
                            <div style="text-align: right;">No. SKRD ......................</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 0px">
                            <div style="text-align: center;">TANDA TERIMA</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 1rem 5rem; border: 0px;">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 25%;">NPWP</td>
                                    <td style="width: 3%;">:</td>
                                    <td>R1000298610014</td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 0px;">
                            <table class="table" style="width: 100%; margin-bottom: 15px">
                                <tr>
                                    <td style="width: 50%" class="ttd text-center">&nbsp;</td>
                                    <td style="width: 50%" class="ttd text-center">
                                        <div class="text-capitalize" style="font-size: 11pt;">
                                            Tulungagung, ……………………………2021
                                        </div>
                                        <div class="text-capitalize" style="font-size: 11pt;">
                                            Yang Menerima
                                        </div>
                                        <div style="height: 10mm"></div>
                                        <div style="font-size: 12pt;">
                                            ( …………………………………..)
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{-- <div>MODEL DPD-10G</div> --}}
</div>