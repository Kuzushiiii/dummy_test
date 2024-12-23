<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end">
            <button class="btn btn-primary dflex align-center" onclick="return modalForm('Add Product', 'modal-lg', '<?= getURL('product/form') ?>')">
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
                            <td class="tableheader">Product Name</td>
                            <td class="tableheader">Category</td>
                            <td class="tableheader">Price</td>
                            <td class="tableheader">Stock</td>
                            <td class="tableheader">file path</td>
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
            productname = $('#productname').val(),
            category = $('#category').val(),
            price = $('#price').val(),
            stock = $('#stock').val(),
            filepath = $('#filepath').val(),
            createdby = $('#createdby').val(),
            updatedby = $('#updatedby').val(),
            id = $('#id').val();

        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                productname: productname,
                category: category,
                price: price,
                stock: stock,
                filepath: filepath,
                createdby: createdby,
                updatedby: updatedby,
                id: id,
            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);

                    // Mengosongkan input setelah berhasil disubmit
                    $('#productname').val("");
                    $('#category').val("");
                    $('#price').val("");
                    $('#stock').val("");
                    $('#filepath').val("");
                    $('#createdby').val("");
                    $('#updatedby').val("");
                    $('#id').val("");
                    tbl.ajax.reload(); // Reload data tabel
                } else {
                    alert(res.pesan); // Menampilkan pesan error dari server
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError); // Menampilkan error jika request gagal
            }
        });
    }
</script>