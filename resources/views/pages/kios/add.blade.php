<form action="{{route('kios.store')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Nomor Kios</label>
                        <input type="text" name="dt[nomor]" class="form-control" placeholder="Masukkan Nomor Kios...">
                    </div>
                    <div class="form-group">
                        <label>Pasar</label>
                        <select class="form-control select2" id="id_pasar" name="dt[id_pasar]" style="width: 100%">
                            @foreach ($pasars as $pasar)
                            <option value="{{$pasar['id']}}">{{$pasar['nama']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kategori Kios</label>
                        <select class="form-control select2" id="kategori" name="dt[kategori]" style="width: 100%">
                            <option value="kios">KIOS</option>
                            <option value="los">LOS</option>
                            <option value="pelataran">PELATARAN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ukuran Kios</label>
                        <input type="text" name="dt[ukuran]" class="form-control" placeholder="Masukkan Ukuran Kios...">
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