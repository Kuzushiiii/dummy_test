<form id="form-project" style="padding-inline: 0px;">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="id" name="id" value="<?= (($form_type == 'edit') ? $row['id'] : '') ?>">
        <?php } ?>
        <label for="projectname">Project Name:</label>
        <input type="text" class="form-input fs-7" id="projectname" name="projectname"
            value="<?= (($form_type == 'edit') ? $row['projectname'] : '') ?>" placeholder="Your project name" required>
    </div>
    <div class="form-group">
        <label class="required">Description:</label>
        <input type="text" class="form-input fs-7" id="description" name="description"
            value="<?= (($form_type == 'edit') ? htmlspecialchars($row['description'], ENT_QUOTES) : '') ?>"
            placeholder="Description..." required>
    </div>
    <div class="form-group">
        <label class="required">Start Date:</label>
        <input type="date" class="form-input fs-7" id="startdate" name="startdate" <?= (($form_type == 'edit') ? '' : 'required') ?> value="<?= (($form_type == 'edit') ? $row['startdate'] : '') ?>">
    </div>
    <div class="form-group">
        <label class="required">End Date:</label>
        <input type="date" class="form-input fs-7" id="enddate" name="enddate"
            value="<?= (($form_type == 'edit') ? $row['enddate'] : '') ?>" required>
    </div>
    <div class="form-group">
        <label class="required">Img:</label>
        <input type="file" class="form-input fs-7" accept=".jpg,.png,.jpeg" id="filepath" name="filepath" <?= (($form_type == 'edit') ? '' : 'required') ?> value="<?= (($form_type == 'edit') ? $row['filepath'] : '') ?>" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-project')">
            <i class="bx bx-revision margin-r-2"></i>
            <span class="fw-normal fs-7">Reset</span>
        </button>
        <button type="button" id="btn-submit" class="btn btn-primary dflex align-center">
            <i class="bx bx-check margin-r-2"></i>
            <span class="fw-normal fs-7"><?= ($form_type == 'edit' ? 'Update' : 'Save') ?></span>
        </button>
    </div>
</form>

<script>
    let isSubmitting = false;
    $(document).ready(function() {
        $(document).on('keydown', '#form-project input', function(e) {
            if (e.key === 'Enter' && isSubmitting) {
                e.preventDefault();
                return false;
            }
        });

        $('#btn-submit').off('click').on('click', function() {
            $('#form-project').trigger('submit');
        });

        
        $("#form-project").off('submit').on('submit', function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            isSubmitting = true;

            // Disable the submit button to prevent multiple submissions
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit span').text('Saving...');

            // Get CSRF token from form and decrypt it
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token_form").val(csrf);

            // Define the link for the add or update operation
            let form_type = "<?= $form_type ?>";
            let link = "<?= getURL('project/add') ?>";
            if (form_type == 'edit') {
                link = "<?= getURL('project/update') ?>";
            }

            // Serialize the form data
            let data = new FormData(this);

            // Perform AJAX request
            $.ajax({
                type: 'post',
                url: link,
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    isSubmitting = false;
                    // Re-enable the submit button
                    $('#btn-submit').prop('disabled', false);
                    $('#btn-submit span').text('<?= ($form_type == 'edit' ? 'Update' : 'Save') ?>');

                    // Update CSRF token after successful response
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    $("#csrf_token_form").val("");

                    let pesan = response.pesan;
                    let notif = 'success';

                    // Check if the operation was successful or not
                    if (response.sukses != 1) {
                        notif = 'error';
                    }
                    if (response.pesan != undefined) {
                        pesan = response.pesan;
                    }

                    // Show notification based on result
                    showNotif(notif, pesan);

                    // If success, close modal and reload table
                    if (response.sukses == 1) {
                        close_modal('modaldetail');
                        tbl.ajax.reload();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    isSubmitting = false;
                    // Re-enable the submit button on error
                    $('#btn-submit').prop('disabled', false);
                    $('#btn-submit span').text('<?= ($form_type == 'edit' ? 'Update' : 'Save') ?>');

                    // Handle AJAX request failure
                    showError(thrownError + ", please contact administrator for further assistance.");
                }
            });
            return false;
        });
    });
</script>
