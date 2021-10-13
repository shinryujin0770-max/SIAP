<form action="{{url('/pasar/delete/') . '/' . $pasar['id']}}" method="post">
    @csrf
    <div class="form-group">
        <label for="catatan">Hapus Pasar ini ? </label>
    </div>
    <hr>
    <button type="button" data-dismiss="modal" class="btn btn-secondary float-right ml-1">Tidak</button>
    <button type="submit" class="btn btn-danger float-right">Ya</button>
</form>