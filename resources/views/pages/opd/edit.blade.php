<form action="{{route('opd.update')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="id" value="{{$opd['id']}}">
                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="dt[code]" class="form-control" placeholder="Masukkan Kode..." value="{{$opd['code']}}">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="dt[name]" class="form-control" placeholder="Masukkan Nama..." value="{{$opd['name']}}">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="dt[address]" class="form-control" placeholder="Masukkan Alamat..." value="{{$opd['address']}}">
                    </div>
                    <hr>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary float-right ml-1">Batal</button>
                    <button type="submit" class="btn btn-primary float-right">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.select2').select2();
</script>