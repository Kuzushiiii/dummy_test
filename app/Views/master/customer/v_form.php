<form id="form-customer" style="padding-inline: 0px;" enctype="multipart/form-data">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="customerid" name="customerid" value="<?= $id ?>">
        <?php } ?>
        <label for="name">Foto Customer : </label>
        <input type="file" class="form-input fs-7" id="foto" name="foto" accept=".jpg,.jpeg,.png" <?= ($form_type == 'edit' ? '' : 'required') ?>>
    </div>
    <div class="form-group">
        <label class="required">Nama :</label>
        <input type="text" class="form-input fs-7" id="nama" name="nama" value="<?= (($form_type == 'edit') ? $row['customername'] : '') ?>" placeholder="Masukan Nama Customer" required>
    </div>
    <div class="form-group">
        <label class="required">Alamat :</label>
        <input type="text" class="form-input fs-7" id="alamat" name="alamat" value="<?= (($form_type == 'edit') ? $row['address'] : '') ?>" placeholder="Masukan Alamat Customer" required>
    </div>
    <div class="form-group">
        <label class="required">Telephone :</label>
        <input type="text" class="form-input fs-7" id="telepon" name="telepon" value="<?= (($form_type == 'edit') ? $row['phone'] : '') ?>" placeholder="Masukan Telephone Customer" required>
    </div>
    <div class="form-group">
        <label class="required">Email :</label>
        <input type="email" class="form-input fs-7" id="email" name="email" value="<?= (($form_type == 'edit') ? $row['email'] : '') ?>" placeholder="Masukan Email Customer" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div class="modal-footer">
        <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-customer')">
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
        $('#form-customer').trigger('submit');
    });

    $("#form-customer").on('submit', function(e) {
        e.preventDefault();
        let csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);
        let form_type = "<?= $form_type ?>";
        let link = "<?= getURL('customer/add') ?>";
        if (form_type == 'edit') {
            link = "<?= getURL('customer/update') ?>";
        }
        let data = new FormData(this); // Use FormData to send files as well.
        $.ajax({
            type: 'post',
            url: link,
            data: data,
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