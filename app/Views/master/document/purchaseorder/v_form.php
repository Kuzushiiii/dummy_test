<style>
    #modaldetail .modal-body {
        max-height: 70vh;
        overflow: scroll;
        overflow-x: hidden;
    }

    #modaldetail .modal-content {
        overflow: visible;
    }

    #modaldetail {
        overflow: visible;
    }

    .select2-dropdown-custom {
        z-index: 1050;
    }
</style>
<form id="form-purchaseorder" enctype="multipart/form-data" method="post" action="<?= ($form_type == 'edit' ? getURL('purchaseorder/update') : getURL('purchaseorder/add')) ?>" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
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

        <div class="modal-footer">
            <button type="button" class="btn btn-warning dflex align-center" onclick="return resetForm('form-purchaseorder')">
                <i class="bx bx-revision margin-r-2"></i>
                <span class="fw-normal fs-7">Reset</span>
            </button>
            <button type="submit" id="btn-submit" class="btn btn-primary dflex align-center">
                <i class="bx bx-check margin-r-2"></i>
                <span class="fw-normal fs-7"><?= ($form_type == 'edit' ? 'Update' : 'Save') ?></span>
            </button>
        </div>
</form>

<?php if ($form_type == 'edit') : ?>
    <div class="form-group mt-3">
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
                <div class="row" style="display:flex; align-items:center;">
                    <button type="button" id="cancel-edit-btn" class="btn btn-secondary dflex align-center margin-r-3" style="display: none; margin-right:100px; background-color:#15432B;">Kembali ke Tambah</button>
                    <button type="button" id="reset-detail-btn" class="btn btn-warning dflex align-center margin-r-3" style="display: none;">
                        <i class="bx bx-revision margin-r-2"></i>
                        <span class="fw-normal fs-7">Reset</span>
                    </button>
                    <button type="button" id="add-detail-btn" class="btn btn-primary dflex align-center margin-r-3">
                        <i class="bx bx-plus-circle margin-r-2"></i>
                        <span class="fw-normal fs-7">Add New</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    </div>
    <div class="mt-4">
        <h5>Purchase Order Details</h5>
        <table id="detailsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
<?php endif ?>


<script>
    $('#btn-submit').off('click').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true);

        $.post($('#form-purchaseorder').attr('action'), $('#form-purchaseorder').serialize(), function(res) {
            $btn.prop('disabled', false);
            if (res.sukses == 1) {
                // tutup modal (ubah '#modalAdd' sesuai id modalmu)
                $('#modalAdd').modal('hide');

                // reload DataTable header. Pastikan nama variabel datatable header sama
                if (typeof purchaseOrderTable !== 'undefined') {
                    purchaseOrderTable.ajax.reload(null, false);
                } else {
                    // fallback: reload halaman kalau datatable header tidak tersedia
                    window.location.reload();
                }

                if (res.csrfToken) $('#csrf_token_form').val(res.csrfToken);
                showNotif('success', res.pesan || 'Tersimpan');
            } else {
                showNotif('error', res.pesan || 'Gagal menyimpan');
            }
        }, 'json').fail(function(xhr) {
            $btn.prop('disabled', false);
            showError('Error: ' + (xhr.responseText || xhr.statusText));
        });
    });

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
        searching: true,
        paging: true,
        lengthMenu: [5, 10, 25],
        info: true,
        language: {
            search: "Search details:"
        },
        drawCallback: function() {
            calculateGrandTotal();
        }
    });

    generateSelect2('#supplierid', '#form-purchaseorder', '<?= getURL('purchaseorder/getsuppliers') ?>', 'Pilih Supplier');
    generateSelect2('#productid', '#modaldetail', '<?= getURL('purchaseorder/getproducts') ?>', 'Pilih Product');
    generateSelect2('#uomid', '#modaldetail', '<?= getURL('purchaseorder/getuoms') ?>', 'Pilih UOM');

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
    function editDetail(id, productId, uomId, qty, price, productName = '') {
        console.log('editDetail called with id:', id);

        try {
            // pastikan select2 punya option agar menampilkan label. Jika generateSelect2 load async,
            ensureSelectOption($('#productid'), productId, productName);
            ensureSelectOption($('#uomid'), uomId, '');

            $('#productid').val(productId).trigger('change');
            $('#uomid').val(uomId).trigger('change');
            $('#qty').val(qty);
            $('#price').val(price);
            calculateTotal();

            // ubah tombol jadi update dan tampilkan tombol batal/reset
            $('#add-detail-btn').text('Update').removeClass('btn-primary').addClass('btn-warning').prepend('<i class="bx bx-check me-1 margin-r-2"></i>').data('detail-id', id);
            $('#reset-detail-btn').show();
            $('#cancel-edit-btn').text('Batal Edit').show();
        } catch (error) {
            console.error('Error in editDetail:', error);
        }
    }

    /** ---------- reset form detail ---------- **/
    function resetDetailForm() {
        $('#productid').val('').trigger('change');
        $('#uomid').val('').trigger('change');
        $('#qty').val('');
        $('#price').val('');
        $('#total').val('');
        $('#add-detail-btn').text('Add New').removeClass('btn-warning').addClass('btn-primary').removeData('detail-id');
        $('#reset-detail-btn').hide();
        $('#cancel-edit-btn').text('Kembali ke Tambah').hide();
    }

    /** ---------- tombol reset & batal ---------- **/
    $('#reset-detail-btn').on('click', resetDetailForm);
    $('#cancel-edit-btn').on('click', function() {
        // Cancel edit: kembali ke mode add tanpa reset form
        $('#add-detail-btn').text('Add New').removeClass('btn-warning').addClass('btn-primary').find('i').removeClass('bx-check').addClass('bx-plus-circle').removeData('detail-id');
        $('#reset-detail-btn').hide();
        $('#cancel-edit-btn').text('Kembali ke Tambah').hide();
        $(resetDetailForm);
    });

    /** ---------- single click handler Add / Update ---------- **/
    $('#add-detail-btn').off('click').on('click', function() {
        const detailId = $(this).data('detail-id');
        console.log('detailId from data:', detailId);
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

        const url = detailId ? '<?= getURL('purchaseorder/updatedetail') ?>' : '<?= getURL('purchaseorder/adddetail') ?>'

        const payload = {
            headerId: '<?= encrypting($id) ?>',
            productId: productId,
            uomId: uomId,
            qty: qty,
            price: price
        };

        console.log('URL:', url);

        if (detailId) {
            payload.id = detailId;
        }

        console.log('Payload:', payload);

        $.post(url, payload, function(res) {
            console.log('Response:', res);
            $('#add-detail-btn').prop('disabled', false);
            if (res.sukses == 1) {
                resetDetailForm();
                detailsTbl.ajax.reload(function() {
                    calculateGrandTotal();
                }, false);
                if (res.csrfToken) {
                    $('#csrf_token').val(encrypter(res.csrfToken));
                }
                showNotif('success', res.pesan || (detailId ? 'Detail updated' : 'Detail added'));
            } else {
                showNotif('error', res.pesan || 'Terjadi kesalahan');
            }
        }, 'json').fail(function(xhr) {
            console.log('Fail response:', xhr.responseText);
            $('#add-detail-btn').prop('disabled', false);
            showError('Error: ' + (xhr.responseText || xhr.statusText));
        });
    });
</script>