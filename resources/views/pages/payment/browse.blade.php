@extends('adminlte::page')

@section('title', 'Pembayaran')

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
                        <label for="filter-tanggal-start">Tanggal</label>
                        <div class="d-flex align-items-center">
                            <input type="date" name="created_at" id="filter-tanggal-start" class="form-control">
                            <div class="mx-2">s/d</div>
                            <input type="date" name="created_at" id="filter-tanggal-end" class="form-control">
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
                    <div class="col-sm-12 col-lg-3">
                        <label for="filter-status">Status</label>
                        <select class="form-control select2" id="filter-status" name="status" style="width: 100%"
                            multiple>
                            <option value="waiting">Belum diajukan</option>
                            <option value="pending">Diproses</option>
                            <option value="paid">Terbayar</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nomor SPK</th>
                            <th>Nama Pekerjaan</th>
                            <th>Tanggal SPK</th>
                            <th>OPD</th>
                            <th>CV/PT Pelaksana</th>
                            <th>Rekening</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th width="100px"></th>
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
    // function ModalAdd() {
    //     $("#modal-add").modal();
    //     $("#load-form").load("<?= url('payment/modalAdd') . '/' ?>")
    // }

    function read(id) {
        location.href = "<?= url('project') . '/' ?>" + id;
    }

    $(function() {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url:  "{{ route('payment.browse') }}",
                data: function(d) {
                    d.filterTanggalStart = $('#filter-tanggal-start').val();
                    d.filterTanggalEnd = $('#filter-tanggal-end').val();
                    d.filterJenis = $('#filter-jenis').val();
                    d.filterStatus = $('#filter-status').val();
                }
            },
            columns: [
                {
                    data: 'spk_number',
                    name: 'spk_number'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'spk_date',
                    name: 'spk_date'
                },
                {
                    data: 'opd_name',
                    name: 'opd_name'
                },
                {
                    data: 'partner_name',
                    name: 'partner_name',
                },
                {
                    data: 'payment_rekening',
                    name: 'payment_rekening',
                },
                {
                    data: 'payment_typea',
                    name: 'payment_typea',
                },
                {
                    data: 'payment_paid',
                    name: 'payment_paid',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#filter-tanggal-start').change(function () {
            table.draw();
        });

        $('#filter-tanggal-end').change(function () {
            table.draw();
        });

        $('#filter-jenis').change(function () {
            table.draw();
        });

        $('#filter-status').change(function () {
            table.draw();
        });
    });
</script>
@stop