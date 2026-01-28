<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection {
        width: 100% !important;
        height: auto !important;
        padding: 0.375rem 0.75rem !important;
        /* Match Bootstrap input padding */
        border: 1px solid #ced4da !important;
        /* Match Bootstrap border */
        border-radius: 0.375rem !important;
        /* Match Bootstrap radius */
    }

    .select2-container .select2-selection .select2-selection__rendered {
        padding: 0 !important;
        line-height: 1.5 !important;
    }
</style>
<form id="form-purchaseorder" enctype="multipart/form-data" style="margin-top: 67px;">
    <div class="form-group">
        <?php if ($form_type == 'edit') { ?>
            <input type="hidden" id="purchaseorderid" name="purchaseOrderId" value="<?= $id ?>">
        <?php } ?>
        <label class="required">Kode Transaksi :</label>
        <input type="text" class="form-input fs-7" id="transactionCode" name="transactionCode" value="<?= (($form_type == 'edit') ? $row['transcode'] : '') ?>" placeholder="Masukan Kode Transaksi" required>
    </div>
    <div class="form-group">
        <label class="required">Tanggal Transaksi :</label>
        <input type="date" class="form-input fs-7" id="transactionDate" name="transactionDate" value="<?= (($form_type == 'edit') ? $row['transdate'] : '') ?>" placeholder="Masukan Tanggal Transaksi" required>
    </div>
    <div class="form-group">
        <label>Supply Date :</label>
        <input type="date" class="form-input fs-7" id="supplyDate" name="supplyDate" value="<?= (($form_type == 'edit') ? $row['supplydate'] : '') ?>" placeholder="Masukan Tanggal Supply">
    </div>
    <div class="form-group">
        <label class="required">Supplier:</label>
        <select class="form-input fs-7" id="supplierid" name="supplierId" required>
            <option value="">-- Pilih Supplier --</option>
            <?php foreach ($suppliers as $sup) : ?>
                <option value="<?= $sup['id'] ?>" <?= ($form_type == 'edit' && $row['supplierid'] == $sup['id']) ? 'selected' : '' ?>>
                    <?= esc($sup['suppliername']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label class="required">Grand Total :</label>
        <input type="number" class="form-input fs-7" id="grandTotal" name="grandTotal" value="<?= (($form_type == 'edit') ? $row['grandtotal'] : '') ?>" readonly>
    </div>
    <div class="form-group">
        <label>Description :</label>
        <textarea class="form-input fs-7" id="description" name="description" placeholder="Masukan Deskripsi" rows="3"><?= (($form_type == 'edit') ? esc($row['description']) : '') ?></textarea>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
    <div class="modal-footer" style="display: flex; justify-content: space-between;">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('purchaseorder') ?>'">Kembali</button>
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-warning dflex align-center margin-r-3" onclick="return resetForm('form-purchaseorder')">
                <i class="bx bx-revision margin-r-3"></i>
                <span class="fw-normal fs-7">Reset</span>
            </button>
            <button type="button" id="btn-submit" class="btn btn-primary dflex align-center">
                <i class="bx bx-check margin-r-2"></i>
                <span class="fw-normal fs-7"><?= ($form_type == 'edit') ? 'Update' : 'Save' ?></span>
            </button>
        </div>
    </div>
</form>
<?php if ($form_type == 'edit') : ?>
    <div class="form-group mt-3" style="padding-right:10px ">
        <h5>Tambah Detail Purchase Order</h5>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="required">Product</label>
                    <select id="productid" name="productId" class="form-input fs-7">
                        <option value="">Pilih Product</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="required">UOM</label>
                    <select id="uomid" name="uomId" class="form-input fs-7">
                        <option value="">Pilih UOM</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="required">Qty</label>
                    <input type="number" id="qty" class="form-input fs-7" step="0.001" placeholder="Masukkan jumlah produk">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="required">Price</label>
                    <input type="number" id="price" class="form-input fs-7" step="0.001" placeholder="Masukkan harga produk">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Total</label>
                    <input type="number" id="total" class="form-input fs-7" readonly placeholder="Total">
                </div>
            </div>

            <div class="modal-footer" style="display:flex; align-items:center;">
                <button type="button" id="add-detail-btn" class="btn btn-primary dflex align-center margin-r-3">
                    <i class="bx bx-plus-circle margin-r-2"></i>
                    <span class="fw-normal fs-7">Add New</span>
                </button>
            </div>
        </div>
        <div class="mt-4">
            <h5>Purchase Order Details</h5>
            <table id="detailsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="tableheader" style="width: 18%;">Product</th>
                        <th class="tableheader">UOM</th>
                        <th class="tableheader">Qty</th>
                        <th class="tableheader">Price</th>
                        <th class="tableheader">Total</th>
                        <th class="tableheader">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    <?php endif ?>
    <?= $this->include('template/v_footer') ?>
    <script>
        /** ---------- Inisialisasi DataTable ---------- **/
        if ($.fn.DataTable.isDataTable('#detailsTable')) {
            $('#detailsTable').DataTable().destroy();
            $('#detailsTable').empty();
        }
        window.detailsTbl = $('#detailsTable').DataTable({
            serverSide: true,
            ajax: {
                url: "<?= getURL('purchaseorder/getdetailsajax') ?>",
                type: "POST",
                data: {
                    headerId: '<?= encrypting($id) ?>'
                }
            },
            columns: [{
                    data: 0,
                    title: "Product",
                    orderable: true
                },
                {
                    data: 1,
                    title: "UOM",
                    orderable: true
                },
                {
                    data: 2,
                    title: "Qty",
                    orderable: true
                },
                {
                    data: 3,
                    title: "Price",
                    orderable: true
                },
                {
                    data: 4,
                    title: "Total",
                    orderable: false
                },
                {
                    data: 5,
                    title: "Actions",
                    orderable: false
                }
            ],
            order: [
                [0, 'asc']
            ],
            searching: true,
            paging: true,
            lengthMenu: [5, 10, 25],
            info: true,
            language: {
                search: "Search details:"
            },

        });

        generateSelect2('#supplierid', '#form-purchaseorder', '<?= base_url('purchaseorder/getsuppliers') ?>', 'Pilih Supplier');
        generateSelect2('#productid', 'body', '<?= base_url('purchaseorder/getproducts') ?>', 'Pilih Product'); 
        generateSelect2('#uomid', 'body', '<?= base_url('purchaseorder/getuoms') ?>', 'Pilih UOM');

        function ensureSelectOption($select, id, text) {
            if ($select.find("option[value='" + id + "']").length === 0) {
                const opt = new Option(text || id, id, true, true);
                $select.append(opt).trigger('change.select2');
            }
        }

        function calculateTotal() {
            const qty = parseFloat($('#qty').val()) || 0;
            const price = parseFloat($('#price').val()) || 0;
            $('#total').val(qty * price);
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            $('#detailsTable tbody tr').each(function() {
                const totalText = $(this).find('td').eq(4).text();
                const normalized = totalText.replace(/\./g, '').replace(',', '.');
                const total = parseFloat(normalized) || 0;
                grandTotal += total;
            });
            $('#grandTotal').val(grandTotal.toFixed(2));
        }
        $('#qty, #price').on('input', calculateTotal);

        /** ---------- fungsi edit dipanggil dari action column ---------- **/
        function editDetail(id, productId, uomId, qty, price, productName = '', uomName = '') {
            $.get('<?= getURL('purchaseorder/editDetailModal') ?>/' + id, function(html) {
                $('#modaldetail-form').html(html);
                $('#modaldetail-title').text('Edit Detail');
                $('#modaldetail').modal('show');
                // Initialize select2 and events
                generateSelect2('#modal-productid', '#edit-detail-form', '<?= base_url('purchaseorder/getproducts') ?>', 'Pilih Product');
                generateSelect2('#modal-uomid', '#edit-detail-form', '<?= base_url('purchaseorder/getuoms') ?>', 'Pilih UOM');
                $('#modal-qty, #modal-price').on('input', function() {
                    const qty = parseFloat($('#modal-qty').val()) || 0;
                    const price = parseFloat($('#modal-price').val()) || 0;
                    $('#modal-total').val(qty * price);
                });
                // Reset function
                function resetModalDetailForm() {
                    var form = $('#edit-detail-form');
                    $('#modal-productid').val(form.data('original-productid')).trigger('change');
                    $('#modal-uomid').val(form.data('original-uomid')).trigger('change');
                    $('#modal-qty').val(form.data('original-qty'));
                    $('#modal-price').val(form.data('original-price'));
                    $('#modal-total').val(form.data('original-qty') * form.data('original-price'));
                }
                $('#reset-modal-detail').on('click', resetModalDetailForm);
                $('#update-modal-detail').off('click').on('click', function() {
                    var formData = new FormData(document.getElementById('edit-detail-form'));
                    formData.append('headerId', '<?= encrypting($id) ?>');
                    formData.append('id', id);
                    $.ajax({
                        url: '<?= getURL('purchaseorder/updatedetail') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(updateRes) {
                            if (updateRes.sukses == 1) {
                                $('#modaldetail').modal('hide');
                                detailsTbl.ajax.reload(null, false);
                                if (updateRes.grandtotal !== undefined) {
                                    $('#grandTotal').val(parseFloat(updateRes.grandtotal).toFixed(2));
                                }
                                showNotif('success', updateRes.pesan || 'Detail updated');
                            } else {
                                showNotif('error', updateRes.pesan || 'Error updating detail');
                            }
                        },
                        error: function(xhr) {
                            showNotif('error', 'Error: ' + xhr.responseText);
                        }
                    });
                });
            }, 'html').fail(function(xhr) {
                showNotif('error', 'Error loading modal: ' + xhr.statusText);
            });
        }

        /** ---------- reset form detail ---------- **/
        function resetDetailForm() {
            $('#productid').val('').trigger('change');
            $('#uomid').val('').trigger('change');
            $('#qty').val('');
            $('#price').val('');
            $('#total').val('');
        }

        /** ---------- tombol reset ---------- **/
        $('#reset-detail-btn').on('click', resetDetailForm);

        /** ---------- add detail ---------- **/
        $('#add-detail-btn').off('click').on('click', function() {
            $(this).prop('disabled', true);
            const productId = $('#productid').val();
            const uomId = $('#uomid').val();
            const qty = $('#qty').val();
            const price = $('#price').val();

            if (!productId || !uomId || !qty || !price) {
                showNotif('error', 'Lengkapi semua data detail (Product, UOM, Qty, Price)');
                $(this).prop('disabled', false);
                return;
            }

            const payload = {
                headerId: '<?= encrypting($id) ?>',
                productId: productId,
                uomId: uomId,
                qty: qty,
                price: price
            };

            $.post('<?= getURL('purchaseorder/adddetail') ?>', payload, function(res) {
                $('#add-detail-btn').prop('disabled', false);
                if (res.sukses == 1) {
                    resetDetailForm();
                    detailsTbl.ajax.reload(null, false);
                    // Reload header table (purchase order list) with delay
                    setTimeout(function() {
                        if (window.purchaseOrderTable) {
                            window.purchaseOrderTable.ajax.reload(null, false);
                        }
                    }, 120);
                    if (res.grandtotal !== undefined) {
                        $('#grandTotal').val(parseFloat(res.grandtotal).toFixed(2));
                    }
                    if (res.csrfToken) {
                        $('#csrf_token').val(encrypter(res.csrfToken));
                    }
                    showNotif('success', res.pesan || 'Detail added');
                } else {
                    showNotif('error', res.pesan || 'Terjadi kesalahan');
                }
            }, 'json').fail(function(xhr) {
                $('#add-detail-btn').prop('disabled', false);
                showError('Error: ' + (xhr.responseText || xhr.statusText));
            });
        });

        /** ---------- Handle Header Form Submission ---------- **/
        $('#btn-submit').click(function() {
            $('#form-purchaseorder').trigger('submit');
        });

        $('#form-purchaseorder').on('submit', function(e) {
            e.preventDefault();
            let csrf = decrypter($("#csrf_token").val());
            $("#csrf_token").val(csrf);
            let form_type = '<?= $form_type ?>';
            let link = '<?= getURL('purchaseorder/add') ?>';
            if (form_type == 'edit') {
                link = '<?= getURL('purchaseorder/update') ?>';
            }
            let formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: link,
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    $("#csrf_token_form").val("");
                    let pesan = response.pesan;
                    let notif = 'success';
                    if (response.sukses != '1') {
                        notif = 'error';
                    }
                    showNotif(notif, pesan);
                     if (response.sukses == '1') {
                         close_modal('modaldetail');
                         if ($('#purchaseorderid').val()) {
                             // edit
                             window.location.href = '<?= base_url('purchaseorder') ?>';
                         } else {
                             // add
                             window.location.href = '<?= base_url('purchaseorder/form/') ?>' + response.newId;
                         }
                         if (window.purchaseOrderTable) {
                             window.purchaseOrderTable.ajax.reload(null, false);
                         }
                     }
                    $('#btn-submit').prop('disabled', false);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    $('#btn-submit').prop('disabled', false);
                    showError('Error: ' + (xhr.responseText || xhr.statusText));
                }
            });
            $('#btn-submit').prop('disabled', true);
        });
    </script>