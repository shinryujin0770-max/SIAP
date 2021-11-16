@extends('adminlte::page')

@section('title', 'Project')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"></h3>
                <h3 class="card-tools">
                    <button onclick="add()" class="right btn btn-md btn-primary">Tambah</button>
                </h3>
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
                            <th>Lokasi</th>
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

<div class="modal fade" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-form">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <!-- Preloader -->
            <div id="modal-preloader">
                <div class="overlay d-flex justify-content-center align-items-center">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>
            </div>
            <!-- End Preloader -->
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function add() {
        location.href = "<?= route('project.add') ?>";
    }

    function read(id) {
        location.href = "<?= url('project') . '/' ?>" + id;
    }

    $(function() {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: "{{ route('project.browse') }}",
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
                    data: 'lokasi',
                    name: 'lokasi',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>
@stop