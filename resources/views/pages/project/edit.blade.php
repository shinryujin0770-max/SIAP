@extends('adminlte::page')

@section('title', 'Paket Pekerjaan Konstruksi')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted text-capitalize">{{$page_subtitle}} {{$category}}</h6>
</div>
@stop

@section('content')
<form action="{{route('project.update')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <input type="hidden" name="id" value="{{$project['id']}}">
        <div class="col-md-12">
            <div class="card rounded-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="font-weight-bolder mb-3 text-capitalize text-primary">Paket Pekerjaan
                                {{$category}}</h5>
                            <input type="hidden" value="{{$category}}" name="project[category]">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Nama Pekerjaan</label>
                                <input type="text" name="project[name]" class="form-control"
                                    placeholder="Masukkan Nama Pekerjaan..." value="{{$project['name']}}">
                                <div class="invalid-feedback text-capitalize error-name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Pagu Anggaran</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            Rp
                                        </div>
                                    </div>
                                    <input type="text" name="project[pagu_anggaran]" id="pagu_anggaran"
                                        class="form-control uang" placeholder="Masukkan Pagu Anggaran..."
                                        value="{{$project['pagu_anggaran']}}">
                                </div>
                                <div class="invalid-feedback text-capitalize error-pagu_anggaran"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal SPK</label>
                                <input type="date" name="project[spk_date]" class="form-control" {{--
                                    min="{{\Carbon\Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD')}}" --}}
                                    placeholder="Masukkan Tanggal SPK..." value="{{$project['spk_date']}}"
                                    @if (!auth()->user()->hasRole('superadmin')) disabled @endif>
                                <div class="invalid-feedback text-capitalize error-spk_date"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">HPS</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            Rp
                                        </div>
                                    </div>
                                    <input type="text" name="project[hps]" id="hps" class="form-control uang"
                                        placeholder="Masukkan HPS..." value="{{$project['hps']}}">
                                </div>
                                <div class="invalid-feedback text-capitalize error-hps"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Nomor SPK</label>
                                <input type="text" name="project[spk_number]" class="form-control"
                                    placeholder="Masukkan Nomor SPK..." value="{{$project['spk_number']}}">
                                <div class="invalid-feedback text-capitalize error-spk_number"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Nilai SPK</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            Rp
                                        </div>
                                    </div>
                                    <input type="text" name="project[spk_amount]" id="spk_amount"
                                        onkeyup="handleChangeSPK()" class="form-control uang"
                                        placeholder="Masukkan Nilai SPK..." value="{{$project['spk_amount']}}">
                                </div>
                                <div class="invalid-feedback text-capitalize error-spk_amount"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card rounded-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bolder mb-3">Lokasi Pekerjaan</p>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Lokasi</label>
                                <input type="text" name="project[lokasi]" class="form-control"
                                    placeholder="Desa/Intansi/OPD" value="{{$project['lokasi']}}">
                                <div class="invalid-feedback text-capitalize error-lokasi"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Kecamatan</label>
                                <select class="form-control select2" id="kecamatan" name="project[id_kecamatan]"
                                    onchange="handleChangeKecamatan(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($kecamatan as $k)
                                    <option value="{{$k['id']}}">
                                        {{$k['nama']}}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-capitalize error-id_kecamatan"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Desa</label>
                                <select class="form-control select2" id="kelurahan" name="project[id_kelurahan]"
                                    style="width: 100%">
                                </select>
                                <div class="invalid-feedback text-capitalize error-id_kelurahan"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card rounded-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bolder mb-3">Pelaksana</p>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">CV/PT</label>
                                <small class="text-muted">*Jika CV/PT tidak ditemukan, masukkan CV/PT baru dan
                                    data
                                    pelaksana
                                    dibawah ini.</small>
                                <select class="form-control select-partner" id="partner_name" name="partner[cv_name]"
                                    onchange="handleChangePartner(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($partners as $partner)
                                    <option value="{{$partner['name']}}">
                                        {{$partner['name']}}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-capitalize error-cv_name"></div>
                            </div>
                            <input type="hidden" name="partner[id]" id="partner_id">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Direktur</label>
                                <input type="text" name="partner[director]" class="form-control" id="partner_director"
                                    placeholder="Masukkan Direktur...">
                                <div class="invalid-feedback text-capitalize error-director"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Alamat</label>
                                <input type="text" name="partner[address]" class="form-control" id="partner_address"
                                    placeholder="Masukkan Alamat...">
                                <div class="invalid-feedback text-capitalize error-address"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bolder mb-3">Periode SPK</p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal Mulai</label>
                                <input type="date" name="project[spk_start]" class="form-control" {{--
                                    min="{{\Carbon\Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD')}}" --}}
                                    value="{{$project['spk_start']}}">
                                <div class="invalid-feedback text-capitalize error-spk_start"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal Selesai</label>
                                <input type="date" name="project[spk_end]" class="form-control" {{--
                                    min="{{\Carbon\Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD')}}" --}}
                                    value="{{$project['spk_end']}}">
                                <div class="invalid-feedback text-capitalize error-spk_end"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <p class="font-weight-bolder mb-3">Adendum</p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal Mulai</label>
                                <input type="date" name="project[adendum_start]" class="form-control"
                                    value="{{$project['adendum_start']}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal Selesai</label>
                                <input type="date" name="project[adendum_end]" class="form-control"
                                    value="{{$project['adendum_end']}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Nomor Adendum</label>
                                <input type="text" name="project[adendum_no]" class="form-control"
                                    value="{{$project['adendum_no']}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted" style="font-size: 11pt">Tanggal Adendum</label>
                                <input type="date" name="project[adendum_date]" class="form-control"
                                    value="{{$project['adendum_date']}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-lg">
                <div class="card-body">
                    <div>
                        <p class="font-weight-bolder mb-3">Pembayaran</p>
                    </div>
                    <div class="form-group">
                        <label class="text-muted" style="font-size: 11pt">Uraian Sub Kegiatan</label>
                        <textarea class="form-control" name="project[uraian]" style="width: 100%">
                            {{$project['uraian']}}
                        </textarea>
                        <div class="invalid-feedback text-capitalize error-uraian"></div>
                    </div>
                    <div class="form-group">
                        <label class="text-muted" style="font-size: 11pt">Kode Rekening</label>
                        <select class="form-control select-rekening" id="kode_rekening" onchange="handleChangeRekening()"
                            style="width: 100%" multiple disabled>
                            @foreach ($rekening as $p)
                            <option value="{{$p}}">
                                {{$p}}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback text-capitalize error-rekening"></div>
                    </div>
                    <div class="form-group">
                        <label class="text-muted" style="font-size: 11pt">Tipe Pembayaran</label>
                        <div class="d-flex">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input input-payment-type" name="project[payment_type]"
                                    type="radio" id="paid" disabled value="paid"
                                    onchange="handleChangePaymentType(this.value)" {{isset($project) &&
                                    $project['payment_type']=='paid' ? 'checked' : '' }}>
                                <label for="paid" class="custom-control-label">Langsung 100%</label>
                            </div>
                            <div class="custom-control custom-radio ml-3">
                                <input class="custom-control-input input-payment-type" name="project[payment_type]"
                                    type="radio" disabled id="installment" value="installment"
                                    onchange="handleChangePaymentType(this.value)" {{isset($project) &&
                                    $project['payment_type']=='installment' ? 'checked' : '' }}>
                                <label for="installment" class="custom-control-label">Termin</label>
                            </div>
                        </div>
                        <div class="invalid-feedback text-capitalize error-payment_type"></div>
                    </div>
                    <div class="form-group">
                        <label class="text-muted" style="font-size: 11pt">Detail Pembayaran</label>
                        <ul class="nav nav-tabs" id="nav-tabs-payment-detail">
                        </ul>
                        <div class="tab-content" id="tabs-payment-detail">
                            <small class="text-danger">Pilih salah satu tipe pembayaran terlebih dahulu dan masukkan
                                minimal satu rekening</small>
                        </div>
                        <div class="invalid-feedback text-capitalize error-termin"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card rounded-lg">
                <div class="card-body">
                    {{-- <button type="button" data-dismiss="modal"
                        class="btn btn-secondary float-right ml-1">Batal</button> --}}
                    <button type="submit" class="btn btn-inverse-primary float-right" id="btn-simpan">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @include('pages.project.modals.modal-overtime')
