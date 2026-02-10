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

    <!-- PROGRESS AREA -->
    <div id="import-progress-wrap" class="hiding import-progress-wrap">
        <div class="mb-1">
            <div class="progress import-progress">
                <div id="importProgressBarPo"
                    class="progress-bar import-progress-bar"
                    role="progressbar"
                    aria-valuenow="0"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width:0%;">
                    0%
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center import-progress-info">
            <div class="small text-muted" id="importStatusTextPo">
                Menyiapkan data...
            </div>
            <div class="small">
                <span class="text-primary fw-semibold" id="importProcessedPo">0</span>
                <span class="text-muted">/</span>
                <span class="text-primary fw-semibold" id="importTotalPo">0</span>
            </div>
        </div>
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

    // ===== ANIMASI TITIK-TITIK UNTUK STATUS IMPORT PO =====
    let statusPoInterval = null;
    let statusPoBaseText = '';

    function startStatusAnimationPo(baseText) {
        statusPoBaseText = baseText || '';
        stopStatusAnimationPo(); // pastikan tidak dobel
        let dots = 0;
        statusPoInterval = setInterval(function() {
            dots = (dots + 1) % 4; // 0–3 titik
            $('#importStatusTextPo').text(statusPoBaseText + '.'.repeat(dots));
        }, 400);
    }

    function stopStatusAnimationPo(finalText) {
        if (statusPoInterval) {
            clearInterval(statusPoInterval);
            statusPoInterval = null;
        }
        if (finalText !== undefined) {
            $('#importStatusTextPo').text(finalText);
        }
    }
    // =====================================================

    // simpan progress import saat ini (untuk animasi)
    let currentImportUiProgress = 0;
    let importProgressTimer = null;

    // animasikan progress bar import dari nilai sekarang ke targetPercent
    function animateImportProgress(targetPercent, baseText) {
        targetPercent = Math.min(100, Math.max(0, targetPercent));

        if (targetPercent <= currentImportUiProgress) {
            // kalau target <= current, cukup update teks status saja
            if (baseText) {
                startStatusAnimationPo(baseText);
            }
            return;
        }

        if (importProgressTimer) {
            clearInterval(importProgressTimer);
            importProgressTimer = null;
        }

        const step = 1; // naik 1% per tick
        const interval = 20; // 20ms per tick ~ 0.5s utk naik 25%

        importProgressTimer = setInterval(function() {
            currentImportUiProgress += step;
            if (currentImportUiProgress >= targetPercent) {
                currentImportUiProgress = targetPercent;
                clearInterval(importProgressTimer);
                importProgressTimer = null;
            }

            $('#importProgressBarPo')
                .css('width', currentImportUiProgress + '%')
                .attr('aria-valuenow', currentImportUiProgress)
                .text(currentImportUiProgress + '%');

            if (baseText) {
                startStatusAnimationPo(baseText);
            }
        }, interval);
    }

    function updateImportUiProgressPo(percent, processed, total, text, withAnimation = false) {
        if (withAnimation) {
            // pakai animasi halus dari currentImportUiProgress ke percent
            animateImportProgress(percent, text);
        } else {
            // update langsung tanpa animasi
            if (importProgressTimer) {
                clearInterval(importProgressTimer);
                importProgressTimer = null;
            }
            currentImportUiProgress = percent;
            $('#importProgressBarPo')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent)
                .text(percent + '%');

            if (text) {
                stopStatusAnimationPo();
                $('#importStatusTextPo').text(text);
            }
        }

        if (processed != null) {
            $('#importProcessedPo').text(processed);
        }
        if (total != null) {
            $('#importTotalPo').text(total);
        }
    }

    // callback yang dipanggil dari v_list.js
    function handleImportProgressUpdate(progress, res) {
        let processed = res.processed || 0;
        let total = res.totalRows || $('#importTotalPo').text() || 0;
        // selama proses berjalan, pakai animasi titik-titik
        updateImportUiProgressPo(
            progress,
            processed,
            total,
            `Memproses ${progress}%`,
            true
        );
    }

    function handleImportProgressError(pesan) {
        $('#btnProcessImportPo').removeAttr('disabled');
        $('#btnCancelImportPo').removeAttr('disabled');
        $('#po_excelfile').removeAttr('disabled');

        if (importProgressTimer) {
            clearInterval(importProgressTimer);
            importProgressTimer = null;
        }
        // stop animasi dan tampilkan pesan error final
        stopStatusAnimationPo(pesan);
    }

    function handleImportFinished(res) {
        // stop animasi dulu
        if (importProgressTimer) {
            clearInterval(importProgressTimer);
            importProgressTimer = null;
        }
        stopStatusAnimationPo();
        updateImportUiProgressPo(
            100,
            res.totalRows || $('#importTotalPo').text(),
            res.totalRows || $('#importTotalPo').text(),
            'Import selesai.'
        );

        $('#btnProcessImportPo').removeAttr('disabled');
        $('#btnCancelImportPo').removeAttr('disabled');
        $('#po_excelfile').removeAttr('disabled');

        if (res.undfhCount && res.undfhCount > 0) {
            $('#importStatusTextPo').text(
                'Import selesai. ' + res.undfhCount + ' baris dilewatkan.'
            );
        }

        // tutup modal otomatis setelah sedikit delay
        setTimeout(function() {
            close_modal('modaldetail');
        }, 2000);
    }

    function handleImportCancelled(pesan) {
        // stop animasi dan update UI saat import dibatalkan
        if (importProgressTimer) {
            clearInterval(importProgressTimer);
            importProgressTimer = null;
        }
        stopStatusAnimationPo();
        updateImportUiProgressPo(
            0,
            $('#importProcessedPo').text(),
            $('#importTotalPo').text(),
            pesan || 'Import dibatalkan.'
        );

        // setelah dibatalkan, proses tidak boleh dijalankan lagi
        $('#btnProcessImportPo').attr('disabled', 'disabled');

        // tombol Cancel menjadi Close saja
        $('#btnCancelImportPo').text('Close').off('click').on('click', function() {
            close_modal('modaldetail');
        });

        // file input boleh diubah lagi (opsional, tapi proses tidak bisa dijalankan)
        $('#po_excelfile').removeAttr('disabled');
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

                    // tampilkan progress di modal yang sama
                    $('#import-progress-wrap').removeClass('hiding');
                    $('#importTotalPo').text(res.totalRows || 0);
                    $('#importProcessedPo').text(0);
                    currentImportUiProgress = 0;
                    updateImportUiProgressPo(0, 0, res.totalRows, 'Mulai memproses...');

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