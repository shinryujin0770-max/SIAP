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
                <label>Nomor DPA/DPPA</label>
                <input type="text" name="data[nomor_dpa]" class="form-control" placeholder="Masukkan Nomor..."
                    value="{{isset($contents['nomor_dpa']) ? $contents['nomor_dpa'] : ''}}">
            </div>
            <div class="form-group">
                <label>Tanggal DPA/DPPA</label>
                <input type="date" name="data[tanggal_dpa]" class="form-control" value="{{isset($contents['tanggal_dpa']) ? $contents['tanggal_dpa']
                    : '' }}">
            </div>
            <div class="form-group">
                <label>NPWP</label>
                <input type="number" name="data[npwp]" class="form-control" placeholder="Masukkan NPWP..."
                    value="{{isset($contents['npwp']) ? $contents['npwp'] : '' }}">
            </div>
            <div class="form-group">
                <label>Bank Referensi</label>
                <input type="text" name="data[bank]" class="form-control" placeholder="Masukkan Bank Referensi..."
                    value="{{isset($contents['bank']) ? $contents['bank'] : '' }}">
            </div>
            <div class="form-group">
                <label>Nomor Rekening Bank</label>
                <input type="text" name="data[nomor_rekening]" class="form-control"
                    placeholder="Masukkan Nomor Rekening Bank..."
                    value="{{isset($contents['nomor_rekening']) ? $contents['nomor_rekening'] : '' }}">
            </div>
            <div class="form-group">
                <label>Nama Pemilik Rekening</label>
                <input type="text" name="data[nama_rekening]" class="form-control"
                    placeholder="Masukkan Nama Pemilik Rekening..."
                    value="{{isset($contents['nama_rekening']) ? $contents['nama_rekening'] : '' }}">
            </div>
            <div class="form-group">
                <label>Masa Pemeliharaan</label>
                <input type="number" name="data[masa_pemeliharaan]" class="form-control"
                    placeholder="Masukkan Masa Pemeliharaan..."
                    value="{{isset($contents['masa_pemeliharaan']) ? $contents['masa_pemeliharaan'] : '' }}">
            </div>
            <div class="form-group">
                <label>Tanggal Penyelesaian Pekerjaan</label>
                <input type="date" name="data[penyelesaian_pekerjaan]" class="form-control"
                    value="{{isset($contents['penyelesaian_pekerjaan']) ? $contents['penyelesaian_pekerjaan'] : ''}}">
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

                        window.location.href = "<?= url('project/') .'/'.$project_details->id_project ?>";
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