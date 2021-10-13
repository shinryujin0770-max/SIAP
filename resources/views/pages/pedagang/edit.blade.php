<form action="{{route('pedagang.update')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="id" value="{{$pedagang['id']}}">
                    <div class="form-group">
                        <label>NIK Pedagang</label>
                        <input type="text" name="dt[nik]" class="form-control" placeholder="Masukkan NIK Pedagang..."
                            value="{{$pedagang['nik']}}">
                    </div>
                    <div class="form-group">
                        <label>Nama Pedagang</label>
                        <input type="text" name="dt[nama]" class="form-control" placeholder="Masukkan Nama Pedagang..."
                            value="{{$pedagang['nama']}}">
                    </div>
                    <div class="form-group">
                        <label>Alamat Pedagang</label>
                        <input type="text" name="dt[alamat]" class="form-control"
                            placeholder="Masukkan Alamat Pedagang..." value="{{$pedagang['alamat']}}">
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir Pedagang</label>
                        <input type="text" name="dt[tempat_lahir]" class="form-control"
                            placeholder="Masukkan Tempat Lahir Pedagang..." value="{{$pedagang['tempat_lahir']}}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir Pedagang</label>
                        <input type="date" name="dt[tanggal_lahir]" class="form-control"
                            placeholder="Masukkan Tanggal Lahir Pedagang..." value="{{$pedagang['tanggal_lahir']}}">
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