<form action="{{route('jenis_dagangan.update')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="id" value="{{$jenis_dagangan['id']}}">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="dt[kategori]" class="form-control select2" style="width: 100%">
                            <option value="A" <?= $jenis_dagangan['kategori'] == 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= $jenis_dagangan['kategori'] == 'B' ? 'selected' : '' ?>>B</option>
                            <option value="C" <?= $jenis_dagangan['kategori'] == 'C' ? 'selected' : '' ?>>C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <input type="text" name="dt[nama]" class="form-control" placeholder="Masukkan Nama Jenis..."
                            value="{{$jenis_dagangan['nama']}}">
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