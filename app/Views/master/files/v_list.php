<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>

<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-between"
            style="background-color:#f8f9fa;border-bottom:1px solid #dee2e6;padding-top:10px;padding-bottom:10px;margin-bottom:8px;">
            <h5 class="mb-0"></h5>
            <button type="button"
                class="btn btn-primary btn-sm dflex align-center"
                style="margin-right: 15px;"
                onclick="modalForm('Add New File', 'modal-lg', '<?= getURL('files/form') ?>', {identifier: this})">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </button>
        </div>
        <div class="card-body" style="background-color:#ffffff;">
            <div class="table-responsive margin-t-14p">
                <table class="table table-striped table-bordered table-master fs-7 w-100" id="filesTable">
                    <thead>
                        <tr>
                            <th class="tableheader">No</th>
                            <th class="tableheader">Real Name</th>
                            <th class="tableheader">File Name</th>
                            <th class="tableheader">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('template/v_footer') ?>

<script>
    Dropzone.autoDiscover = false;
    let filesTable;

    $(document).ready(function() {
        filesTable = $('#filesTable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            order: [
                [1, 'asc']
            ],
            ajax: {
                url: '<?= getURL("files/table"); ?>',
                type: 'POST',
                data: function(d) {
                    d['<?= csrf_token() ?>'] = decrypter($("#csrf_token").val());
                }
            },
            columns: [{
                    data: 0
                },
                {
                    data: 1
                },
                {
                    data: 2
                },
                {
                    data: 3
                },
            ]
        });
    });

    function previewFile(id) {
        const url = '<?= getURL('files/preview') ?>/' + id;
        const html = `
            <div style="text-align:center;">
                <img src="${url}" alt="preview" style="max-width:100%;max-height:70vh;">
            </div>`;
        $('#modaldetail-title').text('Preview File');
        $('#modaldetail-form').html(html);
        $('#modaldetail').modal('show');
    }
</script>