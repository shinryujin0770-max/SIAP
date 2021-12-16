<form action="{{url("/project/$project_details->id_project/detail/$project_details->id")}}" id="submit-form"
    enctype="multipart/form-data">
    <div class="row p-3">
        @csrf
        <div class="col-12">
            <h5 class="font-weight-bolder text-center mb-3">BERITA ACARA PEMERIKSAAN PERENCANAAN</h5>
            @if ($contents)
            <button type="button" onclick="cetak({{$project_details['id']}})" data-dismiss="modal"
                class="btn btn-inverse-info btn-sm float-right position-absolute" style="top: 0; right: 0;"><i
                    class="fa fa-print mr-1"></i> Cetak</button>
            @endif
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nomor</label>
                <input type="text" name="data[nomor]" class="form-control" placeholder="Masukkan Nomor..."
                    value="{{isset($contents['nomor']) ? $contents['nomor'] : ''}}">
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="data[tanggal]" class="form-control"
                    value="{{isset($contents['tanggal']) ? $contents['tanggal'] : ''}}">
            </div>
            <div class="form-group">
                <label>Pelaksanaan Perencanaan Pekerjaan</label>
                <table class="table table-responsive text-center" id="list_pekerjaan">
                    <tr class="text-center">
                        <th>NO</th>
                        <th style="width:40%;">JENIS PEKERJAAN</th>
                        <th style="width:25%;">BAIK/KURANG BAIK</th>
                        <th style="width:35%;">KETERANGAN</th>
                    </tr>
                    @if ($pekerjaan)
                    @foreach ($pekerjaan as $i => $item)
                    <tr id="tr_list_pekerjaan-{{$i + 1}}">
                        <td class="text-center">{{$i + 1}}</td>
                        <td>
                            <input type="text" name="pekerjaan[{{$i}}][jenis]" class="form-control"
                                value="{{$item['jenis']}}">
                        </td>
                        <td class="px-1 px-xl-3">
                            <div class="d-flex justify-content-between align-items-center px-1">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input input-payment-type"
                                        name="pekerjaan[{{$i}}][kondisi]" type="radio" id="baik_{{$i + 1}}" value="baik"
                                        {{$item['kondisi']=='baik' ? 'checked' : '' }}>
                                    <label for="baik_{{$i + 1}}" class="custom-control-label">Baik</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input input-payment-type"
                                        name="pekerjaan[{{$i}}][kondisi]" type="radio" id="kurang_baik_{{$i + 1}}"
                                        value="kurang_baik" {{$item['kondisi']=='kurang_baik' ? 'checked' : '' }}>
                                    <label for="kurang_baik_{{$i + 1}}" class="custom-control-label">Kurang
                                        Baik</label>
                                </div>
                            </div>
                        </td>
                        <td class="text-right">
                            <input type="text" name="pekerjaan[{{$i}}][keterangan]" class="form-control"
                                value="{{$item['keterangan']}}">
                            <small id="{{$i + 1}}" class="text-danger btn_remove"
                                style="cursor: pointer;"><u>Hapus</u></small>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <input type="text" name="pekerjaan[0][jenis]" class="form-control">
                        </td>
                        <td class="px-1 px-xl-3">
                            <div class="d-flex justify-content-between align-items-center px-1">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input input-payment-type" name="pekerjaan[0][kondisi]"
                                        type="radio" id="baik" value="baik">
                                    <label for="baik" class="custom-control-label">Baik</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input input-payment-type" name="pekerjaan[0][kondisi]"
                                        type="radio" id="kurang_baik" value="kurang_baik">
                                    <label for="kurang_baik" class="custom-control-label">Kurang
                                        Baik</label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="pekerjaan[0][keterangan]" class="form-control">
                        </td>
                    </tr>
                    @endif
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
        var i = "{{ isset($pekerjaan) ? count($pekerjaan) : 1 }}"
        $('#add_pekerjaan').click(function() {
        i++;
        $('#list_pekerjaan').append('<tr id="tr_list_pekerjaan-' + i + '">' +
            `<td class="text-center">${i}</td>
            <td>
                <input type="text" name="pekerjaan[${i}][jenis]" class="form-control">
            </td>
            <td class="px-1 px-xl-3">
                <div class="d-flex justify-content-between align-items-center px-1">
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input input-payment-type" name="pekerjaan[${i}][kondisi]"
                            type="radio" id="baik_${i}" value="baik">
                        <label for="baik_${i}" class="custom-control-label">Baik</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input input-payment-type" name="pekerjaan[${i}][kondisi]"
                            type="radio" id="kurang_baik_${i}" value="kurang_baik">
                        <label for="kurang_baik_${i}" class="custom-control-label">Kurang
                            Baik</label>
                    </div>
                </div>
            </td>
            <td class="text-right">
                <input type="text" name="pekerjaan[${i}][keterangan]" class="form-control">
                <small id="${i}" class="text-danger btn_remove" style="cursor: pointer;"><u>Hapus</u></small>
            </td>` +
            '</tr>');
        });

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            console.log(button_id);
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