@extends('adminlte::page')

@section('title', 'SITU')

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
            @if ($situ['is_signatured'])
            <h5><span class="badge badge-success badge-md" data-html="true" data-toggle="tooltip"
                    data-placement="bottom" title="Sudah di Tandatangani">Sudah di Tandatangani</span></h5>
            @else
            <h5><span class="badge badge-danger badge-md" data-html="true" data-toggle="tooltip" data-placement="bottom"
                    title="Menunggu Tandatangan">Menunggu Tandatangan</span></h5>
            @endif
            <a href="{{ url('situ/edit') . '/' . $situ['id'] }}" class="btn btn-primary btn-sm float-right mr-1"><i
                    class="fas fa-fw fa-pencil-alt"></i> Ubah</a>

            @if (!$situ['is_signatured'])
            <button onclick="signature({{$situ['id']}})" class="btn btn-sm btn-info float-right mr-1">
                <i class="fas fa-fw fa-file-signature"></i>
                Tanda tangani
            </button>
            @endif
        </div>
    </div>
    <div class="col-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Sertifikat</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-12 scrolling-wrapper">
                        <div class="row justify-content-center">
                            {{-- {!!$certificate!!} --}}
                            <iframe src="{{ url('situ/'.$situ['id'].'/certificate') }}" width="100%" height="1000">
                                This browser does not support PDFs. Please download the PDF to view it: <a
                                    href="{{ url('situ/'.$situ['id'].'/certificate') }}">Download PDF</a>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
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
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function signature(id) {
        $("#modal-form").modal();
        $("#modal-label").html("Tanda Tangani Sertifikat")
        $("#load-form").load("<?= url('situ/modalSignature') . '/' ?>" + id)
    }
</script>
@stop