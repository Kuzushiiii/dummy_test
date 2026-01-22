<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <a href="<?= base_url('purchaseorder/form') ?>" class="btn btn-primary dflex align-center">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </a>
        </div>
        <div class="card-body" style="background-color: #ffffff;">
            <div class="table-responsive margin-t-14p">
                <table class="table table-striped table-bordered table-master fs-7 w-100">
                    <thead>
                        <tr>
                            <td class="tableheader">No</td>
                            <td class="tableheader">Transaction Code</td>
                            <td class="tableheader">Tanggal Transaksi</td>
                            <td class="tableheader">Tanggal Supply</td>
                            <td class="tableheader">Supplier</td>
                            <td class="tableheader">Grand Total</td>
                            <td class="tableheader">Description</td>
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
            transactionCode = $('#transactioncode').val(),
            transactionDate = $('#transactiondate').val(),
            supplierId = $('#supplierid').val(),
            grandTotal = $('#grandtotal').val(),
            purchaseOrderId = $('#purchaseorderid').val();

        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                transactionCode: transactionCode,
                transactionDate: transactionDate,
                supplierId: supplierId,
                grandTotal: grandTotal,
                purchaseOrderId: purchaseOrderId,
            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);
                    $('#transactioncode').val("");
                    $('#transactiondate').val("");
                    $('#supplierid').val("");
                    $('#grandtotal').val("");
                    $('#purchaseorderid').val("");
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