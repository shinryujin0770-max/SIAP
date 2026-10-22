@extends('adminlte::page')

@section('title', 'RFK Modern')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-rocket mr-2 text-primary"></i> RFK Modern & Interaktif</h1>
            <h6 class="text-muted" id="current-period-label">&nbsp;</h6>
        </div>
        <div class="col-sm-6 text-right">
            <span id="rank-badge" class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 0.9rem; display: none;"></span>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid pb-5">

<!-- Loading Overlay -->
<div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div class="text-center text-white">
        <div class="spinner-border text-primary mb-3" role="status" style="width:3rem; height:3rem;"></div>
        <h5 id="overlay-title" class="font-weight-bold mb-1"></h5>
        <p id="overlay-text" class="mb-0"></p>
    </div>
</div>

    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon bg-primary-soft"><i class="fas fa-wallet text-primary"></i></div>
                <div>
                    <small class="text-muted d-block">Total Pagu</small>
                    <h5 class="font-weight-bold mb-0" id="stat-total-pagu">Rp 0</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon bg-success-soft"><i class="fas fa-coin text-success"></i></div>
                <div>
                    <small class="text-muted d-block">Realisasi Keuangan</small>
                    <h5 class="font-weight-bold mb-0 text-success" id="stat-keu-percent">0.00%</h5>
                    <div class="progress progress-xs mb-0">
                        <div class="progress-bar bg-success" id="stat-keu-progress" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon bg-info-soft"><i class="fas fa-tasks text-info"></i></div>
                <div>
                    <small class="text-muted d-block">Realisasi Fisik</small>
                    <h5 class="font-weight-bold mb-0 text-info" id="stat-fisik-percent">0.00%</h5>
                    <div class="progress progress-xs mb-0">
                        <div class="progress-bar bg-info" id="stat-fisik-progress" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon bg-danger-soft"><i class="fas fa-piggy-bank text-danger"></i></div>
                <div>
                    <small class="text-muted d-block">Sisa Anggaran</small>
                    <h5 class="font-weight-bold mb-0 text-danger" id="stat-sisa-anggaran">Rp 0</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-lg mb-3">
        <div class="card-body py-3">
            <div class="row align-items-center">
                @if($is_superadmin)
                <div class="col-md-4">
                    <label class="small text-muted mb-1">OPD</label>
                    <select id="filter-opd" class="form-control form-control-sm select2">
                        <option value="">Pilih OPD</option>
                        @foreach($opds as $opd)
                        <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                    <input type="hidden" id="filter-opd" value="{{ $id_opd }}">
                @endif
                <div class="col-md-3">
                    <label class="small text-muted mb-1">Periode</label>
                    <input type="month" id="filter-month" class="form-control form-control-sm" value="{{ date('Y-m', strtotime('-1 month')) }}">
                </div>
                <div class="col-md-5">
                    <label class="small text-muted mb-1">Cari Uraian</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="modern-search" class="form-control border-left-0" placeholder="Cari Uraian...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-lg" id="table-card">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold mb-0" id="card-title-opd">Pilih OPD</h5>
                    <small class="text-muted" id="card-title-period">&nbsp;</small>
                </div>
                <div class="text-center">
                    <span id="load-status" class="badge badge-light border text-muted" style="display:none;">
                        <i class="fas fa-circle-notch fa-spin mr-1"></i> Memuat data...
                    </span>
                    <button id="btn-toggle-overtime" class="btn btn-warning btn-xs ml-2 font-weight-bold" style="display:none;" onclick="toggleOvertimeAccess()">
                        <i class="fas fa-lock-open mr-1"></i> Buka Kunci Akses OPD
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive rfk-scroll">
            <table class="table table-bordered table-sm mb-0" id="modern-table">
                <thead class="text-center" style="font-size: 0.78rem;">
                    <tr class="bg-light">
                        <th style="width: 44px;">Expand</th>
                        <th style="width: 116px;">Aksi</th>
                        <th style="width: 104px;">Kode</th>
                        <th style="min-width: 310px;">Uraian</th>
                        <th style="min-width: 150px;">
                            Pagu Anggaran
                            <button type="button" class="btn btn-xs btn-outline-secondary ml-1" onclick="togglePaguHistory()" title="Tampilkan/Sembunyikan Riwayat Perubahan Pagu"><i class="fas fa-history"></i></button>
                        </th>
                        <th id="pagu-history-th" style="display:none; min-width: 140px;">Riwayat Pagu</th>
                        <th style="min-width: 110px;">Bulan Lalu</th>
                        <th style="min-width: 110px;">Bulan Ini</th>
                        <th style="min-width: 120px;">s.d. Bulan Ini</th>
                        <th style="min-width: 120px;">Sisa Anggaran</th>
                        <th style="width: 100px;">Keuangan</th>
                        <th style="width: 88px;">Fisik</th>
                        <th style="min-width: 150px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="modern-tbody" style="font-size: 0.85rem;"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title font-weight-bold" id="edit-modal-title">Edit Detail RFK</h5>
                    <small class="text-muted d-block" id="edit-modal-breadcrumb"></small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quick-edit-form">
                <div class="modal-body pt-3">
                    <input type="hidden" id="edit-id" name="id">
                    <input type="hidden" id="edit-code" name="code">
                    <input type="hidden" id="edit-type" name="type">
                    <input type="hidden" id="edit-parent-id" name="parent_id">
                    <div class="form-group mb-3">
                        <label class="small text-muted mb-1">Uraian</label>
                        <p id="edit-uraian-text" class="font-weight-bold mb-0"></p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Bulan Lalu (Otomatis)</label>
                                <div class="input-group input-group-merge border rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0">Rp</span></div>
                                    <input type="text" class="form-control border-0 uang-input" id="edit-bulan-lalu" name="bulan_lalu">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Bulan Ini</label>
                                <div class="input-group input-group-merge border border-primary rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0 text-primary">Rp</span></div>
                                    <input type="text" class="form-control border-0 uang-input font-weight-bold" id="edit-bulan-ini" name="bulan_ini">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Pagu Anggaran</label>
                                <div class="input-group input-group-merge border border-primary rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0 text-primary">Rp</span></div>
                                    <input type="text" class="form-control border-0 uang-input font-weight-bold" id="edit-pagu" name="pagu_anggaran">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Realisasi Fisik (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control rounded" id="edit-fisik" name="fisik">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">S.D. Bulan Ini (Otomatis)</label>
                                <div class="input-group input-group-merge border rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0">Rp</span></div>
                                    <input type="text" class="form-control border-0 bg-light" id="edit-sd-total" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Sisa Anggaran (Otomatis)</label>
                                <div class="input-group input-group-merge border rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0">Rp</span></div>
                                    <input type="text" class="form-control border-0 bg-light" id="edit-sisa" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Keterangan / Permasalahan</label>
                        <textarea class="form-control rounded" id="edit-keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="add-modal-title">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-form">
                <div class="modal-body pt-3">
                    <input type="hidden" id="add-parent-id" name="parent_id">
                    <input type="hidden" id="add-type" name="type">
                    <div class="form-group">
                        <label class="small font-weight-bold">Uraian</label>
                        <input type="text" class="form-control" id="add-uraian" name="uraian">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Pagu Anggaran</label>
                                <div class="input-group input-group-merge border rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0">Rp</span></div>
                                    <input type="text" class="form-control border-0 uang-input" id="add-pagu" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Bulan Ini</label>
                                <div class="input-group input-group-merge border rounded overflow-hidden">
                                    <div class="input-group-prepend"><span class="input-group-text bg-light border-0">Rp</span></div>
                                    <input type="text" class="form-control border-0 uang-input" id="add-bulan-ini" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Fisik (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" id="add-fisik" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Keterangan</label>
                        <textarea class="form-control" id="add-keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        border: 1px solid #eef1f5;
    }
    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .bg-primary-soft { background: #e8f0fe; }
    .bg-success-soft { background: #e6f7ee; }
    .bg-info-soft    { background: #e5f6fb; }
    .bg-danger-soft  { background: #fdeaea; }

    .rfk-scroll { max-height: 68vh; overflow: auto; }
    #modern-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f1f3f6;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600;
        vertical-align: middle;
    }
    #modern-table td {
        vertical-align: middle;
        padding: .3rem .45rem;
    }
    .row-level-opd          { background-color: #e8f0fe !important; font-weight: bold; }
    .row-level-program      { background-color: #f6f0fb !important; font-weight: 600; }
    .row-level-kegiatan     { background-color: #fdf3ea !important; }
    .row-level-sub_kegiatan { background-color: #ffffff; }
    #modern-table tbody tr:hover td { filter: brightness(.97); }
    #modern-table tbody tr { cursor: pointer; }

    .pill-keu, .pill-fisik {
        display: inline-block;
        min-width: 60px;
        padding: 3px 8px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.78rem;
        text-align: center;
    }
    .pill-danger  { background: #fde8e8; color: #c0392b; }
    .pill-warning { background: #fdf3d0; color: #b7791f; }
    .pill-success { background: #e2f6e9; color: #1e7e45; }
</style>
@stop

@section('js')
<script>
    const DEFAULT_OPD_ID = {!! json_encode($id_opd) !!};
    const IS_SUPERADMIN = {!! json_encode($is_superadmin) !!};
    const OVERTIME_LOCK_MESSAGE = 'Perubahan data RFK sudah ditutup untuk periode ini.';

    let currentFilter = 'all';
    let editingLocked = false;
    let treeData = [];
    let opdSummary = null;
    let showPaguHistory = false;
    const expandedRows = new Set();
    const nodeStore = {};
    let loadSeq = 0;

    $(document).ready(function() {
        if (IS_SUPERADMIN) {
            $('.select2').select2({ width: '100%' });
            if (!$('#filter-opd').val()) {
                const firstOpt = $('#filter-opd option').eq(1).val();
                if (firstOpt) $('#filter-opd').val(firstOpt);
            }
        } else if (DEFAULT_OPD_ID) {
            $('#filter-opd').val(String(DEFAULT_OPD_ID));
        }

        getListData();

        $('#modern-search').on('keyup', debounce(renderTable, 250));

        $('.filter-btn').on('click', function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            renderTable();
        });

        $('#filter-opd, #filter-month').on('change', getListData);

        $('#quick-edit-form').on('submit', function(e) { e.preventDefault(); saveData(); });
        $('#add-form').on('submit', function(e) { e.preventDefault(); saveNew(); });

        $('#edit-bulan-ini').on('input', function() { formatMoneyInput(this); calculateSd(); });
        $('#edit-pagu').on('input', function() { formatMoneyInput(this); calculateSd(); });
        $('#edit-bulan-lalu').on('input', function() { 
            formatMoneyInput(this); 
            calculateSd(); 
        });
        $('#add-pagu, #add-bulan-ini').on('input', function() { formatMoneyInput(this); });
    });

    async function getListData() {
        const opd = $('#filter-opd').val();
        const month = $('#filter-month').val();
        const seq = ++loadSeq;

        if (!opd) {
            $('#modern-tbody').empty();
            $('#table-card').hide();
            return;
        }

        $('#modern-tbody').empty();
        $('#load-status').removeClass('badge-light text-muted border badge-danger badge-success').addClass('badge-light text-muted border').html('<i class="fas fa-circle-notch fa-spin mr-1"></i> Memuat data...').show();
        showLoadingOverlay('Memuat Data RFK', 'Mohon tunggu, data sedang diambil dari server...');

        try {
            const response = await $.ajax({
                url: "{{ route('rfk-v2.fetchTree') }}",
                method: 'GET',
                data: { month: month, opd: opd },
                dataType: 'json',
                timeout: 300000
            });

            if (seq !== loadSeq) return;

            Object.keys(nodeStore).forEach(k => delete nodeStore[k]);
            expandedRows.clear();

            treeData = response.data || [];
            opdSummary = treeData.find(r => r.type === 'opd') || treeData[0] || null;

            function storeRecursive(nodes) {
                (nodes || []).forEach(n => {
                    nodeStore[n.id] = n;
                    expandedRows.add(parseInt(n.id, 10));
                    if (n.children && n.children.length > 0) {
                        storeRecursive(n.children);
                    }
                });
            }
            storeRecursive(treeData);

            const meta = response.meta || {};
            editingLocked = !meta.allow_edit;

            if (IS_SUPERADMIN && meta.toggle_enabled) {
                const isOpen = meta.open_overtime;
                if (isOpen) {
                    $('#btn-toggle-overtime').removeClass('btn-warning').addClass('btn-danger').html('<i class="fas fa-lock mr-1"></i> Tutup Kunci Akses OPD').show();
                } else {
                    $('#btn-toggle-overtime').removeClass('btn-danger').addClass('btn-warning').html('<i class="fas fa-lock-open mr-1"></i> Buka Kunci Akses OPD').show();
                }
            } else {
                $('#btn-toggle-overtime').hide();
            }

            renderRank(meta.rank_info || null);

            if (opdSummary) {
                updateStats();
                $('#card-title-opd').text(opdSummary.uraian);
                $('#card-title-period').text(formatMonthLabel(month));
            }

            renderTable();
            $('#table-card').show();

            const totalRows = $('#modern-tbody tr').length;
            $('#load-status').removeClass('badge-light text-muted border badge-danger').addClass('badge-success text-white').html('<i class="fas fa-check-circle mr-1"></i> Semua data termuat (' + totalRows + ' baris)').show();
            hideLoadingOverlay();
        } catch (e) {
            if (seq !== loadSeq) return;
            console.error('RFK load error:', e);
            $('#load-status').removeClass('badge-light text-muted border').addClass('badge-danger text-white').html('<i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat data').show();
            let msg = 'Gagal memuat data RFK';
            if (e && e.responseJSON && e.responseJSON.message) msg = e.responseJSON.message;
            else if (e && e.statusText) msg += ' (' + e.statusText + ')';
            else if (e && e.message) msg += ' - ' + e.message;
            toastr.error(msg);
        } finally {
            if (seq === loadSeq) {
                $('#loading-overlay').hide();
            }
        }
    }

    function fetchChildren(parentId) {
        return $.ajax({
            url: "{{ route('rfk-v2.fetchChildren') }}",
            method: 'GET',
            data: {
                parent_id: parentId,
                month: $('#filter-month').val(),
                opd: $('#filter-opd').val()
            },
            dataType: 'json'
        });
    }

    async function hydrate(node, depth) {
        // Unused when using fetchTree
    }

    function togglePaguHistory() {
        showPaguHistory = !showPaguHistory;
        if (showPaguHistory) {
            $('#pagu-history-th').show();
            $('.pagu-history-td').show();
        } else {
            $('#pagu-history-th').hide();
            $('.pagu-history-td').hide();
        }
    }

    function showPaguHistoryModal(node) {
        if (!node.code) return;
        const year = $('#filter-month').val().split('-')[0];
        const opd = $('#filter-opd').val();
        
        $.ajax({
            url: "{{ route('rfk-v2.paguHistory') }}",
            method: 'GET',
            data: { code: node.code, opd: opd, year: year },
            success: function(res) {
                let html = `<h5>${res.uraian} (${res.code})</h5><table class="table table-sm"><thead><tr><th>Bulan</th><th>Pagu</th><th>Perubahan</th></tr></thead><tbody>`;
                res.history.forEach(h => {
                    html += `<tr><td>${h.month_name}</td><td>${h.has_data ? formatCurrency(h.pagu) : '-'}</td><td>${h.status !== 'tetap' ? h.status + (h.delta !== 0 ? ' (' + (h.delta > 0 ? '+' : '') + formatCurrency(h.delta) + ')' : '') : 'Tetap'}</td></tr>`;
                });
                html += '</tbody></table>';
                $('#modal-pagu-body').html(html);
                $('#paguHistoryModal').modal('show');
            }
        });
    }

    function showPaguHistoryModalById(nodeId) {
        const node = nodeStore[nodeId];
        if (node) showPaguHistoryModal(node);
    }

    function renderPaguBadge(node) {
        const delta = parseFloat(node.pagu_delta) || 0;
        const prev = node.prev_pagu !== null ? formatCurrency(node.prev_pagu) : 'Tidak ada data bln lalu';
        const onClick = `onclick="showPaguHistoryModalById(${node.id})" style="cursor:pointer"`;
        if (delta === 0) {
            return `<span class="badge badge-light border text-muted" ${onClick} title="Bulan lalu: ${prev}">Tetap (Rp 0)</span>`;
        } else if (delta > 0) {
            return `<span class="badge badge-success" ${onClick} title="Bulan lalu: ${prev}">+ ${formatCurrency(delta)}</span>`;
        } else {
            return `<span class="badge badge-danger" ${onClick} title="Bulan lalu: ${prev}">- ${formatCurrency(Math.abs(delta))}</span>`;
        }
    }

    function updateStats() {
        if (!opdSummary) return;
        $('#stat-total-pagu').text(formatCurrency(opdSummary.pagu_anggaran));
        $('#stat-keu-percent').text(parseFloat(opdSummary.keuangan).toFixed(2) + '%');
        $('#stat-keu-progress').css('width', Math.min(opdSummary.keuangan, 100) + '%');
        $('#stat-fisik-percent').text(parseFloat(opdSummary.fisik).toFixed(2) + '%');
        $('#stat-fisik-progress').css('width', Math.min(opdSummary.fisik, 100) + '%');
        $('#stat-sisa-anggaran').text(formatCurrency(opdSummary.sisa_anggaran));
    }

    // ================= RENDER =================
    function renderTable() {
        const search = ($('#modern-search').val() || '').toLowerCase();
        const tbody = $('#modern-tbody');
        tbody.empty();

        if (!treeData.length) {
            tbody.html('<tr><td colspan="12" class="text-center text-muted py-4">Belum ada data program pada periode ini.</td></tr>');
            return;
        }

        if (opdSummary) {
            tbody.append(renderRow(opdSummary, 0, search));
        }

        let visible = 0;
        treeData.forEach(program => {
            const rows = renderSubtree(program, 1, search, true);
            if (rows) {
                tbody.append(rows.html);
                visible += rows.count;
            }
        });

        if (visible === 0) {
            tbody.append('<tr><td colspan="12" class="text-center text-muted py-4">Tidak ada data yang cocok dengan pencarian/filter.</td></tr>');
        }
    }

    function renderSubtree(node, level, search, parentVisible) {
        const selfMatch = !search || (node.uraian || '').toLowerCase().includes(search) || String(node.code || '').toLowerCase().includes(search);
        const childHtml = [];
        let count = 0;

        let filterBlocked = false;
        if (node.type === 'sub_kegiatan' && currentFilter !== 'all') {
            const keu = parseFloat(node.keuangan) || 0;
            if (currentFilter === 'low' && keu >= 40) filterBlocked = true;
            if (currentFilter === 'mid' && (keu < 40 || keu > 80)) filterBlocked = true;
            if (currentFilter === 'high' && keu <= 80) filterBlocked = true;
        }

        (node.children || []).forEach(child => {
            const res = renderSubtree(child, level + 1, search, selfMatch);
            if (res.html) {
                childHtml.push(res.html);
                count += res.count;
            }
        });

        const show = (selfMatch || childHtml.length > 0) && !filterBlocked;
        if (!show) return { html: '', count: 0 };

        let html = renderRow(node, level, search);
        const isExpanded = expandedRows.has(node.id) || !!search;
        if (childHtml.length > 0 && isExpanded) {
            html += childHtml.join('');
        }

        return { html: html, count: count + 1 };
    }

    function renderRow(node, level, search) {
        const indent = level * 18;
        const hasChildren = (node.children || []).length > 0;
        const isExpanded = expandedRows.has(node.id) || !!search;

        let expander = '';
        if (hasChildren) {
            expander = `<i class="fas fa-${isExpanded ? 'minus' : 'plus'}-square text-primary" style="cursor:pointer; font-size:13px;"></i>`;
        }

        const uraian = (node.type === 'opd' || node.type === 'program')
            ? (node.uraian || '').toUpperCase()
            : (node.uraian || '');

        const bold = hasChildren ? 'font-weight-bold' : '';

        return `
            <tr class="row-level-${node.type}" data-id="${node.id}">
                <td class="text-center" onclick="toggleRow(${node.id}, event)">${expander}</td>
                <td class="text-center" onclick="event.stopPropagation()">${actionButtons(node)}</td>
                <td><div style="padding-left:${indent}px" class="${bold}">${escapeHtml(node.code || '')}</div></td>
                <td><div style="padding-left:${indent}px" class="${bold}">${escapeHtml(uraian)}</div></td>
                <td class="text-right ${bold}">${formatCurrency(node.pagu_anggaran)}</td>
                <td class="text-center pagu-history-td" style="display:${showPaguHistory ? '' : 'none'}">${renderPaguBadge(node)}</td>
                <td class="text-right text-muted">${formatCurrency(node.bulan_lalu)}</td>
                <td class="text-right">${formatCurrency(node.bulan_ini)}</td>
                <td class="text-right">${formatCurrency(node.sd_bulan_ini)}</td>
                <td class="text-right">${formatCurrency(node.sisa_anggaran)}</td>
                <td class="text-center">${moneyPill(node.keuangan)}</td>
                <td class="text-center">${fisikPill(node.fisik)}</td>
                <td class="text-muted ket-td" title="${escapeAttr(node.keterangan || '')}">${escapeHtml(node.keterangan || '')}</td>
            </tr>
        `;
    }

    function actionButtons(node) {
        const disabled = editingLocked;
        const dAttr = disabled ? 'disabled' : '';
        const dClass = disabled ? ' disabled' : '';
        const lockTitle = disabled ? OVERTIME_LOCK_MESSAGE : '';

        let html = '<div class="d-flex justify-content-center">';

        if (node.type === 'opd') {
            html += `<button class="btn btn-success btn-xs mr-1${dClass}" title="${lockTitle || 'Tambah Program'}" ${dAttr} onclick="openAdd(${node.id}, 'opd', event)"><i class="fa fa-plus"></i></button>`;
        } else if (node.type === 'program') {
            html += `<button class="btn btn-success btn-xs mr-1${dClass}" title="${lockTitle || 'Tambah Kegiatan'}" ${dAttr} onclick="openAdd(${node.id}, 'program', event)"><i class="fa fa-plus"></i></button>`;
        } else if (node.type === 'kegiatan') {
            html += `<button class="btn btn-success btn-xs mr-1${dClass}" title="${lockTitle || 'Tambah Sub Kegiatan'}" ${dAttr} onclick="openAdd(${node.id}, 'kegiatan', event)"><i class="fa fa-plus"></i></button>`;
        }

        if (node.type !== 'opd') {
            html += `<button class="btn btn-info btn-xs mr-1${dClass}" title="${lockTitle || 'Edit'}" ${dAttr} onclick="openEditById(${node.id}, event)"><i class="fa fa-edit"></i></button>`;
            html += `<button class="btn btn-danger btn-xs${dClass}" title="${lockTitle || 'Hapus'}" ${dAttr} onclick="removeNode(${node.id}, event)"><i class="fa fa-trash"></i></button>`;
        }

        html += '</div>';
        return html;
    }

    function toggleRow(id, e) {
        if (e) e.stopPropagation();
        if (expandedRows.has(id)) {
            expandedRows.delete(id);
        } else {
            expandedRows.add(id);
        }
        renderTable();
    }

    // Row click = quick popup edit for editable levels
    $('#modern-tbody').on('click', 'tr', function(e) {
        if ($(e.target).closest('button, td:first-child').length) return;
        const id = $(this).data('id');
        if (id) openEditById(id, e);
    });

    function openEditById(id, e) {
        if (e) e.stopPropagation();
        const node = nodeStore[id];
        if (!node) return;

        if (editingLocked && !IS_SUPERADMIN) {
            toastr.warning(OVERTIME_LOCK_MESSAGE + ' Hubungi Super Admin untuk membuka kunci akses.');
            return;
        }

        if (node.type === 'opd') return;

        openEdit(node);
    }

    function openEdit(item) {
        $('#edit-id').val(item.id);
        $('#edit-bulan-lalu').prop('readonly', false).addClass('uang-input').removeClass('bg-light');
        $('#edit-code').val(item.code || '');
        $('#edit-type').val(item.type);
        $('#edit-parent-id').val(item.parent_id || '');
        $('#edit-uraian-text').text(item.uraian || '');
        $('#edit-modal-title').text('Edit ' + typeLabel(item.type));
        $('#edit-modal-breadcrumb').text(buildBreadcrumb(item));
        $('#edit-bulan-lalu').val(formatToCurrencyInput(item.bulan_lalu));
        $('#edit-bulan-ini').val(formatToCurrencyInput(item.bulan_ini));
        $('#edit-pagu').val(formatToCurrencyInput(item.pagu_anggaran));
        $('#edit-fisik').val(parseFloat(item.fisik) || 0);
        $('#edit-keterangan').val(item.keterangan || '');
        $('#edit-modal-title').data('original', item);

        calculateSd();
        $('#quickEditModal').modal('show');
        setTimeout(function() { $('#edit-bulan-ini').focus().select(); }, 400);
    }

    function calculateSd() {
        const pagu = parseCurrency($('#edit-pagu').val());
        const bulanLalu = parseCurrency($('#edit-bulan-lalu').val());
        const bulanIni = parseCurrency($('#edit-bulan-ini').val());
        const total = bulanLalu + bulanIni;

        $('#edit-sd-total').val(formatToCurrencyInput(total));
        $('#edit-sisa').val(formatToCurrencyInput(pagu - total));

        if (total > pagu && pagu > 0) {
            $('#edit-bulan-ini').addClass('is-invalid');
            $('#edit-sd-total').closest('.input-group').addClass('border-danger');
        } else {
            $('#edit-bulan-ini').removeClass('is-invalid');
            $('#edit-sd-total').closest('.input-group').removeClass('border-danger');
        }
    }

    function saveData() {
        const original = $('#edit-modal-title').data('original');
        if (!original) return;

        const pagu = parseCurrency($('#edit-pagu').val());
        const bulanLalu = parseCurrency($('#edit-bulan-lalu').val());
        const bulanIni = parseCurrency($('#edit-bulan-ini').val());

        if ((bulanLalu + bulanIni) > pagu && pagu > 0) {
            toastr.error('Realisasi melebihi Pagu Anggaran!');
            return;
        }

        const payload = {
            id: original.id,
            code: original.code || '',
            uraian: original.uraian || '',
            type: original.type,
            parent_id: original.parent_id || null,
            pagu_anggaran: pagu || '0',
            bulan_lalu: bulanLalu || '0',
            bulan_ini: bulanIni || '0',
            fisik: parseFloat($('#edit-fisik').val()) || '0',
            keterangan: $('#edit-keterangan').val() || '',
            month: $('#filter-month').val(),
            opd: $('#filter-opd').val()
        };

        submitPayload(payload, function() {
            $('#quickEditModal').modal('hide');
        }, $('#quick-edit-form button[type=submit]'));
    }

    function submitPayload(payload, onSuccess, $btn) {
        const originalHtml = $btn ? $btn.html() : null;
        if ($btn) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        }

        $.ajax({
            url: "{{ route('rfk-v2.store') }}",
            method: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    // Update local node store from saved server response (no full reload)
                    if (res.data) {
                        const saved = res.data;
                        const target = nodeStore[saved.id];
                        if (target) {
                            ['code','uraian','type','parent_id','pagu_anggaran','bulan_lalu','bulan_ini','sd_bulan_ini','sisa_anggaran','keuangan','fisik','keterangan','angkas','angkas_prosentase','fisik_rupiah'].forEach(function(k) {
                                if (typeof saved[k] !== 'undefined') target[k] = saved[k];
                            });
                        }
                    }
                    
                    // Refresh UI only
                    renderTable();
                    if (opdSummary) updateStats();
                    toastr.success(res.message || 'Data berhasil disimpan');
                    toastr.info('Koreksi bulan lalu otomatis disinkronkan ke bulan-bulan berikutnya.');
                    if (onSuccess) onSuccess();
                } else {
                    toastr.error(res.message || 'Gagal menyimpan data');
                    if (res.errors) res.errors.forEach(err => toastr.error(err.message));
                }
            },
            error: function(xhr) {
                let msg = 'Gagal menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                toastr.error(msg);
            },
            complete: function() {
                if ($btn) {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            }
        });
    }

    // ================= ADD =================
    function openAdd(parentId, parentType, e) {
        if (e) e.stopPropagation();
        if (editingLocked) {
            toastr.warning(OVERTIME_LOCK_MESSAGE);
            return;
        }

        const childType = parentType === 'opd' ? 'program' : (parentType === 'program' ? 'kegiatan' : 'sub_kegiatan');

        $('#add-parent-id').val(parentId);
        $('#add-type').val(childType);
        $('#add-modal-title').text('Tambah ' + typeLabel(childType));
        $('#add-uraian').val('');
        $('#add-pagu').val('0');
        $('#add-bulan-ini').val('0');
        $('#add-fisik').val('0');
        $('#add-keterangan').val('');

        $('#addModal').modal('show');
        setTimeout(function() { $('#add-uraian').focus(); }, 400);
    }

    function saveNew() {
        const uraian = ($('#add-uraian').val() || '').trim();
        if (!uraian) {
            toastr.error('Uraian wajib diisi');
            return;
        }

        const payload = {
            code: '',
            uraian: uraian,
            type: $('#add-type').val(),
            parent_id: $('#add-parent-id').val(),
            pagu_anggaran: parseCurrency($('#add-pagu').val()) || '0',
            bulan_ini: parseCurrency($('#add-bulan-ini').val()) || '0',
            fisik: parseFloat($('#add-fisik').val()) || '0',
            keterangan: $('#add-keterangan').val() || '',
            month: $('#filter-month').val(),
            opd: $('#filter-opd').val()
        };

        submitPayload(payload, function() {
            $('#addModal').modal('hide');
        }, $('#add-form button[type=submit]'));
    }

    // ================= DELETE =================
    function removeNode(id, e) {
        if (e) e.stopPropagation();
        if (editingLocked) {
            toastr.warning(OVERTIME_LOCK_MESSAGE);
            return;
        }

        const node = nodeStore[id];
        if (!node) return;

        swal({
            title: 'Hapus Data',
            text: 'Data "' + (node.uraian || '') + '" beserta turunannya akan dihapus. Lanjutkan?',
            icon: 'warning',
            buttons: ['Batal', 'Hapus'],
            dangerMode: true
        }).then(function(ok) {
            if (!ok) return;

            $.ajax({
                url: "{{ url('/rfk-v2/cascadeDelete') }}/" + id,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function() {
                    toastr.success('Data berhasil dihapus');
                    getListData();
                },
                error: function(xhr) {
                    let msg = 'Gagal menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    toastr.error(msg);
                }
            });
        });
    }

    // ================= HELPERS =================
    function typeLabel(type) {
        return {
            opd: 'OPD',
            program: 'Program',
            kegiatan: 'Kegiatan',
            sub_kegiatan: 'Sub Kegiatan'
        }[type] || type;
    }

    function buildBreadcrumb(item) {
        const parts = [];
        let current = item;
        let guard = 0;
        while (current && guard < 5) {
            parts.unshift(typeLabel(current.type));
            current = current.parent_id ? nodeStore[current.parent_id] : null;
            guard++;
        }
        return parts.join(' > ');
    }

    function showLoadingOverlay(title, text) {
        $('#overlay-title').text(title);
        $('#overlay-text').text(text);
        $('#loading-overlay').css('display', 'flex');
    }

    function hideLoadingOverlay() {
        $('#loading-overlay').hide();
    }

    function toggleOvertimeAccess() {
        const opd = $('#filter-opd').val();
        const month = $('#filter-month').val();
        const currentlyOpen = $('#btn-toggle-overtime').hasClass('btn-danger');

        $.ajax({
            url: "{{ route('rfk-v2.toggleOvertime') }}",
            method: 'POST',
            data: JSON.stringify({ opd: opd, month: month, open: !currentlyOpen }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                toastr.success(res.message);
                getListData();
            },
            error: function() {
                toastr.error('Gagal mengubah status akses');
            }
        });
    }

    function renderRank(rankInfo) {
        const $b = $('#rank-badge');
        if (!rankInfo || !rankInfo.rank) {
            $b.hide();
            return;
        }
        const monthLabel = formatMonthLabel(rankInfo.year + '-' + rankInfo.month);
        $b.html('<i class="fas fa-trophy mr-1"></i> Peringkat OPD: ' + rankInfo.rank + ' dari ' + rankInfo.total + ' (' + monthLabel + ')');
        $b.show();
    }

    function formatMonthLabel(monthValue) {
        try {
            if (!monthValue) return '';
            const parts = monthValue.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
            return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(d);
        } catch (e) {
            return monthValue || '';
        }
    }

    function formatCurrency(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(parseFloat(val) || 0);
    }

    function formatToCurrencyInput(val) {
        return new Intl.NumberFormat('de-DE', { maximumFractionDigits: 0 }).format(parseFloat(val) || 0);
    }

    function parseCurrency(str) {
        return parseFloat(String(str || '').replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    function formatMoneyInput(el) {
        let raw = (el.value || '').replace(/[^\d,]/g, '');
        if (!raw) {
            el.value = '';
            return;
        }
        const parts = raw.split(',');
        const intPart = parseInt(parts[0] || '0', 10);
        const decPart = parts.length > 1 ? ',' + parts[1].slice(0, 2) : '';
        el.value = new Intl.NumberFormat('de-DE').format(intPart) + decPart;
    }

    function moneyPill(val) {
        const p = parseFloat(val) || 0;
        let cls = 'pill-danger';
        if (p >= 40 && p <= 80) cls = 'pill-warning';
        if (p > 80) cls = 'pill-success';
        return `<span class="pill-keu ${cls}">${p.toFixed(2)}%</span>`;
    }

    function fisikPill(val) {
        const p = parseFloat(val) || 0;
        let cls = 'pill-danger';
        if (p >= 40 && p <= 80) cls = 'pill-warning';
        if (p > 80) cls = 'pill-success';
        return `<span class="pill-fisik ${cls}">${p.toFixed(2)}%</span>`;
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return escapeHtml(str).replace(/'/g, '&#39;');
    }

    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() { func.apply(context, args); }, wait);
        };
    }
</script>
@stop
