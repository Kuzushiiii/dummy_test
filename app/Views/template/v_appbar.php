<div class="appbar sc-sm">
    <div class="row w-100 row-cd">
        <div class="col-2 dflex align-center back" onclick="window.history.go(-1);return false;">
            <i class="bx bx-left-arrow-alt bx-sm"></i>
        </div>
        <div class="col-<?= isset($search) ? ($title != 'Notification' ? '7' : '5') : '10' ?> dflex align-center titles">
            <span class="fw-semibold fs-6 text-primary"><?= $title ?></span>
        </div>
        <?php if (isset($search)) : ?>
            <div class="col-<?= ($title != 'Notification' ? '3' : '5') ?> form-s">
                <div class="dflex align-center justify-between">
                    <i class="bx bx-search icon-search <?= ($title != 'Notification' ? '' : 'd-none') ?>" onclick="return search(this, '<?= $search ?>', 'open')"></i>
                    <form id="form-search" style="display: <?= ($title != 'Notification' ? 'none' : 'block') ?>;">
                        <div class="form-group" style="margin-bottom: 0px !important;">
                            <div class="form-append">
                                <input type="text" name="search" id="search" class="form-input fs-7set <?= getURL('search') ?>" placeholder="Search . .">
                                <i class="bx bx-search form-append-trailing text-primary"></i>
                            </div>
                        </div>
                    </form>
                    <?php if ($title != 'Notification') : ?>
                        <button class="btn btn-primary dflex align-center" onclick="return toPage('<?= getURL($search . '/form') ?>')">
                            <i class="bx bx-plus"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>