</form>
@stop

@section('js')
<script>
    $( '.uang' ).mask('#.##0,00', {reverse: true});
    function handleChangePartner(id) {
        if (id) {
            $("#partner_id").val('');
            $("#partner_director").val('');
            $("#partner_address").val('');
            $.ajax({
                url: "<?= url('partner/ajax/') . '/' ?>" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    if ($.trim(data)) {
                        $("#partner_id").val(data.id);
                        if("<?= $project['director_partner'] ?>"){
                            $("#partner_director").val("<?= $project['director_partner'] ?>");
                            $("#partner_address").val("<?= $project['address_partner'] ?>");
                        }else{
                            $("#partner_director").val(data.director);
                            $("#partner_address").val(data.address);
                        }
                    } else {
                        $("#partner_id").val('');
                        $("#partner_director").val('');
                        $("#partner_address").val('');
                    }
                },
            });
        } else {
            $("#partner_id").val('');
            $("#partner_director").val('');
            $("#partner_address").val('');
        }
    }
    $("#partner_name").val("<?= isset($project) ? $project['partner_name'] : '' ?>").trigger('change');

    function handleChangeKecamatan(id_kecamatan) {
        if (id_kecamatan) {
            $.ajax({
                url: "<?= url('ajax/kelurahan/') . '/' ?>" + id_kecamatan,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $("#kelurahan").empty();
                    if (!$.trim(data)) {
                        $("#kelurahan").append('<option value="">Data Tidak Tersedia</option>');
                    } else {
                        $.each(data, function(key, value) {
                            $("#kelurahan").append('<option></option>');
                            $("#kelurahan").append('<option value="' +
                                value.id + '">' + value.nama +
                                '</option>');
                        });
                        $("#kelurahan").val("<?= isset($project) ? $project['id_kelurahan'] : '' ?>").trigger('change');
                    }
                }
            });
        } else {
            $("#kelurahan").empty();
            $("#kelurahan").append('<option value="">-Mohon Pilih SKPD Terlebih Dahulu-</option>');
        }
    }
    $("#kecamatan").val("<?= isset($project) ? $project['id_kecamatan'] : '' ?>").trigger('change');

    function handleChangeSPK() {
        const typePayment = $('input[name="project[payment_type]"]:checked').val();

        if (typePayment === 'installment') {
            handleChangeTermin()
        } else if (typePayment === 'paid') {
            handleChangePaymentType('paid')
        }
    }

    function handleChangeRekening() {
        const value = $('#kode_rekening').val();
        if (value.length > 0) {
            const typePayment = $('input[name="project[payment_type]"]:checked').val();
            $(".input-payment-type").removeClass("disabled").attr('disabled', false);
            if (typePayment) {
                handleChangePaymentType(typePayment)
            }
        } else {
            $(".input-payment-type").addClass("disabled").attr('disabled', true);
            handleChangePaymentType(null)
        }
    }
    $("#kode_rekening").val(JSON.parse('<?= isset($rekening) ? json_encode($rekening) : "" ?>')).trigger('change');

    function activeTab(tab) {
        $('.nav-tabs a[href="' + tab + '"]').tab('show');
    };

    function handleChangePaymentType(type) {
        $("#nav-tabs-payment-detail").empty();
        $("#tabs-payment-detail").empty();
        const value = $('#kode_rekening').val();
        let tab = '';
        value.map(function (item, i) {
            $('#nav-tabs-payment-detail').append(`
                <li class="nav-item">
                    <a class="nav-link text-muted" onclick="activeTab('#tabs-${i}')" href="#tabs-${i}">
                        ${item}
                    </a>
                </li>
            `)
        })
        const data = JSON.parse('<?= json_encode($project_payments) ?>');
        if (type === 'installment') {
            value.map(function (item, i) {
                if (i === 0) {
                    tab = i;
                }
                $('#tabs-payment-detail').append(
                    '<div class="tab-pane py-3 fade show" id="tabs-' + i + '" role="tabpanel" aria-labelledby="tabs-' + i + '-tab">' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Uang Muka</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][0][type]" value="Uang Muka">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][0][amount]" class="form-control uang termin-angsuran" id="termin-0" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item][0]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Termin Ke-1</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][1][type]" value="Termin 1">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][1][amount]" class="form-control uang termin-angsuran" id="termin-1" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item][1]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Termin Ke-2</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][2][type]" value="Termin 2">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][2][amount]" class="form-control uang termin-angsuran" id="termin-2" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item][2]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Termin Ke-3</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][3][type]" value="Termin 3">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][3][amount]" class="form-control uang termin-angsuran" id="termin-3" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item][3]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Termin Ke-4</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][4][type]" value="Termin 4">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][4][amount]" class="form-control uang termin-angsuran" id="termin-4" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item][4]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Termin Ke-5</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][5][type]" value="Termin 5">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][5][amount]" class="form-control uang termin-angsuran" id="termin-5" onkeyup="handleChangeTermin()" placeholder="0" value="${data[item].length > 5 ? data[item][5]?.amount : 0}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            })
        } else if (type === 'paid') {
            value.map(function (item, i) {
                if (i === 0) {
                    tab = i;
                }
                $('#tabs-payment-detail').append(
                    '<div class="tab-pane py-3 fade show" id="tabs-' + i + '" role="tabpanel" aria-labelledby="tabs-' + i + '-tab">' +
                    '<div class="card shadow-none my-1">' +
                    '<div class="card-body p-0">' +
                    '<div class="row">' +
                    '<div class="col-3 d-flex align-items-center">' +
                    '<label class="text-muted" style="font-size: 11pt">Langsung 100%</label>' +
                    '</div>' +
                    '<div class="col-9">' +
                    '<input type="hidden" name="payment[' + item + '][0][type]" value="Langsung 100%">' +
                    '<div class="input-group mb-3">'+
                    '<div class="input-group-append">'+
                    '<div class="input-group-text">'+
                    'Rp'+
                    '</div>'+
                    '</div>'+
                    `<input type="text" name="payment[${item}][0][amount]" class="form-control uang termin-langsung" id="termin-langsung" onkeyup="handleChangeLunas()" placeholder="0" value="${data[item][0]?.amount}">` +
                    '</div>'+
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            })
        } else {
            $('#tabs-payment-detail').append(
                '<small class="text-danger">Pilih salah satu tipe pembayaran terlebih dahulu dan masukkan minimal satu rekening</small>'
            );
            return;
        }
        activeTab('#tabs-' + tab);
        $( '.uang' ).mask('#.##0,00', {reverse: true});
    }

    function handleChangeLunas() {
        const inputLunas = $('.termin-langsung');
        const totalSPK = parseFloat($('#spk_amount').val().replace(/\./g, '').replace(/\,/g, '.')) || 0;
        let lunas = 0;

        for (var i = 0; i < inputLunas.length; i++) {
            lunas += parseFloat($(inputLunas[i]).val().replace(/\./g, '').replace(/\,/g, '.')) || 0;
        }

        if ((Math.round(lunas * 100) / 100) !== totalSPK) {
            $(".error-termin").addClass("d-block").html('<strong>Total Pembayaran harus sama dengan Nilai SPK</strong>');
            $("#btn-simpan").addClass("disabled").attr('disabled', true);
        } else {
            $(".error-termin").removeClass("d-block");
            $("#btn-simpan").removeClass("disabled").attr('disabled', false);
        }
    }

    function handleChangeTermin() {
        const inputs = $('.termin-angsuran');
        const totalSPK = parseFloat($('#spk_amount').val().replace(/\./g, '').replace(/\,/g, '.')) || 0;
        let lunas = 0;

        for (var i = 0; i < inputs.length; i++) {
            lunas += parseFloat($(inputs[i]).val().replace(/\./g, '').replace(/\,/g, '.')) || 0;
        }

        if ((Math.round(lunas * 100) / 100) !== totalSPK) {
            $(".error-termin").addClass("d-block").html('<strong>Total Pembayaran harus sama dengan Nilai SPK</strong>');
            $("#btn-simpan").addClass("disabled").attr('disabled', true);
        } else {
            $(".error-termin").removeClass("d-block");
            $("#btn-simpan").removeClass("disabled").attr('disabled', false);
        }
    }

    $("#submit-form").submit(function() {
        var form = $(this);
        var mydata = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        });
        $.ajax({
            type: "POST",
            url: form.attr("action"),
            data: mydata,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#btn-simpan").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Memproses...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'overtime'){
                    toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                    toastr.error('SPK/Kontrak anda telah melewati batas tanggal', '', []);

                    $("#modal-overtime").modal();
                }
                if (str === 'password'){
                    toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                    toastr.error('Kata sandi salah!', '', []);
                }
                if (str === 'true') {
                    setTimeout(function() {
                        $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', true);

                        window.location.href = "<?= url('project') ?>";
                    }, 1000);
                } else {
                    $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', false);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                if(xhr.responseJSON.errors){
                    xhr.responseJSON.errors.map((value, key) => {
                        $(".select-" + value.code).next().children().children().addClass('has-error').focus()
                        $(".error-" + value.code).addClass("d-block").html('<strong>' + value.message + '</strong>');
                    })
                }
                $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', false);
            }
        });

        return false;
    });
</script>
@stop