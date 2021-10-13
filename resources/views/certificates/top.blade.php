<style>
    .container-mail {
        font-family: Times, serif;
        width: 100%;
        padding-top: 2mm;
        padding-bottom: 2mm;
        padding-right: 2mm;
        padding-left: 2mm;
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
<div class="container-mail">
    <table class="table">
        <tr>
            <td style="width: 13%">
                <img src="{{ url('logo-tulungagung.png')}}" width="125" height="145">
            </td>
            <td>
                <div class="text-center kop-surat">
                    <p style="margin-bottom: -10px; font-size: 16pt; font-weight: bold">PEMERINTAH KABUPATEN
                        TULUNGAGUNG</p>
                    <p style="margin-bottom: -10px; font-size: 28pt; font-weight: bold">DINAS PENDAPATAN</p>
                    <p style="margin-bottom: -10px; font-size: 10pt">Jl. Ahmad Yani Timur No. 37 Telp. (0355) 320098 Kode
                        Pos 66217</p>
                    <p style="margin-bottom: 1px; font-size: 11pt;">TULUNGAGUNG</p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p>
                    <hr style="border: 1px solid black;">
                    <hr style="border: 0.1px solid black; margin-top: -6px">
                </p>
            </td>
        </tr>
    </table>