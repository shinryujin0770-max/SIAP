<form action="{{url("/project/$project_details->id_project/detail/$project_details->id")}}" id="submit-form"
    enctype="multipart/form-data">
    <div class="row p-3">
        @csrf
        <div class="col-12">
            <h5 class="font-weight-bolder text-center mb-3">PENGANTAR</h5>
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
                    value="{{$contents ? $contents['nomor'] : ''}}">
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="data[tanggal]"
                    min="{{\Carbon\Carbon::now()->isoFormat('YYYY-MM-DD')}}" class="form-control"
                    value="{{$contents ? $contents['tanggal'] : ''}}">
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[ded]" type="checkbox" id="ded"
                    {{(isset($contents['ded']) ? $contents['ded'] : '' ) ? 'checked' : '' }}>
                <label for="ded" class="custom-control-label">Progress Detail Engineering Design (DED)</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[jaminan_pembayaran]" type="checkbox"
                    id="jaminan_pembayaran" {{(isset($contents['jaminan_pembayaran']) ? $contents['jaminan_pembayaran']
                    : '' ) ? 'checked' : '' }}>
                <label for="jaminan_pembayaran" class="custom-control-label">Jaminan Pembayaran Uang Muka /
                    Pelaksanaan</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[laporan_akhir]" type="checkbox"
                    id="laporan_akhir" {{(isset($contents['laporan_akhir']) ? $contents['laporan_akhir'] : '' )
                    ? 'checked' : '' }}>
                <label for="laporan_akhir" class="custom-control-label">Laporan Periode/Akhir</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[jaminan_pemeliharaan]" type="checkbox"
                    id="jaminan_pemeliharaan" {{(isset($contents['jaminan_pemeliharaan']) ?
                    $contents['jaminan_pemeliharaan'] : '' ) ? 'checked' : '' }}>
                <label for="jaminan_pemeliharaan" class="custom-control-label">Jaminan Pemeliharaan</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[sts]" type="checkbox" id="sts"
                    {{(isset($contents['sts']) ? $contents['sts'] : '' ) ? 'checked' : '' }}>
                <label for="sts" class="custom-control-label">Surat Tanda Setoran (STS) denda keterlambatan</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input input-payment-type" name="data[verifikasi_ded]" type="checkbox"
                    id="verifikasi_ded" {{(isset($contents['verifikasi_ded']) ? $contents['verifikasi_ded'] : '' )
                    ? 'checked' : '' }}>
                <label for="verifikasi_ded" class="custom-control-label">Surat Hasil Verifikasi DED dari Dinas
                    Teknis</label>
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