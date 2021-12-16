<form action="{{url("/project/$project_details->id_project/detail/$project_details->id")}}" id="submit-form"
    enctype="multipart/form-data">
    <div class="row p-3">
        @csrf
        <div class="col-12">
            <h5 class="font-weight-bolder text-center mb-3">LAPORAN PENYELESAIAN PELAKSANAAN KONSTRUKSI</h5>
            @if ($contents)
            <button type="button" onclick="cetak({{$project_details['id']}})" data-dismiss="modal"
                class="btn btn-inverse-info btn-sm float-right position-absolute" style="top: 0; right: 0;"><i
                    class="fa fa-print mr-1"></i> Cetak</button>
            @endif
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nomor</label>
                <input type="text" name="data[nomor]" class="form-control" placeholder="Masukkan Nomor..." value="{{$contents ? $contents['nomor'] : ''}}">
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="data[tanggal]" class="form-control" {{$contents ? $contents['tanggal'] : ''}}>
            </div>
            <div class="form-group">
                <label>Konsultan Pengawas</label>
                <input type="text" name="data[konsultan_pengawas]" class="form-control"
                    placeholder="Masukkan Konsultan Pengawas..." {{$contents ? $contents['konsultan_pengawas'] : ''}}>
            </div>
            <div class="form-group">
                <label>Pelaksanaan Perencanaan Pekerjaan</label>
                <table class="table table-responsive text-center" id="list_pekerjaan">
                    <tr class="text-center">
                        <th>NO</th>
                        <th style="width:40%;">JENIS PEKERJAAN</th>
                        <th style="width:25%;">BOBOT (%)</th>
                        <th style="width:35%;">PRESTASI (%)</th>
                        <th style="width:35%;">PROGRES (%)</th>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <input type="text" name="pekerjaan[0][jenis]" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="pekerjaan[0][bobot]" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="pekerjaan[0][prestasi]" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="pekerjaan[0][progress]" class="form-control">
                        </td>
                    </tr>
                </table>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="button" name="add_pekerjaan" id="add_pekerjaan"
                        class="btn btn-outline-primary btn-sm pull-right"><i class="fa fa-plus-circle mr-1"></i>
                        Tambah</button>
                </div>
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
    $(document).ready(function() {
        var i = "{{ isset($mail) && isset($mail['message']) && is_array(json_decode($mail['message'])) ? count(json_decode($mail['message'])) + 1 : 1 }}"
        $('#add_pekerjaan').click(function() {
        i++;
        $('#list_pekerjaan').append('<tr id="tr_list_pekerjaan-' + i + '">' +
            `<td class="text-center">${i}</td>
            <td>
                <input type="text" name="pekerjaan[${i}][jenis]" class="form-control">
            </td>
            <td>
                <input type="number" name="pekerjaan[${i}][bobot]" class="form-control">
            </td>
            <td>
                <input type="number" name="pekerjaan[${i}][prestasi]" class="form-control">
            </td>
            <td class="text-right">
                <input type="number" name="pekerjaan[${i}][progress]" class="form-control">
                <small id="${i}" class="text-danger btn_remove" style="cursor: pointer;"><u>Hapus</u></small>
            </td>` +
            '</tr>');
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            i--;
            $('#tr_list_pekerjaan-' + button_id + '').remove();
        });
    });

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