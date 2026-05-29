<form action="{{url('/situ/delete/') . '/' . $situ['id']}}" method="post">
    @csrf
    <div class="form-group">
        <label for="catatan">Hapus Situ ini ? </label>
    </div>
    <hr>
    <button type="button" data-dismiss="modal" class="btn btn-secondary float-right ml-1">Tidak</button>
    <button type="submit" class="btn btn-danger float-right">Ya</button>
</form>