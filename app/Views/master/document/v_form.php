<form id="form-document" style="padding-inline: 0px;" enctype="multipart/form-data">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="id" name="id" value="<?= $userid ?>">
        <?php } ?>
        <label for="name">Document Name:</label>
        <input type="text" class="form-input fs-7" id="name" name="name" value="<?= $row['documentname'] ?? '' ?>" placeholder="Nama Dokumen" required>
    </div>
    <div class="form-group">
        <label for="dokumen">Masukan Dokumen:</label>
        <input type="file" class="form-input fs-7" id="dokumen" name="dokumen" accept=".doc,.docx,.pdf" <?= $form_type == 'edit' ? '' : 'required' ?>>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" class="form-input fs-7" id="description" name="description" value="<?= $row['description'] ?? '' ?>" placeholder="Masukan Deskripsi Dokumen" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="csrf_token">
    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-document')">
            <i class="bx bx-revision margin-r-2"></i>
            <span class="fw-normal fs-7">Reset</span>
        </button>
        <button type="button" id="btn-submit" class="btn btn-primary dflex align-center">
            <i class="bx bx-check margin-r-2"></i>
            <span class="fw-normal fs-7"><?= $form_type == 'edit' ? 'Update' : 'Upload' ?></span>
        </button> 
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#btn-submit').click(function() {
            $('#form-document').trigger('submit');
        });

        $("#form-document").on('submit', function(e) {
            e.preventDefault();

            // Set up CSRF token and endpoint URLs
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token_form").val(csrf);

            let form_type = "<?= $form_type ?>";
            let link = form_type === 'edit' ?
                "<?= getURL('document/update') ?>" :
                "<?= getURL('document/add') ?>";

            let formData = new FormData(this);

            // AJAX Request
            $.ajax({
                type: 'post',
                url: link,
                data: formData,
                dataType: "json",
                processData: false, // Don't process the files
                contentType: false, // Don't set content type
                success: function(response) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    $("#csrf_token_form").val("");
                    let pesan = response.pesan;
                    let notif = 'success';
                    if (response.sukses != 1) {
                        notif = 'error';
                    }
                    if (response.pesan != undefined) {
                        pesan = response.pesan;
                    }
                    showNotif(notif, pesan);
                    if (response.sukses == 1) {
                        close_modal('modaldetail');
                        tbl.ajax.reload();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    showError(thrownError + ", please contact administrator for further assistance.");
                }
            });

            return false;
        });
    });
</script>