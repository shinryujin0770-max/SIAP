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
                <input type="text" name="data[nomor]" class="form-control" placeholder="Nomor dikeluarkan Rekanan/Pelaksana Pekerjaan Konstruksi"
                    value="" readonly>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="data[tanggal]" class="form-control" value="{{isset($contents['tanggal']) ? $contents['tanggal']
                    : '' }}">
            </div>
            <div class="form-group">
                <label>CV/PT Konsultan Pengawas</label>
                <input type="text" name="data[konsultan_pengawas_nama]" class="form-control"
                    placeholder="Masukkan Konsultan Pengawas..." value="{{isset($contents['konsultan_pengawas_nama']) ? $contents['konsultan_pengawas_nama']
                    : '' }}">
            </div>
            <div class="form-group">
                <label>Direktur Konsultan Pengawas</label>
                <input type="text" name="data[konsultan_pengawas_direktur]" class="form-control"
                    placeholder="Masukkan Konsultan Pengawas..." value="{{isset($contents['konsultan_pengawas_direktur']) ? $contents['konsultan_pengawas_direktur']
                    : '' }}">
            </div>
            <div class="form-group">
                <label>Pelaksanaan Perencanaan Pekerjaan</label>
                <table class="table table-responsive text-center" id="list_pekerjaan">
                    <tr class="text-center">
                        {{-- <th>NO</th> --}}
                        <th style="width:40%;">JENIS PEKERJAAN</th>
                        <th style="width:20%;">BOBOT (%)</th>
                        <th style="width:20%;">PRESTASI (%)</th>
                        <th style="width:20%;">PROGRES (%)</th>
                    </tr>
                    @if ($pekerjaan)
                    @foreach ($pekerjaan as $i => $item)
                    <tr id="tr_list_pekerjaan-{{$i + 1}}">
                        {{-- <td class="text-center">{{$i + 1}}</td> --}}
                        <td>
                            <input type="text" name="pekerjaan[{{$i}}][jenis]" id="jenis-{{$i}}" class="form-control"
                                value="{{$item['jenis']}}">
                        </td>
                        <td>
                            <input type="number" step="any" name="pekerjaan[{{$i}}][bobot]" id="bobot-{{$i}}" onkeyup="calculateProgress({{$i}})" class="form-control"
                                value="{{$item['bobot']}}">
                        </td>
                        <td>
                            <input type="number" step="any" name="pekerjaan[{{$i}}][prestasi]" id="prestasi-{{$i}}" onkeyup="calculateProgress({{$i}})" class="form-control"
                                value="{{$item['prestasi']}}">
                        </td>
                        <td class="text-right">
                            <input type="number" step="any" name="pekerjaan[{{$i}}][progress]" id="progress-{{$i}}" class="form-control" readonly
                                value="{{$item['progress']}}">
                            <small id="{{$i + 1}}" class="text-danger btn_remove"
                                style="cursor: pointer;"><u>Hapus</u></small>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        {{-- <td class="text-center">1</td> --}}
                        <td>
                            <input type="text" name="pekerjaan[0][jenis]" id="jenis-0" class="form-control">
                        </td>
                        <td>
                            <input type="number" step="any" name="pekerjaan[0][bobot]" id="bobot-0" onkeyup="calculateProgress(0)" class="form-control">
                        </td>
                        <td>
                            <input type="number" step="any" name="pekerjaan[0][prestasi]" id="prestasi-0" onkeyup="calculateProgress(0)" class="form-control">
                        </td>
                        <td>
                            <input type="number" step="any" name="pekerjaan[0][progress]" id="progress-0" class="form-control" readonly>
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
            // <td class="text-center">${i}</td>
            `<td>
                <input type="text" name="pekerjaan[${i}][jenis]" id="jenis-${i}" class="form-control">
            </td>
            <td>
                <input type="number" step="any" name="pekerjaan[${i}][bobot]" id="bobot-${i}" onkeyup="calculateProgress(${i})" class="form-control">
            </td>
            <td>
                <input type="number" step="any" name="pekerjaan[${i}][prestasi]" id="prestasi-${i}" onkeyup="calculateProgress(${i})" class="form-control">
            </td>
            <td class="text-right">
                <input type="number" step="any" name="pekerjaan[${i}][progress]" id="progress-${i}" class="form-control">
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

    function calculateProgress(i) {
        const bobot = $(`#bobot-${i}`).val();
        const prestasi = $(`#prestasi-${i}`).val();
        const progress = (parseFloat(prestasi) / parseFloat(bobot) * 100).toFixed(2);
        console.log(prestasi, bobot)
        $(`#progress-${i}`).val(progress);
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