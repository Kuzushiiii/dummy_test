<form id="importPoExcel" style="padding-inline: 0px;" enctype="multipart/form-data">
    <div class="row">
        <div>
            <div class="form-group">
                <label class="required">Excel File</label>
                <input type="file" name="excelfile" id="po_excelfile" accept=".xlsx, .xls" class="form-input"
                    style="padding: 8px;pointer-events: unset !important;">
            </div>
        </div>
    </div>

    <div id="loading-alltrans" class="hiding">
        <h4>
            <i class='bx bx-loader-circle bx-spin text-info'></i>
            <span id="importStatusTextPo">Processing</span>
            <span class="text-primary" id="totalsent">0</span>
            /
            <span id="alltotals" class="text-primary">0</span>
        </h4>
    </div>

    <div class="modal-footer dflex" style="justify-content: space-between !important;">
        <button style="margin: 0 !important;" class="btn btn-info dflex align-center justify-center" type="button"
            onclick="downloadPoTemplate()">
            <i class="bx bx-download margin-r-2"></i>
            <span class="fw-normal fs-7">Template</span>
        </button>
        <div style="margin-left: 0 !important; margin-right: 0 !important;" class="dflex">
            <button class="btn btn-warning dflex align-center margin-r-2" type="button"
                id="btnCancelImportPo">
                <i class="bx bx-x margin-r-2"></i>
                <span class="fw-normal fs-7">Cancel</span>
            </button>
            <button class="btn btn-primary dflex align-center" type="submit" id="btnProcessImportPo">
                <i class="bx bx-check margin-r-2"></i>
                <span class="fw-normal fs-7">Process</span>
            </button>
        </div>
    </div>
</form>

<script>
    function downloadPoTemplate() {
        var url = '<?= base_url('/downloadable/Template PurchaseOrder.xlsx') ?>';
        window.location.href = url;
    }

    function handleImportProgressUpdate(progress, res) {
        let processed = res.processed || 0;
        let total = res.totalRows || $('#alltotals').text() || 0;
        $('#totalsent').text(processed);
        $('#alltotals').text(total);
    }

    function handleImportProgressError(pesan) {
        $('#btnProcessImportPo').removeAttr('disabled');
        $('#btnCancelImportPo').removeAttr('disabled');
        $('#po_excelfile').removeAttr('disabled');

        // tampilkan pesan error di teks processing
        $('#importStatusTextPo').text(pesan || 'Gagal memproses import');
    }

    function handleImportFinished(res) {
        $('#btnProcessImportPo').removeAttr('disabled');
        $('#btnCancelImportPo').removeAttr('disabled');
        $('#po_excelfile').removeAttr('disabled');

        $('#totalsent').text(res.totalRows || $('#alltotals').text());
        $('#alltotals').text(res.totalRows || $('#alltotals').text());

        if (res.undfhCount && res.undfhCount > 0) {
            showNotif('error', res.undfhCount + ' PO dilewatkan');
        }
        showNotif('success', 'Data updated successfully');

        setTimeout(function() {
            close_modal('modaldetail');
        }, 2000);
    }

    function handleImportCancelled(pesan) {
        // izinkan tutup modal via tombol
        $('#btnProcessImportPo').attr('disabled', 'disabled');
        $('#po_excelfile').removeAttr('disabled');

        $('#btnCancelImportPo').text('Close').off('click').on('click', function() {
            close_modal('modaldetail');
        });

        // ubah text processing, tanpa notif js
        $('#importStatusTextPo').text(pesan || 'Proses importnya dibatalkan');
    }

    $(document).ready(function() {
        // ESC: boleh menutup modal hanya jika tidak ada proses import yang berjalan
        $(document).off('keydown.importPo').on('keydown.importPo', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                // importRunning didefinisikan global di v_list.php
                if (typeof importRunning !== 'undefined' && importRunning) {
                    // ada proses import → jangan tutup modal
                    e.preventDefault();
                    return;
                }
                // tidak ada proses import → boleh tutup
                close_modal('modaldetail');
            }
        });

        $('#importPoExcel').off('submit').on('submit', function(e) {
            e.preventDefault();

            let fileInput = $('#po_excelfile')[0];
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Silakan pilih file Excel terlebih dahulu.');
                return false;
            }

            let formData = new FormData();
            formData.append('excelfile', fileInput.files[0]);
            formData.append('<?= csrf_token() ?>', decrypter($("#csrf_token").val()));

            let btn = $('#btnProcessImportPo');
            let oldHtml = btn.html();
            btn.attr('disabled', 'disabled').html('<i class="bx bx-loader bx-spin"></i>');
            $('#btnCancelImportPo').attr('disabled', 'disabled');
            $('#po_excelfile').attr('disabled', 'disabled');

            $.ajax({
                url: '<?= getURL("purchaseorder/startImport"); ?>',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    btn.html(oldHtml);
                    $('#btnCancelImportPo').removeAttr('disabled');

                    if (res.csrfToken) {
                        $("#csrf_token").val(encrypter(res.csrfToken));
                    }

                    if (res.sukses != 1) {
                        $('#po_excelfile').removeAttr('disabled');
                        alert(res.pesan || 'Gagal memulai import');
                        return;
                    }

                    // mulai proses import → kunci modal: tidak bisa ESC / klik luar
                    $('#modaldetail').modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    $('#loading-alltrans').removeClass('hiding');
                    $('#alltotals').text(res.totalRows || 0);
                    $('#totalsent').text(0);
                    $('#importStatusTextPo').text('Processing');

                    // tombol Cancel kini benar-benar cancel proses
                    $('#btnCancelImportPo').off('click').on('click', function() {
                        cancelImport();
                    });

                    // mulai loop chunk di backend
                    startImport(res.importId, res.totalRows);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    btn.html(oldHtml);
                    $('#btnProcessImportPo').removeAttr('disabled');
                    $('#btnCancelImportPo').removeAttr('disabled');
                    $('#po_excelfile').removeAttr('disabled');
                    alert('Terjadi error saat upload file: ' + thrownError);
                }
            });

            return false;
        });

        // default behaviour tombol Cancel sebelum proses mulai
        $('#btnCancelImportPo').off('click').on('click', function() {
            close_modal('modaldetail');
        });
    });
</script>