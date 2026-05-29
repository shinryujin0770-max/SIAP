<form action="{{route('rfk.import')}}" id="submit-form" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Bulan Tahun</label>
                        <input type="month" name="month" class="form-control">
                        <div class="invalid-feedback text-capitalize error-month"></div>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="file" class="form-control">
                        <div class="invalid-feedback text-capitalize error-file"></div>
                    </div>
                    <hr>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary float-right ml-1">Batal</button>
                    <button type="submit" class="btn btn-primary float-right" id="btn-simpan">Simpan</button>
                </div>
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
            dataType: 'json',
            beforeSend: function() {
                $("#btn-simpan").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Memproses...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                toastr.success("Berhasil Menambahkan Data");
                $('#datatable').DataTable().ajax.reload();
                $("#btn-simpan").removeClass("disabled").html('Simpan').attr('disabled', true);
                $("#modal-add").modal('hide')
            },
            error: function(xhr, textStatus, errorThrown) {
                if(xhr.responseJSON?.error_message) {
                    toastr.error("Gagal Menambahkan Data");
                }
                if(xhr.responseJSON?.errors){
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