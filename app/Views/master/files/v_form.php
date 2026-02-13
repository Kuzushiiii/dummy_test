<form id="form-files" enctype="multipart/form-data" style="padding-inline:0;">
    <?= csrf_field() ?>
    <?php if ($form_type === 'edit'): ?>
        <input type="hidden" name="fileid" id="fileid" value="<?= encrypting($fileid) ?>">

        <input type="hidden" name="filename" id="filename" value="">
        <input type="hidden" name="filedirectory" id="filedirectory" value="">
        <input type="hidden" name="originalname" id="originalname" value="">

        <div class="form-group">
            <label>Replace File (optional)</label>
            <div class="dropzone" id="file-dropzone-modal">
                <div class="dz-message">Drop file here or click to upload new file</div>
            </div>
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:6px;">
            <button type="button" class="btn btn-secondary" onclick="close_modal('modaldetail')">Close</button>
            <button type="submit" class="btn btn-primary" id="btn-save-file">
                <i class="bx bx-check margin-r-2"></i>
                <span class="fw-normal fs-7">Save</span>
            </button>
        </div>
    <?php else: ?>
        <!-- ADD MODE: hanya dropzone + tombol Upload -->
        <div class="form-group">
            <label>Upload File</label>
            <div class="dropzone" id="file-dropzone-modal">
                <div class="dz-message">Drop file here or click to upload</div>
            </div>

            <!-- progress bar chunk upload -->
            <div id="chunk-progress-wrapper" style="margin-top:10px; display:none;">
                <div class="progress" style="height: 20px; border-radius: 8px; overflow: hidden;">
                    <div
                        id="chunk-progress-bar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        role="progressbar"
                        style="width: 0%; transition: width 0.2s ease-in-out; border-radius: 8px;"
                        aria-valuenow="0"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        0%
                    </div>
                </div>
                <small id="chunk-progress-text" class="text-muted"></small>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:6px;">
            <button type="button" class="btn btn-secondary" id="btn-close-modal-add">Close</button>
            <button type="button" class="btn btn-danger" id="btn-cancel-upload" style="display:none;">
                <i class="bx bx-x margin-r-2"></i>
                <span class="fw-normal fs-7">Cancel</span>
            </button>
            <button type="button" class="btn btn-primary" id="btn-upload-files">
                <i class="bx bx-upload margin-r-2"></i>
                <span class="fw-normal fs-7">Upload</span>
            </button>
        </div>
    <?php endif; ?>
</form>

