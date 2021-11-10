<style>
    @page {
        margin: 0px;
        size: 21.9cm 34.4cm portrait;
    }

    body {
        margin: 0px;
    }

    .container-back {
        font-family: Times, serif;
        width: 100%;
        height: 100vh;
        background-image: url({{'certificates/' . $kategori_kios . '-back.jpg'}});
        background-position: center;
        background-repeat: no-repeat;
        background-size: 100%;
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

    .text-lowercase {
        text-transform: lowercase;
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
<div class="container-back">
    <div class="text-left" style="position: absolute; top: 210px; left: 10px; padding: 0px 105px;">
        @include('certificates.daful')
    </div>
    <div class="text-center" style="position: absolute; top: 210px; left: 320px; padding: 0px 105px;">
        @include('certificates.stamp')
    </div>
</div>