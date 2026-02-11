<form id="form-files" enctype="multipart/form-data" style="padding-inline:0;">
    <?php if ($form_type === 'edit'): ?>
        <input type="hidden" name="fileid" id="fileid" value="<?= encrypting($fileid) ?>">
    <?php endif; ?>

    <div class="form-group">
        <label class="required">Real Name</label>
        <input type="text" class="form-input fs-7" name="filerealname" id="filerealname"
            value="<?= esc($row['filerealname'] ?? '') ?>" placeholder="Masukan nama real file">
    </div>

    <div class="form-group">
        <label class="required">File Name</label>
        <input type="text" class="form-input fs-7" name="filename" id="filename"
            value="<?= esc($row['filename'] ?? '') ?>" placeholder="Nama file di server">
    </div>

    <!-- hidden field yang diisi dari Dropzone upload -->
    <input type="hidden" name="filedirectory" id="filedirectory"
        value="<?= esc($row['filedirectory'] ?? '') ?>">
    <input type="hidden" name="originalname" id="originalname" value="">

    <div class="form-group">
        <label>Upload File</label>
        <div class="dropzone" id="file-dropzone-modal">
            <?= csrf_field() ?>
            <div class="dz-message">Drop file here or click to upload</div>
        </div>
    </div>

    <div class="modal-footer" style="display:flex;justify-content:space-between;">
        <button type="button" class="btn btn-secondary" onclick="close_modal('modaldetail')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-save-file">
            <i class="bx bx-check margin-r-2"></i>
            <span class="fw-normal fs-7"><?= $form_type === 'edit' ? 'Update' : 'Save' ?></span>
        </button>
    </div>
</form>

<script>
    Dropzone.autoDiscover = false;

    let dzModal = null;

    $(document).ready(function() {
        dzModal = new Dropzone("#file-dropzone-modal", {
            url: '<?= getURL('files/upload') ?>',
            paramName: "file",
            maxFilesize: 10, // MB
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            init: function() {
                this.on("success", function(file, response) {
                    if (response.csrfToken) {
                        $("#csrf_token").val(encrypter(response.csrfToken));
                    }
                    if (response.sukses === 1) {
                        // isi hidden input dari response
                        $('#filename').val(response.filename);
                        $('#filedirectory').val(response.filedirectory);
                        $('#originalname').val(response.originalname);

                        // kalau real name masih kosong, isi otomatis
                        if (!$('#filerealname').val()) {
                            $('#filerealname').val(response.originalname);
                        }

                        showNotif('success', response.pesan || 'Upload berhasil');
                    } else {
                        showNotif('error', response.pesan || 'Upload gagal');
                    }
                });
                this.on("error", function(file, errorMessage) {
                    showNotif('error', errorMessage || 'Upload error');
                });
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        });

        // submit form simpan ke DB
        $('#form-files').off('submit').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#btn-save-file');
            btn.prop('disabled', true);

            const formData = $(this).serialize(); // semua input text + hidden

            $.ajax({
                url: '<?= getURL('files/save') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false);
                    if (res.csrfToken) {
                        $("#csrf_token").val(encrypter(res.csrfToken));
                    }

                    if (res.sukses === 1) {
                        showNotif('success', res.pesan || 'Berhasil disimpan');
                        close_modal('modaldetail');
                        if (window.filesTable) {
                            window.filesTable.ajax.reload(null, false);
                        }
                    } else {
                        showNotif('error', res.pesan || 'Gagal menyimpan');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    showError('Error: ' + (xhr.responseText || xhr.statusText));
                }
            });

            return false;
        });
    });
</script>