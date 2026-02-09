<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x shadow-sm w-100">
        <div class="card-header dflex align-center justify-between"
            style="background-color:#f8f9fa;border-bottom:1px solid #dee2e6;padding-top:10px;padding-bottom:10px;margin-bottom:8px;">
            <!-- kiri filter-->
            <div class="dflex align-center" style="gap:16px;flex-wrap:wrap;">
                <div class="dflex align-center" style="gap:6px; padding-left:10px;">
                    <label for="filterTransDateFrom" class="mb-0" style="white-space:nowrap;font-size:14px;">
                        Transaksi Dari Tanggal
                    </label>
                    <input type="date" id="filterTransDateFrom" class="form-control form-control-sm date-filter-input">
                </div>
                <div class="dflex align-center" style="gap:6px;">
                    <label for="filterTransDateTo" class="mb-0" style="white-space:nowrap;font-size:14px;">
                        Sampai Tanggal
                    </label>
                    <input type="date" id="filterTransDateTo" class="form-control form-control-sm date-filter-input">
                </div>
                <div class="dflex align-center" style="gap:8px;">
                    <label for="filterSupplier" class="mb-0" style="white-space:nowrap;font-size:14px;">
                        Supplier
                    </label>
                    <select id="filterSupplier" class="form-control form-control-sm" style="min-width:220px;">
                        <option value="">All Supplier</option>
                    </select>
                    <button type="button" id="btnApplyFilter" class="btn btn-sm btn-primary me-1">
                        <i class="bx bx-search"></i>
                        Apply
                    </button>
                    <button type="button" id="btnResetFilter" class="btn btn-sm btn-info me-1">
                        <i class="bx bx-reset"></i>
                        Reset
                    </button>
                </div>
            </div>
            <!-- kanan: tombol -->
            <div class="dflex align-center" style="gap:6px; padding-right:10px">
                <a href="<?= base_url('purchaseorder/form') ?>" class="btn btn-primary btn-sm dflex align-center">
                    <i class="bx bx-plus-circle margin-r-2"></i>
                    <span class="fw-normal fs-7">Add New</span>
                </a>
                <button type="button" id="btnExportExcel" class="btn btn-success btn-sm dflex align-center">
                    <i class="bx bx-file margin-r-2"></i>
                    <span class="fw-normal fs-7">Export Excel</span>
                </button>
            </div>
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
    .date-filter-input {
        width: 110px;
        /* paksa lebar input */
        max-width: 110px;
        display: inline-block;
        /* jaga supaya patuh ukuran custom */
        padding-right: 0.25rem;
        /* opsional, biar icon kalender tidak kepotong */
    }

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

    #btnCloseExport {
        padding: 5px 10px;
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
                <button type="button" id="btnCloseExport" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    // ====== DATATABLE + FILTER ======
    let poTable = null;

    $(document).ready(function() {
        $('#filterSupplier').select2({
            placeholder: 'All Supplier',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true
        });

        loadSuppliersFilter();

        poTable = $('.table-master').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: '<?= getURL("purchaseorder/table"); ?>',
                type: 'POST',
                data: function(d) {
                    d.filterTransDateFrom = $('#filterTransDateFrom').val();
                    d.filterTransDateTo = $('#filterTransDateTo').val();
                    d.filterSupplier = $('#filterSupplier').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [{
                    data: 0
                }, // No
                {
                    data: 1
                }, // TransCode
                {
                    data: 2
                }, // Tanggal Transaksi
                {
                    data: 3
                }, // Tanggal Supply
                {
                    data: 4
                }, // Supplier
                {
                    data: 5
                }, // Grand Total
                {
                    data: 6
                }, // Description
                {
                    data: 7
                } // Actions              
            ]
        });

        // tombol apply filter
        $('#btnApplyFilter').on('click', function() {
            const from = $('#filterTransDateFrom').val();
            const to = $('#filterTransDateTo').val();

            // validasi sederhana: from tidak boleh > to
            if (from && to && from > to) {
                alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            poTable.ajax.reload();
        });

        // tombol reset filter
        $('#btnResetFilter').on('click', function() {
            $('#filterTransDateFrom').val('');
            $('#filterTransDateTo').val('');
            $('#filterSupplier').val(null).trigger('change');
            poTable.ajax.reload();
        });
    });

    function loadSuppliersFilter() {
        $.post('<?= getURL("purchaseorder/getsuppliers"); ?>', {
            searchTerm: '',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.data) {
                res.data.forEach(function(s) {
                    $('#filterSupplier').append(
                        $('<option>', {
                            value: s.id,
                            text: s.suppliername ?? s.text
                        })
                    );
                });
            }
        }, 'json');
    }

    let currentExportId = null;
    let exportRunning = false;
    let exportOffset = 0;
    const exportLimit = 500;
    let currentProgress = 0;

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
        currentProgress = 0;

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

    // naikkan progress sedikit demi sedikit sampai targetPercent
    function animateProgress(targetPercent) {
        targetPercent = Math.min(100, Math.max(0, targetPercent)); // clamp 0-100

        if (targetPercent <= currentProgress) {
            return; // sudah sampai atau lewat, tidak perlu animasi
        }

        const step = 1; // naik 1% per frame
        const interval = 20; // setiap 20ms (0.02 detik)

        const timer = setInterval(function() {
            if (!exportRunning && targetPercent < 100) {
                // kalau proses dibatalkan di tengah, jangan lanjut animasi
                clearInterval(timer);
                return;
            }

            currentProgress += step;
            if (currentProgress >= targetPercent) {
                currentProgress = targetPercent;
                clearInterval(timer);
            }

            updateProgressBar(currentProgress, `Memproses... ${currentProgress}%`);
        }, interval);
    }

    function startExport() {
        openExportModal();
        exportRunning = true;
        exportOffset = 0;

        $.post('<?= getURL("purchaseorder/startExport"); ?>', {
            filterTransDateFrom: $('#filterTransDateFrom').val(),
            filterTransDateTo: $('#filterTransDateTo').val(),
            filterSupplier: $('#filterSupplier').val(),
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
            filterTransDateFrom: $('#filterTransDateFrom').val(),
            filterTransDateTo: $('#filterTransDateTo').val(),
            filterSupplier: $('#filterSupplier').val(),
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
            // animasikan dari currentProgress ke progress yang dikirim backend
            animateProgress(progress);

            // update offset untuk request berikutnya (kalau backend kirim offset_next)
            if (typeof res.offset_next !== 'undefined') {
                exportOffset = res.offset_next;
            } else {
                exportOffset += exportLimit;
            }
            if (res.finished) {
                console.log('Export finished, will download. exportId =', currentExportId);
                exportRunning = false;
                animateProgress(100);
                $('#exportStatusText').text('Export selesai. Mengunduh file...');
                $('#btnCancelExport').addClass('d-none');
                $('#btnCloseExport').removeClass('d-none');

                // setelah export selesai, langsung trigger download
                const downloadUrl = '<?= getURL("purchaseorder/downloadExport"); ?>/' + currentExportId;
                console.log('Download URL =', downloadUrl);
                window.location.href = downloadUrl;

                // tutup modal segera
                setTimeout(function() {
                    $('#exportModal').modal('hide');
                }, 3000); // 3 detik
            } else {
                console.log('Export not finished yet, next offset =', exportOffset);
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
            $('#exportStatusText').text(res.pesan || 'Export dibatalkan');

            $('#btnCancelExport').addClass('d-none');
            $('#btnCloseExport').removeClass('d-none');

            // setelah dibatalkan, izinkan ESC & klik luar untuk menutup modal
            $('#exportModal').modal({
                backdrop: true,
                keyboard: true
            });
        }, 'json').fail(function() {
            $('#exportStatusText').text('Gagal membatalkan export');
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