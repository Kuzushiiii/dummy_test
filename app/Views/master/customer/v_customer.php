<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <button class="btn btn-primary dflex align-center" onclick="return modalForm('Tambah Customer', 'modal-lg', '<?= getURL('customer/form') ?>')">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </button>
            <button class="btn btn-success dflex align-center margin-l-2" onclick="return modalForm('Import Customer', 'modal-lg', '<?= getURL('customer/formImport') ?>')">
                <i class="bx bx-import margin-r-2"></i>
                <span class="fw-normal fs-7">Import</span>
            </button>
            <button class="btn btn-primary dflex align-center margin-l-2" onclick="exportExcel()">
                <i class="bx bx-spreadsheet margin-r-2"></i>
                <span class="fw-normal fs-7">Export Excel</span>
            </button>
            <button class="btn btn-warning dflex align-center margin-l-2" onclick="window.location.href='<?= getURL('customer/printpdf') ?>'">
                <i class="bx bx-printer margin-r-2"></i>
                <span class="fw-normal fs-7">Print PDF</span>
            </button>
        </div>
        <div class="card-body" style="background-color: #ffffff;">
            <div class="table-responsive margin-t-14p">
                <table class="table table-striped table-bordered table-master fs-7 w-100">
                    <thead>
                        <tr>
                            <td class="tableheader">No</td>
                            <td class="tableheader">Foto Customer</td>
                            <td class="tableheader">Nama</td>
                            <td class="tableheader">Alamat</td>
                            <td class="tableheader">Telephone</td>
                            <td class="tableheader">Email</td>
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
            username = $('#username').val(),
            password = $('#password').val(),
            fullname = $('#fullname').val(),
            email = $('#email').val(),
            telp = $('#phone').val(),
            userid = $('#userid').val();

        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                filepath: filepath,
                customername: customername,
                address: address,
                phone: phone,
                email: email,
                userid: userid,
            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);
                    $('#filepath').val("");
                    $('#customername').val("");
                    $('#phone').val("");
                    $('#userid').val("");
                    $('#username').val("");
                    $('#password').val("");
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
    function exportExcel() {
        window.location.href = "<?= base_url('customer/exportexcel') ?>";
    }
</script>