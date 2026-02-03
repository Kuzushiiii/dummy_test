<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-end" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <a href="<?= base_url('purchaseorder/form') ?>" class="btn btn-primary dflex align-center">
                <i class="bx bx-plus-circle margin-r-2"></i>
                <span class="fw-normal fs-7">Add New</span>
            </a>
            <button type="button" id="btnExportExcel" class="btn btn-success btn-sm dflex align-center margin-l-2">
                <i class="bx bx-file margin-r-2"></i>
                <span class="fw-normal fs-7">Export Excel</span>
            </button>
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

<style>
    #exportModal .modal-footer {
        padding: 1rem;
        /* jarak atas-bawah footer */
    }

    #exportStatusText {
        margin-top: 1rem;
    }

    #exportModal .progress {
        height: 22px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    #exportModal .progress-bar {
        font-size: 1rem;
        line-height: 22px;
        font-weight: 600;
    }
</style>

<?= $this->include('template/v_footer') ?>
<!-- Modal Export Progress -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Purchase Order</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="progress">
                        <div id="exportProgressBar" class="progress-bar" role="progressbar"
                            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                    </div>
                </div>
                <div id="exportStatusText" class="small">Menyiapkan file...</div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCancelExport" class="btn btn-danger btn-sm">Cancel</button>
                <button type="button" id="btnCloseExport" class="btn btn-secondary btn-sm d-none" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    let currentExportId = null;
    let exportRunning = false;
    let exportOffset = 0
    const exportLimit = 500;

    $('#btnExportExcel').on('click', function() {
        // kalau masih ada proses export berjalan, jangan mulai baru
        if (exportRunning) {
            return;
        }
        startExport();
    });

    $('#btnCancelExport').on('click', function() {
        if (!currentExportId) return;
        cancelExport();
    });

    function openExportModal() {
        $('#exportProgressBar')
            .css('width', '0%')
            .attr('aria-valuenow', 0)
            .text('0%');
        $('#exportStatusText').text('Menyiapkan file...');
        $('#btnCloseExport').addClass('d-none');
        $('#btnCancelExport').removeClass('d-none');
        $('#exportModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#exportModal').modal('show');
    }

    function updateProgressBar(percent, text) {
        $('#exportProgressBar')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent)
            .text(percent + '%');
        if (text) {
            $('#exportStatusText').text(text);
        }
    }

    function startExport() {
        openExportModal();
        exportRunning = true;
        exportOffset = 0;

        $.post('<?= getURL("purchaseorder/startExport"); ?>', {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.sukses == 1) {
                currentExportId = res.exportId;
                processExportChunk();
            } else {
                exportRunning = false;
                updateProgressBar(0, res.pesan || 'Gagal memulai export');
                $('#btnCancelExport').addClass('d-none');
                $('#btnCloseExport').removeClass('d-none');
            }
        }, 'json').fail(function() {
            exportRunning = false;
            updateProgressBar(0, 'Terjadi error saat memulai export');
            $('#btnCancelExport').addClass('d-none');
            $('#btnCloseExport').removeClass('d-none');
        });
    }

    function processExportChunk() {
        if (!exportRunning || !currentExportId) return;

        $.post('<?= getURL("purchaseorder/processExportChunk"); ?>', {
            exportId: currentExportId,
            limit: exportLimit,
            offset: exportOffset,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.sukses != 1) {
                exportRunning = false;
                updateProgressBar(0, res.pesan || 'Gagal memproses export');
                $('#btnCancelExport').addClass('d-none');
                $('#btnCloseExport').removeClass('d-none');
                return;
            }

            const progress = res.progress || 0;
            updateProgressBar(progress, `Memproses... ${progress}%`);

            // update offset untuk request berikutnya (kalau backend kirim offset_next)
            if (typeof res.offset_next !== 'undefined') {
                exportOffset = res.offset_next;
            } else {
                exportOffset += exportLimit;
            }
            if (res.finished) {
                exportRunning = false;
                updateProgressBar(100, 'Export selesai. Mengunduh file...');
                $('#btnCancelExport').addClass('d-none');
                $('#btnCloseExport').removeClass('d-none');

                // trigger download
                window.location.href = '<?= getURL("purchaseorder/downloadExport"); ?>/' + currentExportId;

                // langsung tutup modal setelah selesai
                $('#exportModal').modal('hide');
            } else {
                // lanjut chunk berikutnya
                setTimeout(processExportChunk, 400); // jeda 0.4 detik
            }
        }, 'json').fail(function() {
            exportRunning = false;
            updateProgressBar(0, 'Terjadi error saat memproses export');
            $('#btnCancelExport').addClass('d-none');
            $('#btnCloseExport').removeClass('d-none');
        });
    }

    function cancelExport() {
        if (!currentExportId) return;
        exportRunning = false;

        $.post('<?= getURL("purchaseorder/processExportChunk"); ?>', {
            exportId: currentExportId,
            cancel: 1,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            // apapun responnya, anggap export dibatalkan
            updateProgressBar(0, res.pesan || 'Export dibatalkan');

            $('#btnCancelExport').addClass('d-none');
            $('#btnCloseExport').removeClass('d-none');

            // setelah dibatalkan, izinkan ESC & klik luar untuk menutup modal
            $('#exportModal').modal({
                backdrop: true,
                keyboard: true
            });
        }, 'json').fail(function() {
            updateProgressBar(0, 'Gagal membatalkan export');
            $('#btnCancelExport').addClass('d-none');
            $('#btnCloseExport').removeClass('d-none');

            $('#exportModal').modal({
                backdrop: true,
                keyboard: true
            });
        });
    }
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