@extends('adminlte::page')

@section('title', 'RFK')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-none rounded-lg">
            <div class="card-header border-0">
                <div class="row mb-3">
                    <div class="col-sm-12 col-lg-3">
                        <label for="filter-opd">OPD</label>
                        <select id="filter-opd" class="form-control">
                            <option value="">Pilih OPD</option>
                            @foreach($opds as $opd)
                            <option value="{{$opd->id}}">{{$opd->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-lg-3">
                        <label for="filter-month">Bulan</label>
                        <input type="month" id="filter-month" class="form-control" value="{{date('Y-m')}}">
                    </div>
                </div>
            </div>
            <div class="card-body">
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
                                <th>Bulan Lalu</th>
                                <th>Bulan Ini</th>
                                <th>s.d. Bulan Ini</th>
                                <th>Keuangan</th>
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

@section('js')
<script>
    let list_data = [];
    let charts_data;
    let chart;
    let has_chart = false;
    let expanded_rows = {};
    let child_data_cache = {};
    let isExpandingCollapsing = false; // Flag to prevent double-click

    function download(type) {
        const opd = $('#filter-opd').val();
        if (!opd) {
            toastr.error('Pilih OPD terlebih dahulu');
            return;
        }
        window.open(
            `{{ url('/rfk-v2/laporan/') }}/${type}?` + `month=${$(`#filter-month`).val()}&opd=${opd}`,
            '_blank'
        );
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
                    if (row._editing || row._creating) {
                        const level = getHierarchyLevel(row.type);
                        const indent = level * 20;
                        return `<div style="padding-left: ${indent}px">
                            <textarea class="form-control form-control-sm" name="uraian" rows="2" style="min-width: 300px;">${data || ''}</textarea>
                        </div>`;
                    }
                    const level = getHierarchyLevel(row.type);
                    const indent = level * 20;
                    const fontWeight = row.has_children ? 'font-weight-bold' : '';
                    return `<div style="padding-left: ${indent}px" class="${fontWeight}">${data || ''}</div>`;
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
                        // Auto-calculated field: (bulan_ini / pagu_anggaran) * 100
                        const pagu = parseFloat(row.pagu_anggaran) || 0;
                        const bulanIni = parseFloat(row.bulan_ini) || 0;
                        const calculated = pagu > 0 ? (bulanIni / pagu) * 100 : 0;
                        const colorClass = calculated < 35 ? 'text-danger' : 'text-dark';
                        return `<div style="text-align: center;">
                            <input type="text" class="form-control form-control-sm calculated-field ${colorClass}" name="keuangan" value="${calculated.toFixed(2)}%" readonly style="width: 80px; text-align: center; background-color: #f8f9fa;">
                        </div>`;
                    }
                    const val = parseFloat(data) || 0;
                    const colorClass = val < 35 ? 'text-danger' : 'text-dark';
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
            },
            {
                data: null,
                render: function (data, type, row) {
                    let html = '<div class="d-flex justify-content-center">';

                    // If editing or creating, show Save and Cancel buttons
                    if (row._editing || row._creating) {
                        html += `<button onclick='saveInlineRow(${row.id || 'null'})' class='btn btn-success btn-xs mr-1' title='Simpan'><i class='fa fa-save mr-1'></i> Simpan</button>`;
                        html += `<button onclick='cancelInlineEdit(${row.id || 'null'})' class='btn btn-secondary btn-xs' title='Batal'><i class='fa fa-times mr-1'></i> Batal</button>`;
                        html += '</div>';
                    return html;
                }

                    // Show Add button - OPD can add Program, Program can add Kegiatan, Kegiatan can add Sub Kegiatan
                    if (row.type === 'opd') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1' title='Tambah Program'><i class='fa fa-plus mr-1'></i> Program</button>`;
                    } else if (row.type === 'program') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1' title='Tambah Kegiatan'><i class='fa fa-plus mr-1'></i> Kegiatan</button>`;
                    } else if (row.type === 'kegiatan') {
                        html += `<button onclick='addInlineRow(${row.id}, "${row.type}")' class='btn btn-success btn-xs mr-1' title='Tambah Sub Kegiatan'><i class='fa fa-plus mr-1'></i> Sub Kegiatan</button>`;
                    }

                    // OPD is fixed, can't be edited or deleted
                    if (row.type !== 'opd') {
                        html += `<button onclick='enableInlineEdit(${row.id})' class='btn btn-info btn-xs mr-1'><i class='fa fa-edit'></i></button>`;
                        html += `<button onclick='modalDelete(${row.id})' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></button>`;
                    }

                    html += '</div>';
                    return html;
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

            // Get the code and uraian cells (columns 1 and 2)
            const $cells = $(row).find('td');
            const $codeCell = $cells.eq(1);
            const $uraianCell = $cells.eq(2);

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
            $cells.eq(1).css({});  // Code
            $cells.eq(2).css({'min-width': '300px'});  // Uraian
            $cells.eq(3).css({'text-align': 'right'});  // Pagu Anggaran
            $cells.eq(4).css({'text-align': 'right'});  // Bulan Lalu
            $cells.eq(5).css({'text-align': 'right'});  // Bulan Ini
            $cells.eq(6).css({'text-align': 'right'});  // sd Bulan Ini
            $cells.eq(7).css({'text-align': 'right'});  // Sisa Anggaran
            $cells.eq(8).css({'text-align': 'center'});  // Keuangan %
            $cells.eq(9).css({'text-align': 'center'});  // Fisik %
            $cells.eq(10).css({});  // Keterangan
            $cells.eq(11).css({'text-align': 'center'});  // Actions
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
        // Check cache first
        if (child_data_cache[parent_id]) {
            insertChildrenIntoTable(child_data_cache[parent_id], parentTr);
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
                child_data_cache[parent_id] = children;
                insertChildrenIntoTable(children, parentTr);
            },
            error: function () {
                $('.loader').hide();
                toastr.error('Failed to load children');
            }
        });
    }

    function insertChildrenIntoTable(children, parentTr) {
        if (children.length === 0) return;

        // Get parent row index
        const parentRow = table.row(parentTr);
        const parentIndex = parentRow.index();

        // Add each child as a new row in DataTable AND to list_data
        children.forEach((child, idx) => {
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

            // Move row to correct position (right after parent or last sibling)
            if (idx === 0) {
                $(newRow).insertAfter(parentTr);
            } else {
                // Insert after previous sibling
                const prevSibling = $('.child-of-' + child.parent_id).last();
                $(newRow).insertAfter(prevSibling);
            }
        });

        // Redraw without resorting
        table.draw(false);
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
        table.draw(false);

        // Update button state after collapse
        setTimeout(function() {
            updateToggleButtonState();
        }, 100);
    }

    function refreshTable() {
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

        // Clear table completely
        table.clear();

        // Reset all state
        child_data_cache = {};
        expanded_rows = {};
        isExpandingCollapsing = false; // Reset flag

        // Add data and redraw
        table.rows.add(cleanData);
        table.draw(false);

        // Force a full redraw to ensure everything is rendered
        setTimeout(function() {
            table.draw(false);
        }, 50);

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
            table.draw(false);
            // Update button state and ensure it's enabled, reset flag
            setTimeout(function() {
                const $btn = $('#btn-toggle-expand');
                isExpandingCollapsing = false;
                $btn.prop('disabled', false);
                $btn.css('pointer-events', 'auto');
                updateToggleButtonState();
            }, 100);
            return;
        }

        // Expand all collected rows
        let completedCount = 0;
        const totalCount = rowsToExpand.length;

        rowsToExpand.forEach(function(item) {
            if (item.icon.length > 0) {
                item.icon.removeClass('fa-plus-square').addClass('fa-minus-square');
            }
            expanded_rows[item.id] = true; // Mark as expanded immediately

            // Load children with callback
            loadAndInsertChildrenWithCallback(item.id, item.tr, function() {
                completedCount++;

                // When all current level expansions are complete, expand the next level
                if (completedCount === totalCount) {
                    // Small delay to ensure DOM is updated
                    setTimeout(function() {
                        expandAllRecursive(0); // Recursively expand the next level (reset retry count)
                    }, 150);
                }
            });
        });
    }

    function loadAndInsertChildrenWithCallback(parent_id, parentTr, callback) {
        // Check cache first
        if (child_data_cache[parent_id]) {
            insertChildrenIntoTable(child_data_cache[parent_id], parentTr);
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
                insertChildrenIntoTable(children, parentTr);
                if (callback) callback();
            },
            error: function () {
                toastr.error('Failed to load children');
                if (callback) callback();
            }
        });
    }

    function getListData() {
        const opd = $('#filter-opd').val();
        if (!opd) {
            list_data = [];
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
            },
            success: function (response) {
                $('.loader').hide();
                list_data = response;
                refreshTable();

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

                    // Force a redraw first to ensure all rows are visible
                    table.draw(false);

                    // Wait a bit more to ensure DOM is fully updated
                    setTimeout(function() {
                        // Then expand all recursively (start with retryCount = 0)
                        expandAllRecursive(0);
                    }, 200);
                }, 600); // Increased delay to ensure table is fully rendered
            },
            error: function (response, thrownError) {
                $('.loader').hide();
                toastr.error('Failed to load data');
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

        // Calculate keuangan % (bulan_ini / pagu_anggaran * 100)
        const keuangan = pagu > 0 ? (bulanIni / pagu) * 100 : 0;
        const $keuanganInput = $row.find('input[name="keuangan"]');
        if ($keuanganInput.length) {
            const colorClass = keuangan < 35 ? 'text-danger' : 'text-dark';
            $keuanganInput.val(keuangan.toFixed(2) + '%').removeClass('text-danger text-dark').addClass(colorClass);
        }

        // Update row data for save
        rowData.sd_bulan_ini = sdBulanIni;
        rowData.sisa_anggaran = sisaAnggaran;
        rowData.keuangan = keuangan;
    });

    // Add new inline row for creating
    function addInlineRow(parent_id, parent_type) {
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
            // Simple code generation - server will generate properly
            const siblingCount = 0; // Will be calculated by server
            if (parent_type === 'opd') {
                suggestedCode = parentData.code + '01.';
            } else if (parent_type === 'program') {
                suggestedCode = parentData.code + '1.01';
            } else if (parent_type === 'kegiatan') {
                suggestedCode = parentData.code + '.01';
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

        // Insert after parent or after last child of parent
        if (parentTr) {
            // Find if parent has visible children
            const childrenRows = $('.child-of-' + parent_id);
            if (childrenRows.length > 0) {
                // Insert after last child
                $(newRow).insertAfter(childrenRows.last());
            } else {
                // Insert directly after parent
                $(newRow).insertAfter(parentTr);
            }
        }

        // Redraw table to show input fields
        table.draw(false);

        // Initialize money mask for new inputs
        setTimeout(() => {
            $(newRow).find('.uang-input').mask('#.##0,00', {reverse: true});
        }, 100);
    }

    // Enable inline editing for existing row
    function enableInlineEdit(id) {
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
            table.draw(false);

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
        if (!formData.code || !formData.uraian) {
            toastr.error('Kode dan Uraian harus diisi');
            return;
        }

        // Validate sub_kegiatan specific fields
        if (rowData.type === 'sub_kegiatan') {
            if (!formData.pagu_anggaran || parseFloat(formData.pagu_anggaran) <= 0) {
                toastr.error('Pagu Anggaran harus diisi untuk Sub Kegiatan');
                return;
            }
            if (!formData.bulan_ini) {
                formData.bulan_ini = '0';
            }
            if (!formData.fisik) {
                toastr.error('Realisasi Fisik (%) harus diisi untuk Sub Kegiatan');
                return;
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
                    toastr.success(response.message || 'Berhasil menyimpan data');

                    // Reset all editing flags before reload
                    table.rows().every(function() {
                        const rowData = this.data();
                        if (rowData) {
                            delete rowData._editing;
                            delete rowData._creating;
                            delete rowData._originalData;
                        }
                    });

                    // Always reload data to get updated aggregates and proper hierarchy
                    // This ensures parent aggregates are calculated correctly
                    getListData();
                } else {
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

        table.rows().every(function() {
            const rowData = this.data();
            if (!rowData) return;

            const isEditing = rowData._editing || rowData._creating;
            if (!isEditing) return;

            // For creating (id is null)
            if (searchId === null && (rowData.id === null || rowData.id === undefined)) {
                found = true;
                if (rowData._creating) {
                    // Remove the row if it was a new creation
                    this.remove();
                }
                table.draw(false);
                return false; // Stop iteration
            }

            // For editing (id matches)
            if (searchId !== null && rowData.id !== null && rowData.id !== undefined) {
                const rowId = parseInt(rowData.id);
                if (rowId === searchId) {
                    found = true;
                    if (rowData._originalData) {
                        // Restore original data
                        Object.assign(rowData, rowData._originalData);
                        delete rowData._editing;
                        delete rowData._originalData;
                    } else {
                        // Just remove flags if no original data
                        delete rowData._editing;
                        delete rowData._creating;
                    }
                    table.draw(false);
                    return false; // Stop iteration
                }
            }
        });

        if (!found) {
            console.warn('Row not found for cancel, id:', id);
        }
    }

    function modalDelete(id) {
        // Keep delete modal for now
        swal({
            title: "Hapus Data",
            text: "Apakah anda yakin akan menghapus data ini?",
            icon: "warning",
            buttons: ["Batal", "Ya"],
            dangerMode: true
        }).then(function(res) {
            if (res) {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/rfk-v2/cascadeDelete/') }}/" + id,
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    success: function(response) {
                        toastr.success('Berhasil menghapus data');
                getListData();
                    },
                    error: function() {
                        toastr.error('Gagal menghapus data');
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

    $('#filter-opd').change(function () {
        getListData();
        getChartData();
    });

    $('#filter-month').change(function () {
        getListData();
        getChartData();
    });

    // Note: Using onclick handlers directly in button HTML, no need for event delegation
    // The onclick handlers are defined in the render functions and should work directly
</script>
@stop