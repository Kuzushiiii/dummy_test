<form id="form-product" enctype="multipart/form-data" style="padding-inline: 0px;">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="id" name="id" value="<?= (($form_type == 'edit') ? $row['id'] : '') ?>">
        <?php } ?>
        <label for="productname">Product Name:</label>
        <input type="text" class="form-input fs-7" id="productname" name="productname"
            value="<?= (($form_type == 'edit') ? $row['productname'] : '') ?>" placeholder="Your product name" required>
    </div>
    <div class="form-group">
        <label class="required">category:</label>
        <input type="text" class="form-input fs-7" id="category" name="category"
            value="<?= (($form_type == 'edit') ? htmlspecialchars($row['category'], ENT_QUOTES) : '') ?>"
            placeholder="Category..." required>
    </div>
    <div class="form-group">
        <label class="required">Price:</label>
        <input type="number" class="form-input fs-7" id="price" name="price" <?= (($form_type == 'edit') ? '' : 'required') ?> value="<?= (($form_type == 'edit') ? $row['price'] : '') ?>" placeholder="Price" required>
    </div>
    <div class="form-group">
        <label class="required">Stock:</label>
        <input type="number" class="form-input fs-7" id="stock" name="stock"
            value="<?= (($form_type == 'edit') ? $row['stock'] : '') ?>" placeholder="Stock" required>
    </div>
    <div class="form-group">
        <label class="required">Img:</label>
        <input type="file" class="form-input fs-7" accept=".jpg,.png,.jpeg" id="filepath" name="filepath" <?= (($form_type == 'edit') ? '' : 'required') ?> value="<?= (($form_type == 'edit') ? $row['filepath'] : '') ?>" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-product')">
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
    $(document).ready(function() {
        // Trigger form submission when the save button is clicked
        $('#btn-submit').click(function() {
            $('#form-product').trigger('submit');
        });

        // Submit form via AJAX when the form is submitted
        $("#form-product").on('submit', function(e) {
            e.preventDefault();

            // Get CSRF token from form and decrypt it
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token_form").val(csrf);

            // Define the link for the add or update operation
            let form_type = "<?= $form_type ?>";
            let link = "<?= getURL('product/add') ?>";
            if (form_type == 'edit') {
                link = "<?= getURL('product/update') ?>";
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
                    // Handle AJAX request failure
                    showError(thrownError + ", please contact administrator for further assistance.");
                }
            });
            return false;
        });
    });
</script>
