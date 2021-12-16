@foreach ($project_details as $detail)
<div class="card card-detail-project rounded-xl border-0" onclick="detailBerkas({{$detail['id']}})" style="cursor: pointer">
    <div class="card-body px-5">
        <div class="d-flex justify-content-between align-items-center">
            <img src="{{asset('/assets/documents/'.$detail->type.'.svg')}}" alt="perencanaan" width="200" height="200">
            <div>
                <div class="text-capitalize font-weight-bold">{{str_replace('_', ' ', $detail['type'])}}</div>
                @if ($detail['type'] == 'ringkasan_kontrak')
                <small>{{$detail['rekening']}}</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach