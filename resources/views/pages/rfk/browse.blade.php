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
                        <label for="filter-month">Bulan</label>
                        <input type="month" id="filter-month" class="form-control" value="{{date('Y-m')}}">
                    </div>
                </div>
                <h3 class="card-tools d-flex">
                    <button onclick="download(1)" class="btn btn-md btn-success mr-2"><i
                            class="fa fa-file-excel mr-2"></i>
                        Laporan Bulanan</button>
                    <button onclick="download(3)" class="btn btn-md btn-success mr-2"><i
                            class="fa fa-file-excel mr-2"></i>
                        Laporan Tahunan</button>
                    @if (!$is_overtime)
                    <button onclick="save()" class="btn btn-md btn-primary" id="btn-simpan">
                        Simpan
                    </button>
                    @endif
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-5">
                    <div class="d-flex justify-content-center mb-4">
                        <button class="btn btn-outline-primary" type="button" data-toggle="collapse"
                            data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            Klik untuk melihat Grafik Anggaran Kas vs Realisasi Keuangan vs Realisasi Fisik
                        </button>
                    </div>
                    <div class="collapse" id="collapseExample">
                        <button onclick="generatePdf()" class="btn btn-md btn-success mr-2">
                            <i class="fa fa-file-pdf mr-2"></i> Download PDF
                        </button>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
                <div class="my-3 d-flex">
                    @if (!$is_overtime)
                    <button onclick="modalAdd()" class="btn btn-md btn-primary mr-3" id="btn-simpan">
                        Tambah Uraian
                    </button>
                    <button onclick="importData()" class="right btn btn-md btn-success mr-2">Import</button>
                    @endif
                </div>
                <table id="datatable-list" class="table table-bordered table-hover table-responsive">
                    <thead class="text-center font-weight-bold">
                        <tr>
                            <th rowspan="2">Kode</th>
                            <th rowspan="2">
                                <div style="width: 300px">Uraian Urusan, Organisasi, Program,</div>
                                <div>Kegiatan, dan Sub Kegiatan</div>
                            </th>
                            <th rowspan="2">Pagu Anggaran</th>
                            <th colspan="3">Realisasi Keuangan</th>
                            <th rowspan="2">Sisa Anggaran</th>
                            {{-- <th colspan="2">Anggaran Kas</th> --}}
                            <th colspan="2">Realisasi (%)</th>
                            <th rowspan="2">Keterangan</th>
                            <th rowspan="2">
                                {{-- <div style="width: 300px"></div> --}}
                            </th>
                            {{-- <th rowspan="2">Sisa</th>
                            <th rowspan="2">Prosentase Fisik Kegiatan</th> --}}
                        </tr>
                        <tr>
                            <th>Bulan Lalu</th>
                            <th>Bulan Ini</th>
                            <th>s.d. Bulan Ini</th>
                            {{-- <th>Sampai dengan Bulan Ini</th>
                            <th>Prosentase</th> --}}
                            <th>Keuangan</th>
                            <th>Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="font-weight-light text-dark">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-add">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let list_data;
    let charts_data;
    let chart;
    let has_chart = false;
    let pagu_anggaran = 0;
    let sd_bulan_ini = 0;
    let angkas = 0;

    function resetValue() {
        pagu_anggaran = 0;
        sd_bulan_ini = 0;
        angkas = 0;
    }

    function importData() {
        $("#modal-add").modal();
        $("#load-form").load("{{ route('rfk.modalImport') }}")
    }

    function modalAdd() {
        resetValue()
        $("#modal-add").modal();
        $("#load-form").load("<?= url('rfk/modalAdd') . '/' ?>")
    }
    function modalEdit(id) {
        resetValue()
        $("#modal-add").modal();
        $("#load-form").load("<?= url('rfk/modalEdit') . '/' ?>" + id)
    }

    function modalDelete(id) {
        $("#modal-add").modal();
        $("#load-form").load("<?= url('rfk/modalDelete') . '/' ?>" + id)
    }

    $( '.uang' ).mask('#.##0,00', {reverse: true});

    function download(type) {
        window.open(
            `{{ url('/rfk/laporan/') }}/${type}?` + `month=${$(`#filter-month`).val()}&opd={{$id_opd}}`,
            '_blank' // <- This is what makes it open in a new window.
        );
    }

    const table = $(`#datatable-list`).DataTable({
        autoWidth: false,
        sort: false,
        search: false,
        data : list_data,
        pageLength: 125,
        paging: false,
        ordering: false,
        columns: [
            {
                data: 'code',
                name: 'code'
            },
            {
                data: 'uraian',
                name: 'uraian',
            },
            {
                data: 'pagu_anggaran',
                render: function (data, type, row, meta) {
                    const res = parseFloat(row.pagu_anggaran);
                    return isNaN(res) ? 0 : formatToCurrency(res);
                }
            },
            {
                data: 'bulan_lalu',
                render: function (data, type, row, meta) {
                    const res = parseFloat(row.bulan_lalu);
                    return isNaN(res) ? 0 : formatToCurrency(res);
                }
            },
            {
                data: 'bulan_ini',
                render: function (data, type, row, meta) {
                    const res = parseFloat(row.bulan_ini);
                    return isNaN(res) ? 0 : formatToCurrency(res);
                }
            },
            {
                data: 'sd_bulan_ini',
                render: function (data, type, row, meta) {
                    const res = parseFloat(row.sd_bulan_ini);
                    return isNaN(res) ? 0 : formatToCurrency(res);
                }
            },
            {
                data: 'sisa_anggaran',
                render: function (data, type, row, meta) {
                    const res = parseFloat(row.sisa_anggaran);
                    return isNaN(res) ? 0 : formatToCurrency(res);
                }
            },
            // {
            //     data: 'angkas',
            //     render: function (data, type, row, meta) {
            //         const res = parseFloat(row.angkas);
            //         return isNaN(res) ? 0 : formatToCurrency(res);
            //     }
            // },
            // {
            //     data: 'angkas',
            //     render: function (data, type, row, meta) {
            //         return `<div>${row.angkas_prosentase + '%'}</div>`;
            //     }
            // },
            {
                data: 'angkas',
                render: function (data, type, row, meta) {
                    return `<div class="${row.keuangan < 35 ? 'text-danger' : 'text-dark'}">${row.keuangan + '%'}</div>`;
                }
            },
            {
                data: 'angkas',
                render: function (data, type, row, meta) {
                    let html = ``;
                    html = `<div style="width: 70px">
                        <input type="hidden" id="code_${meta.row}" value="${row.code}">
                        <input type="hidden" id="uraian_${meta.row}" value="${row.uraian}">
                        <input type="hidden" id="pagu_anggaran_${meta.row}" value="${row.pagu_anggaran}">
                        <input type="hidden" id="bulan_lalu_${meta.row}" value="${row.bulan_lalu}">
                        <input type="hidden" id="bulan_ini_${meta.row}" value="${row.bulan_ini}">
                        <input type="hidden" id="sd_bulan_ini_${meta.row}" value="${row.sd_bulan_ini}">
                        <input type="hidden" id="sisa_anggaran_${meta.row}" value="${row.sisa_anggaran}">
                        <input type="hidden" id="angkas_${meta.row}" value="${row.angkas}">
                        <input type="hidden" id="angkas_prosentase_${meta.row}" value="${row.angkas_prosentase}">
                        <input type="hidden" id="keuangan_${meta.row}" value="${row.keuangan}">
                        <div>${parseFloat(row.fisik).toFixed(2) + '%'}</div>
                    </div>`;

                    return html;
                }
            },
            {
                data: 'angkas',
                render: function (data, type, row, meta) {
                    let html = ``;
                    html = `<div style="width: 200px">
                        ${row.keterangan}
                    </div>`;

                    return html;
                }
            },
            {
                data: 'angkas',
                render: function (data, type, row, meta) {
                    let html = ``;
                    @if(!$is_overtime)
                    html = `<div class='d-flex'>
                            <button onclick='modalEdit(${row.id})' class='edit btn btn-info btn-sm'><i class='fa fa-edit'></i></button>
                            <button onclick='modalDelete(${row.id})' class='delete btn btn-danger btn-sm ml-2'><i class='fa fa-trash'></i></button>
                        </div>`;
                    @endif
                    return html;
                }
            },
            // {
            //     data: 'angkas',
            //     render: function (data, type, row, meta) {
            //         // const FISIK = isNaN(parseInt(row.fisik)) ? 0 : parseInt(row.fisik);
            //         // const pagu_anggaran = isNaN(parseInt(row.pagu_anggaran)) ? 0 : parseInt(row.pagu_anggaran);
            //         // const prosentase = (FISIK * pagu_anggaran / 100).toFixed(2);

            //         return `<div class="text-dark">${formatToCurrency(row.sisa)}</div>`;
            //     }
            // },
            // {
            //     data: 'angkas',
            //     render: function (data, type, row, meta) {
            //         const SISA = isNaN(parseInt(row.sisa)) ? 0 : parseInt(row.sisa);
            //         const pagu_anggaran = isNaN(parseInt(row.pagu_anggaran)) ? 0 : parseInt(row.pagu_anggaran);
            //         const prosentase = (SISA / pagu_anggaran * 100).toFixed(2);

            //         return `<div class="${prosentase < 35 ? 'text-danger' : 'text-dark'}">${prosentase + '%'}</div>`;
            //     }
            // },
        ]
    });

    function refreshTable() {
        table.clear().rows.add(list_data).draw();
        $( '.uang' ).mask('#.##0,00', {reverse: true});
    }

    function getListData() {
        $.ajax({
            async: true,
            crossDomain: true,
            url: "{{ url('/rfk/browse') }}?" + `month=${$(`#filter-month`).val()}`,
            method: 'GET',
            dataType: 'json',
            contentType: "application/json",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            beforeSend: function () {
                $('.loader').show();
            },
            success: function (response) {
                $('.loader').hide();

                // $('#btn-simpan').attr("disabled", false)
                // if (response[0].status && response[0].status !== 'draft') {
                //     $('#btn-simpan').attr("disabled", true)
                // }
                list_data = response;
                refreshTable();
            },
            error: function (response, thrownError) {
                $('.loader').hide();
            }
        })
    }
    getListData();

    $('#filter-month').change(function (value) {
        getListData();
    });

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

    function handleFisik(fisik) {
        const id = fisik.id.substr(6)
        const data = {
            "month": $(`#filter-month`).val(),
            "code": $(`#code_${id}`).val(),
            "uraian": $(`#uraian_${id}`).val(),
            "pagu_anggaran": $(`#pagu_anggaran_${id}`).val(),
            "bulan_lalu": $(`#bulan_lalu_${id}`).val(),
            "bulan_ini": $(`#bulan_ini_${id}`).val(),
            "sd_bulan_ini": $(`#sd_bulan_ini_${id}`).val(),
            "sisa_anggaran": $(`#sisa_anggaran_${id}`).val(),
            "angkas": $(`#angkas_${id}`).val(),
            "angkas_prosentase": $(`#angkas_prosentase_${id}`).val(),
            "keuangan": $(`#keuangan_${id}`).val(),
            "fisik": $(`#fisik_${id}`).val(),
            "keterangan": $(`#keter_${id}`).val(),
        };

        $.ajax({
            type: 'POST',
            dataType: "json",
            data: JSON.stringify(data),
            contentType: 'application/json',
            url: "{!! route('rfk.store') !!}",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            beforeSend: function(){
                $(".loader").show();
            },
            success: function (response) {
                $(".loader").hide();
                getListData();
            },
            error: function(xhr){
                $(".loader").hide();
                getListData();
            }
        });
    }

    function save() {
        const data = {
            "month": $(`#filter-month`).val(),
        };

        swal({
            title: "Simpan Pendaftaran",
            text: "Apakah anda yakin akan melakukan simpan untuk pendaftaran ini?",
            icon: "warning",
            buttons: ["Batal", "Ya"]
        }).then(function (res) {
                if (res) {
                    $.ajax({
                        type: 'POST',
                        dataType: "json",
                        data: JSON.stringify(data),
                        contentType: 'application/json',
                        url: "{!! route('rfk.save') !!}",
                        headers: {
                            'X-CSRF-TOKEN': "{{csrf_token()}}"
                        },
                        beforeSend: function(){
                            $(".loader").show();
                        },
                        success: function (response) {
                            $(".loader").hide();
                            getListData();
                            toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                            toastr.success('Berhasil Menyimpan Data', '', []);
                        },
                        error: function(xhr){
                            $(".loader").hide();
                            toastr.options = {"closeButton":true,"closeClass":"toast-close-button","closeDuration":300,"closeEasing":"swing","closeHtml":"<button><i class=\"icon-off\"><\/i><\/button>","closeMethod":"fadeOut","closeOnHover":true,"containerId":"toast-container","debug":false,"escapeHtml":false,"extendedTimeOut":10000,"hideDuration":1000,"hideEasing":"linear","hideMethod":"fadeOut","iconClass":"toast-info","iconClasses":{"error":"toast-error","info":"toast-info","success":"toast-success","warning":"toast-warning"},"messageClass":"toast-message","newestOnTop":false,"onHidden":null,"onShown":null,"positionClass":"toast-top-right","preventDuplicates":true,"progressBar":true,"progressClass":"toast-progress","rtl":false,"showDuration":300,"showEasing":"swing","showMethod":"fadeIn","tapToDismiss":true,"target":"body","timeOut":5000,"titleClass":"toast-title","toastClass":"toast"};
                            toastr.error('Gagal Menyimpan Data, Data RFK tidak sesuai', '', []);
                        }
                    });
                } else {
                    $("#btn-simpan").removeClass("disabled").attr('disabled', false);
                }
            }, function (dismiss) {
            $("#btn-simpan").removeClass("disabled").attr('disabled', false);
        })
    }

    function showChart() {
        if(!has_chart) {
            componentChart()
            has_chart = true;
        } else {
            chart.destroy();
            componentChart()
        }
    }

    function componentChart() {
        chart = new Chart(
            document.getElementById('myChart'),
            {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [
                        {
                            label: 'Pagu Anggaran',
                            backgroundColor: '#FF6666',
                            borderColor: '#FF6666',
                            data: charts_data?.pagu_anggaran ?? [],
                        },{
                            label: 'Realisasi Keuangan',
                            backgroundColor: '#66FFB2',
                            borderColor: '#66FFB2',
                            data: charts_data?.keuangan ?? [],
                        },{
                            label: 'Realisasi Fisik',
                            backgroundColor: '#FFFF00',
                            borderColor: '#FFFF00',
                            data: charts_data?.fisik ?? [],
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback(value, index) {
                                    return value + '%';
                                }
                            },
                        }
                    }
                },
                plugins: [
                    {
                        beforeDraw: (chart) => {
                            const {ctx} = chart;
                            ctx.fillStyle = 'white';
                            ctx.fillRect(0, 0, chart.width, chart.height);
                            ctx.restore();
                        }
                    }
                ]
            }
        );
    }

    function getChartData() {
        $.ajax({
            async: true,
            crossDomain: true,
            url: "{{ url('/rfk/chart') }}?" + `opd={{$id_opd}}`,
            method: 'GET',
            dataType: 'json',
            contentType: "application/json",
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            beforeSend: function () {
            },
            success: function (response) {
                charts_data = response;
                showChart();
            },
            error: function (response, thrownError) {
            }
        })
    }
    getChartData();

    function generatePdf() {
        const canvas = document.getElementById('myChart');

        const canvasImage = canvas.toDataURL('image/jpeg', 1.0);

        let pdf = new jsPDF();
        pdf.setFontSize(20);
        pdf.addImage(canvasImage, 'JPEG', 15, 15, 185, 150);
        pdf.save('grafik.pdf')
    }
</script>
@stop