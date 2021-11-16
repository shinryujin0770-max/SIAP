@extends('adminlte::page')

@section('title', 'Project')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<form action="{{route('project.store')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Nama Pekerjaan</label>
                                <input type="text" name="project[name]" class="form-control"
                                    placeholder="Masukkan Nama Pekerjaan...">
                                <div class="invalid-feedback text-capitalize error-name"></div>
                            </div>
                            <div class="form-group">
                                <label>Tanggal SPK</label>
                                <input type="date" name="project[spk_date]" class="form-control"
                                    placeholder="Masukkan Tanggal SPK...">
                                <div class="invalid-feedback text-capitalize error-spk_date"></div>
                            </div>
                            <div class="form-group">
                                <label>Nomor SPK</label>
                                <input type="text" name="project[spk_number]" class="form-control"
                                    placeholder="Masukkan Nomor SPK...">
                                <div class="invalid-feedback text-capitalize error-spk_number"></div>
                            </div>
                            <div class="form-group">
                                <label>Nilai SPK</label>
                                <input type="number" name="project[spk_amount]" id="spk_amount" class="form-control"
                                    placeholder="Masukkan Nilai SPK...">
                                <div class="invalid-feedback text-capitalize error-spk_amount"></div>
                            </div>
                            <div class="card shadow-none border">
                                <div class="card-header">
                                    <h5 class="font-weight-bold mb-0">Pelaksana</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>CV/PT</label>
                                        <small class="text-muted">*Jika CV/PT tidak ditemukan, masukkan CV/PT baru dan
                                            data
                                            pelaksana
                                            dibawah ini.</small>
                                        <select class="form-control select-partner" id="partner_name"
                                            name="partner[cv_name]" onchange="handleChangePartner(this.value)"
                                            style="width: 100%">
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
                                    <div class="form-group">
                                        <label>Direktur</label>
                                        <input type="text" name="partner[director]" class="form-control"
                                            id="partner_director" placeholder="Masukkan Direktur...">
                                        <div class="invalid-feedback text-capitalize error-director"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" name="partner[address]" class="form-control"
                                            id="partner_address" placeholder="Masukkan Alamat...">
                                        <div class="invalid-feedback text-capitalize error-address"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-none border">
                                <div class="card-header">
                                    <h5 class="font-weight-bold mb-0">Periode SPK</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Lama</label>
                                        <input type="text" name="project[spk_days]" class="form-control"
                                            placeholder="Lama Pekerjaan (Hari/Kalender)...">
                                        <div class="invalid-feedback text-capitalize error-spk_days"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="project[spk_start]" class="form-control">
                                        <div class="invalid-feedback text-capitalize error-spk_start"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="project[spk_end]" class="form-control">
                                        <div class="invalid-feedback text-capitalize error-spk_end"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-none border">
                                <div class="card-header">
                                    <h5 class="font-weight-bold mb-0">Adendum</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="project[adendum_start]" class="form-control">
                                        <div class="invalid-feedback text-capitalize error-adendum_start"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="project[adendum_end]" class="form-control">
                                        <div class="invalid-feedback text-capitalize error-adendum_end"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-none border">
                                <div class="card-header">
                                    <h5 class="font-weight-bold mb-0">Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tipe Pembayaran</label>
                                        <div class="d-flex">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" name="project[payment_type]"
                                                    type="radio" id="paid" value="paid"
                                                    onchange="handleChangePaymentType(this.value)">
                                                <label for="paid" class="custom-control-label">Lunas</label>
                                            </div>
                                            <div class="custom-control custom-radio ml-3">
                                                <input class="custom-control-input" name="project[payment_type]"
                                                    type="radio" id="installment" value="installment"
                                                    onchange="handleChangePaymentType(this.value)">
                                                <label for="installment" class="custom-control-label">Angsuran</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback text-capitalize error-payment_type"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Detail Pembayaran</label>
                                        <div id="payment-detail"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Lokasi</label>
                                <input type="text" name="project[lokasi]" class="form-control"
                                    placeholder="Desa/Intansi/OPD">
                                <div class="invalid-feedback text-capitalize error-lokasi"></div>
                            </div>
                            <button type="button" data-dismiss="modal"
                                class="btn btn-secondary float-right ml-1">Batal</button>
                            <button type="submit" class="btn btn-primary float-right" id="btn-simpan">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    function handleChangePartner(id) {
        if (id) {
            $.ajax({
                url: "<?= url('partner/ajax/') . '/' ?>" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    if ($.trim(data)) {
                        $("#partner_id").val(data.id);
                        $("#partner_director").val(data.director);
                        $("#partner_address").val(data.address);
                    }
                }
            });
        }
    }

    function handleChangePaymentType(type) {
        $("#payment-detail").empty();
        if (type === 'installment') {
            $('#payment-detail').append(
                '<div class="card shadow-none border">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-2 d-flex align-items-center">' +
                '<label>Uang Muka</label>' +
                '</div>' +
                '<div class="col-10">' +
                '<input type="hidden" name="payment[0][type]" value="Uang Muka">' +
                '<input type="text" name="payment[0][amount]" class="form-control" placeholder="100.000.000">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="card shadow-none border">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-2 d-flex align-items-center">' +
                '<label>Termin 1</label>' +
                '</div>' +
                '<div class="col-10">' +
                '<input type="hidden" name="payment[1][type]" value="Termin 1">' +
                '<input type="text" name="payment[1][amount]" class="form-control" placeholder="100.000.000">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="card shadow-none border">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-2 d-flex align-items-center">' +
                '<label>Termin 2</label>' +
                '</div>' +
                '<div class="col-10">' +
                '<input type="hidden" name="payment[2][type]" value="Termin 2">' +
                '<input type="text" name="payment[2][amount]" class="form-control" placeholder="100.000.000">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="card shadow-none border">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-2 d-flex align-items-center">' +
                '<label>Termin 3</label>' +
                '</div>' +
                '<div class="col-10">' +
                '<input type="hidden" name="payment[3][type]" value="Termin 3">' +
                '<input type="text" name="payment[3][amount]" class="form-control" placeholder="100.000.000">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
            );
        } else {
            $('#payment-detail').append(
                '<div class="card shadow-none border">' +
                '<div class="card-body">' +
                '<div class="row">' +
                '<div class="col-2 d-flex align-items-center">' +
                '<label>Langsung 100%</label>' +
                '</div>' +
                '<div class="col-10">' +
                '<input type="hidden" name="payment[0][type]" value="Langsung 100%">' +
                '<input type="hidden" name="payment[0][amount]" value="' + $('#spk_amount').val() + '">' +
                '<div> Rp ' + $('#spk_amount').val() + '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
            );
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