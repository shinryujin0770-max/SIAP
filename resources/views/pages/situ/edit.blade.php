@extends('adminlte::page')

@section('title', 'SITU')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<form action="{{route('situ.update')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" name="id" value="{{$situ['id']}}">
                            <div class="form-group">
                                <label>Pasar</label>
                                <select class="form-control select2" id="id_pasar" name="dt[id_pasar]"
                                    onchange="handleChangePasar(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($pasars as $pasar)
                                    <option value="{{$pasar['id']}}">{{$pasar['nama']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kios</label>
                                <select class="form-control select2" id="id_kios" name="dt[id_kios]"
                                    style="width: 100%">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Dagangan</label>
                                <select class="form-control select2" id="id_jenis_dagangan" name="dt[id_jenis_dagangan]"
                                    style="width: 100%">
                                    @foreach ($jenis_dagangans as $jenis_dagangan)
                                    <option value="{{$jenis_dagangan['id']}}">{{$jenis_dagangan['nama']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>NIK Pemilik</label>
                                <select class="form-control select-pedagang" id="nik_pemilik" name="pedagang[nik]"
                                    onchange="handleChangeNIK(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($pedagangs as $pedagang)
                                    <option value="{{$pedagang['nik']}}">
                                        {{$pedagang['nik']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Pemilik</label>
                                <input type="text" name="pedagang[nama]" class="form-control" id="nama_pemilik"
                                    placeholder="Masukkan Nama Pemilik...">
                            </div>
                            <div class="form-group">
                                <label>Alamat Pemilik</label>
                                <input type="text" name="pedagang[alamat]" class="form-control" id="alamat_pemilik"
                                    placeholder="Masukkan Alamat Pemilik...">
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir Pemilik</label>
                                <input type="text" name="pedagang[tempat_lahir]" class="form-control"
                                    id="tempat_lahir_pemilik" placeholder="Masukkan Tempat Lahir Pemilik...">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir Pemilik</label>
                                <input type="date" name="pedagang[tanggal_lahir]" class="form-control"
                                    id="tanggal_lahir_pemilik" placeholder="Masukkan Tanggal Lahir Pemilik...">
                            </div>
                            <div class="form-group">
                                <label>Foto Pemilik</label>
                                <div class="my-2">
                                    <img src="{{$foto}}" alt="foto" style="width: 150px;">
                                </div>
                                <input type="file" name="foto" class="form-control" id="foto">
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="skrd" type="checkbox" id="skrd"
                                    {{$situ['is_skrd'] ? 'checked' : '' }} value="option1">
                                <label for="skrd" class="custom-control-label">SITU SKRD</label>
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
    function handleChangePasar(id) {
        if (id) {
            $.ajax({
                url: "<?= url('kios/ajax/') . '/' ?>" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $("#id_kios").empty();
                    if (!$.trim(data)) {
                        $("#id_kios").append('<option value="">Data Tidak Tersedia</option>');
                    } else {
                        $.each(data, function(key, value) {
                            $("#id_kios").append('<option></option>');
                            $("#id_kios").append('<option value="' +
                                value.id + '">' + value.nomor +
                                '</option>');
                        });

                        $("#id_kios").val("<?= $situ['id_kios'] ?>").trigger('change');
                    }
                }
            });
        } else {
            $("#id_kios").empty();
            $("#id_kios").append('<option value="">-Mohon Pilih SKPD Terlebih Dahulu-</option>');
        }
    }
    $("#id_pasar").val("<?= $situ['id_pasar'] ?>").trigger('change');
    $("#id_jenis_dagangan").val("<?= $situ['id_jenis_dagangan'] ?>").trigger('change');

    function handleChangeNIK(id) {
        if (id) {
            $.ajax({
                url: "<?= url('pedagang/ajax/') . '/' ?>" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    if ($.trim(data)) {
                        $("#nama_pemilik").val(data.nama);
                        $("#alamat_pemilik").val(data.alamat);
                        $("#tempat_lahir_pemilik").val(data.tempat_lahir);
                        $("#tanggal_lahir_pemilik").val(data.tanggal_lahir);
                    }
                }
            });
        }
    }

    $("#nik_pemilik").val("<?= $situ['nik_pedagang'] ?>").trigger('change');

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

                        window.location.href = "<?= url('situ') . '/' . $situ['id'] ?>";
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