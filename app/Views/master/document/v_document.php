<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end">
        <div class="button-group">
    <!-- Add New Button -->
    <button class="btn btn-primary d-flex align-center" style="margin: 0.5rem;" onclick="return modalForm('Add Category', 'modal-lg', '<?= getURL('document/form') ?>')">
        <i class="bx bx-plus-circle margin-r-2"></i> <!-- Ikon untuk Add -->
        <span class="fw-normal fs-7">Add New</span>
    </button>

    <!-- Export to Excel Button -->
    <button class="btn btn-success d-flex align-center" style="margin: 0.5rem;" onclick="window.location.href='<?= base_url('Document/export') ?>'">
        <i class="bx bx-spreadsheet margin-r-2"></i> <!-- Ikon untuk Excel -->
        <span class="fw-normal fs-7">Export to Excel</span>
    </button>

    <!-- Export to PDF Button -->
    <button class="btn btn-danger d-flex align-center" style="margin: 0.5rem;" onclick="window.location.href='<?= base_url('Document/exportpdf') ?>'">
        <i class="bx bx-file margin-r-2"></i> <!-- Ikon untuk PDF -->
        <span class="fw-normal fs-7">Export to PDF</span>
    </button>
</div>

        </div>

        <div class="table-responsive margin-t-14p">
            <table class="table table-bordered table-master fs-7 w-100">
                <thead>
                    <tr>
                        <td class="tableheader">No</td>
                        <td class="tableheader">Documentname</td>
                        <td class="tableheader">Description</td>
                        <td class="tableheader">FilePath</td>
                        <td class="tableheader">Actions</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
    function submitData() {
        let link = $('#linksubmit').val(),
            categoryname = $('#documentname').val(),
            description = $('#description').val(),
            filepath = $('#fullname').val();

        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                categoryname: documentname,
                description: description,
                filepath: filepath,

            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);
                    $('#documentname').val("");
                    $('#description').val("");
                    $('#filepath').val("");
                    $('#addDocumentModal').modal('hide');
                    tbl.ajax.reload();
                } else {
                    alert(res.pesan);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        })
    }
</script>
</body>

</html>