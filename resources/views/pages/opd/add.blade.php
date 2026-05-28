<form action="{{route('opd.store')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="dt[code]" class="form-control" placeholder="Masukkan Kode...">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="dt[name]" class="form-control" placeholder="Masukkan Nama...">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="dt[address]" class="form-control" placeholder="Masukkan Alamat...">
                    </div>
                    <hr>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary float-right ml-1">Batal</button>
                    <button type="submit" class="btn btn-primary float-right">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>