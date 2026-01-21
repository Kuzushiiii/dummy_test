<style>
    img[alt="side-avatar"] {
        margin-right: 0px !important;
        margin-bottom: 0px !important;
        width: 85px !important;
        height: 100% !important;
        border: 1px solid rgba(108, 108, 108, 0.35);
        border-radius: 5rem;
        padding: 2px;
    }

    @media (max-width: 768px) {
        .btn-logout {
            display: none !important;
        }
        
    }
</style>
<aside>
    <div class="sidebar">
        <div class="project-logo">
            <div class="dflex align-center justify-center" style="width: 100%;">
                <!-- <h5>Serenity Logo</h5> -->
                <div class="text-center" style="display:flex;flex-direction:column;align-items: center;">
                    <div style="width:max-content;height: max-content;position:relative;">
                        <img src="<?= getAvatar(getSession('userid')) ?>" class="side-avatar" alt="side-avatar" loading="lazy">
                        <div class="float-profile" style="position: absolute;left:-5px;top:0px;">
                            <!-- <button class="btn btn-warning dflex align-center" style="border-radius: 50% !important;" onclick="return toPage('<?= getURL('myprofile') ?>')">
                                <i class="bx bx-edit-alt float-icon fs-6set"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="side-name">
                        <span class="fw-semibold"><?= getSession('name') ?></span>
                    </div>
                    <div class="side-role">
                        <span class="fw-normal fs-7set text-dark"></span>
                    </div>
                </div>
            </div>
            <i class='bx bx-chevron-left side-toggle shrink lg'></i>
        </div>
        <div class="sidebar-nav">
            <a href='<?= getURL('user') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bx-user'></i>
                    <span class='fw-normal fs-7'>User</span>
                </div>
            </a>
            <a href='<?= getURL('product') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-package'></i>
                    <span class='fw-normal fs-7'>Product</span>
                </div>
            </a>
            <a href='<?= getURL('category') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-category'></i>
                    <span class='fw-normal fs-7'>Category</span>
                </div>
            </a>
            <a href='<?= getURL('project') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bx-git-branch'></i>
                    <span class='fw-normal fs-7'>Project</span>
                </div>
            </a>
            <a href='<?= getURL('document') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-file-blank'></i>
                    <span class='fw-normal fs-7'>Document</span>
                </div>
            </a>
            <a href='<?= getURL('supplier') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-building-house'></i>
                    <span class='fw-normal fs-7'>Supplier</span>
                </div>
            </a>
            <a href='<?= getURL('customer') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-buildings'></i>
                    <span class='fw-normal fs-7'>Customer</span>
                </div>
            </a>
            <a href='<?= getURL('purchaseorder') ?>' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='bx bxs-cart-download'></i>
                    <span class='fw-normal fs-7'>Purchase Order</span>
                </div>
        <button class=" btn btn-primary dflex align-center w-100 btn-logout" onclick="return toPage('<?= getURL('logout') ?>')" style="display: none;">
            <i class="bx bx-log-out margin-r-3"></i>
            <span class="fw-normal fs-7">Log Out</span>
        </button>
    </div>
</aside>