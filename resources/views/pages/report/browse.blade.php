@extends('adminlte::page')

@section('title', 'Laporan')

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
                    <div class="col-sm-12 col-lg-6">
                        <label for="filter-start">Tanggal</label>
                        <div class="d-flex align-items-center">
                            <input type="date" name="created_at" id="filter-start" class="form-control">
                            <div class="mx-2">s/d</div>
                            <input type="date" name="created_at" id="filter-end" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-3">
                        <label for="filter-jenis">Jenis Pekerjaan</label>
                        <select class="form-control select2" id="filter-jenis" name="jenis" style="width: 100%"
                            multiple>
                            <option value="perencanaan">Perencanaan</option>
                            <option value="konstruksi">Konstruksi</option>
                            <option value="pengawasan">Pengawasan</option>
                        </select>
                    </div>
                </div>
                <h3 class="card-tools">
                    <button onclick="download()" class="btn btn-md btn-info"><i class="fa fa-file-excel mr-2"></i>
                        Download</button>
                </h3>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover table-responsive">
                    <thead>
                        <tr>
                            <th>Nama Pekerjaan</th>
                            <th>Jenis Pekerjaan</th>
                            <th>Pemenang</th>
                            <th>Pagu Anggaran (Rp)</th>
                            <th>HPS (Rp)</th>
                            <th>Kontrak (Rp)</th>
                            <th>Tanggal SPK</th>
                            <th>Tanggal Mulai Kontrak</th>
                            <th>Tanggal Selesai Kontrak</th>
                            <th>OPD</th>
                            <th>Realisasi Kontrak</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-add">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content shadow-none border-0" style="background: transparent;">
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function download() {
        const filterStart = $('#filter-start').val();
        const filterEnd = $('#filter-end').val();
        const jenis = $('#filter-jenis').val();
        window.open(
            "<?= url('report/export?jenis=') ?>" + jenis + `&start=${filterStart}&end=${filterEnd}`,
            '_blank' // <- This is what makes it open in a new window.
        );
    }

    function read(id) {
        location.href = "<?= url('project') . '/' ?>" + id;
    }

    $(function() {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url:  "{{ route('report.browse') }}",
                data: function(d) {
                    d.filterStart = $('#filter-start').val();
                    d.filterEnd = $('#filter-end').val();
                    d.filterJenis = $('#filter-jenis').val();
                }
            },
            columns: [
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category',
                    name: 'category',
                },
                {
                    data: 'partner_name',
                    name: 'partner_name',
                },
                {
                    data: 'pagu_anggaran',
                    name: 'pagu_anggaran',
                },
                {
                    data: 'hps',
                    name: 'hps',
                },
                {
                    data: 'spk_amount',
                    name: 'spk_amount',
                },
                {
                    data: 'spk_date',
                    name: 'spk_date'
                },
                {
                    data: 'spk_start',
                    name: 'spk_start'
                },
                {
                    data: 'spk_end',
                    name: 'spk_end'
                },
                {
                    data: 'opd_name',
                    name: 'opd_name'
                },
                {
                    data: 'amount',
                    name: 'amount',
                },
            ]
        });

        $('#filter-start').change(function () {
            table.draw();
        });

        $('#filter-end').change(function () {
            table.draw();
        });

        $('#filter-jenis').change(function () {
            table.draw();
        });

    });
</script>
@stop