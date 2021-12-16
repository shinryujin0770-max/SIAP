<form action="{{route('project.store')}}" id="submit-form" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            @foreach (json_decode($payment_detail['content'], true) as $key => $item)
                            <div class="form-group">
                                <label>{{$item['title']}}</label>
                                @if (strpos(strtolower($item['title']), 'nomor') !== false)
                                <input type="text" name="payment[{{$key}}]" class="form-control"
                                    placeholder="Masukkan {{$item['title']}}...">
                                @else
                                <input type="date" name="payment[{{$key}}]" class="form-control"
                                    placeholder="Masukkan {{$item['title']}}...">
                                @endif
                            </div>
                            @endforeach
                            <button type="button" data-dismiss="modal"
                                class="btn btn-secondary float-right ml-1">Batal</button>
                            <button type="submit" class="btn btn-primary float-right" id="btn-simpan">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" onclick="cetak({{$project_payment['id_project']}})" data-dismiss="modal"
        class="btn btn-primary float-right">Cetak</button>
</form>

<script>
    function cetak(id) {
        window.open(
            "<?= url('project/print') . '/' ?>" + id + '?kategori={{$kategori}}&page=berita_acara',
            '_blank' // <- This is what makes it open in a new window.
        );
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