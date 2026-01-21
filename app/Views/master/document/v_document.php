<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <button class="btn btn-primary dflex align-center" onclick="return modalForm('Add Document', 'modal-lg', '<?= getURL('document/form') ?>')">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </button>
            <button class="btn btn-success dflex align-center margin-l-2" onclick="return modalForm('Import Document', 'modal-lg', '<?= getURL('document/formImport') ?>')">
                <i class="bx bx-import margin-r-2"></i>
                <span class="fw-normal fs-7">Import</span>
            </button>
            <button class="btn btn-primary dflex align-center margin-l-2" onclick="window.location.href='<?= base_url('Document/export') ?>'">
                <i class="bx bx-spreadsheet margin-r-2"></i>
                <span class="fw-normal fs-7">Export Excel</span>
            </button>
            <button class="btn btn-danger dflex align-center margin-l-2" onclick="window.location.href='<?= base_url('Document/exportpdf') ?>'">
                <i class="bx bx-printer margin-r-2"></i>
                <span class="fw-normal fs-7">Export PDF</span>
            </button>

        </div>

        <div class="card-body" style="background-color: #ffffff;">
            <div class="table-responsive margin-t-14p">
            <table class="table table-striped table-bordered table-master fs-7 w-100">
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