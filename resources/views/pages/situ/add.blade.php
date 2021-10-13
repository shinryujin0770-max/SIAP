@extends('adminlte::page')

@section('title', 'SITU')

@section('content_header')
<h1 class="m-0 text-dark">
    {{$page_title}}
</h1>
@stop

@section('content')
<form action="{{route('situ.store')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Pasar</label>
                                <select class="form-control select2" id="id_pasar" name="dt[id_pasar]"
                                    onchange="handleChangePasar(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($pasars as $pasar)
                                    <option value="{{$pasar['id']}}">{{$pasar['nama']}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-capitalize error-id_pasar"></div>
                            </div>
                            <div class="form-group">
                                <label>Kios</label>
                                <select class="form-control select2" id="id_kios" name="dt[id_kios]"
                                    style="width: 100%">
                                    <option></option>
                                </select>
                                <div class="invalid-feedback text-capitalize error-id_kios"></div>
                            </div>
                            <div class="form-group">
                                <label>Jenis Dagangan</label>
                                <select class="form-control select2" id="id_jenis_dagangan" name="dt[id_jenis_dagangan]"
                                    style="width: 100%">
                                    @foreach ($jenis_dagangans as $jenis_dagangan)
                                    <option value="{{$jenis_dagangan['id']}}">{{$jenis_dagangan['nama']}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-capitalize error-id_jenis_dagangan"></div>
                            </div>
                            <div class="form-group">
                                <label>NIK Pemilik</label>
                                <small class="text-muted">*Jika NIK tidak ditemukan, masukkan NIK baru dan data pemilik
                                    dibawah ini.</small>
                                <select class="form-control select-pedagang" id="nik_pemilik" name="pedagang[nik]"
                                    onchange="handleChangeNIK(this.value)" style="width: 100%">
                                    <option></option>
                                    @foreach ($pedagangs as $pedagang)
                                    <option value="{{$pedagang['nik']}}">
                                        {{$pedagang['nik']}}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback text-capitalize error-nik"></div>
                            </div>
                            <div class="form-group">
                                <label>Nama Pemilik</label>
                                <input type="text" name="pedagang[nama]" class="form-control" id="nama_pemilik"
                                    placeholder="Masukkan Nama Pemilik...">
                                <div class="invalid-feedback text-capitalize error-nama"></div>
                            </div>
                            <div class="form-group">
                                <label>Alamat Pemilik</label>
                                <input type="text" name="pedagang[alamat]" class="form-control" id="alamat_pemilik"
                                    placeholder="Masukkan Alamat Pemilik...">
                                <div class="invalid-feedback text-capitalize error-alamat"></div>
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir Pemilik</label>
                                <input type="text" name="pedagang[tempat_lahir]" class="form-control"
                                    id="tempat_lahir_pemilik" placeholder="Masukkan Tempat Lahir Pemilik...">
                                <div class="invalid-feedback text-capitalize error-tempat_lahir"></div>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir Pemilik</label>
                                <input type="date" name="pedagang[tanggal_lahir]" class="form-control"
                                    id="tanggal_lahir_pemilik" placeholder="Masukkan Tanggal Lahir Pemilik...">
                                <div class="invalid-feedback text-capitalize error-tanggal_lahir"></div>
                            </div>
                            <div class="form-group">
                                <label>Foto Pemilik</label>
                                <input type="file" name="foto" class="form-control" id="foto">
                                <div class="invalid-feedback text-capitalize error-foto"></div>
                            </div>
                            <hr>
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
                    }
                }
            });
        } else {
            $("#id_kios").empty();
            $("#id_kios").append('<option value="">-Mohon Pilih SKPD Terlebih Dahulu-</option>');
        }
    }

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

                        window.location.href = "<?= url('situ') ?>";
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