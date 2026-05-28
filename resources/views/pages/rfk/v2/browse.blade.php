@extends('adminlte::page')

@section('title', 'RFK')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
@php
$hideOpdFilter = $is_admin_rfk ?? false;
$defaultOpdId = $id_opd ?? null;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card shadow-none rounded-lg">
            <div class="card-header border-0">
                <div class="row mb-3">
                    <div class="col-sm-12 col-lg-3" @if($hideOpdFilter) style="display: none;" @endif>
                        <label for="filter-opd">OPD</label>
                        <select id="filter-opd" class="form-control">
                            <option value="">Pilih OPD</option>
                            @foreach($opds as $opd)
                            <option value="{{$opd->id}}" @if($hideOpdFilter && $defaultOpdId==$opd->id) selected
                                @endif>{{$opd->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-3">
                        <label for="filter-month">Bulan</label>
                        <input type="month" id="filter-month" class="form-control"
                            value="{{date('Y-m', strtotime('-1 month'))}}">
                    </div>
                    @if(($is_superadmin ?? false))
                    <div class="col-sm-12 col-lg-3 d-flex align-items-end mt-3 mt-lg-0">
                        <button type="button" id="btn-resync-previous" class="btn btn-warning btn-block">
                            <i class="fa fa-history mr-1"></i> Sync Ulang
                        </button>
                    </div>
                    @endif
                    <div class="col-sm-12 col-lg-3 d-flex align-items-end mt-3 mt-lg-0">
                        <div class="btn-group btn-block">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-file-excel mr-1"></i> Laporan
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" id="export-lengkap">
                                    <i class="fa fa-file-excel mr-1"></i> Laporan Lengkap
                                </a>
                                @if($is_superadmin ?? false)
                                <a class="dropdown-item" href="#" id="export-rekap">
                                    <i class="fa fa-file-excel mr-1"></i> Laporan Rekap
                                </a>
                                @endif
                                <a class="dropdown-item" href="#" id="export-program">
                                    <i class="fa fa-file-excel mr-1"></i> Laporan Program
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if($is_superadmin ?? false)
                    <div class="col-sm-12 col-lg-12 d-flex align-items-end">
                        <div class="w-100">
                            <label class="d-block">Akses Overtime</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="overtime-access-switch"
                                    disabled>
                                <label class="custom-control-label" for="overtime-access-switch"
                                    id="overtime-access-switch-label">Pilih OPD terlebih dahulu</label>
                            </div>
                            <small class="text-muted d-block mt-1">Buka akses ketika overtime agar admin RFK dapat
                                melakukan perubahan.</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-body" style="position: relative;">
                <!-- Loading Overlay -->
                <div id="table-loading-overlay" class="table-loading-overlay" style="display: none;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-2 text-primary font-weight-bold">Memuat data...</div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div id="keuangan-legend" class="alert alert-info mb-0" style="display: none; padding: 8px 12px;">
                            <small>
                                <strong>Keterangan Warna Realisasi Keuangan:</strong>
                                <span class="ml-3">
                                    <span class="badge badge-danger">Merah</span> = Di bawah rata-rata OPD
                                </span>
                                <span class="ml-2">
                                    <span class="badge badge-warning">Kuning</span> = Rata-rata OPD s/d +5%
                                </span>
                                <span class="ml-2">
                                    <span class="badge badge-success">Hijau</span> = Di atas rata-rata OPD +5%
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable-list" class="table table-bordered table-hover table-sm">
                        <thead class="text-center font-weight-bold bg-light">
                            <tr>
                                <th rowspan="2" style="width: 30px;" class="text-center p-2">
                                    <button id="btn-toggle-expand" class="btn btn-sm btn-outline-primary"
                                        style="display: none;">
                                        <i class="fas fa-expand-alt"></i>
                                    </button>
                                </th>
                                <th rowspan="2" class="text-center" style="width: 130px; min-width: 130px;">Aksi</th>
                                <th rowspan="2">Kode</th>
                                <th rowspan="2">
                                    <div style="width: 300px">Uraian Urusan, Organisasi, Program,</div>
                                    <div>Kegiatan, dan Sub Kegiatan</div>
                                </th>
                                <th rowspan="2">Pagu Anggaran</th>
                                <th colspan="3">Realisasi Keuangan</th>
                                <th rowspan="2">Sisa Anggaran</th>
                                <th colspan="2">Realisasi (%)</th>
                                <th rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th title="Nilai otomatis dari s.d. Bulan Ini bulan sebelumnya">Bulan Lalu <i class="fas fa-info-circle text-muted" style="font-size: 0.8em;"></i></th>
                                <th>Bulan Ini</th>
                                <th>s.d. Bulan Ini</th>
                                 <th>
                                     <div>Keuangan</div>
                                     <small id="avg-keuangan-indicator" class="text-muted" style="font-weight: normal; display: block; margin-top: 2px;">
                                         Rata-rata Keseluruhan OPD: <span id="avg-keuangan-value">0.00</span>%
                                     </small>
                                 </th>
                                <th>Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark" style="font-size: 0.9rem;">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .table-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        border-radius: 0.25rem;
    }

    .table-loading-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Color classes for keuangan based on OPD average */
    .text-danger {
        color: #dc3545 !important;
        font-weight: bold;
    }

    .text-warning {
        color: #ffc107 !important;
        font-weight: bold;
    }

    .text-success {
        color: #28a745 !important;
        font-weight: bold;
    }
</style>
@stop

@section('js')
<script>
    // Debug mode flag - set to true to enable console logging
    const DEBUG_MODE = false;
    const BASE_IS_OVERTIME = @json($is_overtime ?? false);
    const IS_ADMIN_RFK = @json($is_admin_rfk ?? false);
    const IS_SUPERADMIN = @json($is_superadmin ?? false);
    const DEFAULT_OPD_ID = @json($id_opd ?? null);
    const OVERTIME_LOCK_MESSAGE = 'Perubahan data RFK sudah ditutup untuk periode ini.';

    // Preserve original console methods and disable when DEBUG_MODE is false
    const originalConsole = {
        log: console.log.bind(console),
        warn: console.warn.bind(console),
        error: console.error.bind(console)
    };

    if (!DEBUG_MODE) {
        console.log = function () {};
        console.warn = function () {};
        console.error = function () {};
    } else {
        console.log = originalConsole.log;
        console.warn = originalConsole.warn;
        console.error = originalConsole.error;
    }

    let list_data = [];
    let charts_data;
    let chart;
    let has_chart = false;
    let expanded_rows = {};
    let child_data_cache = {};
    let isExpandingCollapsing = false; // Flag to prevent double-click
    let loadingOperations = 0; // Counter for loading operations
    let currentIsOvertime = BASE_IS_OVERTIME;
    let currentOpenOvertime = false;
    let editingLocked = IS_ADMIN_RFK && !IS_SUPERADMIN && BASE_IS_OVERTIME;
    let isUpdatingOvertimeSwitch = false;
    const $overtimeSwitch = $('#overtime-access-switch');
    const $overtimeSwitchLabel = $('#overtime-access-switch-label');
    let overtimeToggleEnabled = false;
    const $resyncButton = $('#btn-resync-previous');
    let opdAverageKeuangan = 0; // Store average keuangan for the OPD
    let serverAverageLoaded = false; // Flag: true when average is fetched from server
    let isAutoExpandingAfterLoad = false;

    // Helper functions for loading overlay
    function showTableLoading(message) {
        loadingOperations++;
        const overlay = $('#table-loading-overlay');
        if (message) {
            overlay.find('.mt-2').text(message);
        }
        overlay.fadeIn(200);
        console.log('[LOADING] Show loading, count:', loadingOperations, 'message:', message);
    }

    function hideTableLoading() {
        loadingOperations = Math.max(0, loadingOperations - 1);
        console.log('[LOADING] Hide loading, count:', loadingOperations);
        if (loadingOperations === 0) {
            $('#table-loading-overlay').fadeOut(200);
            console.log('[LOADING] Loading overlay hidden');
        }
    }

    // Force hide loading (for emergency reset)
    function forceHideTableLoading() {
        loadingOperations = 0;
        $('#table-loading-overlay').fadeOut(200);
        console.log('[LOADING] Force hide loading overlay');
    }

    function formatMonthLabel(monthValue) {
        try {
            if (!monthValue) {
                return '';
            }
            const [year, month] = monthValue.split('-');
            const date = new Date(parseInt(year, 10), parseInt(month, 10) - 1, 1);
            return new Intl.DateTimeFormat('id-ID', {
                month: 'long',
                year: 'numeric'
            }).format(date);
        } catch (error) {
            return monthValue || '';
        }
    }

    function getPreviousMonthValue(monthValue) {
        try {
            if (!monthValue) {
                return null;
            }
            const [year, month] = monthValue.split('-');
            const date = new Date(parseInt(year, 10), parseInt(month, 10) - 1, 1);
            date.setMonth(date.getMonth() - 1);
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        } catch (error) {
            return null;
        }
    }

    function updateEditingState(meta = {}) {
        if (!meta || typeof meta !== 'object') {
            meta = {};
        }

        if (Object.prototype.hasOwnProperty.call(meta, 'is_overtime')) {
            currentIsOvertime = !!meta.is_overtime;
        }

        if (Object.prototype.hasOwnProperty.call(meta, 'open_overtime')) {
            currentOpenOvertime = !!meta.open_overtime;
        }

        if (Object.prototype.hasOwnProperty.call(meta, 'toggle_enabled')) {
            overtimeToggleEnabled = !!meta.toggle_enabled;
        }

        let allowEdit;
        if (Object.prototype.hasOwnProperty.call(meta, 'allow_edit')) {
            allowEdit = !!meta.allow_edit;
        } else {
            allowEdit = !(IS_ADMIN_RFK && !IS_SUPERADMIN && currentIsOvertime && !currentOpenOvertime);
        }

        editingLocked = !allowEdit;
        if (IS_SUPERADMIN && allowEdit) {
            editingLocked = false;
        }
        console.log('[OVERTIME] State updated:', {
            currentIsOvertime,
            currentOpenOvertime,
            editingLocked,
            overtimeToggleEnabled
        });

        updateResyncButtonState();
    }

    function updateResyncButtonState() {
        if (!$resyncButton.length) {
            return;
        }

        const opd = $('#filter-opd').val();
        const month = $('#filter-month').val();

        let canSync = false;
        if (IS_SUPERADMIN) {
            canSync = !!month;
        } else {
            canSync = !!opd && !!month && !editingLocked;
        }

        $resyncButton.prop('disabled', !canSync);
    }

    function renderOvertimeSwitch(options = {}) {
        if (!$overtimeSwitch.length) {
            return;
        }

        const forceDisable = options.forceDisable || false;
        const message = options.message || null;

        isUpdatingOvertimeSwitch = true;

        if (forceDisable) {
            currentOpenOvertime = false;
            overtimeToggleEnabled = false;
            $overtimeSwitch.prop('checked', false).prop('disabled', true);
            $overtimeSwitchLabel.text(message || 'Pilih OPD terlebih dahulu');
            $overtimeSwitch.closest('.custom-control').addClass('text-muted');
            isUpdatingOvertimeSwitch = false;
            updateResyncButtonState();
            return;
        }

        if (!currentIsOvertime) {
            currentOpenOvertime = false;
            overtimeToggleEnabled = false;
            $overtimeSwitch.prop('checked', false).prop('disabled', true);
            $overtimeSwitchLabel.text('Overtime belum aktif');
            $overtimeSwitch.closest('.custom-control').addClass('text-muted');
            isUpdatingOvertimeSwitch = false;
            updateResyncButtonState();
            return;
        }

        if (!overtimeToggleEnabled) {
            currentOpenOvertime = false;
            $overtimeSwitch.prop('checked', false).prop('disabled', true);
            $overtimeSwitchLabel.text('Overtime sudah ditutup oleh sistem');
            $overtimeSwitch.closest('.custom-control').addClass('text-muted');
            isUpdatingOvertimeSwitch = false;
            updateResyncButtonState();
            return;
        }

        $overtimeSwitch.closest('.custom-control').removeClass('text-muted');
        $overtimeSwitch.prop('disabled', false);
        $overtimeSwitch.prop('checked', currentOpenOvertime);
        $overtimeSwitchLabel.text(currentOpenOvertime ? 'Akses Overtime Dibuka' : 'Akses Overtime Ditutup');

        isUpdatingOvertimeSwitch = false;
        updateResyncButtonState();
    }

    function handleOvertimeToggle(shouldOpen) {
        if (!$overtimeSwitch.length) {
            return;
        }

        if (!overtimeToggleEnabled) {
            renderOvertimeSwitch();
            return;
        }

        const opd = $('#filter-opd').val();
        if (!opd) {
            toastr.error('Pilih OPD terlebih dahulu');
            renderOvertimeSwitch({forceDisable: true});
            return;
        }

        const previousState = currentOpenOvertime;

        $.ajax({
            type: 'POST',
            url: "{{ route('rfk-v2.toggleOvertime') }}",
            data: {
                opd: opd,
                month: $('#filter-month').val(),
                open: shouldOpen ? 1 : 0
            },
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            beforeSend: function () {
                $('.loader').show();
                $overtimeSwitch.prop('disabled', true);
            },
            success: function (response) {
                $('.loader').hide();

                if (response.meta) {
                    updateEditingState(response.meta);
                } else {
                    currentOpenOvertime = shouldOpen;
                }

                renderOvertimeSwitch();

                toastr.success(response.message || 'Status overtime diperbarui');

                // Refresh table data to make sure UI follows the latest access rules
                getListData();
            },
            error: function (xhr) {
                $('.loader').hide();
                let errorMessage = 'Gagal memperbarui status overtime';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                currentOpenOvertime = previousState;
                renderOvertimeSwitch();
            }
        });
    }

    function syncFromPrevious() {
        if (!$resyncButton.length) {
            return;
        }

        const opd = $('#filter-opd').val();
        const month = $('#filter-month').val();

        if (!month) {
            toastr.error('Pilih bulan terlebih dahulu');
            updateResyncButtonState();
            return;
        }

        if (!opd && !IS_SUPERADMIN) {
            toastr.error('Pilih OPD terlebih dahulu');
            updateResyncButtonState();
            return;
        }

        const monthLabel = formatMonthLabel(month) || month;
        const previousMonthValue = getPreviousMonthValue(month);
        const previousMonthLabel = previousMonthValue ? (formatMonthLabel(previousMonthValue) || previousMonthValue) : 'bulan sebelumnya';

        const scopeText = opd ? 'OPD yang dipilih' : 'SEMUA OPD';

        swal({
            title: "Sync Ulang Data",
            text: `Data bulan ${monthLabel} untuk ${scopeText} akan diganti dengan data dari ${previousMonthLabel}. Lanjutkan?`,
            icon: "warning",
            buttons: ["Batal", "Ya"],
            dangerMode: true
        }).then(function (result) {
            if (!result) {
                updateResyncButtonState();
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('rfk-v2.resync') }}",
                data: {
                    month: month,
                    opd: opd
                },
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                beforeSend: function () {
                    $('.loader').show();
                    showTableLoading('Mengambil data bulan sebelumnya...');
                    $resyncButton.prop('disabled', true);
                },
                success: function (response) {
                    $('.loader').hide();
                    hideTableLoading();

                    if (response.message) {
                        toastr.success(response.message);
                    } else {
                        toastr.success('Data berhasil disinkronisasi ulang dari bulan sebelumnya.');
                    }

                    if (response.meta) {
                        updateEditingState(response.meta);
                        renderOvertimeSwitch();
                    }

                    getListData();
                },
                error: function (xhr) {
                    $('.loader').hide();
                    hideTableLoading();
                    let errorMessage = 'Gagal sinkronisasi ulang data';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function () {
                    updateResyncButtonState();
                }
            });
        });
    }

    // Helper function to format data from server to match table format
    function formatServerDataToTableData(serverData) {
        return {
            id: serverData.id,
            parent_id: serverData.parent_id,
            type: serverData.type,
            code: serverData.code,
            uraian: serverData.uraian,
            pagu_anggaran: parseFloat(serverData.pagu_anggaran || 0),
            bulan_lalu: parseFloat(serverData.bulan_lalu || 0),
            bulan_ini: parseFloat(serverData.bulan_ini || 0),
            sd_bulan_ini: parseFloat(serverData.sd_bulan_ini || 0),
            sisa_anggaran: parseFloat(serverData.sisa_anggaran || 0),
            keuangan: parseFloat(serverData.keuangan || 0),
            fisik: parseFloat(serverData.fisik || 0),
            keterangan: serverData.keterangan || '',
            has_children: serverData.has_children || false,
            _editing: false,
            _creating: false,
            _originalData: null
        };
    }

    // Update a single row in the table without reloading
    function updateRowInTable(rowId, newData, skipDraw = false) {
        let rowUpdated = false;
        let rowTr = null;

        table.rows({search: 'none'}).every(function() {
            const rowData = this.data();
            if (rowData && rowData.id && parseInt(rowData.id) === parseInt(rowId)) {
                // Format new data
                const formattedData = formatServerDataToTableData(newData);

                // Store tr reference before updating
                rowTr = $(this.node());

                // Update row data
                this.data(formattedData);

                // Update in list_data as well
                const index = list_data.findIndex(r => r && r.id && parseInt(r.id) === parseInt(rowId));
                if (index >= 0) {
                    list_data[index] = formattedData;
                } else {
                    list_data.push(formattedData);
                }

                // Also update in child_data_cache if parent's cache is active
                const parentId = formattedData.parent_id;
                if (parentId && child_data_cache[parentId]) {
                    const cacheIndex = child_data_cache[parentId].findIndex(c => parseInt(c.id) === parseInt(rowId));
                    if (cacheIndex >= 0) {
                        child_data_cache[parentId][cacheIndex] = newData;
                    } else {
                        child_data_cache[parentId].push(newData);
                    }
                    // Sort cache by code
                    child_data_cache[parentId].sort((a, b) => (a.code || '').toString().localeCompare((b.code || '').toString()));
                }

                rowUpdated = true;
                return false; // Stop iteration
            }
        });

        if (rowUpdated) {
            // Only recalculate from cache if server hasn't provided the value yet
            if (!serverAverageLoaded) {
                calculateOpdAverageKeuanganFromCache();
            }

            // Update DOM attributes to maintain hierarchy
            if (rowTr && rowTr.length > 0) {
                const formattedData = formatServerDataToTableData(newData);
                rowTr.attr('data-parent-id', formattedData.parent_id || '');
                rowTr.removeClass(function(index, className) {
                    return (className.match(/\bchild-of-\d+\b/g) || []).join(' ');
                });
                if (formattedData.parent_id) {
                    rowTr.addClass('child-of-' + formattedData.parent_id);
                }
                rowTr.attr('data-level', getHierarchyLevel(formattedData.type));
            }

            // Only draw if not skipping (for batch updates)
            if (!skipDraw) {
                // Redraw table but restore order if changed
                const orderBeforeDraw = logDOMOrder('BEFORE UPDATE ROW DRAW');
                table.draw(false);
                const orderAfterDraw = logDOMOrder('AFTER UPDATE ROW DRAW');
                if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'updateRow')) {
                    console.log('[UPDATE ROW] Updated row in table:', rowId);
                }
            } else {
                console.log('[UPDATE ROW] Updated row in table (draw skipped):', rowId);
            }
        } else {
            console.warn('[UPDATE ROW] Row not found in table:', rowId);
        }

        return rowUpdated;
    }

    // Update parent rows in the table
    function updateParentRowsInTable(updatedParents) {
        if (!updatedParents || updatedParents.length === 0) {
            return;
        }

        // Update all parents first
        updatedParents.forEach(function(parentData) {
            updateRowInTable(parentData.id, parentData);
        });

        // Only recalculate from cache if server hasn't provided the value yet
        if (!serverAverageLoaded) {
            calculateOpdAverageKeuanganFromCache();
        }

        // After all updates, ensure order is correct
        const orderAfterUpdate = logDOMOrder('AFTER UPDATE PARENTS');
        // Sort list_data hierarchically to ensure consistency
        list_data = sortListDataHierarchically(list_data);

        console.log('[UPDATE PARENTS] Updated', updatedParents.length, 'parent rows');
    }

    function download(type) {
        const opd = $('#filter-opd').val();
        let url = `{{ url('/rfk-v2/laporan/') }}/${type}?month=${$('#filter-month').val()}`;
        if (opd) {
            url += `&opd=${opd}`;
        }
        window.open(url, '_blank');
    }

    function getHierarchyLevel(type) {
        const levels = {
            'opd': 0,
            'program': 1,
            'kegiatan': 2,
            'sub_kegiatan': 3
        };
        return levels[type] || 0;
    }

    function formatToCurrency(number) {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR',
            currencyDisplay: "code"
        })
        .format(number)
        .replace('EUR', "")
        .trim();
    }

    // Fetch average keuangan from ALL OPDs in Kabupaten Tulungagung (not just selected OPD)
    function fetchOpdAverageKeuangan(callback) {
        const month = $('#filter-month').val();
        serverAverageLoaded = false;

        if (!month) {
            opdAverageKeuangan = 0;
            updateAverageKeuanganIndicator(0, 0);
            if (callback) callback();
            return;
        }

        $.ajax({
            url: "{{ route('rfk-v2.opdAverage') }}",
            method: 'GET',
            data: {
                month: month
                // Note: opd parameter is no longer needed - we calculate from ALL OPDs
            },
            success: function (response) {
                if (response.success && response.data) {
                    opdAverageKeuangan = parseFloat(response.data.opd_keuangan) || 0;
                    serverAverageLoaded = true;
                    const count = parseInt(response.data.count) || 0;

                    console.log('[AVERAGE KEUANGAN] Fetched from server (ALL OPDs):', opdAverageKeuangan.toFixed(2) + '%', 'from', count, 'sub_kegiatan');
                    console.log('[AVERAGE KEUANGAN] Scope:', response.data.opd_name, '- Overall OPD Level Keuangan:', response.data.opd_keuangan + '%');

                    // Update indicator in table header
                    updateAverageKeuanganIndicator(opdAverageKeuangan, count);
                } else {
                    console.warn('[AVERAGE KEUANGAN] Invalid response, using fallback');
                    calculateOpdAverageKeuanganFromCache();
                }

                if (callback) callback();
            },
            error: function (xhr) {
                console.warn('[AVERAGE KEUANGAN] Failed to fetch from server:', xhr.status, xhr.statusText);
                // Fallback to calculating from available data
                calculateOpdAverageKeuanganFromCache();
                if (callback) callback();
            }
        });
    }

    // Calculate average keuangan for the OPD from ALL available data (fallback)
    function calculateOpdAverageKeuanganFromCache() {
        let totalKeuangan = 0;
        let count = 0;
        const processedIds = new Set();

        // First, collect from list_data (top-level data)
        list_data.forEach(function(row) {
            if (row && row.type === 'sub_kegiatan' && row.id) {
                const keuangan = parseFloat(row.keuangan) || 0;
                totalKeuangan += keuangan;
                count++;
                processedIds.add(parseInt(row.id));
            }
        });

        // Then, collect from child_data_cache (expanded children)
        Object.keys(child_data_cache).forEach(function(parentId) {
            const children = child_data_cache[parentId];
            if (Array.isArray(children)) {
                children.forEach(function(child) {
                    if (child && child.type === 'sub_kegiatan' && child.id) {
                        const childId = parseInt(child.id);
                        // Only add if not already processed
                        if (!processedIds.has(childId)) {
                            const keuangan = parseFloat(child.keuangan) || 0;
                            totalKeuangan += keuangan;
                            count++;
                            processedIds.add(childId);
                        }
                    }
                });
            }
        });

        // Finally, collect from DataTable rows (in case some are only in table)
        table.rows({search: 'none'}).every(function() {
            const row = this.data();
            if (row && row.type === 'sub_kegiatan' && row.id) {
                const rowId = parseInt(row.id);
                // Only add if not already processed
                if (!processedIds.has(rowId)) {
                    const keuangan = parseFloat(row.keuangan) || 0;
                    totalKeuangan += keuangan;
                    count++;
                    processedIds.add(rowId);
                }
            }
        });

        const average = count > 0 ? totalKeuangan / count : 0;
        opdAverageKeuangan = average;

        console.log('[AVERAGE KEUANGAN] Calculated from cache:', average.toFixed(2) + '%', 'from', count, 'unique sub_kegiatan rows');

        // Update indicator in table header
        updateAverageKeuanganIndicator(average, count);

        return average;
    }

    // Update the average keuangan indicator in the table header
    function updateAverageKeuanganIndicator(average, count) {
        const $indicator = $('#avg-keuangan-indicator');
        const $value = $('#avg-keuangan-value');
        const $legend = $('#keuangan-legend');

        $value.text(average.toFixed(2));
        $indicator.show();
        $legend.show();
    }

    // Get color class based on keuangan value compared to OPD average
    function getKeuanganColorClass(keuangan) {
        const average = opdAverageKeuangan;
        const threshold = average + 5; // Yellow threshold: average + 5%

        if (keuangan < average) {
            return 'text-danger'; // Red: below average
        } else if (keuangan >= average && keuangan <= threshold) {
            return 'text-warning'; // Yellow: between average and average+5%
        } else {
            return 'text-success'; // Green: above average+5%
        }
    }

    const table = $(`#datatable-list`).DataTable({
        autoWidth: false,
        sort: false,
        search: false,
        data: list_data,
        pageLength: 100,
        paging: false,
        ordering: false,
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
                render: function (data, type, row) {
                    // Don't show expand icon when editing or creating
                    if (row._editing || row._creating) {
                        return '';
                    }
                    if (row.has_children || row.type !== 'sub_kegiatan') {
                        // Check if this row is expanded
                        const isExpanded = expanded_rows[row.id] || false;
                        const iconClass = isExpanded ? 'fa-minus-square' : 'fa-plus-square';
                        return `<i class="fas ${iconClass} text-primary" style="cursor: pointer; font-size: 14px;"></i>`;
                    }
                    return '';
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let html = '<div class="d-flex justify-content-center">';
                    const isDisabled = editingLocked;
                    const disabledAttr = isDisabled ? 'disabled' : '';
                    const disabledClass = isDisabled ? ' disabled' : '';

                    const addTitleProgram = isDisabled ? OVERTIME_LOCK_MESSAGE : 'Tambah Program';
                    const addTitleKegiatan = isDisabled ? OVERTIME_LOCK_MESSAGE : 'Tambah Kegiatan';
                    const addTitleSubKegiatan = isDisabled ? OVERTIME_LOCK_MESSAGE : 'Tambah Sub Kegiatan';
                    const editTitle = isDisabled ? OVERTIME_LOCK_MESSAGE : 'Edit';
                    const deleteTitle = isDisabled ? OVERTIME_LOCK_MESSAGE : 'Hapus';

                    // If editing or creating, show Save and Cancel buttons
                    if (row._editing || row._creating) {
                        html += `<button onclick='saveInlineRow(${row.id || 'null'})' class='btn btn-success btn-xs mr-1' title='Simpan'><i class='fa fa-save mr-1'></i> Simpan</button>`;
                        html += `<button onclick='cancelInlineEdit(${row.id || 'null'})' class='btn btn-secondary btn-xs' title='Batal'><i class='fa fa-times mr-1'></i> Batal</button>`;
                        html += '</div>';
                        return html;
                    }

                    // Show Add button - OPD can add Program, Program can add Kegiatan, Kegiatan can add Sub Kegiatan
                    if (row.type === 'opd') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1${disabledClass}' title='${addTitleProgram}' ${disabledAttr}><i class='fa fa-plus mr-1'></i> Program</button>`;
                    } else if (row.type === 'program') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1${disabledClass}' title='${addTitleKegiatan}' ${disabledAttr}><i class='fa fa-plus mr-1'></i> Kegiatan</button>`;
                    } else if (row.type === 'kegiatan') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1${disabledClass}' title='${addTitleSubKegiatan}' ${disabledAttr}><i class='fa fa-plus mr-1'></i> Sub Kegiatan</button>`;
                    }

                    // OPD is fixed, can't be edited or deleted
                    if (row.type !== 'opd') {
                        html += `<button onclick='enableInlineEdit(${row.id})' class='btn btn-info btn-xs mr-1${disabledClass}' title='${editTitle}' ${disabledAttr}><i class='fa fa-edit'></i></button>`;
                        html += `<button onclick='modalDelete(${row.id})' class='btn btn-danger btn-xs${disabledClass}' title='${deleteTitle}' ${disabledAttr}><i class='fa fa-trash'></i></button>`;
                    }

                    html += '</div>';
                    return html;
                }
            },
            {
                data: 'code',
                render: function (data, type, row) {
                    if (row._editing || row._creating) {
                        const level = getHierarchyLevel(row.type);
                        const indent = level * 20;
                        return `<div style="padding-left: ${indent}px">
                            <input type="text" class="form-control form-control-sm" name="code" value="${data || ''}" style="width: 200px;">
                        </div>`;
                    }
                    const level = getHierarchyLevel(row.type);
                    const indent = level * 20;
                    return `<div style="padding-left: ${indent}px">${data}</div>`;
                }
            },
            {
                data: 'uraian',
                render: function (data, type, row) {
                    let dispData = data || '';
                    if (row.type === 'opd' || row.type === 'program') {
                        dispData = dispData.toUpperCase();
                    }
                    if (row._editing || row._creating) {
                        const level = getHierarchyLevel(row.type);
                        const indent = level * 20;
                        return `<div style="padding-left: ${indent}px">
                            <textarea class="form-control form-control-sm" name="uraian" rows="2" style="min-width: 300px;">${dispData}</textarea>
                        </div>`;
                    }
                    const level = getHierarchyLevel(row.type);
                    const indent = level * 20;
                    const fontWeight = row.has_children ? 'font-weight-bold' : '';
                    return `<div style="padding-left: ${indent}px" class="${fontWeight}">${dispData}</div>`;
                }
            },
            {
                data: 'pagu_anggaran',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        return `<div style="text-align: right;">
                            <input type="text" class="form-control form-control-sm uang-input" name="pagu_anggaran" value="${formatToCurrency(parseFloat(data) || 0)}" style="width: 130px; text-align: right;">
                        </div>`;
                    }
                    return '<div style="text-align: right;">' + formatToCurrency(parseFloat(data) || 0) + '</div>';
                }
            },
            {
                data: 'bulan_lalu',
                render: function (data) {
                    return '<div style="text-align: right;">' + formatToCurrency(parseFloat(data) || 0) + '</div>';
                }
            },
            {
                data: 'bulan_ini',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        return `<div style="text-align: right;">
                            <input type="text" class="form-control form-control-sm uang-input" name="bulan_ini" value="${formatToCurrency(parseFloat(data) || 0)}" style="width: 110px; text-align: right;">
                        </div>`;
                    }
                    return '<div style="text-align: right;">' + formatToCurrency(parseFloat(data) || 0) + '</div>';
                }
            },
            {
                data: 'sd_bulan_ini',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        // Auto-calculated field: bulan_lalu + bulan_ini
                        const bulanLalu = parseFloat(row.bulan_lalu) || 0;
                        const bulanIni = parseFloat(row.bulan_ini) || 0;
                        const calculated = bulanLalu + bulanIni;
                        return `<div style="text-align: right;">
                            <input type="text" class="form-control form-control-sm calculated-field" name="sd_bulan_ini" value="${formatToCurrency(calculated)}" readonly style="width: 130px; text-align: right; background-color: #f8f9fa;">
                        </div>`;
                    }
                    return '<div style="text-align: right;">' + formatToCurrency(parseFloat(data) || 0) + '</div>';
                }
            },
            {
                data: 'sisa_anggaran',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        // Auto-calculated field: pagu_anggaran - sd_bulan_ini
                        const pagu = parseFloat(row.pagu_anggaran) || 0;
                        const bulanLalu = parseFloat(row.bulan_lalu) || 0;
                        const bulanIni = parseFloat(row.bulan_ini) || 0;
                        const sdBulanIni = bulanLalu + bulanIni;
                        const calculated = pagu - sdBulanIni;
                        return `<div style="text-align: right;">
                            <input type="text" class="form-control form-control-sm calculated-field" name="sisa_anggaran" value="${formatToCurrency(calculated)}" readonly style="width: 130px; text-align: right; background-color: #f8f9fa;">
                        </div>`;
                    }
                    return '<div style="text-align: right;">' + formatToCurrency(parseFloat(data) || 0) + '</div>';
                }
            },
            {
                data: 'keuangan',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        // Auto-calculated field: (sd_bulan_ini / pagu_anggaran) * 100
                        const pagu = parseFloat(row.pagu_anggaran) || 0;
                        const bulanLalu = parseFloat(row.bulan_lalu) || 0;
                        const bulanIni = parseFloat(row.bulan_ini) || 0;
                        const sdBulanIni = bulanLalu + bulanIni;
                        const calculated = pagu > 0 ? (sdBulanIni / pagu) * 100 : 0;
                        const colorClass = getKeuanganColorClass(calculated);
                        return `<div style="text-align: center;">
                            <input type="text" class="form-control form-control-sm calculated-field ${colorClass}" name="keuangan" value="${calculated.toFixed(2)}%" readonly style="width: 80px; text-align: center; background-color: #f8f9fa;">
                        </div>`;
                    }
                    const val = parseFloat(data) || 0;
                    const colorClass = getKeuanganColorClass(val);
                    return `<div style="text-align: center;" class="${colorClass}">${val.toFixed(2)}%</div>`;
                }
            },
            {
                data: 'fisik',
                render: function (data, type, row) {
                    if ((row._editing || row._creating) && row.type === 'sub_kegiatan') {
                        return `<div style="text-align: center;">
                            <input type="number" class="form-control form-control-sm" name="fisik" value="${parseFloat(data) || 0}" min="0" max="100" step="0.01" style="width: 80px; text-align: center;">
                        </div>`;
                    }
                    const val = parseFloat(data) || 0;
                    return `<div style="text-align: center;">${val.toFixed(2)}%</div>`;
                }
            },
            {
                data: 'keterangan',
                render: function (data, type, row) {
                    if (row._editing || row._creating) {
                        return `<div>
                            <textarea class="form-control form-control-sm" name="keterangan" rows="1" style="width: 150px;">${data || ''}</textarea>
                        </div>`;
                    }
                    return `<div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">${data || ''}</div>`;
                }
            }
        ],
        rowCallback: function(row, data) {
            // Apply background colors based on type
            const colors = {
                'opd': '#e3f2fd',
                'program': '#f3e5f5',
                'kegiatan': '#fff3e0',
                'sub_kegiatan': '#ffffff'
            };
            $(row).css('background-color', colors[data.type] || '#ffffff');

            // Apply indentation based on hierarchy level
            const level = getHierarchyLevel(data.type);
            const indent = level * 20; // 20px per level

            // Get the code and uraian cells (columns 2 and 3 after Actions)
            const $cells = $(row).find('td');
            const $codeCell = $cells.eq(2);
            const $uraianCell = $cells.eq(3);

            // Apply padding-left for indentation
            $codeCell.css('padding-left', (indent + 8) + 'px');
            $uraianCell.css('padding-left', (indent + 8) + 'px');

            // Make text bold for items with children
            if (data.has_children) {
                $codeCell.css('font-weight', 'bold');
                $uraianCell.css('font-weight', 'bold');
            }

            // Apply consistent column widths and alignment
            $cells.eq(0).css({'text-align': 'center'});  // Expand icon
            $cells.eq(1).css({'text-align': 'center'});  // Actions (moved to column 1)
            $cells.eq(2).css({});  // Code (moved to column 2)
            $cells.eq(3).css({'min-width': '300px'});  // Uraian (moved to column 3)
            $cells.eq(4).css({'text-align': 'right'});  // Pagu Anggaran
            $cells.eq(5).css({'text-align': 'right'});  // Bulan Lalu
            $cells.eq(6).css({'text-align': 'right'});  // Bulan Ini
            $cells.eq(7).css({'text-align': 'right'});  // sd Bulan Ini
            $cells.eq(8).css({'text-align': 'right'});  // Sisa Anggaran
            $cells.eq(9).css({'text-align': 'center'});  // Keuangan %
            $cells.eq(10).css({'text-align': 'center'});  // Fisik %
            $cells.eq(11).css({});  // Keterangan
        }
    });

    $('#datatable-list tbody').on('click', 'td.details-control', function () {
        const tr = $(this).closest('tr');
        const row = table.row(tr);
        const data = row.data();
        const icon = $(this).find('i');

        if (icon.hasClass('fa-minus-square')) {
            // Collapse - hide all children
            icon.removeClass('fa-minus-square').addClass('fa-plus-square');
            collapseChildren(data.id);
            expanded_rows[data.id] = false;
        } else {
            // Expand - load and show children
            icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            loadAndInsertChildren(data.id, tr);
            expanded_rows[data.id] = true;
        }

        // Update toggle button state after expand/collapse
        setTimeout(function() {
            updateToggleButtonState();
        }, 200);
    });

    function loadAndInsertChildren(parent_id, parentTr) {
        // Find the actual parent row in DOM before inserting
        let actualParentTr = findParentRowInDom(parent_id, parentTr);

        // Check cache first
        if (child_data_cache[parent_id]) {
            insertChildrenIntoTable(child_data_cache[parent_id], actualParentTr);
            return;
        }

        $.ajax({
            url: "{{ route('rfk-v2.fetchChildren') }}",
            method: 'GET',
            data: {
                parent_id: parent_id,
                month: $('#filter-month').val(),
                opd: $('#filter-opd').val()
            },
            beforeSend: function () {
                $('.loader').show();
            },
            success: function (children) {
                $('.loader').hide();
                hideTableLoading();
                child_data_cache[parent_id] = children;
                // Find parent again in case DOM changed during AJAX
                actualParentTr = findParentRowInDom(parent_id, actualParentTr);
                insertChildrenIntoTable(children, actualParentTr);
            },
            error: function () {
                $('.loader').hide();
                hideTableLoading();
                toastr.error('Failed to load children');
            }
        });
    }

    function insertChildrenIntoTable(children, parentTr) {
        if (children.length === 0) return;

        // Get parent row data to get parent_id
        const parentRow = table.row(parentTr);
        const parentData = parentRow.data();
        const parentId = parentData ? parentData.id : null;

        if (!parentId) {
            console.error('[INSERT CHILDREN] Parent ID not found');
            return;
        }

        console.log('[INSERT CHILDREN] Inserting', children.length, 'children for parent', parentId,
            '(', parentData.code, parentData.uraian, ')');
        console.log('[INSERT CHILDREN] Children:', children.map(c => `${c.code} (${c.type})`).join(', '));

        // Log DOM order before insert
        const orderBefore = logDOMOrder(`BEFORE INSERT (parent:${parentId})`);

        // Find the actual parent row in DOM (it might have moved after previous inserts)
        // CRITICAL: We must find the parent row in DOM every time to ensure correct insertion point
        let actualParentTr = findParentRowInDom(parentId, parentTr);

        if (!actualParentTr || actualParentTr.length === 0) {
            console.error('Parent row not found in DOM for parent_id:', parentId);
            return;
        }

        const tbody = $('#datatable-list tbody');

        // Find the insertion point: after the last child of this parent, or after the parent if no children exist
        // CRITICAL: We must find the last child of THIS parent in DOM order, not just any child
        let insertAfterRow = actualParentTr;

        // Get all rows in tbody in DOM order
        const allRows = tbody.find('tr');
        let lastChildOfThisParent = null;
        let parentFound = false;
        let maxChildIndex = -1;

        // Iterate through all rows to find parent and its last child
        allRows.each(function() {
            const $row = $(this);
            const rowData = table.row($row).data();

            // Check if this is the parent row
            if (rowData && rowData.id && parseInt(rowData.id) === parentId) {
                parentFound = true;
                // Reset maxChildIndex when we find parent, as children should come after
                maxChildIndex = -1;
            }

            // If we've found the parent, look for its children
            if (parentFound && rowData && rowData.parent_id && parseInt(rowData.parent_id) === parentId) {
                const rowIndex = $row.index();
                if (rowIndex > maxChildIndex) {
                    maxChildIndex = rowIndex;
                    lastChildOfThisParent = $row;
                }
            }

            // If we encounter a row that is not a child of this parent and we've already found children,
            // we've passed all children of this parent
            if (parentFound && rowData && rowData.parent_id && parseInt(rowData.parent_id) !== parentId) {
                // Check if this row is a sibling or parent of a different branch
                // If it's at the same or lower level than our parent, we've passed all children
                const rowLevel = getHierarchyLevel(rowData.type);
                const parentLevel = getHierarchyLevel(parentData.type);
                if (rowLevel <= parentLevel && rowData.id !== parentId) {
                    // We've moved to a different branch, stop looking
                    return false; // Break the loop
                }
            }
        });

        if (lastChildOfThisParent && lastChildOfThisParent.length > 0) {
            insertAfterRow = lastChildOfThisParent;
        }

        // Add each child as a new row in DataTable AND to list_data
        // Insert them one by one, always after the previous sibling or parent
        children.forEach((child, idx) => {
            // Verify child belongs to this parent
            if (child.parent_id !== parentId) {
                console.warn('Child parent_id mismatch:', child.parent_id, 'expected:', parentId);
                return; // Skip this child
            }

            // Add to list_data for search purposes
            const existingIndex = list_data.findIndex(r => r && r.id === child.id);
            if (existingIndex === -1) {
                list_data.push(child);
            } else {
                // Update existing entry
                list_data[existingIndex] = child;
            }

            // Add row to table data
            const newRow = table.row.add(child).node();

            // Mark as child and add CSS class
            $(newRow).attr('data-parent-id', child.parent_id);
            $(newRow).addClass('child-of-' + child.parent_id);
            $(newRow).attr('data-level', getHierarchyLevel(child.type));

            // CRITICAL: Insert after the insertion point (parent or last sibling)
            // Then update insertion point to this new row for next sibling
            $(newRow).insertAfter(insertAfterRow);
            insertAfterRow = newRow; // Update reference for next sibling

            console.log('[INSERT CHILDREN] Inserted child', child.code, 'after',
                insertAfterRow === actualParentTr ? 'parent' : 'previous sibling');
        });

        // Log DOM order after insert
        const orderAfter = logDOMOrder(`AFTER INSERT (parent:${parentId})`);

        // Verify children are after parent
        const parentIndex = orderAfter.findIndex(r => r.id === parentId);
        if (parentIndex >= 0) {
            children.forEach(function(child) {
                const childIndex = orderAfter.findIndex(r => r.id === child.id);
                if (childIndex >= 0 && childIndex <= parentIndex) {
                    console.error('[INSERT CHILDREN] ❌ Child', child.code, 'is BEFORE parent!',
                        'Parent index:', parentIndex, 'Child index:', childIndex);
                } else if (childIndex >= 0) {
                    console.log('[INSERT CHILDREN] ✓ Child', child.code, 'is after parent',
                        'Parent index:', parentIndex, 'Child index:', childIndex);
                }
            });
        }

        // IMPORTANT: Do NOT call table.draw(false) here as it may reorder rows
        // The rows are already in correct DOM position after insertAfter
        // Only redraw if absolutely necessary, and do it after a delay
        setTimeout(function() {
            // Verify that children are in correct position
            const verifyParentTr = findParentRowInDom(parentId, actualParentTr);
            if (verifyParentTr && verifyParentTr.length > 0) {
                const parentIndex = verifyParentTr.index();
                const verifyChildren = tbody.find('.child-of-' + parentId);

                let allCorrect = true;
                verifyChildren.each(function() {
                    const childIndex = $(this).index();
                    if (childIndex <= parentIndex) {
                        console.error('Child found before parent! Parent index:', parentIndex, 'Child index:', childIndex);
                        allCorrect = false;
                    }
                });

                // Only redraw if verification fails (should not happen)
                if (!allCorrect) {
                    console.warn('[TABLE.DRAW] Children position incorrect, forcing redraw');
                    const orderBeforeDraw = logDOMOrder('BEFORE FORCED DRAW');
                    table.draw(false);
                    const orderAfterDraw = logDOMOrder('AFTER FORCED DRAW');
                    console.warn('[TABLE.DRAW] Order changed:', orderBeforeDraw.length !== orderAfterDraw.length ||
                        orderBeforeDraw.some((r, i) => r.id !== orderAfterDraw[i]?.id));
                }
            }
        }, 100);
    }

    function collapseChildren(parent_id) {
        // Find all direct children
        const childrenRows = $('.child-of-' + parent_id);

        childrenRows.each(function() {
            const childTr = $(this);
            const childRow = table.row(childTr);
            const childData = childRow.data();

            if (childData) {
                // If this child is expanded, collapse it first
                const childIcon = childTr.find('.details-control i');
                if (childIcon.hasClass('fa-minus-square')) {
                    childIcon.removeClass('fa-minus-square').addClass('fa-plus-square');
                    collapseChildren(childData.id);
                    expanded_rows[childData.id] = false;
                }

                // Remove the row from DataTable
                childRow.remove();
            }
        });

        // Redraw table
        console.log('[TABLE.DRAW] Calling draw() after collapseChildren');
        const orderBeforeDraw = logDOMOrder('BEFORE DRAW (collapse)');
        table.draw(false);
        const orderAfterDraw = logDOMOrder('AFTER DRAW (collapse)');
        if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'collapse')) {
            console.log('[TABLE.DRAW] ✓ Order preserved after collapse');
        }

        // Update button state after collapse
        setTimeout(function() {
            updateToggleButtonState();
        }, 100);
    }

    // Sort list_data hierarchically: parent followed by its children
    function sortListDataHierarchically(data) {
        if (!data || data.length === 0) return [];

        const sorted = [];
        const processed = new Set();
        const dataMap = new Map();

        // Create a map for quick lookup
        data.forEach(row => {
            if (row && row.id) {
                dataMap.set(parseInt(row.id), row);
            }
        });

        // Helper function to add a row and its children recursively
        function addRowAndChildren(rowId) {
            if (processed.has(rowId)) return;

            const row = dataMap.get(rowId);
            if (!row) return;

            // Add the row itself
            sorted.push(row);
            processed.add(rowId);

            // Find and add all children of this row
            const children = data.filter(r =>
                r && r.parent_id && parseInt(r.parent_id) === rowId
            );

            // Sort children by code for consistent order
            children.sort((a, b) => {
                const codeA = (a.code || '').toString();
                const codeB = (b.code || '').toString();
                return codeA.localeCompare(codeB);
            });

            // Recursively add children
            children.forEach(child => {
                if (child && child.id) {
                    addRowAndChildren(parseInt(child.id));
                }
            });
        }

        // Start with top-level rows (those with null parent_id or parent_id not in data)
        const topLevelRows = data.filter(row => {
            if (!row || !row.id) return false;
            if (!row.parent_id) return true; // OPD level
            const parentId = parseInt(row.parent_id);
            return !dataMap.has(parentId); // Parent not in current data
        });

        // Sort top-level rows by code
        topLevelRows.sort((a, b) => {
            const codeA = (a.code || '').toString();
            const codeB = (b.code || '').toString();
            return codeA.localeCompare(codeB);
        });

        // Add all top-level rows and their descendants
        topLevelRows.forEach(row => {
            if (row && row.id) {
                addRowAndChildren(parseInt(row.id));
            }
        });

        // Add any remaining rows that weren't processed (shouldn't happen, but safety check)
        data.forEach(row => {
            if (row && row.id && !processed.has(parseInt(row.id))) {
                sorted.push(row);
            }
        });

        return sorted;
    }

    // Helper function to log current DOM order
    function logDOMOrder(label) {
        const tbody = $('#datatable-list tbody');
        const order = [];
        tbody.find('tr').each(function() {
            const tr = $(this);
            const rowData = table.row(tr).data();
            if (rowData) {
                // Include rows with id: null (new rows being created)
                const rowId = rowData.id !== null && rowData.id !== undefined ? parseInt(rowData.id) : null;
                order.push({
                    id: rowId,
                    code: rowData.code || '',
                    uraian: rowData.uraian || '',
                    parent_id: rowData.parent_id || null,
                    type: rowData.type || '',
                    tr: tr // Store tr reference for restoration
                });
            }
        });
        console.log(`[DOM ORDER ${label}]`, order.map(r => {
            const idStr = r.id === null ? 'null' : r.id;
            return `${r.code} (${r.type}, id:${idStr}, parent:${r.parent_id})`;
        }).join(' -> '));
        return order;
    }

    // Helper function to restore DOM order if it was changed by table.draw()
    function restoreDOMOrderIfChanged(orderBefore, orderAfter, context) {
        // For addInlineRow, we need to handle new rows (id: null) specially
        const hasNewRows = orderAfter.some(r => r.id === null || r.id === undefined);
        const orderBeforeHasNewRows = orderBefore.some(r => r.id === null || r.id === undefined);

        // If there are new rows in orderAfter but not in orderBefore, include them
        let finalOrderBefore = [...orderBefore]; // Create a copy
        if (hasNewRows && !orderBeforeHasNewRows && context === 'addInlineRow') {
            // Find new rows in orderAfter and add them to orderBefore in correct position
            const newRows = orderAfter.filter(r => r.id === null || r.id === undefined);
            console.log('[RESTORE ORDER] Found', newRows.length, 'new rows to preserve');

            // For each new row, find its parent and insert it after parent or last sibling
            newRows.forEach(function(newRow) {
                if (!newRow.parent_id) return;

                // Find where to insert: after parent or after last sibling of parent
                let insertIndex = -1;
                for (let i = 0; i < finalOrderBefore.length; i++) {
                    const row = finalOrderBefore[i];
                    if (row.id === newRow.parent_id) {
                        // Found parent, insert after it
                        insertIndex = i + 1;
                        // Check if there are siblings after parent
                        for (let j = i + 1; j < finalOrderBefore.length; j++) {
                            const sibling = finalOrderBefore[j];
                            if (sibling.parent_id === newRow.parent_id) {
                                insertIndex = j + 1;
                            } else {
                                break; // No more siblings
                            }
                        }
                        break;
                    }
                }

                // Create a copy of newRow with tr reference if available
                const newRowWithTr = {
                    ...newRow,
                    tr: newRow.tr || null // Preserve tr reference if available
                };

                if (insertIndex >= 0) {
                    finalOrderBefore.splice(insertIndex, 0, newRowWithTr);
                    console.log('[RESTORE ORDER] Inserted new row at index', insertIndex, 'after parent', newRow.parent_id);
                } else {
                    // Parent not found, append at end
                    finalOrderBefore.push(newRowWithTr);
                    console.log('[RESTORE ORDER] Parent not found, appended new row at end');
                }
            });
        }

        const orderChanged = finalOrderBefore.length !== orderAfter.length ||
            finalOrderBefore.some((r, i) => {
                const afterRow = orderAfter[i];
                if (!afterRow) return true;
                // For rows with id: null, compare by parent_id and type
                if (r.id === null || r.id === undefined) {
                    return afterRow.id !== null && afterRow.id !== undefined ||
                        r.parent_id !== afterRow.parent_id ||
                        r.type !== afterRow.type;
                }
                return r.id !== afterRow.id;
            });

        if (orderChanged) {
            console.error(`[TABLE.DRAW] ❌ Order CHANGED after ${context}! Restoring correct order...`);

            const tbody = $('#datatable-list tbody');
            const rowsToReorder = [];

            // Find each row in DOM in the correct order
            finalOrderBefore.forEach(function(row) {
                // Try to use stored tr reference first
                if (row.tr && row.tr.length > 0) {
                    // Verify it's still the correct row
                    const rowData = table.row(row.tr).data();
                    if (rowData) {
                        // For rows with id: null, compare by parent_id and type
                        if (row.id === null || row.id === undefined) {
                            if (rowData.id === null || rowData.id === undefined) {
                                if (rowData.parent_id === row.parent_id && rowData.type === row.type) {
                                    rowsToReorder.push(row.tr);
                                    return;
                                }
                            }
                        } else if (rowData.id && parseInt(rowData.id) === row.id) {
                            rowsToReorder.push(row.tr);
                            return;
                        }
                    }
                }

                // If tr reference is invalid, find it again
                let found = false;
                table.rows({search: 'none'}).every(function() {
                    const rowData = this.data();
                    if (!rowData) return;

                    // For rows with id: null, compare by parent_id, type, and code
                    if (row.id === null || row.id === undefined) {
                        if (rowData.id === null || rowData.id === undefined) {
                            if (rowData.parent_id === row.parent_id &&
                                rowData.type === row.type &&
                                rowData.code === row.code) {
                                const tr = $(this.node());
                                if (tr.length > 0) {
                                    rowsToReorder.push(tr);
                                    found = true;
                                    return false; // Stop iteration
                                }
                            }
                        }
                    } else if (rowData.id && parseInt(rowData.id) === row.id) {
                        const tr = $(this.node());
                        if (tr.length > 0) {
                            rowsToReorder.push(tr);
                            found = true;
                            return false; // Stop iteration
                        }
                    }
                });

                if (!found && (row.id === null || row.id === undefined)) {
                    console.warn('[RESTORE ORDER] Could not find new row with parent_id:', row.parent_id, 'type:', row.type, 'code:', row.code);
                }
            });

            // Reorder DOM to match correct order
            if (rowsToReorder.length === finalOrderBefore.length) {
                tbody.empty();
                rowsToReorder.forEach(function(tr) {
                    tbody.append(tr);
                });
                console.log(`[TABLE.DRAW] ✓ Order restored after ${context}`);
                logDOMOrder(`AFTER RESTORE (${context})`);
                return true; // Order was restored
            } else {
                console.error(`[TABLE.DRAW] ❌ Failed to restore order, row count mismatch. Expected: ${finalOrderBefore.length}, Found: ${rowsToReorder.length}`);
                return false;
            }
        }
        return false; // Order was not changed
    }

    function refreshTable() {
        console.log('[REFRESH TABLE] Starting refreshTable()');
        showTableLoading('Menyusun ulang tabel...');

        // Log current DOM order before refresh
        const orderBefore = logDOMOrder('BEFORE REFRESH');

        // Calculate OPD average keuangan from available cache (only if server value not loaded)
        if (!serverAverageLoaded) {
            calculateOpdAverageKeuanganFromCache();
        }

        // Reset all editing/creating flags before refreshing
        table.rows().every(function() {
            const rowData = this.data();
            if (rowData) {
                delete rowData._editing;
                delete rowData._creating;
                delete rowData._originalData;
            }
        });

        // Ensure fresh data without editing flags
        const cleanData = list_data.map(row => {
            const cleanRow = {...row};
            delete cleanRow._editing;
            delete cleanRow._creating;
            delete cleanRow._originalData;
            return cleanRow;
        });

        console.log('[REFRESH TABLE] Clean data count:', cleanData.length);

        // CRITICAL: Sort data hierarchically before adding to table
        // This ensures parent rows come before their children
        const sortedData = sortListDataHierarchically(cleanData);

        console.log('[REFRESH TABLE] Sorted data order:', sortedData.map(r => `${r.code} (${r.type}, parent:${r.parent_id})`).join(' -> '));

        // Reset all state
        child_data_cache = {};
        expanded_rows = {};
        isExpandingCollapsing = false; // Reset flag

        // CRITICAL: Rebuild table by reordering DOM without using clear() or draw()
        // This preserves the correct hierarchical order
        const tbody = $('#datatable-list tbody');
        const existingRowsMap = new Map();
        const dataMap = new Map();

        // Create map of sorted data for quick lookup
        sortedData.forEach(function(dataRow) {
            if (dataRow && dataRow.id) {
                dataMap.set(parseInt(dataRow.id), dataRow);
            }
        });

        // Collect all existing rows
        tbody.find('tr').each(function() {
            const tr = $(this);
            const rowData = table.row(tr).data();
            if (rowData && rowData.id) {
                existingRowsMap.set(parseInt(rowData.id), tr);
            }
        });

        // Remove rows that are no longer in sortedData
        existingRowsMap.forEach(function(tr, rowId) {
            if (!dataMap.has(rowId)) {
                table.row(tr).remove();
                tr.remove();
            }
        });

        // Build ordered array of rows based on sortedData
        const rowsInOrder = [];
        sortedData.forEach(function(dataRow) {
            if (!dataRow || !dataRow.id) return;
            const rowId = parseInt(dataRow.id);
            let tr = existingRowsMap.get(rowId);

            if (!tr || tr.length === 0) {
                // Row doesn't exist, create it
                tr = $(table.row.add(dataRow).node());
                existingRowsMap.set(rowId, tr);
            } else {
                // Row exists, update its data
                table.row(tr).data(dataRow);
            }

            rowsInOrder.push({
                tr: tr,
                data: dataRow
            });
        });

        // Reorder DOM to match sortedData order
        console.log('[REFRESH TABLE] Reordering DOM, rowsInOrder count:', rowsInOrder.length);
        tbody.empty();
        rowsInOrder.forEach(function(item) {
            tbody.append(item.tr);

            // Mark row and add CSS classes
            const tr = item.tr;
            const rowData = item.data;
            $(tr).attr('data-parent-id', rowData.parent_id || '');
            $(tr).removeClass(function(index, className) {
                return (className.match(/\bchild-of-\d+\b/g) || []).join(' ');
            });
            if (rowData.parent_id) {
                $(tr).addClass('child-of-' + rowData.parent_id);
            }
            $(tr).attr('data-level', getHierarchyLevel(rowData.type));
        });

        // Log DOM order after reorder
        const orderAfter = logDOMOrder('AFTER REORDER (before any draw)');

        // DO NOT call table.draw(false) - it will reorder rows!
        // The DOM is already in correct hierarchical order

        // Verify order is correct
        let orderCorrect = true;
        for (let i = 0; i < sortedData.length && i < orderAfter.length; i++) {
            if (sortedData[i].id !== orderAfter[i].id) {
                orderCorrect = false;
                console.error('[REFRESH TABLE] ORDER MISMATCH at index', i,
                    'Expected:', sortedData[i].code, 'Got:', orderAfter[i].code);
                break;
            }
        }
        if (orderCorrect) {
            console.log('[REFRESH TABLE] ✓ Order is correct after reorder');
        }

        // Show/hide toggle button based on data
        if (list_data.length > 0) {
            const $btn = $('#btn-toggle-expand');
            $btn.show();
            $btn.prop('disabled', false); // Ensure button is enabled
            $btn.css('pointer-events', 'auto'); // Ensure button is clickable
            updateToggleButtonState();
        } else {
            $('#btn-toggle-expand').hide();
        }

        // Hide loading after a short delay to ensure table is rendered
        setTimeout(function() {
            hideTableLoading();
        }, 300);
    }

    // Function to check if all rows are expanded
    function isAllExpanded() {
        let hasExpandableRows = false;
        let allExpanded = true;

        table.rows().every(function() {
            const data = this.data();
            if (data && (data.has_children || data.type !== 'sub_kegiatan')) {
                hasExpandableRows = true;
                if (!expanded_rows[data.id]) {
                    allExpanded = false;
                    return false; // Stop iteration
                }
            }
        });

        return hasExpandableRows && allExpanded;
    }

    // Function to update toggle button state
    function updateToggleButtonState() {
        const $btn = $('#btn-toggle-expand');
        if ($btn.length === 0) return; // Button doesn't exist

        // Don't enable if process is still running
        if (!isExpandingCollapsing) {
            $btn.prop('disabled', false);
            $btn.css('pointer-events', 'auto');
        }

        const allExpanded = isAllExpanded();

        if (allExpanded) {
            $btn.removeClass('btn-outline-primary').addClass('btn-outline-secondary');
            $btn.html('<i class="fas fa-compress-alt"></i>');
        } else {
            $btn.removeClass('btn-outline-secondary').addClass('btn-outline-primary');
            $btn.html('<i class="fas fa-expand-alt"></i>');
        }
    }

    // Toggle Expand/Collapse All functionality
    // Use event delegation to ensure it works even after DOM updates
    $(document).on('click', '#btn-toggle-expand', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);

        // Prevent double-click: check flag first
        if (isExpandingCollapsing || $btn.prop('disabled')) {
            return false;
        }

        // Set flag and disable button immediately
        isExpandingCollapsing = true;
        $btn.prop('disabled', true);
        $btn.css('pointer-events', 'none'); // Extra protection

        const allExpanded = isAllExpanded();

        if (allExpanded) {
            // Collapse all
            table.rows().every(function() {
                const data = this.data();
                if (data && expanded_rows[data.id]) {
                    const tr = $(this.node());
                    const icon = tr.find('.details-control i');

                    if (icon.hasClass('fa-minus-square')) {
                        icon.removeClass('fa-minus-square').addClass('fa-plus-square');
                        collapseChildren(data.id);
                        expanded_rows[data.id] = false;
                    }
                }
            });
        } else {
            // Expand all (start with retryCount = 0)
            expandAllRecursive(0);
        }

        // Re-enable button and reset flag after a delay to ensure DOM is updated
        setTimeout(function() {
            isExpandingCollapsing = false;
            $btn.prop('disabled', false);
            $btn.css('pointer-events', 'auto');
            updateToggleButtonState();
            // Ensure loading is hidden
            if (loadingOperations > 0) {
                console.warn('[LOADING] Loading operations still active after toggle, forcing hide');
                forceHideTableLoading();
            }
        }, 1000); // Increased delay to prevent rapid clicks
    });

    function expandAllRecursive(retryCount = 0) {
        const rowsToExpand = [];
        const maxRetries = 3;

        // Collect all currently visible rows that can be expanded
        // Use search: 'none' to get all rows regardless of filter
        table.rows({search: 'none'}).every(function() {
            const data = this.data();
            if (!data || !data.id) return; // Skip invalid rows

            const tr = $(this.node());
            if (!tr || tr.length === 0) return; // Skip if row node not found

            const icon = tr.find('.details-control i');

            // Check if row can be expanded and is not already expanded
            const canExpand = data && (data.has_children || data.type !== 'sub_kegiatan');
            const isExpanded = expanded_rows[data.id] || false;
            const iconIsPlus = icon.length === 0 || icon.hasClass('fa-plus-square');

            if (canExpand && !isExpanded && iconIsPlus) {
                rowsToExpand.push({
                    id: data.id,
                    tr: tr,
                    icon: icon,
                    data: data
                });
            }
        });

        if (rowsToExpand.length === 0) {
            // If no rows to expand but we haven't retried, try again after a delay
            // This handles cases where table hasn't fully rendered yet
            if (retryCount < maxRetries) {
                setTimeout(function() {
                    expandAllRecursive(retryCount + 1);
                }, 300);
                return;
            }

            // All rows expanded (or no expandable rows), redraw to update icons
            console.log('[TABLE.DRAW] Calling draw() after expandAllRecursive complete');
            const orderBeforeDraw = logDOMOrder('BEFORE DRAW (expandAll complete)');

            table.draw(false);
            const orderAfterDraw = logDOMOrder('AFTER DRAW (expandAll complete)');

            // Restore order if changed
            if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'expandAll')) {
                console.log('[TABLE.DRAW] ✓ Order preserved after expandAll');
            }

            // Hide loading immediately when expansion is complete
            hideTableLoading();

            if (isAutoExpandingAfterLoad) {
                toastr.success('Semua data berhasil dimuat.');
                isAutoExpandingAfterLoad = false;
            }

            // Calculate average from all expanded data (only if server value not loaded)
            if (!serverAverageLoaded) {
                calculateOpdAverageKeuanganFromCache();
            }

            // Update button state and ensure it's enabled, reset flag
            setTimeout(function() {
                const $btn = $('#btn-toggle-expand');
                isExpandingCollapsing = false;
                $btn.prop('disabled', false);
                $btn.css('pointer-events', 'auto');
                updateToggleButtonState();
                // Ensure loading is hidden (in case it wasn't already)
                if (loadingOperations > 0) {
                    console.warn('[LOADING] Loading operations still active, forcing hide');
                    forceHideTableLoading();
                }
            }, 100);
            return;
        }

        // Sort rows by their DOM position to ensure correct order
        // This ensures parent rows are expanded in the order they appear in the table
        rowsToExpand.sort(function(a, b) {
            const posA = a.tr.index();
            const posB = b.tr.index();
            return posA - posB; // Sort by DOM position (ascending)
        });

        // Expand rows sequentially (not in parallel) to ensure correct insertion order
        let currentIndex = 0;

        function expandNext() {
            if (currentIndex >= rowsToExpand.length) {
                // All rows expanded, recursively expand the next level
                setTimeout(function() {
                    expandAllRecursive(0); // Recursively expand the next level (reset retry count)
                }, 150);
                return;
            }

            const item = rowsToExpand[currentIndex];
            currentIndex++;

            if (item.icon.length > 0) {
                item.icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            }
            expanded_rows[item.id] = true; // Mark as expanded immediately

            // Load children with callback - expand next only after this one completes
            loadAndInsertChildrenWithCallback(item.id, item.tr, function() {
                // After children are inserted, wait a bit to ensure DOM is fully updated
                // Do NOT call table.draw(false) here as it may reorder rows
                // The rows are already in correct DOM position after insertAfter
                setTimeout(function() {
                    expandNext();
                }, 150); // Delay to ensure DOM is updated before next insert
            });
        }

        // Start sequential expansion
        expandNext();
    }

    function loadAndInsertChildrenWithCallback(parent_id, parentTr, callback) {
        // Check cache first
        if (child_data_cache[parent_id]) {
            // Find the actual parent row in DOM before inserting
            let actualParentTr = findParentRowInDom(parent_id, parentTr);
            insertChildrenIntoTable(child_data_cache[parent_id], actualParentTr);
            if (callback) callback();
            return;
        }

        $.ajax({
            url: "{{ route('rfk-v2.fetchChildren') }}",
            method: 'GET',
            data: {
                parent_id: parent_id,
                month: $('#filter-month').val(),
                opd: $('#filter-opd').val()
            },
            success: function (children) {
                child_data_cache[parent_id] = children;
                // Find the actual parent row in DOM before inserting
                let actualParentTr = findParentRowInDom(parent_id, parentTr);
                insertChildrenIntoTable(children, actualParentTr);
                if (callback) callback();
            },
            error: function () {
                toastr.error('Failed to load children');
                if (callback) callback();
            }
        });
    }

    // Helper function to find parent row in DOM
    function findParentRowInDom(parentId, fallbackTr) {
        let actualParentTr = fallbackTr;
        let foundParent = false;

        // Search for the parent row by iterating through all rows
        table.rows({search: 'none'}).every(function() {
            const rowData = this.data();
            if (rowData && rowData.id && parseInt(rowData.id) === parentId) {
                const tr = $(this.node());
                if (tr.length > 0) {
                    actualParentTr = tr;
                    foundParent = true;
                    return false; // Stop iteration
                }
            }
        });

        if (!foundParent) {
            console.warn('Parent row not found in DOM for parent_id:', parentId, 'using fallback');
        }

        return actualParentTr;
    }

    function getListData() {
        const opd = $('#filter-opd').val();
        if (!opd) {
            list_data = [];
            currentIsOvertime = BASE_IS_OVERTIME;
            currentOpenOvertime = false;
            updateEditingState({
                is_overtime: currentIsOvertime,
                open_overtime: currentOpenOvertime,
                allow_edit: true,
                toggle_enabled: false
            });
            renderOvertimeSwitch({forceDisable: true});
            updateResyncButtonState();
            refreshTable();
            return;
        }

        $.ajax({
            async: true,
            crossDomain: true,
            url: "{{ route('rfk-v2.browse') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                month: $('#filter-month').val(),
                opd: opd
            },
            contentType: "application/json",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            beforeSend: function () {
                $('.loader').show();
                showTableLoading('Memuat data dari server...');
            },
            success: function (response) {
                $('.loader').hide();
                const rows = Array.isArray(response) ? response : (response.data || []);
                const meta = Array.isArray(response) ? {} : (response.meta || {});

                updateEditingState(meta);
                renderOvertimeSwitch();
                updateResyncButtonState();

                console.log('[GET LIST DATA] Received', rows.length, 'rows from server');
                if (rows.length > 0) {
                    console.log('[GET LIST DATA] Data order from server:', rows.map(r => `${r.code} (${r.type}, parent:${r.parent_id})`).join(' -> '));
                    isAutoExpandingAfterLoad = true;
                } else {
                    toastr.info('Tidak ada data untuk periode ini.');
                }

                list_data = rows;

                // === Dynamic Navbar Ranking Update ===
                var rankInfo = meta.rank_info || null;
                var navbarRank = $('#navbar-opd-rank');
                var navbarDropdown = $('#navbar-dropdown-opd-rank');
                if (rankInfo && rankInfo.rank) {
                    navbarRank.find('.rank-val').text(rankInfo.rank);
                    navbarRank.css('display', 'block');
                    navbarDropdown.find('.rank-val').text(rankInfo.rank);
                    navbarDropdown.find('.total-val').text(rankInfo.total);
                    navbarDropdown.css('display', 'block');
                } else {
                    navbarRank.css('display', 'none');
                    navbarDropdown.css('display', 'none');
                }

                // Fetch OPD average keuangan from server (all sub_kegiatan data)
                fetchOpdAverageKeuangan(function() {
                    console.log('[GET LIST DATA] Average fetched, OPD average:', opdAverageKeuangan.toFixed(2) + '%');

                    // Log DOM order before refresh
                    const orderBeforeRefresh = logDOMOrder('BEFORE GETLISTDATA REFRESH');
                    refreshTable();
                    // Log DOM order after refresh (will be logged inside refreshTable)

                    // Auto-expand all rows after data is loaded
                    // Use a longer delay to ensure table is fully rendered and DOM is ready
                    setTimeout(function() {
                        // Ensure all state is reset
                        expanded_rows = {};
                        child_data_cache = {};
                        isExpandingCollapsing = false; // Reset flag

                        // Ensure button is enabled before expanding
                        const $btn = $('#btn-toggle-expand');
                        $btn.prop('disabled', false);
                        $btn.css('pointer-events', 'auto');

                        // Show loading during expand all
                        showTableLoading('Memperluas semua data...');

                        // Do NOT call table.draw(false) here as it may reorder rows
                        // The data is already in correct hierarchical order from refreshTable()
                        // Just wait a bit to ensure DOM is fully updated, then expand
                        setTimeout(function() {
                            // Then expand all recursively (start with retryCount = 0)
                            expandAllRecursive(0);

                            // Safety timeout: force hide loading after 10 seconds if still showing
                            setTimeout(function() {
                                if (loadingOperations > 0) {
                                    console.warn('[LOADING] Safety timeout: forcing hide loading after 10 seconds');
                                    forceHideTableLoading();
                                }
                            }, 10000);
                        }, 200);
                    }, 600); // Increased delay to ensure table is fully rendered
                });
            },
            error: function (response, thrownError) {
                $('.loader').hide();
                hideTableLoading();
                toastr.error('Failed to load data');
                renderOvertimeSwitch();
                updateResyncButtonState();
            }
        })
    }

    // Initialize money mask for inline inputs
    $(document).on('focus', '.uang-input', function() {
        $(this).mask('#.##0,00', {reverse: true});
    });

    // Auto-calculate fields when pagu_anggaran or bulan_ini changes
    $(document).on('input keyup blur', 'input[name="pagu_anggaran"], input[name="bulan_ini"]', function() {
        const $input = $(this);
        const $row = $input.closest('tr');
        const rowData = table.row($row).data();

        if (!rowData || rowData.type !== 'sub_kegiatan' || (!rowData._editing && !rowData._creating)) {
            return;
        }

        // Get current values
        let paguValue = $row.find('input[name="pagu_anggaran"]').val() || '0';
        let bulanIniValue = $row.find('input[name="bulan_ini"]').val() || '0';
        const bulanLalu = parseFloat(rowData.bulan_lalu) || 0;

        // Parse money format
        paguValue = paguValue.toString().replace(/\./g, '').replace(',', '.');
        bulanIniValue = bulanIniValue.toString().replace(/\./g, '').replace(',', '.');

        const pagu = parseFloat(paguValue) || 0;
        const bulanIni = parseFloat(bulanIniValue) || 0;

        // Update row data for calculation
        rowData.pagu_anggaran = pagu;
        rowData.bulan_ini = bulanIni;

        // Calculate sd_bulan_ini (bulan_lalu + bulan_ini)
        const sdBulanIni = bulanLalu + bulanIni;
        const $sdBulanIniInput = $row.find('input[name="sd_bulan_ini"]');
        if ($sdBulanIniInput.length) {
            $sdBulanIniInput.val(formatToCurrency(sdBulanIni));
        }

        // Calculate sisa_anggaran (pagu_anggaran - sd_bulan_ini)
        const sisaAnggaran = pagu - sdBulanIni;
        const $sisaAnggaranInput = $row.find('input[name="sisa_anggaran"]');
        if ($sisaAnggaranInput.length) {
            $sisaAnggaranInput.val(formatToCurrency(sisaAnggaran));
        }

        // Calculate keuangan % (sd_bulan_ini / pagu_anggaran * 100)
        const keuangan = pagu > 0 ? (sdBulanIni / pagu) * 100 : 0;
        const $keuanganInput = $row.find('input[name="keuangan"]');
        if ($keuanganInput.length) {
            const colorClass = getKeuanganColorClass(keuangan);
            $keuanganInput.val(keuangan.toFixed(2) + '%').removeClass('text-danger text-warning text-success text-dark').addClass(colorClass);
        }

        // Update row data for save
        rowData.sd_bulan_ini = sdBulanIni;
        rowData.sisa_anggaran = sisaAnggaran;
        rowData.keuangan = keuangan;
    });

    // Add new inline row for creating
    function addInlineRow(parent_id, parent_type) {
        if (editingLocked) {
            toastr.warning(OVERTIME_LOCK_MESSAGE);
            return;
        }
        // Determine child type
        const childTypeMap = {
            'opd': 'program',
            'program': 'kegiatan',
            'kegiatan': 'sub_kegiatan'
        };
        const childType = childTypeMap[parent_type] || 'sub_kegiatan';

        // Get parent row data
        let parentData = null;
        let parentTr = null;
        table.rows().every(function() {
            const data = this.data();
            if (data.id === parent_id) {
                parentData = data;
                parentTr = $(this.node());
            }
        });

        if (!parentData) {
            toastr.error('Parent row tidak ditemukan');
            return;
        }

        // Generate suggested code (will be overridden by server if empty)
        let suggestedCode = '';
        if (parentData.code) {
            // Count existing siblings from list_data (parse to int for type safety)
            const siblingCount = list_data.filter(r =>
                r && r.parent_id && parseInt(r.parent_id) === parseInt(parent_id) && r.type === childType
            ).length;
            const nextNumber = siblingCount + 1;

            if (parent_type === 'opd') {
                // OPD → Program: {parent_code}.{count+1} (2 digits)
                suggestedCode = parentData.code + '.' + String(nextNumber).padStart(2, '0');
            } else if (parent_type === 'program') {
                // Program → Kegiatan: {parent_code}.{count+1}
                suggestedCode = parentData.code + '.' + nextNumber;
            } else if (parent_type === 'kegiatan') {
                // Kegiatan → Sub Kegiatan: {parent_code}.{count+1}
                suggestedCode = parentData.code + '.' + nextNumber;
            }
        }

        // Create new row data
        const newRowData = {
            id: null,
            parent_id: parent_id,
            type: childType,
            code: suggestedCode,
            uraian: '',
            pagu_anggaran: 0,
            bulan_lalu: 0,
            bulan_ini: 0,
            sd_bulan_ini: 0,
            sisa_anggaran: 0,
            keuangan: 0,
            fisik: 0,
            keterangan: '',
            has_children: false,
            _creating: true,
            _originalData: null
        };

        // Add row to table
        const newRow = table.row.add(newRowData).node();
        const $newRow = $(newRow);

        // Add to list_data so that sibling count and other operations detect it
        list_data.push(newRowData);

        // Mark as child and add CSS class
        $newRow.attr('data-parent-id', newRowData.parent_id);
        $newRow.addClass('child-of-' + newRowData.parent_id);
        $newRow.attr('data-level', getHierarchyLevel(newRowData.type));

        // Insert after parent or after last child of parent
        if (parentTr) {
            // Find if parent has visible children
            const childrenRows = $('.child-of-' + parent_id);
            if (childrenRows.length > 0) {
                // Insert after last child
                $newRow.insertAfter(childrenRows.last());
            } else {
                // Insert directly after parent
                $newRow.insertAfter(parentTr);
            }
        }

        // CRITICAL: For addInlineRow, we need to preserve the new row position
        // Store the new row reference for restore
        const newRowReference = {
            id: null,
            code: newRowData.code,
            uraian: newRowData.uraian,
            parent_id: newRowData.parent_id,
            type: newRowData.type,
            tr: $newRow // Store tr reference directly
        };

        // Wait a bit for DOM to update, then get order AFTER new row is inserted
        setTimeout(function() {
            // Get order AFTER new row is inserted (so it's included in restore)
            const orderBeforeDraw = logDOMOrder('BEFORE DRAW (addInlineRow - after insert)');

            // Verify new row is in the order, if not, add it manually
            let hasNewRow = orderBeforeDraw.some(r =>
                (r.id === null || r.id === undefined) &&
                r.parent_id === newRowData.parent_id &&
                r.type === newRowData.type
            );

            if (!hasNewRow) {
                console.warn('[ADD INLINE ROW] New row not found in DOM order, adding manually');
                // Find parent in orderBeforeDraw and insert new row after it
                const parentIndex = orderBeforeDraw.findIndex(r => r.id === parent_id);
                if (parentIndex >= 0) {
                    // Find last sibling of parent
                    let insertIndex = parentIndex + 1;
                    for (let i = parentIndex + 1; i < orderBeforeDraw.length; i++) {
                        if (orderBeforeDraw[i].parent_id === parent_id) {
                            insertIndex = i + 1;
                        } else {
                            break;
                        }
                    }
                    orderBeforeDraw.splice(insertIndex, 0, newRowReference);
                    hasNewRow = true;
                    console.log('[ADD INLINE ROW] Manually added new row to orderBeforeDraw at index', insertIndex);
                }
            } else {
                console.log('[ADD INLINE ROW] New row found in DOM, will preserve it');
            }

            // Redraw table to show input fields
            console.log('[TABLE.DRAW] Calling draw() after addInlineRow');
            table.draw(false);
            const orderAfterDraw = logDOMOrder('AFTER DRAW (addInlineRow)');

            // Check if new row is still present after draw
            const hasNewRowAfter = orderAfterDraw.some(r =>
                (r.id === null || r.id === undefined) &&
                r.parent_id === newRowData.parent_id &&
                r.type === newRowData.type
            );
            if (hasNewRow && !hasNewRowAfter) {
                console.error('[ADD INLINE ROW] ❌ New row LOST after draw! This should not happen.');
            }

            // Restore order if changed (this will include the new row if it was in orderBeforeDraw)
            if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'addInlineRow')) {
                console.log('[TABLE.DRAW] ✓ Order preserved after addInlineRow');
            } else {
                // Verify new row is still present after restore
                const orderAfterRestore = logDOMOrder('AFTER RESTORE CHECK (addInlineRow)');
                const hasNewRowAfterRestore = orderAfterRestore.some(r =>
                    (r.id === null || r.id === undefined) &&
                    r.parent_id === newRowData.parent_id &&
                    r.type === newRowData.type
                );
                if (hasNewRow && !hasNewRowAfterRestore) {
                    console.error('[ADD INLINE ROW] ❌ New row LOST after restore!');
                } else if (hasNewRow && hasNewRowAfterRestore) {
                    console.log('[ADD INLINE ROW] ✓ New row preserved after restore');
                }
            }
        }, 50); // Increased delay to ensure DOM is fully updated

        // Initialize money mask for new inputs
        setTimeout(() => {
            $(newRow).find('.uang-input').mask('#.##0,00', {reverse: true});
        }, 100);
    }

    // Enable inline editing for existing row
    function enableInlineEdit(id) {
        if (editingLocked) {
            toastr.warning(OVERTIME_LOCK_MESSAGE);
            return;
        }
        // Convert to number for comparison
        id = parseInt(id);

        if (isNaN(id) || id <= 0) {
            toastr.error('ID tidak valid: ' + id);
            return;
        }

        // First, check if any row is already in editing/creating mode
        let alreadyEditing = false;
        table.rows().every(function() {
            const rowData = this.data();
            if (rowData && (rowData._editing || rowData._creating)) {
                const rowId = parseInt(rowData.id);
                if (rowId === id) {
                    alreadyEditing = true;
                    return false;
                }
            }
        });

        if (alreadyEditing) {
            toastr.warning('Row sedang dalam mode edit');
            return;
        }

        // Find the row in list_data first (source of truth)
        let foundInListData = false;
        let targetIndex = -1;

        for (let i = 0; i < list_data.length; i++) {
            const row = list_data[i];
            if (row && row.id && parseInt(row.id) === id) {
                // Check if already editing
                if (row._editing || row._creating) {
                    toastr.warning('Row sedang dalam mode edit');
                    return;
                }

                // Store original data
                row._originalData = JSON.parse(JSON.stringify(row));
                row._editing = true;
                foundInListData = true;
                targetIndex = i;
                break;
            }
        }

        // If not found in list_data, try to find in DataTable directly
        if (!foundInListData) {
            let foundInTable = false;
            let tableRowData = null;

            // Search in DataTable
            table.rows({search: 'none'}).every(function() {
                const rowData = this.data();
                if (rowData && rowData.id && parseInt(rowData.id) === id) {
                    // Found in table, check if already editing
                    if (rowData._editing || rowData._creating) {
                        toastr.warning('Row sedang dalam mode edit');
                        return false;
                    }

                    // Add to list_data for future reference
                    tableRowData = rowData;
                    const existingIndex = list_data.findIndex(r => r && r.id === id);
                    if (existingIndex === -1) {
                        list_data.push(rowData);
                        targetIndex = list_data.length - 1;
                    } else {
                        targetIndex = existingIndex;
                        list_data[existingIndex] = rowData;
                    }

                    // Store original data
                    rowData._originalData = JSON.parse(JSON.stringify(rowData));
                    rowData._editing = true;
                    foundInTable = true;
                    return false;
                }
            });

            if (!foundInTable) {
                const availableIds = list_data.map(r => r ? {id: r.id, type: r.type, code: r.code} : null).filter(r => r);
                toastr.error('Row tidak ditemukan dengan ID: ' + id);
                console.error('Row not found in list_data or DataTable. Looking for ID:', id, 'Available rows in list_data:', availableIds);
                return;
            }

            // Update list_data with the found row
            if (tableRowData && targetIndex >= 0) {
                list_data[targetIndex] = tableRowData;
            }
        }

        // Ensure targetIndex is set
        if (targetIndex < 0) {
            console.error('targetIndex not set, cannot update row');
            toastr.error('Gagal mengaktifkan mode edit');
            return;
        }

        // Update the row in DataTable - find and update all instances
        const listRow = list_data[targetIndex];

        if (!listRow || !listRow._editing) {
            console.error('Row data not properly set for editing', listRow);
            toastr.error('Gagal mengaktifkan mode edit');
            return;
        }

        // Find the row in DataTable and update it
        let rowFound = false;
        let rowNode = null;

        table.rows({search: 'none'}).every(function() {
            const rowData = this.data();
            if (rowData && rowData.id && parseInt(rowData.id) === id) {
                // Get the row API object and node
                const rowApi = this;
                rowNode = this.node();

                console.log('Updating row data:', rowData.id, 'Before:', rowData._editing);

                // Create a new data object with all properties from listRow
                const newData = {...listRow};

                console.log('After update, newData._editing:', newData._editing);

                // Use row().data() to set the new data - this is the proper DataTable way
                rowApi.data(newData);

                // Invalidate the row to force re-render with new data
                rowApi.invalidate('data');
                rowFound = true;
                return false; // Stop iteration
            }
        });

        // If row was found and updated, redraw to show input fields
        if (rowFound) {
            // Force redraw with data update
            console.log('[TABLE.DRAW] Calling draw() after enableInlineEdit');
            const orderBeforeDraw = logDOMOrder('BEFORE DRAW (enableInlineEdit)');
            table.draw(false);
            const orderAfterDraw = logDOMOrder('AFTER DRAW (enableInlineEdit)');
            if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'enableInlineEdit')) {
                console.log('[TABLE.DRAW] ✓ Order preserved after enableInlineEdit');
            }

            // Verify after draw
            setTimeout(() => {
                table.rows({search: 'none'}).every(function() {
                    const rowData = this.data();
                    if (rowData && rowData.id && parseInt(rowData.id) === id) {
                        console.log('After draw, row._editing:', rowData._editing);
                        return false;
                    }
                });
            }, 100);
        } else {
            // If not found in table, try to refresh from list_data
            console.warn('Row not found in DataTable, refreshing table');
            refreshTable();
            // Wait a bit then try to enable edit again
            setTimeout(function() {
                enableInlineEdit(id);
            }, 200);
            return;
        }

        // Initialize money mask after redraw
        setTimeout(() => {
            let maskApplied = false;
            table.rows().every(function() {
                const rowData = this.data();
                if (!rowData || !rowData.id) return;

                const rowId = parseInt(rowData.id);
                if (rowId === id && rowData._editing) {
                    const rowNode = $(this.node());
                    rowNode.find('.uang-input').mask('#.##0,00', {reverse: true});
                    maskApplied = true;
                    return false; // Stop iteration
                }
            });

            if (!maskApplied) {
                console.warn('Money mask not applied for row ID:', id);
            }
        }, 200);
    }

    // Save inline row (create or update)
    function saveInlineRow(id) {
        let rowData = null;
        let rowNode = null;

        // Convert id to number if not null
        const searchId = id === null || id === 'null' ? null : parseInt(id);

        // Find the row that is currently being edited or created
        table.rows().every(function() {
            const data = this.data();
            if (!data) return;

            const isEditing = data._editing || data._creating;
            if (!isEditing) return;

            // For creating (id is null)
            if (searchId === null && (data.id === null || data.id === undefined)) {
                rowData = data;
                rowNode = $(this.node());
                return false; // Stop iteration
            }

            // For editing (id matches)
            if (searchId !== null && data.id !== null && data.id !== undefined) {
                const rowId = parseInt(data.id);
                if (rowId === searchId) {
                    rowData = data;
                    rowNode = $(this.node());
                    return false; // Stop iteration
                }
            }
        });

        if (!rowData || !rowNode) {
            toastr.error('Row tidak ditemukan atau tidak sedang dalam mode edit');
            return;
        }

        // Collect form data from inputs
        const formData = {
            parent_id: rowData.parent_id,
            type: rowData.type,
            month: $('#filter-month').val(),
            opd: $('#filter-opd').val()
        };

        // Get values from input fields
        rowNode.find('input, textarea').each(function() {
            const name = $(this).attr('name');
            let value = $(this).val();

            if (name === 'pagu_anggaran' || name === 'bulan_ini' || name === 'sd_bulan_ini' || name === 'sisa_anggaran') {
                // Parse money format: Indonesian format (1.000.000,00)
                // Format: dots as thousand separator, comma as decimal separator
                if (value && value.toString().trim() !== '') {
                    let rawValue = value.toString().trim();
                    const originalValue = rawValue; // For debugging

                    // Remove all dots (thousand separators) - dots are not part of the number
                    rawValue = rawValue.replace(/\./g, '');

                    // Replace comma with dot (decimal separator) - convert to standard format
                    rawValue = rawValue.replace(',', '.');

                    // Parse to float and convert back to string to ensure correct format
                    const numValue = parseFloat(rawValue);
                    if (isNaN(numValue) || numValue < 0) {
                        console.warn('Invalid money value:', originalValue, '->', rawValue);
                        value = '0';
                    } else {
                        // Use the parsed number directly to avoid string issues
                        value = numValue.toString();
                        // Debug log to verify parsing
                        if (originalValue !== value) {
                            console.log('Money parsing:', originalValue, '->', rawValue, '->', value);
                        }
                    }
                } else {
                    value = '0';
                }
            } else if (name === 'keuangan') {
                // Parse percentage value (remove % sign)
                if (value && value.toString().trim() !== '') {
                    value = value.toString().replace('%', '').trim();
                    const numValue = parseFloat(value);
                    if (isNaN(numValue) || numValue < 0) {
                        value = '0';
                    } else {
                        value = numValue.toString();
                    }
                } else {
                    value = '0';
                }
            }

            formData[name] = value;
        });

        // For calculated fields, use rowData if input field is readonly/not found
        // This ensures calculated values are included even if they're readonly inputs
        if (rowData.type === 'sub_kegiatan') {
            // Use calculated values from rowData if available
            if (rowData.sd_bulan_ini !== undefined && !formData.sd_bulan_ini) {
                formData.sd_bulan_ini = rowData.sd_bulan_ini.toString();
            }
            if (rowData.sisa_anggaran !== undefined && !formData.sisa_anggaran) {
                formData.sisa_anggaran = rowData.sisa_anggaran.toString();
            }
            if (rowData.keuangan !== undefined && !formData.keuangan) {
                formData.keuangan = rowData.keuangan.toString();
            }
        }

        // Add id if editing
        if (rowData._editing && rowData.id) {
            formData.id = rowData.id;
        }

        // Set default values for non-sub_kegiatan or if not provided
        if (rowData.type !== 'sub_kegiatan') {
            formData.pagu_anggaran = formData.pagu_anggaran || '0';
            formData.bulan_ini = formData.bulan_ini || '0';
            formData.fisik = formData.fisik || '0';
        }

        // Validate required fields
        if (formData.type === 'opd' || formData.type === 'program') {
            if (formData.uraian) {
                formData.uraian = formData.uraian.toUpperCase();
            }
        }

        if (!formData.code || !formData.uraian) {
            toastr.error('Kode dan Uraian harus diisi');
            return;
        }

        // Validate sub_kegiatan specific fields
        if (rowData.type === 'sub_kegiatan') {
            if (!formData.pagu_anggaran || parseFloat(formData.pagu_anggaran) <= 0) {
                formData.pagu_anggaran = '0';
                // toastr.error('Pagu Anggaran harus diisi untuk Sub Kegiatan');
                // return;
            }
            if (!formData.bulan_ini) {
                formData.bulan_ini = '0';
            }
            if (!formData.fisik) {
                formData.fisik = '0';
                // toastr.error('Realisasi Fisik (%) harus diisi untuk Sub Kegiatan');
                // return;
            }
        }

        // Show loading
        const saveBtn = rowNode.find('button[onclick*="saveInlineRow"]');
        saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        // Submit via AJAX
        $.ajax({
            type: 'POST',
            url: "{{ route('rfk-v2.store') }}",
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            success: function(response) {
                if (response.success) {
                    console.log('[SAVE INLINE ROW] Save successful, response:', response);
                    toastr.success(response.message || 'Berhasil menyimpan data');

                    // Get the row node before updating (for new rows, we need to find it)
                    const savedData = response.data;
                    const isNewRow = !rowData.id || rowData.id === null || rowData.id === undefined;

                    // Update the saved row in table
                    if (isNewRow) {
                        // For new rows, find the row with _creating flag and update it
                        let newRowFound = false;
                        const formattedData = formatServerDataToTableData(savedData);
                        table.rows({search: 'none'}).every(function() {
                            const currentRowData = this.data();
                            if (currentRowData && (currentRowData.id === null || currentRowData.id === undefined) && currentRowData._creating) {
                                // This is the new row we just created
                                this.data(formattedData);

                                // Update in list_data
                                const index = list_data.findIndex(r => r && (r.id === null || r.id === undefined) && r._creating);
                                if (index >= 0) {
                                    list_data[index] = formattedData;
                                }

                                newRowFound = true;
                                return false; // Stop iteration
                            }
                        });

                        if (!newRowFound) {
                            console.warn('[SAVE INLINE ROW] New row not found, adding to table');
                            // Add new row to table
                            table.row.add(formattedData);
                            list_data.push(formattedData);
                        }

                        // Also add to child_data_cache of parent if parent's child cache is active
                        const parentId = formattedData.parent_id;
                        if (parentId && child_data_cache[parentId]) {
                            // Check if already in child_data_cache to prevent duplicates
                            const cacheIndex = child_data_cache[parentId].findIndex(c => c.id === savedData.id);
                            if (cacheIndex === -1) {
                                child_data_cache[parentId].push(savedData);
                            } else {
                                child_data_cache[parentId][cacheIndex] = savedData;
                            }
                            // Sort cache by code
                            child_data_cache[parentId].sort((a, b) => (a.code || '').toString().localeCompare((b.code || '').toString()));
                        }
                    } else {
                        // For existing rows, update directly
                        updateRowInTable(savedData.id, savedData);
                    }

                    // Update parent rows if any
                    if (response.updated_parents && response.updated_parents.length > 0) {
                        updateParentRowsInTable(response.updated_parents);
                    }

                    // Reset editing flags
                    table.rows({search: 'none'}).every(function() {
                        const currentRowData = this.data();
                        if (currentRowData) {
                            delete currentRowData._editing;
                            delete currentRowData._creating;
                            delete currentRowData._originalData;
                        }
                    });

                    // Sort list_data hierarchically to ensure consistency
                    list_data = sortListDataHierarchically(list_data);

                    // Redraw table but restore order if changed
                    const orderBeforeDraw = logDOMOrder('BEFORE SAVE DRAW');
                    table.draw(false);
                    const orderAfterDraw = logDOMOrder('AFTER SAVE DRAW');
                    if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'saveInlineRow')) {
                        console.log('[SAVE INLINE ROW] Table updated without reload');
                    }

                    // Hide loading
                    hideTableLoading();
                    saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                } else {
                    hideTableLoading();
                    toastr.error(response.message || 'Gagal menyimpan data');
                    if (response.errors && Array.isArray(response.errors)) {
                        response.errors.forEach(function(error) {
                            toastr.error(error.message || error);
                        });
                    }
                    saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
                }
            },
            error: function(xhr) {
                hideTableLoading();
                let errorMessage = 'Gagal menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
            }
        });
    }

    // Cancel inline editing
    function cancelInlineEdit(id) {
        // Convert id to number if not null
        const searchId = id === null || id === 'null' ? null : parseInt(id);
        let found = false;
        let rowDataToCancel = null;

        // First, find and update in list_data (source of truth)
        if (searchId === null) {
            // For creating (id is null)
            for (let i = list_data.length - 1; i >= 0; i--) {
                const row = list_data[i];
                if (row && (row.id === null || row.id === undefined) && row._creating) {
                    list_data.splice(i, 1); // Remove from list_data
                    found = true;
                    break;
                }
            }
        } else {
            // For editing (id matches)
            for (let i = 0; i < list_data.length; i++) {
                const row = list_data[i];
                if (row && row.id && parseInt(row.id) === searchId && (row._editing || row._creating)) {
                    if (row._originalData) {
                        // Restore original data
                        Object.assign(row, row._originalData);
                        delete row._editing;
                        delete row._originalData;
                    } else {
                        // Just remove flags if no original data
                        delete row._editing;
                        delete row._creating;
                    }
                    rowDataToCancel = row;
                    found = true;
                    break;
                }
            }
        }

        // Then update DataTable
        table.rows().every(function() {
            const rowData = this.data();
            if (!rowData) return;

            const isEditing = rowData._editing || rowData._creating;
            if (!isEditing) return;

            // For creating (id is null)
            if (searchId === null && (rowData.id === null || rowData.id === undefined)) {
                if (rowData._creating) {
                    // Remove the row if it was a new creation
                    this.remove();
                }
                return false; // Stop iteration
            }

            // For editing (id matches)
            if (searchId !== null && rowData.id !== null && rowData.id !== undefined) {
                const rowId = parseInt(rowData.id);
                if (rowId === searchId) {
                    if (rowDataToCancel) {
                        // Use data from list_data (source of truth)
                        const newData = JSON.parse(JSON.stringify(rowDataToCancel));
                        this.data(newData);
                    } else if (rowData._originalData) {
                        // Restore original data
                        Object.assign(rowData, rowData._originalData);
                        delete rowData._editing;
                        delete rowData._originalData;
                        this.data(rowData);
                    } else {
                        // Just remove flags if no original data
                        delete rowData._editing;
                        delete rowData._creating;
                        this.data(rowData);
                    }
                    return false; // Stop iteration
                }
            }
        });

        // Redraw table to reflect changes
        console.log('[TABLE.DRAW] Calling draw() after cancelInlineEdit');
        const orderBeforeDraw = logDOMOrder('BEFORE DRAW (cancelInlineEdit)');
        table.draw(false);
        const orderAfterDraw = logDOMOrder('AFTER DRAW (cancelInlineEdit)');
        if (!restoreDOMOrderIfChanged(orderBeforeDraw, orderAfterDraw, 'cancelInlineEdit')) {
            console.log('[TABLE.DRAW] ✓ Order preserved after cancelInlineEdit');
        }

        if (!found) {
            console.warn('Row not found for cancel, id:', id);
        }
    }

    // Remove row from table without reloading
    function removeRowFromTable(rowId) {
        let rowRemoved = false;
        const idsToRemove = [parseInt(rowId)];

        // First, collect all children IDs to remove
        function collectChildren(parentId) {
            table.rows({search: 'none'}).every(function() {
                const rowData = this.data();
                if (rowData && rowData.parent_id && parseInt(rowData.parent_id) === parseInt(parentId)) {
                    if (rowData.id) {
                        const childId = parseInt(rowData.id);
                        idsToRemove.push(childId);
                        collectChildren(childId); // Recursively collect grandchildren
                    }
                }
            });
        }

        collectChildren(rowId);

        // Remove all collected rows
        idsToRemove.forEach(function(idToRemove) {
            // Remove from child_data_cache of any parent
            Object.keys(child_data_cache).forEach(function(parentId) {
                if (child_data_cache[parentId]) {
                    const cacheIndex = child_data_cache[parentId].findIndex(c => parseInt(c.id) === idToRemove);
                    if (cacheIndex >= 0) {
                        child_data_cache[parentId].splice(cacheIndex, 1);
                    }
                }
            });
            // If this ID itself has children in the cache, delete its cache entry
            if (child_data_cache[idToRemove]) {
                delete child_data_cache[idToRemove];
            }

            table.rows({search: 'none'}).every(function() {
                const rowData = this.data();
                if (rowData && rowData.id && parseInt(rowData.id) === idToRemove) {
                    // Remove from table
                    this.remove();

                    // Remove from list_data
                    const index = list_data.findIndex(r => r && r.id && parseInt(r.id) === idToRemove);
                    if (index >= 0) {
                        list_data.splice(index, 1);
                    }

                    rowRemoved = true;
                    return false; // Stop iteration
                }
            });
        });

        if (rowRemoved) {
            // Only recalculate from cache if server hasn't provided the value yet
            if (!serverAverageLoaded) {
                calculateOpdAverageKeuanganFromCache();
            }

            // Sort list_data hierarchically after removal
            list_data = sortListDataHierarchically(list_data);

            // Don't draw here - let the caller handle drawing after parent updates
            // This prevents multiple draws and ensures parent updates are visible
            console.log('[DELETE ROW] Removed', idsToRemove.length, 'row(s) from table (draw will be handled by caller)');
        } else {
            console.warn('[DELETE ROW] Row not found in table:', rowId);
        }

        return rowRemoved;
    }

    function modalDelete(id) {
        if (editingLocked) {
            toastr.warning(OVERTIME_LOCK_MESSAGE);
            return;
        }
        // Keep delete modal for now
        swal({
            title: "Hapus Data",
            text: "Apakah anda yakin akan menghapus data ini?",
            icon: "warning",
            buttons: ["Batal", "Ya"],
            dangerMode: true
        }).then(function(res) {
            if (res) {
                showTableLoading('Menghapus data...');
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/rfk-v2/cascadeDelete/') }}/" + id,
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Berhasil menghapus data');

                            console.log('[DELETE ROW] Response:', response);
                            console.log('[DELETE ROW] Updated parents:', response.updated_parents);

                            // Remove row from table first
                            removeRowFromTable(id);

                            // Update parent rows if any (after removing child)
                            if (response.updated_parents && response.updated_parents.length > 0) {
                                console.log('[DELETE ROW] Updating', response.updated_parents.length, 'parent rows');

                                // Update all parents first (skip draw for each to avoid multiple redraws)
                                const updatedParentIds = [];
                                response.updated_parents.forEach(function(parentData) {
                                    console.log('[DELETE ROW] Updating parent:', {
                                        id: parentData.id,
                                        code: parentData.code,
                                        type: parentData.type,
                                        pagu: parentData.pagu_anggaran,
                                        bulan_ini: parentData.bulan_ini,
                                        sd_bulan_ini: parentData.sd_bulan_ini,
                                        sisa: parentData.sisa_anggaran,
                                        keuangan: parentData.keuangan,
                                        fisik: parentData.fisik
                                    });

                                    // Verify parent exists in table before updating
                                    let parentExists = false;
                                    table.rows({search: 'none'}).every(function() {
                                        const rowData = this.data();
                                        if (rowData && rowData.id && parseInt(rowData.id) === parseInt(parentData.id)) {
                                            parentExists = true;
                                            return false; // Stop iteration
                                        }
                                    });

                                    if (parentExists) {
                                        // Update with skipDraw=true to batch all updates
                                        updateRowInTable(parentData.id, parentData, true);
                                        updatedParentIds.push(parentData.id);
                                    } else {
                                        console.warn('[DELETE ROW] Parent not found in table:', parentData.id, parentData.code);
                                    }
                                });

                                // Sort list_data after all updates
                                list_data = sortListDataHierarchically(list_data);

                                // Final draw with order restoration (only once after all updates)
                                const orderBeforeFinalDraw = logDOMOrder('BEFORE DELETE FINAL DRAW');
                                table.draw(false);
                                const orderAfterFinalDraw = logDOMOrder('AFTER DELETE FINAL DRAW');
                                if (!restoreDOMOrderIfChanged(orderBeforeFinalDraw, orderAfterFinalDraw, 'deleteRowFinal')) {
                                    console.log('[DELETE ROW] All parents updated successfully');
                                }

                                // Verify parent values after update
                                setTimeout(function() {
                                    response.updated_parents.forEach(function(parentData) {
                                        table.rows({search: 'none'}).every(function() {
                                            const rowData = this.data();
                                            if (rowData && rowData.id && parseInt(rowData.id) === parseInt(parentData.id)) {
                                                const paguMatch = Math.abs(rowData.pagu_anggaran - parseFloat(parentData.pagu_anggaran || 0)) < 0.01;
                                                const bulanIniMatch = Math.abs(rowData.bulan_ini - parseFloat(parentData.bulan_ini || 0)) < 0.01;
                                                const sdMatch = Math.abs(rowData.sd_bulan_ini - parseFloat(parentData.sd_bulan_ini || 0)) < 0.01;

                                                console.log('[DELETE ROW] Verified parent after update:', {
                                                    id: rowData.id,
                                                    code: rowData.code,
                                                    pagu: rowData.pagu_anggaran,
                                                    'expected_pagu': parentData.pagu_anggaran,
                                                    'pagu_match': paguMatch,
                                                    bulan_ini: rowData.bulan_ini,
                                                    'expected_bulan_ini': parentData.bulan_ini,
                                                    'bulan_ini_match': bulanIniMatch,
                                                    sd_bulan_ini: rowData.sd_bulan_ini,
                                                    'expected_sd': parentData.sd_bulan_ini,
                                                    'sd_match': sdMatch
                                                });

                                                if (!paguMatch || !bulanIniMatch || !sdMatch) {
                                                    console.error('[DELETE ROW] ❌ Parent values do not match!');
                                                }

                                                return false;
                                            }
                                        });
                                    });
                                }, 500);
                            } else {
                                console.warn('[DELETE ROW] No updated parents in response');
                            }

                            hideTableLoading();
                            console.log('[DELETE ROW] Table updated without reload');
                        } else {
                            hideTableLoading();
                            toastr.error(response.message || 'Gagal menghapus data');
                        }
                    },
                    error: function(xhr) {
                        hideTableLoading();
                        let errorMessage = 'Gagal menghapus data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            }
        });
    }

    function save() {
        const opd = $('#filter-opd').val();
        if (!opd) {
            toastr.error('Pilih OPD terlebih dahulu');
            return;
        }

        const data = {
            "month": $(`#filter-month`).val(),
            "opd": opd
        };

        swal({
            title: "Simpan Data RFK",
            text: "Apakah anda yakin akan menyimpan data RFK ini?",
            icon: "warning",
            buttons: ["Batal", "Ya"]
        }).then(function (res) {
                if (res) {
                    $.ajax({
                        type: 'POST',
                        dataType: "json",
                        data: JSON.stringify(data),
                        contentType: 'application/json',
                        url: "{!! route('rfk-v2.save') !!}",
                        headers: {
                            'X-CSRF-TOKEN': "{{csrf_token()}}"
                        },
                        beforeSend: function(){
                            $(".loader").show();
                        },
                        success: function (response) {
                            $(".loader").hide();
                            getListData();
                        toastr.success('Berhasil Menyimpan Data');
                        },
                        error: function(xhr){
                            $(".loader").hide();
                        toastr.error('Gagal Menyimpan Data');
                    }
                });
            }
        });
    }

    // Function to update URL query parameters
    function updateURLParameters() {
        const opd = $('#filter-opd').val();
        const month = $('#filter-month').val();

        const url = new URL(window.location);

        // For security, don't show OPD filter in URL for admin RFK
        if (!IS_ADMIN_RFK) {
            if (opd) {
                url.searchParams.set('opd', opd);
            } else {
                url.searchParams.delete('opd');
            }
        } else {
            // Remove opd parameter from URL for admin RFK
            url.searchParams.delete('opd');
        }

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        // Update URL without reloading the page
        window.history.pushState({}, '', url);
    }

    // Function to load filters from URL query parameters
    function loadFiltersFromURL() {
        const url = new URL(window.location);
        const opdParam = url.searchParams.get('opd');
        const monthParam = url.searchParams.get('month');

        // Set month filter if exists in URL
        if (monthParam) {
            $('#filter-month').val(monthParam);
        }

        // Set OPD filter if exists in URL (and not admin_rfk with default OPD)
        if (opdParam && !IS_ADMIN_RFK) {
            $('#filter-opd').val(opdParam);
        }
    }

    $(function () {
        if (IS_SUPERADMIN) {
            renderOvertimeSwitch({forceDisable: true});
        }

        updateResyncButtonState();

        // Load filters from URL first
        loadFiltersFromURL();

        if (IS_ADMIN_RFK) {
            if (DEFAULT_OPD_ID) {
                $('#filter-opd').val(DEFAULT_OPD_ID);
            }
            $('#filter-opd').trigger('change');
        } else {
            // For non-admin, trigger change if OPD is set from URL
            const opd = $('#filter-opd').val();
            if (opd) {
                $('#filter-opd').trigger('change');
            }
        }

        if ($overtimeSwitch.length) {
            $overtimeSwitch.on('change', function () {
                if (isUpdatingOvertimeSwitch) {
                    return;
                }
                handleOvertimeToggle($(this).is(':checked'));
            });
        }

        if ($resyncButton.length) {
            $resyncButton.on('click', function () {
                if ($(this).prop('disabled')) {
                    return;
                }
                syncFromPrevious();
            });
        }
    });

    $('#filter-opd').change(function () {
        updateURLParameters();
        updateResyncButtonState();
        getListData();
    });

    $('#filter-month').change(function () {
        updateURLParameters();
        updateResyncButtonState();
        getListData();
    });

    // Export handlers
    $('#export-lengkap').click(function(e) {
        e.preventDefault();
        const month = $('#filter-month').val();

        if (!month) {
            toastr.error('Silakan pilih bulan terlebih dahulu');
            return;
        }

        const url = "{{ route('rfk-v2.export', ['type' => 1]) }}" + '?month=' + month;
        window.open(url, '_blank');
    });

    $('#export-rekap').click(function(e) {
        e.preventDefault();
        const month = $('#filter-month').val();

        if (!month) {
            toastr.error('Silakan pilih bulan terlebih dahulu');
            return;
        }

        const url = "{{ route('rfk-v2.export', ['type' => 2]) }}" + '?month=' + month;
        window.open(url, '_blank');
    });

    $('#export-program').click(function(e) {
        e.preventDefault();
        const month = $('#filter-month').val();

        if (!month) {
            toastr.error('Silakan pilih bulan terlebih dahulu');
            return;
        }

        const url = "{{ route('rfk-v2.export', ['type' => 3]) }}" + '?month=' + month;
        window.open(url, '_blank');
    });

    // Note: Using onclick handlers directly in button HTML, no need for event delegation
    // The onclick handlers are defined in the render functions and should work directly
</script>
@stop