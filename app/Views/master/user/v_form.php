<form id="form-user" style="padding-inline: 0px;">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="password_lama" name="password_lama" value="<?= $row['password'] ?>">
            <input type="hidden" id="id" name="id" value="<?= (($form_type == 'edit') ? $userid : '') ?>">
            <input type="hidden" id="username_old" name="username_old" value="<?= (($form_type == 'edit') ? $row['username'] : '') ?>" required>
        <?php } ?>
        <label for="name">Name : </label>
        <input type="text" class="form-input fs-7" id="name" name="name" value="<?= (($form_type == 'edit') ? $row['fullname'] : '') ?>" placeholder="@ex: Admin Staff" required>
    </div>
    <div class="form-group">
        <label class="required">Username :</label>
        <input type="text" class="form-input fs-7" id="username" name="username" value="<?= (($form_type == 'edit') ? $row['username'] : '') ?>" placeholder="@ex: admin##" required>
    </div>
    <div class="form-group">
        <label class="required">Password :</label>
        <input type="password" class="form-input fs-7" id="password" name="password" <?= (($form_type == 'edit') ? '' : 'required') ?> placeholder="••••••••••••">
    </div>
    <div class="form-group">
        <label class="required">Email :</label>
        <input type="email" class="form-input fs-7" id="email" name="email" value="<?= (($form_type == 'edit') ? $row['email'] : '') ?>" placeholder="@ex: admin##" required>
    </div>
    <div class="form-group">
        <label class="required">Phone :</label>
        <input type="text" class="form-input fs-7" id="phone" name="phone" value="<?= (($form_type == 'edit') ? $row['telp'] : '') ?>" placeholder="@ex: admin##" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-user')">
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
        $('#btn-submit').click(function() {
            $('#form-user').trigger('submit');
        })
        $("#form-user").on('submit', function(e) {
            e.preventDefault();
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token_form").val(csrf);
            let form_type = "<?= $form_type ?>";
            let link = "<?= getURL('user/add') ?>"
            if (form_type == 'edit') {
                link = "<?= getURL('user/update') ?>"
            }
            let data = $(this).serialize();
            $.ajax({
                type: 'post',
                url: link,
                data: data,
                dataType: "json",
                success: function(response) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    $("#csrf_token_form").val("");
                    let pesan = response.pesan;
                    let notif = 'success'
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
                    showError(thrownError + ", please contact administrator for the further")
                }
            });
            return false;
        })
    })
</script>