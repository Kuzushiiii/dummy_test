<form id="form-supplier" class="form" style="padding-inline: 0px;" enctype="multipart/form-data">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="id" name="id" value="<?= (($form_type == 'edit') ? $userid : '') ?>">
        <?php } ?>
        <label for="suppliername">Supplier Name : </label>
        <input type="text" class="form-input fs-7" id="suppliername" name="suppliername"
            value="<?= (($form_type == 'edit') ? $row['suppliername'] : '') ?>" placeholder="@ex: PT. Supplier"
            required>
    </div>

    <div class="form-group">
        <label for="address">Address : </label>
        <input type="text" class="form-input fs-7" id="address" name="address"
            value="<?= (($form_type == 'edit') ? $row['address'] : '') ?>" placeholder="@ex: Supplier Address" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone : </label>
        <input type="text" class="form-input fs-7" id="phone" name="phone"
            value="<?= (($form_type == 'edit') ? $row['phone'] : '') ?>" placeholder="@ex: Phone Number" required>
    </div>
    <div class="form-group">
        <label for="email">Email : </label>
        <input type="email" class="form-input fs-7" id="email" name="email"
            value="<?= (($form_type == 'edit') ? $row['email'] : '') ?>" placeholder="@ex: example@gmail.com" required>
    </div>
    <div class="form-group">
        <label for="filepath">FilePath : </label>
        <input type="file" class="form-input fs-7" id="filepath" name="filepath"
            value="<?= (($form_type == 'edit') ? $row['filepath'] : '') ?>" required accept="image/*">
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" id="btn-reset"
            onclick="return resetForm('form-supplier')" <?= ($form_type == 'edit') ? 'disabled' : '' ?>>
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
    $(document).ready(function () {
        let form_type = '<?= $form_type ?>';
        if (form_type == 'edit') {
            $('#btn-reset').attr('disabled', true);
        }
        $('#btn-submit').click(function () {
            $('#form-supplier').trigger('submit');
        });
        $('#form-supplier').on('submit', function (e) {
            e.preventDefault();
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token").val(csrf);
            let form_type = '<?= $form_type ?>';
            let link = '<?= getURL('supplier/add') ?>';
            if (form_type == 'edit') {
                link = '<?= getURL('supplier/update') ?>';
            }
            let formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: link,
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    $("#csrf_token_form").val("");
                    let pesan = response.message;
                    let notif = 'success';
                    if (response.status != 1) {
                        notif = 'error';
                    }
                    if (response.message != undefined) {
                        pesan = response.message;
                    }
                    showNotif(notif, pesan);
                    if (response.status == 1) {
                        close_modal('modaldetail');
                        tbl.ajax.reload();
                    }
                    $('#btn-submit').on('click', function (e) {
                        $(this).attr('disabled', true);
                        setTimeout(function () {
                            $('#btn-submit').attr('disabled', false);
                        },
                            3000);
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    showError(thrownError + ", please contact administrator for the further");
                }
            });
            return false;
        });
    });
</script>