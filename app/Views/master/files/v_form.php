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
        </div>

        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:6px;">
            <button type="button" class="btn btn-secondary" onclick="close_modal('modaldetail')">Close</button>
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
            maxFilesize: 20,
            maxFiles: 2,
            uploadMultiple: false,
            parallelUploads: 2,
            autoProcessQueue: formType === 'add' ? false : true,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            init: function() {
                const dz = this;

                if (formType === 'add') {
                    // tombol Upload untuk ADD
                    $('#btn-upload-files').off('click').on('click', function() {
                        if (dz.getQueuedFiles().length === 0) {
                            showNotif('error', 'Silakan pilih file terlebih dahulu');
                            return;
                        }
                        $(this).prop('disabled', true);
                        dz.processQueue();
                    });

                    dz.on("success", function(file, response) {
                        if (response && response.csrfToken) {
                            $("#csrf_token").val(encrypter(response.csrfToken));
                        }
                        if (response && response.sukses === 1) {
                            showNotif('success', response.pesan || 'Upload berhasil');
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

                    dz.on("queuecomplete", function() {
                        $('#btn-upload-files').prop('disabled', false);

                        // reload datatable utama
                        if (window.filesTable && typeof window.filesTable.ajax !== 'undefined') {
                            window.filesTable.ajax.reload(null, false);
                        }

                        // bersihkan file di dropzone dan tutup modal
                        dz.removeAllFiles(true);
                        close_modal('modaldetail');
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