<div class="loader" style="display: none">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<h3 class="text-center mb-4 text-danger">{{$page_subtitle}}</h3>

<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
</div>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Detail Item yang akan Dihapus</h5>
        <table class="table table-sm table-borderless">
            <tr>
                <td width="30%"><strong>Tipe</strong></td>
                <td>: {{$type_label}}</td>
            </tr>
            <tr>
                <td><strong>Kode</strong></td>
                <td>: {{$rfk_detail->code}}</td>
            </tr>
            <tr>
                <td><strong>Uraian</strong></td>
                <td>: {{$rfk_detail->uraian}}</td>
            </tr>
        </table>
    </div>
</div>

@if($children_count > 0)
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <strong>Perhatian!</strong> Item ini memiliki <strong>{{$children_count}}</strong> item turunan (children).
    <br>
    Menghapus item ini akan menghapus <strong>SEMUA</strong> item turunan di bawahnya.
</div>
@endif

<form id="form-delete" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{$rfk_detail->id}}">

    <div class="form-group">
        <p class="text-center mb-4">
            Apakah Anda yakin ingin menghapus item ini
            @if($children_count > 0)
                <strong>beserta {{$children_count}} item turunannya</strong>
            @endif
            ?
        </p>
    </div>

    <div class="form-group text-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger" id="btn-delete">
            <i class="fa fa-trash mr-2"></i>
            Ya, Hapus
            @if($children_count > 0)
                ({{$children_count + 1}} item)
            @endif
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#form-delete').submit(function(e) {
            e.preventDefault();

            const id = $('input[name="id"]').val();

            // Double confirmation for items with children
            @if($children_count > 0)
            swal({
                title: "Konfirmasi Penghapusan",
                text: "Item ini memiliki {{$children_count}} item turunan. Semua akan dihapus. Lanjutkan?",
                icon: "warning",
                buttons: ["Batal", "Ya, Hapus Semua"],
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    performDelete(id);
                }
            });
            @else
            performDelete(id);
            @endif
        });

        function performDelete(id) {
            $.ajax({
                type: 'POST',
                url: "{{ url('/rfk-v2/cascadeDelete') }}/" + id,
                contentType: 'application/json',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                beforeSend: function() {
                    $('#btn-delete').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span>Menghapus...');
                    $('.loader').show();
                },
                success: function(response) {
                    $('.loader').hide();
                    $('#btn-delete').prop('disabled', false).html('<i class="fa fa-trash mr-2"></i>Ya, Hapus');

                    if (response.success) {
                        toastr.success(response.message || 'Berhasil Menghapus Data');
                        $('#modal-add').modal('hide');

                        // Update table without reload
                        if (typeof removeRowFromTable === 'function') {
                            const id = $('input[name="id"]').val();
                            removeRowFromTable(id);

                            // Update parent rows if any
                            if (response.updated_parents && response.updated_parents.length > 0 && typeof updateParentRowsInTable === 'function') {
                                updateParentRowsInTable(response.updated_parents);
                            }
                        } else if (typeof getListData === 'function') {
                            // Fallback: reload table if functions not available
                            getListData();
                        }
                    } else {
                        toastr.error(response.message || 'Gagal menghapus data');
                    }
                },
                error: function(xhr) {
                    $('.loader').hide();
                    $('#btn-delete').prop('disabled', false).html('<i class="fa fa-trash mr-2"></i>Ya, Hapus');

                    let errorMessage = 'Gagal menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        }
    });
</script>
