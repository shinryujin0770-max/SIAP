<form action="{{url("/project/deleteMany/$ids")}}" id="delete-form">
    <div class="row p-3">
        @csrf
        <div class="col-12 d-flex justify-content-center align-items-center">
            <img src="{{asset('/assets/projects/delete.svg')}}" alt="perencanaan" width="250" height="250" class="mt-3">
        </div>
        <div class="col-12">
            <h6 class="text-center mb-3">
                Apakah anda yakin akan menghapus pekerjaan ini ?
            </h6>
        </div>
        <div class="col-sm-12">
            <div class="mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-gradient-primary mr-2" id="btn-delete">Hapus</button>
                <button type="button" data-dismiss="modal" class="btn btn-inverse-secondary ml-1">Batal</button>
            </div>
        </div>
    </div>
</form>

<script>
    $("#delete-form").submit(function() {
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
                $("#btn-delete").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Menghapus...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'true') {
                    setTimeout(function() {
                        $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', true);
                        window.location.href = "<?= url('project') ?>";
                    }, 1000);
                } else {
                    $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', false);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                if(xhr.responseJSON.errors){
                    xhr.responseJSON.errors.map((value, key) => {
                        $(".select-" + value.code).next().children().children().addClass('has-error').focus()
                        $(".error-" + value.code).addClass("d-block").html('<strong>' + value.message + '</strong>');
                    })
                }
                $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', false);
            }
        });

        return false;
    });
</script>