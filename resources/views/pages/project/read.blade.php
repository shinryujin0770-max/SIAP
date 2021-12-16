@extends('adminlte::page')

@section('title', 'APBD')

@section('content_header')
<div class="m-0 text-dark">
    <h1 class="d-inline">{{$page_title}}</h1>
    <h6 class="d-inline text-muted">{{$page_subtitle}}</h6>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12 mb-2">
        <div class="tools">
            @can ('project-write')
            <a href="{{ url('project/edit') . '/' . $project['id'] }}"
                class="btn btn-inverse-primary btn-sm float-right mr-1 shadow-sm"><i class="fas fa-fw fa-pencil-alt mr-1"></i>
                Ubah</a>
            <button onclick="details()" class="btn btn-inverse-primary btn-sm float-right mr-1 shadow-sm"><i
                    class="fas fa-fw fa-file mr-1"></i>
                Berkas</a>
                @endcan
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-sm rounded-xl">
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold">Perencanaan</h5>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nama Pekerjaan</div>
                        <div class="font-weight-bold">{{$project['name']}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Tanggal SPK</div>
                        <div class="font-weight-bold">{{$project['spk_date']}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nomor SPK</div>
                        <div class="font-weight-bold">{{$project['spk_number']}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nilai SPK</div>
                        <div class="font-weight-bold">Rp {{number_format($project['spk_amount'], 0, '',
                            '.')}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Pagu Anggaran</div>
                        <div class="font-weight-bold">Rp {{number_format($project['pagu_anggaran'], 0, '',
                            '.')}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">HPS</div>
                        <div class="font-weight-bold">Rp {{number_format($project['hps'], 0, '',
                            '.')}}</div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="card shadow-sm rounded-xl">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold">Pelaksana</h5>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">CV/PT</div>
                        <div class="font-weight-bold">{{$project['partner_name']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Direktur</div>
                        <div class="font-weight-bold">{{$project['partner_director']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Alamat</div>
                        <div class="font-weight-bold">{{$project['partner_address']}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm rounded-xl">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold">Periode SPK</h5>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Lama (Hari)</div>
                        <div class="font-weight-bold">{{$project['spk_days']}} hari</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Tanggal Mulai</div>
                        <div class="font-weight-bold">
                            {{\Carbon\Carbon::parse($project['spk_start'])->isoFormat('DD MMMM YYYY')}}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Tanggal Selesai</div>
                        <div class="font-weight-bold">
                            {{\Carbon\Carbon::parse($project['spk_end'])->isoFormat('DD MMMM YYYY')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm rounded-xl">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold">Adendum</h5>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Tanggal Mulai</div>
                        <div class="font-weight-bold">
                            {{\Carbon\Carbon::parse($project['adendum_start'])->isoFormat('DD MMMM YYYY')}}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Tanggal Selesai</div>
                        <div class="font-weight-bold">
                            {{\Carbon\Carbon::parse($project['adendum_end'])->isoFormat('DD MMMM YYYY')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="card shadow-sm rounded-xl">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold">Pembayaran</h5>
                    </div>
                    <div class="col-12">
                        @foreach ($project_payments as $payment)
                        @if ($payment['total'])
                        <div class="card shadow-sm rounded-xl" onclick="detailPayment({{$payment['id']}})"
                            style="cursor: pointer">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div>{{$payment['type']}}</div>
                                        <div class="font-weight-bold">Rp {{number_format($payment['total'], 0, '',
                                            '.')}}</div>
                                    </div>
                                    <div
                                        class="{{$payment['paid'] ? 'bg-success' : 'bg-danger'}} py-1 px-2 rounded-circle">
                                        <i class="fa {{$payment['paid'] ? 'fa-check' : 'fa-times'}}"
                                            style="{{!$payment['paid'] ? 'margin: 0 2px;' : ''}}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal right fade border-0 shadow-none" id="modal-right" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-none border-0" style="background: transparent;">
            {{-- <div class="modal-header">
                <h5 class="modal-title" id="modal-right-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> --}}
            <div class="modal-body">
                <div id="load-payment"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-form">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-xl border-0">
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    function details(id) {
        $("#modal-right").modal();
        // $("#modal-right-title").html("Berkas APBD")
        $("#load-payment").load('{{url("project/$project->id/details")}}')
    }

    function detailBerkas(id) {
        $("#modal-form").modal();
        $("#load-form").load('{{url("/project/$project->id/detail/") . "/"}}' + id)
    }

    function cetak(id) {
        window.open(
            "<?= url('project/print') . '/' ?>" + id,
            '_blank' // <- This is what makes it open in a new window.
        );
    }
</script>
@stop