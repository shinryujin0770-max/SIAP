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
    <div class="col-12 mb-2">
        <div class="tools">
            @can ('project-write')
            <a href="{{ url('project/edit') . '/' . $project['id'] }}"
                class="btn btn-primary btn-sm float-right mr-1"><i class="fas fa-fw fa-pencil-alt"></i> Ubah</a>
            @endcan
        </div>
    </div>
    <div class="col-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Perencanaan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted">Nama Pekerjaan</div>
                        <div class="font-weight-bold">{{$project['name']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted">Tanggal SPK</div>
                        <div class="font-weight-bold">{{$project['spk_date']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted">Nomor SPK</div>
                        <div class="font-weight-bold">{{$project['spk_number']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted">Nilai SPK</div>
                        <div class="font-weight-bold">{{$project['spk_amount']}}</div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="font-weight-bold">Pelaksana</div>
            </div>
            <div class="card-body">
                <div class="row">
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
        <div class="card">
            <div class="card-header">
                <div class="font-weight-bold">Periode SPK</div>
            </div>
            <div class="card-body">
                <div class="row">
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
        <div class="card">
            <div class="card-header">
                <div class="font-weight-bold">Adendum</div>
            </div>
            <div class="card-body">
                <div class="row">
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
        <div class="card">
            <div class="card-header font-weight-bold">Pembayaran</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @foreach ($project_payments as $payment)
                        @if ($payment['amount'])
                        <div class="card" onclick="detailPayment({{$payment['id']}})" style="cursor: pointer">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div>{{$payment['type']}}</div>
                                        <div class="font-weight-bold">Rp {{number_format($payment['amount'], 0, '',
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

<div class="modal right fade" id="modal-payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="load-payment"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-form">
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
    function detailPayment(id) {
        $("#modal-payment").modal();
        $("#load-payment").load("<?= url('project/payment') . '/' ?>" + id)
    }

    function editPayment(id) {
        $("#modal-form").modal();
        $("#load-form").load("<?= url('project/payment') . '/' ?>" + id + '/edit')
    }
</script>
@stop