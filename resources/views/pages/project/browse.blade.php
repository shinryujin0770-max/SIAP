@extends('adminlte::page')

@section('title', 'Paket Pekerjaan Konstruksi')

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
                <h3 class="card-tools d-flex">
                    <button onclick="ModalAdd()" class="btn btn-fw btn-gradient-primary">Tambah</button>
                    @if (auth()->user()->hasRole('superadmin'))
                    <button onclick="modalDelete()" class="btn btn-fw btn-danger ml-3">Hapus</button>
                    @endif
                </h3>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nomor SPK</th>
                            <th>Nama Pekerjaan</th>
                            <th>Jenis Pekerjaan</th>
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

<div class="modal fade" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-add">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content shadow-none border-0" style="background: transparent;">
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-delete">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-xl border-0">
            <div class="modal-body">
                <div id="load-delete"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let projects = [];
    function ModalAdd() {
        $("#modal-add").modal();
        $("#load-form").load("<?= url('project/modalAdd') . '/' ?>")
    }

    function modalDelete() {
        if (!projects.length) {
            alert('Tidak ada project yang dipilih!')
            return;
        }
        $("#modal-delete").modal();
        $("#load-delete").load("<?= url('project/modalDelete') . '/' ?>" + projects.join(','))
    }

    function read(id) {
        location.href = "<?= url('project') . '/' ?>" + id;
    }

    function chooseProject(event, id) {
        projects = projects.filter(value => value !== id);
        if (event.checked) projects.push(id);
    }

    $(function() {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url:  "{{ route('project.browse') }}",
                data: function(d) {
                    d.filterStart = $('#filter-start').val();
                    d.filterEnd = $('#filter-end').val();
                    d.filterJenis = $('#filter-jenis').val();
                }
            },
            columns: [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'spk_number',
                    name: 'spk_number'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category',
                    name: 'category'
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