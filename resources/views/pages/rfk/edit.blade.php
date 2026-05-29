<form action="{{url("/rfk")}}" id="submit-form" enctype="multipart/form-data">
    @csrf
    <div class="row p-3">
        <div class="col-12">
            <div class="form-group">
                <label>Bulan Tahun</label>
                <input type="month" name="month" class="form-control" value="{{$year_month}}">
                <div class="invalid-feedback text-capitalize error-month"></div>
            </div>
            <div class="form-group">
                <label>Kode</label>
                <input type="text" name="code" class="form-control" value="{{$rfk_detail['code']}}">
                <div class="invalid-feedback text-capitalize error-code"></div>
            </div>
            <div class="form-group">
                <label>Uraian Urusan, Organisasi, Program, Kegiatan, dan Sub Kegiatan</label>
                <input type="text" name="uraian" class="form-control" value="{{$rfk_detail['uraian']}}">
                <div class="invalid-feedback text-capitalize error-uraian"></div>
            </div>
            <div class="form-group">
                <label>Pagu Anggaran</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="pagu_anggaran" class="form-control uang" id="pagu_anggaran" placeholder="0"
                        value="{{$rfk_detail['pagu_anggaran']}}" onkeyup="handleChangePaguAnggaran(this.value)">
                </div>
                <div class="invalid-feedback text-capitalize error-pagu_anggaran"></div>
            </div>
            <div class="form-group">
                <label>Realisasi Keuangan Bulan Lalu</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="bulan_lalu" class="form-control uang" placeholder="0"
                        value="{{$rfk_detail['bulan_lalu']}}">
                </div>
                <div class="invalid-feedback text-capitalize error-bulan_lalu"></div>
            </div>
            <div class="form-group">
                <label>Realisasi Keuangan Bulan Ini</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="bulan_ini" class="form-control uang" placeholder="0"
                        value="{{$rfk_detail['bulan_ini']}}">
                </div>
                <div class="invalid-feedback text-capitalize error-bulan_ini"></div>
            </div>
            <div class="form-group">
                <label>Realisasi Keuangan s.d. Bulan Ini</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="sd_bulan_ini" class="form-control uang" id="sd_bulan_ini" placeholder="0"
                        value="{{$rfk_detail['sd_bulan_ini']}}" onkeyup="handleChangeSdBulanIni(this.value)">
                </div>
                <div class="invalid-feedback text-capitalize error-sd_bulan_ini"></div>
            </div>
            <div class="form-group">
                <label>Sisa Anggaran</label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="sisa_anggaran" readonly class="form-control uang" id="sisa_anggaran"
                        value="{{$rfk_detail['sisa_anggaran']}}" placeholder="0">
                </div>
                <div class="invalid-feedback text-capitalize error-sisa_anggaran"></div>
            </div>
            <div class="form-group">
                <label>Anggaran Kas Sampai dengan Bulan Ini </label>
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            Rp
                        </div>
                    </div>
                    <input type="text" name="angkas" class="form-control uang" id="angkas" placeholder="0"
                        value="{{$rfk_detail['angkas']}}" onkeyup="handleChangeAngkas(this.value)">
                </div>
                <div class="invalid-feedback text-capitalize error-angkas"></div>
            </div>
            <div class="form-group">
                <label>Anggaran Kas Prosentase</label>
                <div class="input-group mb-3">
                    <input type="number" step=".01" readonly class="form-control" name="angkas_prosentase"
                        id="angkas_prosentase" value="{{$rfk_detail['angkas_prosentase']}}" placeholder="0">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="invalid-feedback text-capitalize error-prosentase"></div>
            </div>
            <div class="form-group">
                <label>Realisasi Keuangan</label>
                <div class="input-group mb-3">
                    <input type="number" step=".01" readonly class="form-control" name="keuangan" id="keuangan"
                        value="{{$rfk_detail['keuangan']}}" placeholder="0">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="invalid-feedback text-capitalize error-keuangan"></div>
            </div>
            <div class="form-group">
                <label>Realisasi Fisik</label>
                <div class="input-group mb-3">
                    <input type="number" step=".01" class="form-control" name="fisik" placeholder="0"
                        value="{{$rfk_detail['fisik']}}">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                <div class="invalid-feedback text-capitalize error-fisik"></div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" class="form-control" name="keterangan" value="{{$rfk_detail['keterangan']}}">
                <div class="invalid-feedback text-capitalize error-keterangan"></div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-gradient-primary mr-2" id="btn-add">Simpan</button>
                <button type="button" data-dismiss="modal" class="btn btn-inverse-secondary ml-1">Batal</button>
            </div>
        </div>
    </div>
</form>
<script>
    $( '.uang' ).mask('#.##0,00', {reverse: true});
    pagu_anggaran = parseFloat("{{$rfk_detail['pagu_anggaran']}}");
    sd_bulan_ini = parseFloat("{{$rfk_detail['sd_bulan_ini']}}");
    angkas = parseFloat("{{$rfk_detail['angkas']}}");

    function convertToNumber(value) {
        let formattedNumber = value;
        formattedNumber = formattedNumber.replace(/\./g, '');
        formattedNumber = formattedNumber.replace(',', '.');

        return parseFloat(formattedNumber);
    }

    function handleChangePaguAnggaran(value) {
        pagu_anggaran = convertToNumber(value);
        calculate()
    }

    function handleChangeSdBulanIni(value) {
        sd_bulan_ini = convertToNumber(value);
        calculate()
    }

    function handleChangeAngkas(value) {
        angkas = convertToNumber(value);
        calculate()
    }

    function calculate() {
        const sisa_anggaran = pagu_anggaran - sd_bulan_ini;
        const angkas_prosentase = (angkas / pagu_anggaran * 100).toFixed(2);
        const keuangan = (sd_bulan_ini / pagu_anggaran * 100).toFixed(2);

        console.log(pagu_anggaran, sd_bulan_ini, angkas)
        console.log(sisa_anggaran, angkas_prosentase, keuangan)

        $('#sisa_anggaran').val(sisa_anggaran)
        $('#angkas_prosentase').val(angkas_prosentase)
        $('#keuangan').val(keuangan)
    }
    calculate()

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
                $("#btn-add").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Memproses...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'true') {
                    setTimeout(function() {
                        getListData();
                        $("#btn-add").removeClass("disabled").html('Simpan').attr('disabled', true);

                        toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                        toastr.success('Berhasil Menyimpan Data', '', []);
                        $("#modal-add").modal('hide')
                    }, 1000);
                } else {
                    setTimeout(function() {
                        toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                        toastr.error(str, '', []);
                    }, 1000);
                    $("#btn-add").removeClass("disabled").html('Simpan').attr('disabled', false);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                if(xhr.responseJSON.errors){
                    xhr.responseJSON.errors.map((value, key) => {
                        $(".select-" + value.code).next().children().children().addClass('has-error').focus()
                        $(".error-" + value.code).addClass("d-block").html('<strong>' + value.message + '</strong>');
                    })
                }
                $("#btn-add").removeClass("disabled").html('Simpan').attr('disabled', false);
            }
        });

        return false;
    });
</script>