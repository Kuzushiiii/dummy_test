<form id="edit-detail-form" data-original-productid="<?= $detail['productid'] ?>" data-original-uomid="<?= $detail['uomid'] ?>" data-original-qty="<?= $detail['qty'] ?>" data-original-price="<?= $detail['price'] ?>">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div class="form-group">
        <label class="required">Product</label>
        <select id="modal-productid" name="productId" class="form-input fs-7" required>
            <option value="">Pilih Product</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $detail['productid'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['text']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="required">UOM</label>
        <select id="modal-uomid" name="uomId" class="form-input fs-7" required>
            <option value="">Pilih UOM</option>
            <?php foreach ($uoms as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $detail['uomid'] == $u['id'] ? 'selected' : '' ?>><?= esc($u['text']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="required">Qty</label>
        <input type="number" id="modal-qty" name="qty" class="form-input fs-7" step="0.001" value="<?= $detail['qty'] ?>" required>
    </div>
    <div class="form-group">
        <label class="required">Price</label>
        <input type="number" id="modal-price" name="price" class="form-input fs-7" step="0.001" value="<?= $detail['price'] ?>" required>
    </div>
    <div class="form-group">
        <label>Total</label>
        <input type="number" id="modal-total" name="total" class="form-input fs-7" value="<?= $detail['qty'] * $detail['price'] ?>" readonly>
    </div>
</form>
<div class="modal-footer">
    <button type="button" id="reset-modal-detail" class="btn btn-warning">Reset</button>
    <button type="button" id="update-modal-detail" class="btn btn-primary">Update</button>
</div>