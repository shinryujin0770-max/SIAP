@extends('adminlte::page')

@section('title', 'Pedagang')

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
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>NIK Pedagang</th>
                            <th>Nama Pedagang</th>
                            <th>Alamat Pedagang</th>
                            <th>Tempat Lahir Pedagang</th>
                            <th>Tanggal Lahir Pedagang</th>
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
    function edit(id) {
        $("#modal-form").modal();
        $("#modal-label").html("Ubah Data")
        $('#modal-form').on('shown.bs.modal', function () {
            $("#modal-preloader").delay(500).fadeOut(300);
        });

        // reset loading display when modal is closed
        $('#modal-form').on('hidden.bs.modal', function (e) {
            $("#modal-preloader").show();
        })
        $("#load-form").load("<?= url('pedagang/edit') . '/' ?>" + id)
    }

    $(function() {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: "{{ route('pedagang.browse') }}",
            columns: [
                {
                    data: 'nik',
                    name: 'nik'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'tempat_lahir',
                    name: 'tempat_lahir'
                },
                {
                    data: 'tanggal_lahir',
                    name: 'tanggal_lahir'
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