<script>
    Dropzone.autoDiscover = false;

    let dzModal = null;

    $(document).ready(function() {
        const formType = '<?= $form_type ?>';

        dzModal = new Dropzone("#file-dropzone-modal", {
            url: '<?= getURL('files/upload') ?>',
            paramName: "file",
            params: {
                form_type: formType
            },
            maxFilesize: 100,
            maxFiles: 2,
            uploadMultiple: false,
            parallelUploads: 1,
            autoProcessQueue: formType === 'add' ? false : true,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            previewsContainer: "#file-dropzone-modal",
            clickable: "#file-dropzone-modal",
            init: function() {
                const dz = this;

                if (formType === 'add') {
                    let isUploading = false;
                    let isCancelled = false;
                    let currentUploadId = null;

                    const modalSelector = '#modaldetail';

                    // lock modal dari ESC / klik backdrop saat upload
                    $(modalSelector)
                        .off('hide.bs.modal.filesUpload')
                        .on('hide.bs.modal.filesUpload', function(e) {
                            if (isUploading) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return false;
                            }
                        });

                    $('#btn-close-modal-add')
                        .off('click.filesUpload')
                        .on('click.filesUpload', function(e) {
                            if (isUploading) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                return false;
                            }
                            // kalau tidak sedang upload, boleh tutup modal
                            close_modal('modaldetail');
                        });

                    //handler agar ketika file di remove, proses chuknya akan di batalkan
                    dz.on("removedfile", function(file) {
                        if (isUploading && currentUploadId) {
                            isCancelled = true;
                            $('#btn-cancel-upload').prop('disabled', true);
                        }
                    });

                    //cegah tambah file baru ketika proses chunknya berjalan
                    dz.on("addedfile", function(file) {
                        if (isUploading) {
                            dz.removeFile(file);
                        } else {
                            const files = dz.getAcceptedFiles();
                            if (files.length > 1) {
                                while (files.length > 1) {
                                    dz.removeFile(files[0]);
                                }
                            }
                        }
                    });

                    async function uploadInChunks(file) {
                        const chunkSize = 2 * 1024 * 1024; // 2 MB
                        const totalSize = file.size;
                        const totalChunks = Math.ceil(totalSize / chunkSize);
                        const originalName = file.name;

                        // ukuran max 100 MB (frontend)
                        const maxSizeBytes = 100 * 1024 * 1024;
                        if (totalSize > maxSizeBytes) {
                            showNotif('error', 'Ukuran file maksimal 100 MB');
                            return;
                        }

                        isUploading = true;
                        isCancelled = false;
                        currentUploadId = 'upl_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

                        $('#file-dropzone-modal').css('pointer-events', 'none').addClass('dz-disabled');

                        $('#btn-upload-files').prop('disabled', true);
                        $('#btn-cancel-upload').show().prop('disabled', false);
                        $('#chunk-progress-wrapper').show();
                        updateProgress(0, totalSize, 0);

                        let uploadedBytesSoFar = 0;

                        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                            if (isCancelled) {
                                await cancelUploadOnServer(currentUploadId);
                                resetUploadUI();
                                showNotif('error', 'Upload dibatalkan');
                                return;
                            }

                            const start = chunkIndex * chunkSize;
                            const end = Math.min(start + chunkSize, totalSize);
                            const blob = file.slice(start, end);

                            const formData = new FormData();
                            formData.append('file', blob);
                            formData.append('uploadId', currentUploadId);
                            formData.append('chunkIndex', chunkIndex);
                            formData.append('totalChunks', totalChunks);
                            formData.append('originalName', originalName);
                            formData.append('totalSize', totalSize);
                            formData.append('form_type', formType);
                            formData.append('<?= csrf_token() ?>', decrypter($("#csrf_token").val()));

                            try {
                                const res = await fetch('<?= getURL('files/chunkUpload') ?>', {
                                    method: 'POST',
                                    body: formData
                                });

                                const json = await res.json();
                                if (json && json.csrfToken) {
                                    $("#csrf_token").val(encrypter(json.csrfToken));
                                }

                                if (!json || json.sukses !== 1) {
                                    resetUploadUI();
                                    showNotif('error', (json && json.pesan) ? json.pesan : 'Gagal upload chunk');
                                    return;
                                }

                                // update total uploaded bytes (akumulasi semua chunk yang sudah sukses)
                                uploadedBytesSoFar = end;
                                const percent = Math.round((uploadedBytesSoFar / totalSize) * 100);
                                updateProgress(percent, totalSize, uploadedBytesSoFar);

                                // trigger event bawaan Dropzone (kalau mau pakai styling tambahan)
                                dz.emit('uploadprogress', file, percent, uploadedBytesSoFar);
                                if (percent === 100) {
                                    dz.emit('success', file, json);
                                    dz.emit('complete', file);
                                }

                                // kalau last chunk dan sukses
                                if (json.isLastChunk) {
                                    resetUploadUI();
                                    showNotif('success', json.pesan || 'Upload selesai');

                                    // reload datatable utama
                                    if (window.filesTable && typeof window.filesTable.ajax !== 'undefined') {
                                        window.filesTable.ajax.reload(null, false);
                                    }

                                    dz.removeAllFiles(true);

                                    setTimeout(function() {
                                        close_modal('modaldetail');
                                    }, 1000);
                                    return;
                                }
                            } catch (e) {
                                resetUploadUI();
                                showNotif('error', 'Error saat upload chunk: ' + e.message);
                                return;
                            }
                        }

                        resetUploadUI();
                    }

                    function updateProgress(percent, totalSize, uploadedBytes) {
                        const $bar = $('#chunk-progress-bar');

                        $bar
                            .css('width', percent + '%')
                            .attr('aria-valuenow', percent)
                            .text(percent + '%');

                        const uploadedMB = (uploadedBytes / (1024 * 1024)).toFixed(2);
                        const totalMB = (totalSize / (1024 * 1024)).toFixed(2);
                        $('#chunk-progress-text').text(uploadedMB + ' MB / ' + totalMB + ' MB');

                        // kalau sudah 100%, matikan animasi gerak
                        if (percent >= 100) {
                            $bar.removeClass('progress-bar-animated');
                        } else {
                            $bar.addClass('progress-bar-animated');
                        }
                    }

                    function resetUploadUI() {
                        isUploading = false;
                        isCancelled = false;
                        currentUploadId = null;
                        $('#btn-upload-files').prop('disabled', false);
                        $('#btn-cancel-upload').hide().prop('disabled', false);
                        $('#chunk-progress-wrapper').hide();
                        $('#chunk-progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
                        $('#chunk-progress-text').text('');

                        $('#file-dropzone-modal').css('pointer-events', '').removeClass('dz-disabled');

                        const modalSelector = '#modaldetail';
                        // lepas lock ESC / backdrop
                        $(modalSelector).off('hide.bs.modal.filesUpload');
                    }

                    async function cancelUploadOnServer(uploadId) {
                        const fd = new FormData();
                        fd.append('uploadId', uploadId);
                        fd.append('<?= csrf_token() ?>', decrypter($("#csrf_token").val()));

                        try {
                            const res = await fetch('<?= getURL('files/cancelUpload') ?>', {
                                method: 'POST',
                                body: fd
                            });
                            const json = await res.json();
                            if (json && json.csrfToken) {
                                $("#csrf_token").val(encrypter(json.csrfToken));
                            }
                        } catch (e) {
                            // abaikan error cancel
                        }
                    }

                    //cegah klik di area dropzone ketika upload sedang berjalan
                    $('#file-dropzone-modal').off('click.filesUpload').on('click.filesUpload', function(e) {
                        if (isUploading) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return false;
                        }
                    });

                    $('#btn-upload-files').off('click').on('click', function() {
                        const files = dz.getAcceptedFiles();
                        if (!files || files.length === 0) {
                            showNotif('error', 'Silakan pilih file terlebih dahulu');
                            return;
                        }
                        if (isUploading) {
                            return;
                        }
                        uploadInChunks(files[0]);
                    });

                    $('#btn-cancel-upload').off('click').on('click', function() {
                        if (!isUploading || !currentUploadId) {
                            return;
                        }
                        $(this).prop('disabled', true);
                        isCancelled = true;
                    });

                } else {
                    // EDIT MODE: upload file baru jika dipilih, tapi tidak langsung insert ke DB
                    dz.on("success", function(file, response) {
                        if (response && response.csrfToken) {
                            $("#csrf_token").val(encrypter(response.csrfToken));
                        }
                        if (response && response.sukses === 1) {
                            // simpan info file baru di hidden input
                            $('#filename').val(response.filename);
                            $('#filedirectory').val(response.filedirectory);
                            $('#originalname').val(response.originalname);

                            // update juga field Real Name supaya ikut nama file baru
                            if (response.originalname) {
                                $('#filerealname').val(response.originalname);
                            }

                            showNotif('success', response.pesan || 'File baru diupload, klik Save untuk simpan');
                        } else {
                            showNotif('error', (response && response.pesan) ? response.pesan : 'Upload gagal');
                        }
                    });

                    dz.on("error", function(file, errorMessage) {
                        let msg = errorMessage;
                        if (typeof errorMessage === 'object' && errorMessage !== null && errorMessage.pesan) {
                            msg = errorMessage.pesan;
                        }
                        showNotif('error', msg || 'Upload error');
                    });

                    // batasi hanya 1 file
                    dz.on("maxfilesexceeded", function(file) {
                        dz.removeAllFiles();
                        dz.addFile(file);
                    });
                }
            }
        });

        if (formType === 'edit') {
            // submit form untuk update metadata (dan file kalau ada)
            $('#form-files').off('submit').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btn-save-file');
                btn.prop('disabled', true);

                $.ajax({
                    url: '<?= getURL('files/save') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        btn.prop('disabled', false);
                        if (res && res.csrfToken) {
                            $("#csrf_token").val(encrypter(res.csrfToken));
                        }

                        if (res && res.sukses === 1) {
                            showNotif('success', res.pesan || 'Data berhasil diupdate');
                            close_modal('modaldetail');
                            if (window.filesTable) {
                                window.filesTable.ajax.reload(null, false);
                            }
                        } else {
                            showNotif('error', (res && res.pesan) ? res.pesan : 'Gagal menyimpan perubahan');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        showNotif('error', xhr.responseText || xhr.statusText);
                    }
                });

                return false;
            });
        }
    });
</script>