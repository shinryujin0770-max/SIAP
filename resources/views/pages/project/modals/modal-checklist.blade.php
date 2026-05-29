<form action="{{url("/project/$project->id/checklist")}}" id="submit-form"
    enctype="multipart/form-data">
    @csrf
    <div class="row p-3">
        <div class="col-12 d-flex justify-content-center align-items-center position-relative">
            <button type="button" onclick="cetak({{$project['content'] ? $project['id'] : $pengantar[0]['id']}})"
                data-dismiss="modal" class="btn btn-inverse-info btn-sm float-right position-absolute"
                style="top: 0px; right: 0px;"><i class="fa fa-print mr-1"></i> Lihat Pengantar</button>
            <img src="{{asset('/assets/projects/checklist.svg')}}" alt="perencanaan" width="250" height="250"
                class="mt-3">
        </div>
        <div class="col-12">
            <h6 class="text-center mb-3 font-sm">Buka Dokumen Checklist untuk menyelesaikan pembayaran ini.
            </h6>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Nomor</label>
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text">600/</span>
                    </div>
                    <input type="text" class="form-control" name="nomor" width="10px" placeholder="No Checklist..."
                        required value="{{$checklist ? $checklist['nomor'] : ''}}" {{$checklist ? 'readonly' : '' }}>
                    <div class="input-group-append">
                        <span class="input-group-text">/20.02.02/{{\Carbon\Carbon::parse()->format('Y')}}</span>
                    </div>
                </div>
                @if (!$checklist)
                <small class="text-muted"><i class="fas fa-info-circle"></i> Nomor Checklist Terakhir : </small><small
                    class="text-danger">{{$last_no}}</small>
                @endif
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                    value="{{$checklist ? $checklist['tanggal'] : ''}}">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-gradient-primary mr-2">Buka</button>
                <button type="button" data-dismiss="modal" class="btn btn-inverse-secondary ml-1">Batal</button>
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
                        cetakChecklist('{{$project->id}}', "{{str_replace(' ', '_', $type)}}")

                        window.location.href = "<?= url('project/') .'/'.$project->id_project ?>";
                    }, 1000);
                } else {
                    setTimeout(function() {
                        toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                        toastr.error(str, '', []);
                    }, 1000);
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