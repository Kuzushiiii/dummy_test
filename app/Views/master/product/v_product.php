<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <button class="btn btn-primary dflex align-center" onclick="return modalForm('Add Product', 'modal-lg', '<?= getURL('product/form') ?>')">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </button>
            <button class="btn btn-success dflex align-center margin-l-2" onclick="return modalForm('Import Product', 'modal-lg', '<?= getURL('product/formImport') ?>')">
                <i class="bx bx-import margin-r-2"></i>
                <span class="fw-normal fs-7">Import</span>
            </button>
            <button class="btn btn-primary dflex align-center margin-l-2" onclick="downloadexcel()">
                <i class="bx bx-spreadsheet margin-r-2"></i>
                <span class="fw-normal fs-7">Export</span>
            </button>
            <button class="btn btn-danger dflex align-center margin-l-2" onclick="downloadpdf()">
                <i class="bx bx-printer margin-r-2"></i>
                <span class="fw-normal fs-7">PDF</span>
            </button>
        </div>
        <div class="card-body" style="background-color: #ffffff;">
            <div class="table-responsive margin-t-14p">
                <table class="table table-striped table-bordered table-master fs-7 w-100">
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
    function downloadexcel(){
        window.location.href = '<?= getURL('product/export')?>';
    }
    function downloadpdf(){
        window.location.href = '<?= getURL('product/pdf')?>';
    }
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

                    $('#productname').val("");
                    $('#category').val("");
                    $('#price').val("");
                    $('#stock').val("");
                    $('#filepath').val("");
                    $('#createdby').val("");
                    $('#updatedby').val("");
                    $('#id').val("");
                    tbl.ajax.reload();
                } else {
                    alert(res.pesan);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError); 
            }
        });
    }
</script>