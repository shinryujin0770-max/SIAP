<div class="row p-5">
    <div class="col-sm-12 col-lg-4 d-flex justify-content-center align-items-center">
        <div class="card card-add-project rounded-xl" style="width: 300px; height: 300px; cursor: pointer;" onclick="add('perencanaan')">
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="text-center" style="font-size: 14pt;">
                    <img src="{{ asset('assets/projects/perencanaan.jpg')}}" alt="perencanaan" style="width: 100%;">
                    <div>Perencanaan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4 d-flex justify-content-center align-items-center">
        <div class="card card-add-project rounded-xl" style="width: 300px; height: 300px; cursor: pointer;" onclick="add('konstruksi')">
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="text-center" style="font-size: 14pt;">
                    <img src="{{ asset('assets/projects/konstruksi.png')}}" alt="konstruksi" style="width: 100%;">
                    <div>Konstruksi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4 d-flex justify-content-center align-items-center">
        <div class="card card-add-project rounded-xl" style="width: 300px; height: 300px; cursor: pointer;" onclick="add('pengawasan')">
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="text-center" style="font-size: 14pt;">
                    <img src="{{ asset('assets/projects/pengawasan.png')}}" alt="pengawasan" style="width: 100%;" style="object-fit: cover;">
                    <div>Pengawasan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-flex justify-content-center align-items-center">
        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle mr-1"></i> Tutup</button>
    </div>
</div>

<script>
    function add(category) {
        location.href = "<?= url('project/add') . '?category=' ?>" + category;
    }

    function transfer() {
        $("#modal-form").modal();
        $("#load-form").load("<?= url('situ/modalTransfer') . '/' ?>")
    }
</script>