<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-between">
            <div class="dflex align-center" style="gap: 10px;">
                <button class="btn btn-primary dflex align-center" onclick="window.location.href='<?= getURL('category/export') ?>'">
                    <i class="bx bx-export margin-r-2"></i>
                    <span class="fw-normal fs-7">Export to Excel</span>
                </button>
                <button class="btn btn-primary dflex align-center" onclick="window.location.href='<?= getURL('category/exportPdf') ?>'">
                    <i class="bx bx-export margin-r-2"></i>
                    <span class="fw-normal fs-7">Export to PDF</span>
                </button>
            </div>
            <button class="btn btn-primary dflex align-center" onclick="return modalForm('Add Category', 'modal-lg', '<?= getURL('category/form') ?>')">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive margin-t-14p">
                <table class="table table-bordered table-master fs-7 w-100">
                    <thead>
                        <tr>
                            <td class="tableheader">No</td>
                            <td class="tableheader">CategoryName</td>
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
            categoryname = $('#categoryname').val(),
            description = $('#description').val(),
            filepath = $('#fullname').val();
            
        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                categoryname: username,
                description: description,
                filepath: filepath,
            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);
                    $('#categoryname').val("");
                    $('#description').val("");
                    $('#filepath').val("");
                    $('#addCategoryModal').modal('hide');
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