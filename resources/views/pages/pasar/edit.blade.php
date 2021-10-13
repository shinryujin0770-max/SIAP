<form action="{{route('pasar.update')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="id" value="{{$pasar['id']}}">
                    <div class="form-group">
                        <label>Nama Pasar</label>
                        <input type="text" name="dt[nama]" class="form-control" placeholder="Masukkan Nama Pasar..."
                            value="{{$pasar['nama']}}">
                    </div>
                    <div class="form-group">
                        <label>Lokasi Pasar</label>
                        <input type="text" name="dt[lokasi]" class="form-control" placeholder="Masukkan Lokasi Pasar..."
                            value="{{$pasar['lokasi']}}">
                    </div>
                    <div class="form-group">
                        <label>Kelas Pasar</label>
                        <input type="number" name="dt[kelas]" class="form-control" placeholder="Masukkan Kelas Pasar..."
                            value="{{$pasar['kelas']}}">
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