<form action="{{url('/situ_reregistration/signature/') . '/' . $situ_registration['id']}}" method="post">
    @csrf
    <div class="form-group text-center">
        <label for="catatan">Tandatangani daftar ulang untuk situ ini ? </label>
        <small class="d-block">Situ dengan kode {{ $situ['kode']}}</small>
        <small class="d-block">Daftar ulang ke {{ $situ_registration['registration']}}</small>
    </div>
    <div class="d-flex justify-content-center align-items-center mt-4">
        <button type="submit" class="btn btn-primary float-right w-25">Ya</button>
        <button type="button" data-dismiss="modal" class="btn btn-secondary float-right w-25 ml-1">Tidak</button>
    </div>
</form>