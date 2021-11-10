<form action="{{route('kios.update')}}" method="post" enctype="multipart/form-data">
    <div class="row pb-3">
        @csrf
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" name="id" value="{{$kios['id']}}">
                    <div class="form-group">
                        <label>Nomor Kios</label>
                        <input type="text" name="dt[nomor]" class="form-control" placeholder="Masukkan Nomor Kios..."
                            value="{{$kios['nomor']}}">
                    </div>
                    <div class="form-group">
                        <label>Pasar</label>
                        <select class="form-control select2" id="id_pasar" name="dt[id_pasar]" style="width: 100%">
                            @foreach ($pasars as $pasar)
                            <option value="{{$pasar['id']}}" {{$pasar['id']==$kios['id_pasar'] ? 'selected' : '' }}>
                                {{$pasar['nama']}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kategori Kios</label>
                        <select class="form-control select2" id="kategori" name="dt[kategori]" style="width: 100%">
                            <option value="kios" {{$kios['kategori']=='kios' ? 'selected' : '' }}>KIOS</option>
                            <option value="los" {{$kios['kategori']=='los' ? 'selected' : '' }}>LOS</option>
                            <option value="pelataran" {{$kios['kategori']=='pelataran' ? 'selected' : '' }}>PELATARAN
                            </option>
                            <option value="grosir" {{$kios['kategori']=='grosir' ? 'selected' : '' }}>GROSIR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ukuran Kios</label>
                        <input type="text" name="dt[ukuran]" class="form-control" placeholder="Masukkan Ukuran Kios..."
                            value="{{$kios['ukuran']}}">
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