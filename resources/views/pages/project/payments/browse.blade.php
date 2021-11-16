@foreach ($payment_detail as $detail)
<div class="card" onclick="editPayment({{$detail['id']}})" style="cursor: pointer">
    <div class="card-body">
        <div class="text-capitalize font-weight-bold">{{str_replace('_', ' ', $detail['type'])}}</div>
        <div class="row">
            @foreach (json_decode($detail['content'], true) as $item)
            <div class="col-12">
                <div class="text-muted">{{$item['title']}}</div>
                <div class="font-weight-bold">{{$item['value'] ?: '-'}}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach