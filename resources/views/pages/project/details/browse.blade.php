{{-- // Ringkasan Kontrak --}}
@foreach ($project_details['ringkasan_kontrak'] as $ringkasan_kontrak)
<div class="card card-detail-project rounded-xl border-0" style="cursor: pointer">
    <div class="card-body position-relative">
        @if ($overtime)
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);"
            onclick="openOvertime()">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">{{!$project_details['bast'][0]['content'] ? 'Harap isi berkas atas terlebih
                    dahulu' :
                    ($project->overtime_paid == 1 ? 'Menunggu persetujuan keterlambatan dari
                    Superadmin' : 'Harap lakukan pengajuan keterlambatan terlebih dahulu.')}}</p>
            </div>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center px-5"
            onclick="detailBerkas({{$ringkasan_kontrak['id']}})">
            <img src="{{asset('/assets/documents/'.$ringkasan_kontrak->type.'.svg')}}" alt="perencanaan" width="200"
                height="200">
            <div class="text-right">
                <div class="text-capitalize font-weight-bold">Ringkasan Kontrak</div>
                <small>{{$ringkasan_kontrak['rekening']}}</small>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- // Laporan Selesai --}}
@if (isset($project_details['laporan_selesai']))
<div class="card card-detail-project rounded-xl border-0" style="cursor: pointer">
    <div class="card-body position-relative">
        @if ($overtime || !$project_details['ringkasan_kontrak'][0]['content'])
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);"
            onclick="openOvertime()">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">{{!$project_details['ringkasan_kontrak'][0]['content'] ? 'Harap isi berkas atas
                    terlebih dahulu' :
                    ($project->overtime_paid == 1 ? 'Menunggu persetujuan keterlambatan dari
                    Superadmin' : 'Harap lakukan pengajuan keterlambatan terlebih dahulu.')}}</p>
            </div>
        </div>
        @endif
        @if (!$limit)
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">Pembayaran yang diajukan harus lebih dari sama dengan 75% terlebih dahulu</p>
            </div>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center px-5"
            onclick="detailBerkas({{$project_details['laporan_selesai'][0]['id']}})">
            <img src="{{asset('/assets/documents/'.$project_details['laporan_selesai'][0]->type.'.svg')}}"
                alt="perencanaan" width="200" height="200">
            <div class="text-right">
                <div class="text-capitalize font-weight-bold">Laporan Selesai</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- // Berita Acara --}}
<div class="card card-detail-project rounded-xl border-0" style="cursor: pointer">
    <div class="card-body position-relative">
        @if ($overtime || !$project_details['ringkasan_kontrak'][0]['content'] ||
        (isset($project_details['laporan_selesai']) &&
        !$project_details['laporan_selesai'][0]['content']))
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);"
            onclick="openOvertime()">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">{{(isset($project_details['laporan_selesai']) &&
                    !$project_details['laporan_selesai'][0]['content']) ? 'Harap isi berkas
                    atas terlebih dahulu' : ($project->overtime_paid == 1 ? 'Menunggu persetujuan keterlambatan dari
                    Superadmin' : 'Harap lakukan pengajuan keterlambatan terlebih dahulu.')}}</p>
            </div>
        </div>
        @endif

        @if (!$limit)
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">Pembayaran yang diajukan harus lebih dari sama dengan 75% terlebih dahulu</p>
            </div>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center px-5"
            onclick="detailBerkas({{$project_details['berita_acara'][0]['id']}})">
            <img src="{{asset('/assets/documents/'.$project_details['berita_acara'][0]->type.'.svg')}}"
                alt="perencanaan" width="200" height="200">
            <div class="text-right">
                <div class="text-capitalize font-weight-bold">Berita Acara Pemeriksaan</div>
            </div>
        </div>
    </div>
</div>

{{-- // BAST --}}
<div class="card card-detail-project rounded-xl border-0" style="cursor: pointer">
    <div class="card-body position-relative">
        @if ($overtime || !$project_details['berita_acara'][0]['content'])
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);"
            onclick="openOvertime()">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">{{!$project_details['berita_acara'][0]['content'] ? 'Harap isi berkas atas
                    terlebih dahulu' :
                    ($project->overtime_paid == 1 ? 'Menunggu persetujuan keterlambatan dari
                    Superadmin' : 'Harap lakukan pengajuan keterlambatan terlebih dahulu.')}}</p>
            </div>
        </div>
        @endif

        @if (!$limit)
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">Pembayaran yang diajukan harus lebih dari sama dengan 75% terlebih dahulu</p>
            </div>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center px-5"
            onclick="detailBerkas({{$project_details['bast'][0]['id']}})">
            <img src="{{asset('/assets/documents/'.$project_details['bast'][0]->type.'.svg')}}" alt="perencanaan"
                width="200" height="200">
            <div class="text-right">
                <div class="text-capitalize font-weight-bold">Berita Acara Serah Terima</div>
            </div>
        </div>
    </div>
</div>

{{-- // Pengantar --}}
<div class="card card-detail-project rounded-xl border-0" style="cursor: pointer">
    <div class="card-body position-relative">
        @if ($overtime || !$project_details['bast'][0]['content'])
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);"
            onclick="openOvertime()">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">{{!$project_details['bast'][0]['content'] ? 'Harap isi berkas atas
                    terlebih dahulu' :
                    ($project->overtime_paid == 1 ? 'Menunggu persetujuan keterlambatan dari
                    Superadmin' : 'Harap lakukan pengajuan keterlambatan terlebih dahulu.')}}</p>
            </div>
        </div>
        @endif

        @if (!$limit)
        <div class="position-absolute h-100 w-100 rounded-xl p-3 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; backdrop-filter: blur(2px); background-color: rgba(0, 0, 0, 0.75);">
            <div class="text-center">
                <i class="fa far fa-lock mb-3" style="cursor: pointer; font-size: 60pt; color: #fff;"></i>
                <p style="color: #fff;">Pembayaran yang diajukan harus lebih dari sama dengan 75% terlebih dahulu</p>
            </div>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center px-5"
            onclick="detailBerkas({{$project_details['pengantar'][0]['id']}})">
            <img src="{{asset('/assets/documents/'.$project_details['pengantar'][0]->type.'.svg')}}" alt="perencanaan"
                width="200" height="200">
            <div class="text-right">
                <div class="text-capitalize font-weight-bold">Pengantar</div>
            </div>
        </div>
    </div>
</div>

@if ($overtime && $project->overtime_paid == 0)
<script>
    function openOvertime() {
        $("#modal-overtime").modal()
    }

    // $("#modal-overtime").modal();
</script>
@endif