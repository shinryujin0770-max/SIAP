@extends('adminlte::page')

@section('title', 'Pengguna')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<form action="{{route('user.update')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" name="id" value="{{$user['id']}}">
                            <div class="form-group">
                                <label>Dasar Surat Putusan</label>
                                <input type="text" name="dt[dasar_surat_putusan]" class="form-control"
                                    id="dasar_surat_putusan" placeholder="Masukkan Dasar Surat Putusan..."
                                    value="{{$user['dasar_surat_putusan']}}">
                                <div class="invalid-feedback text-capitalize error-dasar_surat_putusan"></div>
                            </div>
                            <div class="form-group">
                                <label>Email Pengguna</label>
                                <input type="email" name="dt[email]" class="form-control" id="email"
                                    placeholder="Masukkan Email Pengguna..." value="{{$user['email']}}">
                                <div class="invalid-feedback text-capitalize error-email"></div>
                            </div>
                            <div class="form-group">
                                <label>Username Pengguna</label>
                                <input type="text" name="dt[username]" class="form-control" id="username"
                                    placeholder="Masukkan Username Pengguna..." value="{{$user['username']}}">
                                <div class="invalid-feedback text-capitalize error-username"></div>
                            </div>
                            <div class="form-group">
                                <label>Nama Pengguna</label>
                                <input type="text" name="dt[name]" class="form-control" id="name"
                                    placeholder="Masukkan Nama Pengguna..." value="{{$user['name']}}">
                                <div class="invalid-feedback text-capitalize error-name"></div>
                            </div>
                            <div class="form-group">
                                <label>NIP Pengguna</label>
                                <input type="text" name="dt[nip]" class="form-control" id="nip"
                                    placeholder="Masukkan NIP Pengguna..." value="{{$user['nip']}}">
                                <div class="invalid-feedback text-capitalize error-nip"></div>
                            </div>
                            <div class="form-group">
                                <label>Tanggal SK Pengguna</label>
                                <input type="date" name="dt[sk_date]" class="form-control" id="sk_date"
                                    placeholder="Masukkan Tanggal SK Pengguna..." value="{{$user['sk_date']}}">
                                <div class="invalid-feedback text-capitalize error-sk_date"></div>
                            </div>
                            <div class="form-group">
                                <label>No. SK Pengguna</label>
                                <input type="text" name="dt[sk_number]" class="form-control" id="sk_number"
                                    placeholder="Masukkan No. SK Pengguna..." value="{{$user['sk_number']}}">
                                <div class="invalid-feedback text-capitalize error-sk_number"></div>
                            </div>
                            <div class="form-group">
                                <label>Jabatan Pengguna</label>
                                <select class="form-control select2" name="dt[jabatan]" style="width: 100%">
                                    <option value="Pengguna Anggaran" {{$user['jabatan']=='Pengguna Anggaran'
                                        ? 'selected' : '' }}>Pengguna Anggaran</option>
                                    <option value="Kuasa Pengguna Anggaran"
                                        {{$user['jabatan']=='Kuasa Pengguna Anggaran' ? 'selected' : '' }}>Kuasa
                                        Pengguna Anggaran</option>
                                    <option value="Pejabat Pembuat Komitmen"
                                        {{$user['jabatan']=='Pejabat Pembuat Komitmen' ? 'selected' : '' }}>Pejabat
                                        Pembuat Komitmen</option>
                                </select>
                                <div class="invalid-feedback text-capitalize error-jabatan"></div>
                            </div>
                            <div class="form-group">
                                <label>Role pengguna</label>
                                <select class="form-control select2" id="role" name="role" style="width: 100%">
                                    <option value="superadmin" {{$user['role']=='superadmin' ? 'selected' : '' }}>
                                        Superadmin</option>
                                    <option value="admin_opd" {{$user['role']=='admin_opd' ? 'selected' : '' }}>Admin
                                        OPD</option>
                                    <option value="admin_rfk" {{$user['role']=='admin_rfk' ? 'selected' : '' }}>Admin
                                        RFK</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>OPD</label>
                                <select class="form-control select2" id="id_opd" name="dt[id_opd]" style="width: 100%">
                                    @foreach ($opds as $opd)
                                    <option value="{{$opd['id']}}" {{$user['id_opd']==$opd['id'] ? 'selected' : '' }}>
                                        {{$opd['name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Password field --}}
                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group mb-3">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="{{ __('adminlte::adminlte.password') }}">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span
                                                class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback text-capitalize error-password"></div>
                                </div>
                            </div>

                            {{-- Confirm password field --}}
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <div class="input-group mb-3">
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span
                                                class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback text-capitalize error-password_confirmation"></div>
                                </div>
                            </div>
                            <hr>
                            <button type="button" data-dismiss="modal"
                                class="btn btn-secondary float-right ml-1">Batal</button>
                            <button type="submit" class="btn btn-primary float-right">Simpan</button>
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
                $(".invalid-feedback").removeClass("d-block");
                $("#btn-simpan").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Memproses...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'true') {
                    setTimeout(function() {
                        $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', true);

                        window.location.href = "<?= url('user') ?>";
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