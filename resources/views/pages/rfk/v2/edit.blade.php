<div class="loader" style="display: none">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<h3 class="text-center mb-4">{{$page_subtitle}}</h3>

@if(isset($breadcrumb) && count($breadcrumb) > 0)
<div class="alert alert-info mb-3">
    <strong>Hierarki:</strong>
    @foreach($breadcrumb as $index => $item)
        {{ $item['uraian'] ?? $item['code'] }}
        @if($index < count($breadcrumb) - 1)
            <i class="fas fa-chevron-right mx-2"></i>
        @endif
    @endforeach
</div>
@endif

<div class="alert alert-secondary mb-3">
    <small><strong>Terakhir diubah:</strong> {{$rfk_detail->updated_at->format('d/m/Y H:i:s')}}</small>
</div>

<form id="form-edit" method="POST">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="id" value="{{$rfk_detail->id}}">
    <input type="hidden" name="parent_id" value="{{$rfk_detail->parent_id}}">
    <input type="hidden" name="type" value="{{$rfk_detail->type}}">
    <input type="hidden" name="month" value="{{$year_month}}">
    @if($rfk_detail->rfk)
    <input type="hidden" name="opd" value="{{$rfk_detail->rfk->id_opd}}">
    @endif

    <!-- Context Info -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Bulan & Tahun</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{$year_month}}" readonly>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Tipe</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{$type_label}}" readonly>
        </div>
    </div>

    <!-- Basic Fields -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Kode</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="code" id="code" value="{{$rfk_detail->code}}" readonly>
            <small class="form-text text-muted">Kode tidak dapat diubah setelah dibuat</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Uraian {{$type_label}} <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <textarea class="form-control" name="uraian" id="uraian" rows="3" required>{{$rfk_detail->uraian}}</textarea>
        </div>
    </div>

    @if($rfk_detail->type === 'sub_kegiatan')
    <!-- Financial Fields (Sub Kegiatan Only) -->
    <hr class="my-4">
    <h5 class="mb-3">Data Keuangan</h5>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Pagu Anggaran <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control uang" name="pagu_anggaran" id="pagu_anggaran" value="{{number_format($rfk_detail->pagu_anggaran, 2, ',', '.')}}" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan Bulan Ini <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control uang" name="bulan_ini" id="bulan_ini" value="{{number_format($rfk_detail->bulan_ini, 2, ',', '.')}}" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan Bulan Lalu</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="bulan_lalu_display" id="bulan_lalu_display" value="{{number_format($rfk_detail->bulan_lalu, 2, ',', '.')}}" readonly>
            <input type="hidden" name="bulan_lalu" id="bulan_lalu" value="{{$rfk_detail->bulan_lalu}}">
            <small class="form-text text-muted">Otomatis dari bulan sebelumnya</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan s.d. Bulan Ini</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="sd_bulan_ini_display" id="sd_bulan_ini_display" value="{{number_format($rfk_detail->sd_bulan_ini, 2, ',', '.')}}" readonly>
            <input type="hidden" name="sd_bulan_ini" id="sd_bulan_ini" value="{{$rfk_detail->sd_bulan_ini}}">
            <small class="form-text text-muted">Otomatis: Bulan Lalu + Bulan Ini</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Sisa Anggaran</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="sisa_anggaran_display" id="sisa_anggaran_display" value="{{number_format($rfk_detail->sisa_anggaran, 2, ',', '.')}}" readonly>
            <input type="hidden" name="sisa_anggaran" id="sisa_anggaran" value="{{$rfk_detail->sisa_anggaran}}">
            <small class="form-text text-muted">Otomatis: Pagu Anggaran - s.d. Bulan Ini</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Anggaran Kas</label>
        <div class="col-sm-9">
            <input type="text" class="form-control uang" name="angkas" id="angkas" value="{{number_format($rfk_detail->angkas, 2, ',', '.')}}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan (%)</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="keuangan_display" value="{{number_format($rfk_detail->keuangan, 2, '.', '')}}" readonly>
            <input type="hidden" name="keuangan" id="keuangan" value="{{$rfk_detail->keuangan}}">
            <small class="form-text text-muted">Otomatis: (Bulan Ini / Pagu Anggaran) × 100</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Fisik (%) <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="number" class="form-control" name="fisik" id="fisik" min="0" max="100" step="0.01" value="{{$rfk_detail->fisik}}" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Keterangan</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="keterangan" id="keterangan" rows="3">{{$rfk_detail->keterangan}}</textarea>
        </div>
    </div>
    @else
    <!-- For Program and Kegiatan - Show read-only summary -->
    <hr class="my-4">
    <h5 class="mb-3">Data Keuangan <small class="text-muted">(Agregasi dari Sub Kegiatan)</small></h5>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Data keuangan untuk {{$type_label}} dihitung otomatis dari agregasi Sub Kegiatan dibawahnya.
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Pagu Anggaran</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{number_format($rfk_detail->pagu_anggaran, 2, ',', '.')}}" readonly>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan s.d. Bulan Ini</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{number_format($rfk_detail->sd_bulan_ini, 2, ',', '.')}}" readonly>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Sisa Anggaran</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{number_format($rfk_detail->sisa_anggaran, 2, ',', '.')}}" readonly>
        </div>
    </div>
    @endif

    <!-- Buttons -->
    <div class="form-group row">
        <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btn-submit">Simpan Perubahan</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        const isSubKegiatan = {{ $rfk_detail->type === 'sub_kegiatan' ? 'true' : 'false' }};

        // Initialize money mask
        $('.uang').mask('#.##0,00', {reverse: true});

        // Auto-calculate functions (only for Sub Kegiatan)
        @if($rfk_detail->type === 'sub_kegiatan')
        function parseMoneyValue(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatMoney(value) {
            return new Intl.NumberFormat('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        }

        function calculate() {
            const pagu = parseMoneyValue($('#pagu_anggaran').val());
            const bulanLalu = parseMoneyValue($('#bulan_lalu').val());
            const bulanIni = parseMoneyValue($('#bulan_ini').val());

            const sdBulanIni = bulanLalu + bulanIni;
            $('#sd_bulan_ini').val(sdBulanIni);
            $('#sd_bulan_ini_display').val(formatMoney(sdBulanIni));

            const sisaAnggaran = pagu - sdBulanIni;
            $('#sisa_anggaran').val(sisaAnggaran);
            $('#sisa_anggaran_display').val(formatMoney(sisaAnggaran));

            // Calculate realisasi keuangan percentage: (Bulan Ini / Pagu Anggaran) × 100
            const keuanganPercent = pagu > 0 ? (bulanIni / pagu) * 100 : 0;
            $('#keuangan').val(keuanganPercent);
            $('#keuangan_display').val(keuanganPercent.toFixed(2));
        }

        $('#pagu_anggaran, #bulan_ini').on('input change', function() {
            calculate();
        });

        calculate();
        @endif

        // Form submission
        $('#form-edit').submit(function(e) {
            e.preventDefault();

            const formData = {};
            $(this).serializeArray().forEach(function(item) {
                formData[item.name] = item.value;
            });

            delete formData.bulan_lalu_display;
            delete formData.sd_bulan_ini_display;
            delete formData.sisa_anggaran_display;
            delete formData.keuangan_display;

            $.ajax({
                type: 'POST',
                url: "{{ route('rfk-v2.store') }}",
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                beforeSend: function() {
                    $('#btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Menyimpan...');
                    $('.loader').show();
                },
                success: function(response) {
                    $('.loader').hide();
                    $('#btn-submit').prop('disabled', false).html('Simpan Perubahan');

                    if (response.success) {
                        toastr.success(response.message || 'Berhasil Menyimpan Data');
                        $('#modal-add').modal('hide');
                        if (typeof getListData === 'function') {
                            getListData();
                        }
                    } else {
                        if (response.errors && Array.isArray(response.errors)) {
                            response.errors.forEach(function(error) {
                                toastr.error(error.message);
                            });
                        } else {
                            toastr.error(response.message || 'Gagal menyimpan data');
                        }
                    }
                },
                error: function(xhr) {
                    $('.loader').hide();
                    $('#btn-submit').prop('disabled', false).html('Simpan Perubahan');

                    let errorMessage = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        });
    });
</script>
