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
    <div class="col-12 mb-2">
        <div class="tools">
            @can ('project-write')
            @if (auth()->user()->hasRole('superadmin') && !$already_checklist)
            <button onclick="modalDelete()" class="btn btn-inverse-danger btn-sm float-right mr-1 shadow-sm"><i
                    class="fas fa-fw fa-trash mr-1"></i>
                Hapus</button>
            @endif
            @if (auth()->user()->hasRole('superadmin') || (auth()->user()->hasRole('admin_opd') &&
            !$already_checklist))
            <a href="{{ url('project/edit') . '/' . $project['id'] }}"
                class="btn btn-inverse-primary btn-sm float-right mr-1 shadow-sm"><i
                    class="fas fa-fw fa-pencil-alt mr-1"></i>
                Ubah</a>
            @endif
            {{-- <button onclick="details()" class="btn btn-inverse-primary btn-sm float-right mr-1 shadow-sm"><i
                    class="fas fa-fw fa-file mr-1"></i>
                Berkas</a> --}}
                @endcan
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow-sm rounded-xl">
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 font-weight-bold text-capitalize">{{$project['category']}}</h5>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nama Pekerjaan</div>
                        <div class="font-weight-bold">{{$project['name']}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Tanggal SPK</div>
                        <div class="font-weight-bold">{{\Carbon\Carbon::parse($project['spk_date'])->isoFormat('DD MMMM
                            YYYY')}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nomor SPK</div>
                        <div class="font-weight-bold">{{$project['spk_number']}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Nilai SPK</div>
                        <div class="font-weight-bold">Rp {{number_format($project['spk_amount'], 2, ',',
                            '.')}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">Pagu Anggaran</div>
                        <div class="font-weight-bold">Rp {{number_format($project['pagu_anggaran'], 2, ',',
                            '.')}}</div>
                    </div>
                    <div class="col-sm-4 col-md-4 my-2">
                        <div class="text-muted">HPS</div>
                        <div class="font-weight-bold">Rp {{number_format($project['hps'], 2, ',',
                            '.')}}</div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-md-12">
        <div class="card rounded-lg">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="font-weight-bolder mb-3">Lokasi Pekerjaan</p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Lokasi</div>
                        <div class="font-weight-bold">{{$project['lokasi']}}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Kecamatan</div>
                        <div class="font-weight-bold">{{$project['kecamatan_name']}}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Desa</div>
                        <div class="font-weight-bold">{{$project['kelurahan_name']}}</div>
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
                        <h5 class="mb-3 font-weight-bold">Pelaksana</h5>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">CV/PT</div>
                        <div class="font-weight-bold">{{$project['name_partner']}}</div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Direktur</div>
                        <div class="font-weight-bold">{{$project['director_partner'] ?? $project['partner_director']}}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Alamat</div>
                        <div class="font-weight-bold">{{$project['address_partner'] ?? $project['partner_address']}}
                        </div>
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
                        <div class="text-muted">Lama (hari)</div>
                        <div class="font-weight-bold">{{$project['spk_days'] + 1}} hari</div>
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
                            {{$project['adendum_start'] ?
                            \Carbon\Carbon::parse($project['adendum_start'])->isoFormat('DD MMMM YYYY'): '-'}}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="text-muted">Tanggal Selesai</div>
                        <div class="font-weight-bold">
                            {{$project['adendum_end'] ? \Carbon\Carbon::parse($project['adendum_end'])->isoFormat('DD
                            MMMM YYYY'): '-'}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-muted">
            <div>
                Diunggah oleh {{$project['created_by'] ?? 'System'}} pada tanggal
                {{\Carbon\Carbon::parse($project['created_at'])->isoFormat('DD-MM-YYYY HH:mm')}}
            </div>
            <div>
                Diperbaharui oleh {{$project['updated_by'] ?? 'System'}} pada tanggal
                {{\Carbon\Carbon::parse($project['updated_at'])->isoFormat('DD-MM-YYYY HH:mm')}}
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
                        <div class="form-group">
                            <label class="text-muted" style="font-size: 11pt">Uraian Sub Kegiatan</label>
                            <div>{{(preg_replace('/\s+/', '', $project['uraian']) ?? '-')}}</div>
                        </div>
                        <small class="text-muted">Total pembayaran yang sudah diajukan sekarang sejumlah <span
                                class="text-primary">Rp {{number_format($totalPaid, 2, ',', '.')}}</span></small>
                        <ul class="nav nav-tabs mt-3">
                            @php
                            $i = 0;
                            @endphp
                            @foreach ($project_payments as $rekening => $payments)
                            <li class="nav-item">
                                <a class="nav-link text-muted" onclick="activeTab('#tabs-{{$i}}')" href="#tabs-{{$i}}">
                                    {{$rekening}}
                                </a>
                            </li>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @php
                            $i = 0;
                            @endphp
                            @foreach ($project_payments as $rekening => $payments)
                            @php
                            $total = $payments->sum('amount');
                            $totalPaidRekening = $payments->whereNotNull('content')->sum('amount');
                            $limit = $project['payment_type'] == 'paid' ? true : $totalPaidRekening >= ($total * 75 /
                            100);
                            @endphp
                            <div class="tab-pane py-3 fade show" id="tabs-{{$i}}" role="tabpanel"
                                aria-labelledby="tabs-{{$i}}-tab">
                                <div class="d-flex w-100 mt-3">
                                    <button onclick="details('{{$rekening}}')"
                                        class="btn btn-inverse-primary float-right mr-1 shadow-sm rounded-xl w-100 btn-app d-flex justify-content-between"
                                        style="min-height: 75px;">
                                        {{-- <i class="fas fa-fw fa-file mr-1"></i> --}}
                                        <div class="text-left">
                                            <div style="font-size: 12pt">Berkas Rekening {{$rekening}}</div>
                                            <small>Ringkasan Kontrak, Laporan Selesai, Berita Acara Pemeriksaan, Berita
                                                Acara Serah Terima, dan Pengantar</small>
                                        </div>
                                        <img src="{{asset('/assets/documents/berkas.svg')}}" alt="perencanaan"
                                            width="130" height="130">
                                    </button>
                                </div>
                                @foreach ($payments as $key => $payment)
                                @if ($payment['amount'])
                                <div class="card shadow rounded-xl" @if (auth()->user()->hasRole('superadmin'))
                                    @if ($payment['content'] || ($limit && $pengantar[0]['content']))
                                    onclick="modalConfirmationChecklist({{$payment['id']}}, '{{str_replace(' ', '_',
                                    str_replace('%', '', $payment->type))}}', '{{$rekening}}')"
                                    @endif
                                    @else
                                    @if (!$ringkasan_kontrak[$i]['content'])
                                    onclick="openNeedRingkasanKontrak('{{$rekening}}')"
                                    @endif
                                    @if (!$payment['paid'])
                                    @if (!$limit)
                                    onclick="modalPengantar('{{str_replace(' ', '_', str_replace('%', '',
                                    $payment->type))}}', '{{$rekening}}')"
                                    @else
                                    onclick="details('{{$rekening}}')"
                                    @endif
                                    @else
                                    onclick="modalPengantar('{{str_replace(' ', '_', str_replace('%', '',
                                    $payment->type))}}', '{{$rekening}}')"
                                    @endif
                                    @endif
                                    style="cursor: pointer">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div>{{$payment['type']}}</div>
                                                <div class="font-weight-bold">Rp {{number_format($payment['amount'], 2,
                                                    ',',
                                                    '.')}}</div>
                                                <small class="text-danger">Klik untuk mengisi berkas Pengantar
                                                    pembayaran ini!</small>
                                            </div>
                                            <div
                                                class="{{$payment['paid'] ? 'bg-success' : ($payment['content'] || ($limit && $pengantar[0]['content']) ? 'bg-warning' : 'bg-danger')}} py-2 px-2 rounded-circle d-flex align-items-center">
                                                <i class="fa {{$payment['paid'] ? 'fa-check' : ($payment['content'] || ($limit && $pengantar[0]['content']) ? 'fa-exclamation' : 'fa-times')}}"
                                                    style="{{$payment['paid'] ? 'margin: 1px 3px;' :  ($payment['content'] || ($limit && $pengantar[0]['content']) ? 'margin: 1px 7px;' : 'margin: 1px 5px;')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                $can = (bool) $payment['content'];
                                @endphp
                                @endif
                                @endforeach
                                @if ($limit)
                                <small class="text-danger">Pembayaran yang diajukan pada rekening {{$rekening}} sudah
                                    lebih dari sama dengan 75%, harap pastikan seluruh berkas pada rekening
                                    {{$rekening}} telah terisi.</small>
                                @endif
                            </div>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal right fade border-0 shadow-none" id="modal-right" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-none border-0" style="background: transparent;">
            <div class="modal-body">
                <div id="load-payment"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-form">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-xl border-0">
            <!-- Preloader -->
            <div id="modal-preloader">
                <div class="overlay d-flex justify-content-center align-items-center">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>
            </div>
            <!-- End Preloader -->
            <div class="modal-body">
                <div id="load-form"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    id="modal-confirmation-checklist">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-xl border-0">
            <div class="modal-body">
                <div id="load-checklist"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    id="modal-need-ringkasan-kontrak">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-xl border-0">
            <div class="modal-body">
                <div class="row p-3">
                    <div class="col-12">
                        <h6 class="text-center">Sebelum mengajukan pembayaran, harap lengkapi berkas Ringkasan
                            Kontrak terlebih dahulu.</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-delete">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-xl border-0">
            <div class="modal-body">
                <form action="{{url("/project/delete/$project->id")}}" id="delete-form">
                    <div class="row p-3">
                        @csrf
                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <img src="{{asset('/assets/projects/delete.svg')}}" alt="perencanaan" width="250"
                                height="250" class="mt-3">
                        </div>
                        <div class="col-12">
                            <h6 class="text-center mb-3">
                                Apakah anda yakin akan menghapus pekerjaan ini ?
                            </h6>
                        </div>
                        <div class="col-sm-12">
                            <div class="mt-3 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-gradient-primary mr-2"
                                    id="btn-delete">Hapus</button>
                                <button type="button" data-dismiss="modal"
                                    class="btn btn-inverse-secondary ml-1">Batal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script>
    function activeTab(tab) {
        $('.nav-tabs a[href="' + tab + '"]').tab('show');
    };

    activeTab('#tabs-' + 0);
    function details(rekening) {
        $("#modal-right").modal();
        // $("#modal-right-title").html("Berkas Paket Pekerjaan")
        $("#load-payment").load('{{url("project/$project->id/details")}}/' + rekening)
    }

    function detailBerkas(id) {
        $("#modal-form").modal();
        $('#modal-form').on('shown.bs.modal', function () {
            $("#modal-preloader").delay(1000).fadeOut(300);
        });

        $('#modal-form').on('hidden.bs.modal', function (e) {
            $("#modal-preloader").show();
        })
        $("#load-form").load('{{url("/project/$project->id/detail/") . "/"}}' + id)
    }

    function modalDelete(type, rekening) {
        $("#modal-delete").modal();
    }

    function modalPengantar(type, rekening) {
        $("#modal-form").modal();
        $('#modal-form').on('shown.bs.modal', function () {
            $("#modal-preloader").delay(1000).fadeOut(300);
        });

        $('#modal-form').on('hidden.bs.modal', function (e) {
            $("#modal-preloader").show();
        })
        $("#load-form").load('{{url("/project/$project->id/pengantar/") . "/"}}' + type + '/' + rekening)
    }

    function cetak(id) {
        window.open(
            "<?= url('project/print') . '/' ?>" + id,
            '_blank' // <- This is what makes it open in a new window.
        );
    }

    function modalConfirmationChecklist(id, type, rekening) {
        @hasrole('superadmin')
        $("#modal-confirmation-checklist").modal();
        $("#load-checklist").load('{{url("/project") . "/"}}' + id + '/checklist/' + type + '/' + rekening)
        @endhasrole
    }

    function cetakChecklist(id, type) {
        window.open(
            "<?= url('project/printChecklist') . '/' ?>" + id + '/' + type,
            '_blank' // <- This is what makes it open in a new window.
        );
    }

    function openNeedRingkasanKontrak(rekening) {
        $("#modal-need-ringkasan-kontrak").modal()
        details(rekening)
    }

    $("#delete-form").submit(function() {
        var form = $(this);
        var mydata = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        });
        $.ajax({
            type: "POST",
            url: form.attr("action"),
            data: mydata,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#btn-delete").addClass("disabled").html("<i class='la la-spinner la-spin'></i>  Menghapus...").attr('disabled', true);
                form.find(".show_error").slideUp().html("");
            },
            success: function(response, textStatus, xhr) {
                var str = response;
                if (str === 'true') {
                    setTimeout(function() {
                        $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', true);
                        window.location.href = "<?= url('project') ?>";
                    }, 1000);
                } else {
                    $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', false);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                if(xhr.responseJSON.errors){
                    xhr.responseJSON.errors.map((value, key) => {
                        $(".select-" + value.code).next().children().children().addClass('has-error').focus()
                        $(".error-" + value.code).addClass("d-block").html('<strong>' + value.message + '</strong>');
                    })
                }
                $("#btn-delete").removeClass("disabled").html('Hapus').attr('disabled', false);
            }
        });

        return false;
    });
</script>
@stop