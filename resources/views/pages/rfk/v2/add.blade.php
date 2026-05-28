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

<form id="form-add" method="POST">
    @csrf

    <!-- Hidden Fields -->
    @if(isset($parent))
    <input type="hidden" name="parent_id" value="{{$parent->id}}">
    <input type="hidden" name="type" value="{{$child_type}}">
    <input type="hidden" name="opd" value="{{$parent->rfk->id_opd}}">
    @endif
    <input type="hidden" name="month" value="{{$year_month ?? date('Y-m')}}">

    <!-- Context Info -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Bulan & Tahun</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{$year_month ?? date('Y-m')}}" readonly>
        </div>
    </div>

    @if(isset($parent))
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">{{$parent_type_label ?? 'Parent'}}</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" value="{{$parent->uraian ?? $parent->code}}" readonly>
        </div>
    </div>
    @endif

    <!-- Basic Fields -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Kode <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="code" id="code" value="{{$suggested_code ?? ''}}" required>
            <small class="form-text text-muted">Kode akan digenerate otomatis jika dikosongkan</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Uraian {{$type_label ?? 'Item'}} <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <textarea class="form-control" name="uraian" id="uraian" rows="3" required></textarea>
        </div>
    </div>

    @if(isset($child_type) && $child_type === 'sub_kegiatan')
    <!-- Financial Fields (Sub Kegiatan Only) -->
    <hr class="my-4">
    <h5 class="mb-3">Data Keuangan</h5>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Pagu Anggaran <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control uang" name="pagu_anggaran" id="pagu_anggaran" value="0" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan Bulan Ini <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control uang" name="bulan_ini" id="bulan_ini" value="0" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan Bulan Lalu</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="bulan_lalu_display" id="bulan_lalu_display" value="0" readonly>
            <input type="hidden" name="bulan_lalu" id="bulan_lalu" value="0">
            <small class="form-text text-muted">Otomatis dari bulan sebelumnya</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan s.d. Bulan Ini</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="sd_bulan_ini_display" id="sd_bulan_ini_display" value="0" readonly>
            <input type="hidden" name="sd_bulan_ini" id="sd_bulan_ini" value="0">
            <small class="form-text text-muted">Otomatis: Bulan Lalu + Bulan Ini</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Sisa Anggaran</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="sisa_anggaran_display" id="sisa_anggaran_display" value="0" readonly>
            <input type="hidden" name="sisa_anggaran" id="sisa_anggaran" value="0">
            <small class="form-text text-muted">Otomatis: Pagu Anggaran - s.d. Bulan Ini</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Keuangan (%)</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="keuangan_display" value="0.00" readonly>
            <input type="hidden" name="keuangan" id="keuangan" value="0">
            <small class="form-text text-muted">Otomatis: (Bulan Ini / Pagu Anggaran) × 100</small>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Realisasi Fisik (%) <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input type="number" class="form-control" name="fisik" id="fisik" min="0" max="100" step="0.01" value="0" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Keterangan</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
        </div>
    </div>
    @else
    <!-- For Program and Kegiatan - Hidden fields with default values -->
    <input type="hidden" name="pagu_anggaran" id="pagu_anggaran" value="0">
    <input type="hidden" name="bulan_lalu" id="bulan_lalu" value="0">
    <input type="hidden" name="bulan_ini" id="bulan_ini" value="0">
    <input type="hidden" name="angkas" id="angkas" value="0">
    <input type="hidden" name="fisik" id="fisik" value="0">
    <input type="hidden" name="keterangan" id="keterangan" value="">
    @endif

    <!-- Buttons -->
    <div class="form-group row">
        <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btn-submit">Simpan</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        const isSubKegiatan = {{ isset($child_type) && $child_type === 'sub_kegiatan' ? 'true' : 'false' }};

        // Initialize money mask
        $('.uang').mask('#.##0,00', {reverse: true});

        // Auto-calculate functions (only for Sub Kegiatan)
        @if(isset($child_type) && $child_type === 'sub_kegiatan')
        function parseMoneyValue(value) {
            if (!value) return 0;
            // Remove dots and replace comma with dot
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

            // Calculate s.d. bulan ini
            const sdBulanIni = bulanLalu + bulanIni;
            $('#sd_bulan_ini').val(sdBulanIni);
            $('#sd_bulan_ini_display').val(formatMoney(sdBulanIni));

            // Calculate sisa anggaran
            const sisaAnggaran = pagu - sdBulanIni;
            $('#sisa_anggaran').val(sisaAnggaran);
            $('#sisa_anggaran_display').val(formatMoney(sisaAnggaran));

            // Calculate realisasi keuangan percentage: (Bulan Ini / Pagu Anggaran) × 100
            const keuanganPercent = pagu > 0 ? (bulanIni / pagu) * 100 : 0;
            $('#keuangan').val(keuanganPercent);
            $('#keuangan_display').val(keuanganPercent.toFixed(2));
        }

        // Trigger calculation on input change
        $('#pagu_anggaran, #bulan_ini').on('input change', function() {
            calculate();
        });

        // Initial calculation
        calculate();
        @endif

        // Form submission
        $('#form-add').submit(function(e) {
            e.preventDefault();

            const formData = {};
            $(this).serializeArray().forEach(function(item) {
                formData[item.name] = item.value;
            });

            // Remove display fields
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
                    $('#btn-submit').prop('disabled', false).html('Simpan');

                    if (response.success) {
                        toastr.success(response.message || 'Berhasil Menyimpan Data');
                        $('#modal-add').modal('hide');
                        // Reload the main table
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
                    $('#btn-submit').prop('disabled', false).html('Simpan');

                    let errorMessage = 'Gagal menyimpan data';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors && Array.isArray(xhr.responseJSON.errors)) {
                            xhr.responseJSON.errors.forEach(function(error) {
                                toastr.error(error.message);
                            });
                            return;
                        }
                    }
                    toastr.error(errorMessage);
                }
            });
        });
    });
</script>
