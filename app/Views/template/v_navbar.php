<style>
    .badge-notif {
        position: absolute;
        top: -24%;
        bottom: 38%;
        left: 50%;
        font-size: 8.8px;
        padding: 5px;
        border-radius: 5rem;
        background-color: rgba(237, 41, 57);
        color: #fff;
        text-align: center;
    }

    .dps {
        width: 100%;
        background-color: rgba(255, 255, 255);
        padding-inline: 5px;
        padding-block: 6px;
        border-radius: 5px;
        margin-top: 10px;
    }

    .dps .dropdown-content.company {
        left: 0;
    }
</style>
<nav class="navbar sc-sm">
    <div class="navbar-head <?= ($title == 'Job To Do' ? 'linear-grad' : '') ?>">
        <div class="row">
            <div class="col-12 dflex align-center justify-between">
                <div class="dflex align-center">
                    <i class='bx bx-menu-alt-left margin-r-3 side-toggle slide'></i>
                    <h5><?= ($title == 'Job To Do' ? 'Dashboard' : $title) ?></h5>
                </div>
                <div class="bells">
                    <i class="bx bxs-bell" onclick="return toPage('/notif')"></i>
                    <span class="bg-warning">6</span>
                </div>
                <div class="dps">
                    <a href="javascript:void(0);" class="nav-icon">
                        <div class="dropdown" style="position: relative;">
                            <div class="dflex align-center">
                                <i class="bx bx-building margin-r-2"></i>
                                <span class="fw-semibold fs-7 text-dark"><?= getSession('companyname') ?></span>
                            </div>
                            <style>
                                .dropdown-content.company .dropdown-item {
                                    align-items: start !important;
                                }
                            </style>
                            <div class="dropdown-content company" style="width: 250px">
                                <div class="notif" style="max-height: 250px; overflow: auto;" id="list-company-hp">

                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php if ($title == 'Job To Do') : ?>
                <div class="col-12 margin-t-2">
                    <div class="row card-dt">
                        <div class="col-12">
                            <div class="card rounded bg-white w-100" style="padding: 0px;">
                                <div class="card bg-primary-40 w-100 p-x p-y-3 dflex align-center justify-center">
                                    <div class="dflex align-center">
                                        <img src="<?= getURL('/images/dashboard/new.png') ?>" width="60" alt="">
                                        <div class="row text-primary" style="margin-left: 20px;">
                                            <span class="fw-semibold fs-5">New Order</span>
                                            <span class="fw-semibold fs-6">6</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card rounded bg-white w-100" style="padding: 0px;">
                                <div class="card bg-violet-40 w-100">
                                    <div class="row text-violet">
                                        <div class="col-12 dflex justify-center">
                                            <img src="<?= getURL('/images/dashboard/process.png') ?>" width="45" alt="">
                                        </div>
                                        <div class="col-12">
                                            <div class="row text-center">
                                                <span class="fw-semibold fs-6">On Process</span>
                                                <span class="fw-normal fs-6">2</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card rounded bg-white w-100" style="padding: 0px;">
                                <div class="card bg-warning-40 w-100">
                                    <div class="row text-warning">
                                        <div class="col-12 dflex justify-center">
                                            <img src="<?= getURL('/images/dashboard/delivery.png') ?>" width="45" alt="">
                                        </div>
                                        <div class="col-12">
                                            <div class="row text-center">
                                                <span class="fw-semibold fs-6">Delivery</span>
                                                <span class="fw-normal fs-6">12</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card rounded bg-white w-100" style="padding: 0px;">
                                <div class="card bg-success-40 w-100">
                                    <div class="row text-success">
                                        <div class="col-12 dflex justify-center">
                                            <img src="<?= getURL('/images/dashboard/delivered.png') ?>" width="45" alt="">
                                        </div>
                                        <div class="col-12">
                                            <div class="row text-center">
                                                <span class="fw-semibold fs-6">Delivered</span>
                                                <span class="fw-normal fs-6">8</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card rounded bg-white w-100" style="padding: 0px;">
                                <div class="card bg-danger-40 w-100">
                                    <div class="row text-danger">
                                        <div class="col-12 dflex justify-center">
                                            <img src="<?= getURL(' /images/dashboard/cancel.png') ?>" width="45" alt="">
                                        </div>
                                        <div class="col-12">
                                            <div class="row text-center">
                                                <span class="fw-semibold fs-6">Cancel</span>
                                                <span class="fw-normal fs-6">4</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<nav class="navbar sc-lg">
    <i class="bx bx-chevron-right side-toggle slide"></i>
    <div class="nav-item dflex align-center justify-between p-x">
        <?php if (empty($nobc)) : ?>
            <div class="row bc">
                <h5 class="fw-semibold fs-5"><?= $section ?></h5>
                <!-- <span class="fw-normal fs-7 text-dark">Welcome Back</span> -->
                <div class="breadcrumb">
                    <?php foreach ($breadcrumb as $bread) : ?>
                        <?php for ($a = 0; $a < count($bread); $a++) : ?>
                            <span class="breadcrumb-item"><?= $bread[$a] ?></span>
                        <?php endfor; ?>
                    <?php
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="dflex align-center m-item-3 right" style="<?= (!empty($nobc) ? 'padding-block: 10px;width:100% !important;' : '') ?>">
            <a href="javascript:void(0);" class="nav-icon">
                <div class="dropdown" style="position: relative;">
                    <div class="dflex align-center w-100">
                        <i class="bx bx-building margin-r-2"></i>
                        <span class="fw-semibold fs-7 text-dark"><?= getSession('companyname') ?></span>
                    </div>
                    <style>
                        .dropdown-content.company .dropdown-item {
                            align-items: start !important;
                        }
                    </style>
                    <div class="dropdown-content company" style="width: max-content">
                        <div class="notif" style="max-height: 250px; overflow: auto;" id="list-company">

                        </div>
                    </div>
                </div>
            </a>
            <a href="javascript:void(0);" class="nav-icon">
                <div class="dropdown" style="position: relative;">
                    <i class="bx bx-bell text-secondary fs-4"></i>
                    <span class="badge-notif">0</span>
                    <style>
                        .dropdown-content.bell .dropdown-item {
                            align-items: start !important;
                            display: flex !important;
                        }

                        @media (max-width: 768px) {
                            .dropdown-content.bell .dropdown-item {
                                display: flex !important;
                                align-items: start !important;
                            }
                        }
                    </style>
                    <div class="dropdown-content bell" style="width: 300px">
                        <div class="dropdown-header" style="padding: 10px; padding-bottom: 4px; padding-top: 4px; border-bottom: 1px solid rgba(108, 108, 108, 0.15)">
                            <span class="fw-semibold fs-7">Notifications</span>
                        </div>
                        <div class="notif" style="max-height: 250px; overflow: auto;" id="list-notifikasi">

                        </div>
                        <div class="dropdown-footer dflex align-center" style="justify-content: center !important; width: 100%; padding-top: 8px; border-top: 1px solid rgba(108, 108, 108, 0.15)">
                            <span class="fw-normal text-primary fs-7set" type="global" notif="" onclick="read_notif(this)">Read All Notification</span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="javascript:void()" onclick="logOut()" class="nav-icon" aria-label="Log Out" data-microtip-position="bottom" role="tooltip">
                <i class="bx bx-power-off text-danger fs-4"></i>
            </a>
        </div>
    </div>
</nav>