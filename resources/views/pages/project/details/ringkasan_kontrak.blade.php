<form action="{{url("/project/$project_details->id_project/detail/$project_details->id")}}" id="submit-form"
    enctype="multipart/form-data">
    <div class="row p-3">
        @csrf
        <div class="col-12">
            <h5 class="font-weight-bolder text-center mb-3">RINGKASAN KONTRAK</h5>
            @if ($contents)
            <button type="button" onclick="cetak({{$project_details['id']}})" data-dismiss="modal"
                class="btn btn-inverse-info btn-sm float-right position-absolute" style="top: 0; right: 0;"><i
                    class="fa fa-print mr-1"></i> Cetak</button>
            @endif
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Kode Rekening</label>
                <div>{{$project_details['rekening']}}</div>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="data[tanggal]" class="form-control"
                    value="{{$contents ? $contents['tanggal'] : ''}}">
            </div>
            <div class="form-group">
                <label>Nomor DPA/DPPA</label>
                <input type="text" name="data[nomor_dpa]" class="form-control" placeholder="Masukkan Nomor..."
                    value="{{$contents ? $contents['nomor_dpa'] : ''}}">
            </div>
            <div class="form-group">
                <label>Tanggal DPA/DPPA</label>
                <input type="date" name="data[tanggal_dpa]" class="form-control" {{$contents ? $contents['tanggal_dpa']
                    : '' }}>
            </div>
            <div class="form-group">
                <label>Bank Referensi</label>
                <input type="text" name="data[bank]" class="form-control" placeholder="Masukkan Bank Referensi..."
                    value="{{$contents ? $contents['bank'] : '' }}">
            </div>
            <div class="form-group">
                <label>Nomor Rekening Bank</label>
                <input type="text" name="data[nomor_rekening]" class="form-control"
                    placeholder="Masukkan Nomor Rekening Bank..."
                    value="{{$contents ? $contents['nomor_rekening'] : '' }}">
            </div>
            <div class="form-group">
                <label>Nama Pemilik Rekening</label>
                <input type="text" name="data[nama_rekening]" class="form-control"
                    placeholder="Masukkan Nama Pemilik Rekening..."
                    value="{{$contents ? $contents['nama_rekening'] : '' }}">
            </div>
            <div class="form-group">
                <label>Masa Pemeliharaan</label>
                <input type="number" name="data[masa_pemeliharaan]" class="form-control"
                    placeholder="Masukkan Masa Pemeliharaan..."
                    value="{{$contents ? $contents['masa_pemeliharaan'] : '' }}">
            </div>
            <div class="form-group">
                <label>Tanggal Penyelesaian Pekerjaan</label>
                <input type="date" name="data[penyelesaian_pekerjaan]" class="form-control"
                    value="{{$contents ? $contents['penyelesaian_pekerjaan'] : ''}}">
            </div>
            <div class="form-group">
                <label>Nama Kuasa Pengguna Anggaran</label>
                <input type="text" name="data[kuasa_pengguna_anggaran]" class="form-control"
                    placeholder="Masukkan Nama Kuasa Pengguna Anggaran..."
                    value="{{$contents ? $contents['nama_kuasa_pengguna_anggaran'] : '' }}">
            </div>
            <div class="form-group">
                <label>NIP Kuasa Pengguna Anggaran</label>
                <input type="text" name="data[kuasa_pengguna_anggaran]" class="form-control"
                    placeholder="Masukkan NIP Kuasa Pengguna Anggaran..."
                    value="{{$contents ? $contents['nip_kuasa_pengguna_anggaran'] : '' }}">
            </div>
            <div class="mt-5">
                <button type="button" data-dismiss="modal"
                    class="btn btn-inverse-secondary float-right ml-1">Batal</button>
                <button type="submit" class="btn btn-gradient-primary float-right" id="btn-simpan">Simpan</button>
            </div>
        </div>
    </div>
</form>

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
                $("#btn-simpan").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Memproses...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'true') {
                    setTimeout(function() {
                        $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', true);

                        toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                        toastr.success('Berhasil Menyimpan Dokumen', '', []);
                        $("#modal-form").modal('hide');
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