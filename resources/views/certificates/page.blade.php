<style>
    @page {
        margin: 0px;
        size: 21.9cm 34.4cm portrait;
    }

    body {
        margin: 0px;
    }

    .container-mail {
        font-family: Times, serif;
        width: 100%;
        height: 100vh;
        background-image: url({{'certificates/' . $kategori_kios . '-front.jpg'}});
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
<div class="container-mail page-break-after">

    <div class="text-center" style="position: absolute; top: 300px; padding: 0px 220px;">
        <b class="text-uppercase">{{$nama_pasar}}</b>
        <hr style="margin: 0">
        <b>Nomor : {{$nomor_situ}}</b>
    </div>

    <div class="text-uppercase" style="position: absolute; top: 440px; left: 360px;">
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$nama_pedagang}}</div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">
            {{$tempat_lahir_pedagang}}, {{$tanggal_lahir_pedagang}}
        </div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$nik_pedagang}}</div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$alamat_pedagang}}
        </div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$nama_kios}}</div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$ukuran_kios}}</div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$nama_jenis_dagangan}}
        </div>
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$berlaku_sampai}}
        </div>
    </div>

    <div class="text-capitalize" style="position: absolute; bottom: 311px; left: 572px;">
        <div style="font-size: 11pt; margin-bottom: 1.2px; margin-top: 1px;">{{$tanggal_terbit}}</div>
    </div>
    <div class="text-capitalize text-center" style="position: absolute; bottom: 160px; left: 190px;">
        <img src="{{url($foto)}}" alt="pedagang" style="width: 3cm; height: 4cm; object-fit: cover;">
    </div>
    <div class="text-capitalize text-center" style="position: absolute; bottom: 100px; left: 352px;">
        @if ($is_signatured)
        <p class="text-capitalize" style="font-size: 10pt;">
            {{$user->jabatan}} <br>
            Kabupaten Tulungagung
        </p>
        <div style="position: relative; height: 20mm; display: flex; justify-content: center; align-items: center">
            <img src="{{$signature}}" style="height: 20mm; align-self: center;">
        </div>
        @else
        <div style="height: 20mm"></div>
        @endif
        @if ($is_signatured)
        <div style="font-size: 10pt;">
            <u>{{$user->name}}</u>
        </div>
        <div style="font-size: 10pt;">
            {{$user->pangkat}}
        </div>
        <div style="font-size: 10pt;">
            NIP. {{$user->nip}}
        </div>
        @endif
    </div>
</div>

@include('certificates.back')