
</div>
</div>
</div>
<input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">
<input type="hidden" id="list_dtids" value="">
</body>
<script src="<?= base_url('public/js/ckeditor5.js') ?>"></script>
<!-- Modal Preview -->
<div class="modal fade" id="modalprev" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalprevLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <div class="spans">
                        <span class="modal-title fs-6set fw-normal" id="modalprev-title"></span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalprev')">&times;</button>
                </div>
            </div>
            <div class="modal-body dflex align-center justify-center form-preview" style="padding: 12px;" id="modelbodyprev">
            </div>
        </div>
    </div>
</div>
<!-- Modal Form -->
<div class="modal fade" id="modaldetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog" id="modaldetail-size" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" name="modaldetail-id" id="modaldetail-id">
                <input type="hidden" name="modaldetail-link" id="modaldetail-link">
                <div class="dflex justify-between align-center" style="width: 100%;border-bottom: 1px solid rgba(25, 75, 120, 0.15); padding-block: 4px;">
                    <span class="modal-title fs-6set fw-normal text-dark" id="modaldetail-title" style="width: 90% !important;"></span>
                    <button type="button" class="btn text-dark" style="height:max-content;font-size: 24px;padding: 0px !important;padding-block: 0px !important;" id="btn-close-modaldetail" onclick="close_modal('modaldetail')">&times;</button>
                </div>
            </div>
            <div class="modal-body margin-t-2" id="modaldetail-form">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaldetailtwo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog" id="modaldetailtwo-size" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" name="modaldetailtwo-id" id="modaldetailtwo-id">
                <input type="hidden" name="modaldetailtwo-link" id="modaldetailtwo-link">
                <div class="dflex justify-between align-center" style="width: 100%;border-bottom: 1px solid rgba(25, 75, 120, 0.15); padding-block: 4px;">
                    <span class="modal-title fs-6set fw-normal text-dark" id="modaldetailtwo-title"></span>
                    <button type="button" class="btn text-dark" style="height:max-content;font-size: 24px;padding: 0px !important;padding-block: 0px !important;" id="btn-close-modaldetail" onclick="close_modal('modaldetailtwo')">&times;</button>
                </div>
            </div>
            <div class="modal-body margin-t-2" id="modaldetailtwo-form">

            </div>
        </div>
    </div>
</div>
<!-- Modal Log Out -->
<div class="modal fade" id="modalout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" name="modalout-link" id="modalout-link" />
                <div class="dflex justify-center align-center" style="width: 100%">
                    <div class="spans text-center">
                        <span class="modal-title fs-6set fw-normal text-dark" id="modalout-title">Log Out</span>
                    </div>
                </div>
            </div>
            <div class="modal-body p-x-2" style="padding-bottom: 1rem;">
                <div class="dflex justify-center align-center text-center">
                    <span class="fw-normal fs-7">Are you sure want to Log Out ?</span>
                </div>
                <div class="dflex justify-center align-center text-center margin-t-2">
                    <span class="fw-normal fs-7set text-dark">your unsaved data will be lost</span>
                </div>
                <div class="dflex justify-center align-center margin-t-18p">
                    <button class="btn btn-success dflex align-center margin-r-2" onclick="return logOut('yes')">
                        <span class="fw-normal fs-7set">Yes, Continue</span>
                    </button>
                    <button class="btn btn-danger dflex align-center" onclick="return close_modal('modalout')">
                        <span class="fw-normal fs-7set">No, Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Release -->
<div class="modal fade" id="modalrel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <div class="spans">
                        <span class="modal-title fs-6set fw-normal" id="modalrel-title"></span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalrel')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Are you sure to <span id="type-release"></span> this data ?</span>
                <div class="plus-message">

                </div>
                <div id="modalrel-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-warning" id="cancel-release" onclick="close_modal('modalrel')"><span class="fw-normal fs-7">No, Cancel</span></button>
                <button type="button" class="btn btn-primary" id="confirm-release"><span class="fw-normal fs-7">Yes, Continue</span></button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cropper -->
<div class="modal fade" id="modalCropper" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <div class="spans">
                        <h5 class="modal-title fs-6set text-dark" id="modalCropperLabel">Pre-Process Photo Profile</h5>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalCropper')">&times;</button>
                </div>
            </div>
            <div class="modal-body text-center" style="margin-bottom:18px;">
                <div class="row">
                    <div class="col-8" style="padding-right: 8px;border-right:1px solid rgba(108, 108, 108, 0.25)">
                        <div style="width: 100%;height:100%;">
                            <img src="" alt="profile-img" loading="lazy" id="profile-img">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="dflex align-center justify-center">
                            <div style="width: 90%;height:250px;padding:4px;margin-block:4px;border:1px solid rgba(108, 108, 108, 0.25)">
                                <img src="<?= base_url('images/blank.jpg') ?>" loading="lazy" alt="preview" id="preview-alt" style="width: 100%;height:100%;object-fit:contain;">
                            </div>
                        </div>
                        <div class="dflex align-center justify-center">
                            <button class="btn btn-primary dflex align-center justify-center" style="width: 90%;" id="btn-crop">
                                <i class="bx bx-crop margin-r-3"></i>
                                <span class="fw-normal fs-7">Save Images</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cancel Booking -->
<div class="modal fade" id="modalcancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <input type="hidden" name="hdid" id="modalcancel-hdid">
                    <input type="hidden" name="link" id="modalcancel-link">
                    <input type="hidden" name="types" id="modalcancel-type">
                    <div class="spans">
                        <span class="modal-title fs-6set fw-normal" id="modalcancel-title"><span id="modalcancel-typetitle" style="text-transform: capitalize;"></span> Booking Stock</span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalcancel')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Are you sure to <span id="modalcancel-typecancel"></span> this booking stock ?</span>
                <div class="plus-message">

                </div>
                <div id="modalcancel-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-warning" id="cancel-cancel" onclick="close_modal('modalcancel')"><span class="fw-normal fs-7">No, Cancel</span></button>
                <button type="button" class="btn btn-primary" id="confirm-cancel-booking"><span class="fw-normal fs-7">Yes, Continue</span></button>
            </div>
        </div>
    </div>
</div>
<!-- Move to Order -->
<div class="modal fade" id="modalorder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <input type="hidden" name="hdid" id="modalorder-hdid">
                    <input type="hidden" name="link" id="modalorder-link">
                    <div class="spans">
                        <span class="modal-title fs-6set fw-normal" id="modalorder-title"><span id="modalorder-typetitle" style="text-transform: capitalize;"></span>Move to Order Booking Stock</span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalorder')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Are you sure to move this booking stock to order ?</span>
                <div class="plus-message">

                </div>
                <div id="modalorder-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-warning" id="cancel-order" onclick="close_modal('modalorder')"><span class="fw-normal fs-7">No, Cancel</span></button>
                <button type="button" class="btn btn-primary" id="confirm-order"><span class="fw-normal fs-7">Yes, Continue</span></button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Delete -->
<div class="modal fade" id="modaldel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row w-100 dflex justify-between" style="padding:0px;height:max-content;">
                    <div class="col-10 dflex align-center">
                        <span class="modal-title fs-6set fw-normal" id="modaldel-title">
                        </span>
                    </div>
                    <div class="col-1 dflex align-center justify-end">
                        <button type="button" class="btn text-dark" style="font-size: 24px;width:max-content;height:max-content;padding: 0px;margin-right:8px;" onclick="close_modal('modaldel')">×</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Apakah anda yakin akan hapus data ?</span>
                <div class="plus-message">

                </div>
                <div id="modaldel-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-secondary" id="cancel-delete" onclick="close_modal('modaldel')"><span class="fw-normal fs-7">No, Keep It</span></button>
                <button type="button" class="btn btn-danger" id="confirm-delete"><span class="fw-normal fs-7">Yes, Delete It</span></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modaldeltwo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <div class="spans">
                        <span class="modal-title fs-6set fw-normal" id="modaldeltwo-title"></span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modaldeltwo')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Are you sure to delete this data ?</span>
                <div class="plus-message">

                </div>
                <div id="modaldeltwo-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-secondary" id="cancel-delete" onclick="close_modal('modaldeltwo')"><span class="fw-normal fs-7">No, Keep It</span></button>
                <button type="button" class="btn btn-danger" id="confirm-delete"><span class="fw-normal fs-7">Yes, Delete It</span></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="errorModalInfo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="errorModalInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body dflex align-center justify-center">
                <div class="text-center p-y-2">
                    <h5 class="modal-title text-danger" id="errorModalInfoLabel">Internal Server Error !</h5>
                    <img src="<?= base_url('public/icon/error-icon.png') ?>" width="200px">
                    <h6>Reload your page or contact administrator for the further</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Synchronize -->
<div class="modal fade" id="modalsync" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="dflex justify-between align-center" style="width: 100%">
                    <div class="spans" style="width: 90%;">
                        <span class="modal-title fs-6set fw-normal" id="sync-title"></span>
                    </div>
                    <button type="button" class="btn text-dark" style="font-size: 24px" onclick="close_modal('modalsync')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div>
                    <span class="fw-normal fs-7set text-dark">Do you want to pull this machine data?</span>
                    <div class="plus-message">
                    </div>
                </div>
                <div id="sync-assets">

                </div>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-secondary" id="cancel-sync" onclick="close_modal('modalsync')"><span class="fw-normal fs-7">Cancel</span></button>
                <button type="button" class="btn btn-primary" id="confirm-sync"><span class="fw-normal fs-7">Pull</span></button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Slide Up -->
<div class="slideUp-form" id="slideUp-form">
    <div class="slideUp-content">
        <div class="slideUp-title">
            <div class="title-span">
                <span id="title-slideUp"></span>
                <i class='bx bx-x' onclick="hideSlide()"></i>
            </div>
        </div>
        <div class="form-slideUp" id="form-slideUp">
        </div>
    </div>
</div>
<!-- Modal Cancel Request -->
<div class="modal fade" id="modal-cancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2147483647 !important" data-bs-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row w-100 dflex justify-between" style="padding:0px;height:max-content;">
                    <div class="col-10 dflex align-center">
                        <span class="modal-title fs-6set fw-normal" id="modal-title">
                            Cancel Request
                        </span>
                    </div>
                    <div class="col-1 dflex align-center justify-end">
                        <button type="button" class="btn text-dark" style="font-size: 24px;width:max-content;height:max-content;padding: 0px;margin-right:8px;" onclick="close_modal('modal-cancel')">×</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <span class="fw-normal fs-7set text-dark">Are you sure to cancel this request?</span>
            </div>
            <div class="modal-footer margin-t-2 p-x-2">
                <button type="button" class="btn btn-secondary" id="cancel-delete" onclick="close_modal('modal-cancel')"><span class="fw-normal fs-7">No, Keep It</span></button>
                <button type="button" class="btn btn-danger" id="confirm-cancel"><span class="fw-normal fs-7">Yes, Cancel It</span></button>
            </div>
        </div>
    </div>
</div>

</html>
<script>
    var total_error = 0;
    var arpaymentPrintSetting = {
        folio: {
            setengahhalaman: {
                uprow: 7,
                downrow: 5
            },
            satuhalaman: {
                uprow: 40,
                downrow: 5
            }
        }
    }

    var cashbankPrintSetting = {
        folio: {
            setengahhalaman: {
                uprow: 9,
                downrow: 5
            },
            satuhalaman: {
                uprow: 36,
                downrow: 5
            }
        }
    }

    // socket.on('connect_error', err => {
    //     // console.clear();
    //     total_error++;
    //     if (total_error > 3) {
    //         $("#errorModalInfo").modal('show')
    //     }
    // })
    // socket.on('connect', err => {
    //     total_error = 0;
    // $("#errorModalInfo").modal('hide');
    // console.clear();
    // })

    var notyf = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        }
    });
    var custom_notyf = new Notyf({
        types: [{
            type: 'process',
            background: 'grey',
            icon: {
                className: 'bx bx-radio-circle bx-burst bx-md text-white',
                tagName: 'i',
                text: ''
            }
        }]
    })

    function showSuccess(msg) {
        notyf.success(msg);
    }

    function showError(msg) {
        notyf.error(msg);
    }

    function showNotif(type, msg, duration = 2000) {
        notyf.open({
            type: type,
            message: msg,
            duration: duration
        })
    }

    function zoom() {
        document.body.style.zoom = '90%';
    }

    function openFilter() {
        $('#filter-tab').slideToggle(100);
    }

    function dp($parent = "") {
        const dropdown = document.querySelectorAll(`${$parent} .dropdown`);
        dropdown.forEach((e) => {
            e.addEventListener('click', () => {
                if (e.classList.contains('active')) {
                    e.classList.remove('active')
                } else {
                    e.classList.add('active')
                }
            })
        })
    }

    // slide up
    function showSlideForm(title, link = '', type = 'default', data = {}) {
        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: data,
            success: function(res) {
                $('#title-slideUp').text(title);
                $('#slideUp-form').addClass('openSlide')
                $('#form-slideUp').html(res.view);
            }
        })
    }

    function hideSlide() {
        $('#slideUp-form').removeClass('openSlide');
    }

    function resetForm(formid) {
        $(`#${formid}`)[0].reset();
    }

    function modalRelease(title, type) {
        $('#modalrel-title').text(title);
        $("#type-release").text(type)
        $('#modalrel').modal('show');
    }

    function getIcon(ext) {
        let icon = "";
        if (ext == 'txt') {
            icon = 'bx bxs-file-txt';
        } else if (ext == 'png' || ext == 'jpeg' || ext == 'jpg') {
            icon = 'bx bxs-file-image';
        } else if (ext == 'doc' || ext == 'docx') {
            icon = 'bx bxs-file-doc';
        } else if (ext == 'pdf') {
            icon = 'bx bxs-file-pdf';
        } else {
            icon = "bx bxs-file";
        }
        return icon;
    }

    // func update status SO
    function changeSO(type, soid) {
        var link = '<?= getURL('salesorder/upstat') ?>';
        // default dulu (sekedar update status, karena masih belum tau dengan description,expedition,reason reject nya)
        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                status: type,
                soid: soid,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
            },
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                var atype = '';
                if (res.sukses == 1) {
                    atype = "success";
                    setTimeout(() => {
                        window.location.href = '<?= getURL('salesorder') ?>'
                    }, 100);
                } else {
                    atype = "error";
                }
                showNotif(atype, res.pesan);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
            }
        })
    }

    function sideHasParent() {
        $('.sidebar .sidebar-nav .sidebar-item').each(function() {
            $(this).click(function() {
                if ($(this).hasClass('side-parent')) {
                    if (!$(this).parent().parent().parent().parent().hasClass('show-lg')) {
                        $(this).toggleClass('active');
                        $(this).children('span').toggleClass('lg');
                        if ($(this).hasClass('active')) {
                            $(this).parent().find('.submenu').slideDown('fast')
                        } else {
                            $(this).parent().find('.submenu').slideUp('fast');
                        }
                    }
                } else {
                    $(this).addClass('active');
                }
                // $(this).parent().siblings().children('.sidebar-item').removeClass('active')
            })
        })
    }

    function sidebarEvent() {
        $('.submenu .submenu-item').each(function() {
            $(this).click(function(e) {
                e.stopPropagation();
            })
        })
        subMenu();
        $('.side-toggle.slide').click(function() {
            $('aside').toggleClass('show-sm');
            $('aside').removeClass('show-lg');
            subMenu();
        })
        $('.side-toggle.shrink').click(function() {
            $('aside').toggleClass('show-lg');
            $('aside').removeClass('show-sm');
            subMenu();
        })
    }

    function subMenu() {
        if ($('aside').hasClass('show-lg')) {
            let r = $('.sidebar-item');
            r.removeClass('active')
            r.parent().find('.submenu').fadeOut();
            $('aside.show-lg .sidebar .sidebar-nav .sidebar-item.side-parent').hover(
                function() {
                    $(this).addClass('openSub')
                },
                function() {
                    $(this).removeClass('openSub')
                    $('.subChild').removeClass('opened');
                }
            )
        }
    }

    // Mobile view drop attachment
    function dropAttach(elem) {
        $(elem).parent().parent().remove();
        if ($('#file-append').children('.col-3').length == 1) {
            $('#emptys').fadeIn('slow')
            $('#file-append').fadeOut('slow')
        }
    }

    // Mobile view tab pane
    function tabChange(elem, link, title, changeTo, parent) {
        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                change: changeTo,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
            },
            success: function(response) {
                $("#csrf_token").val(encrypter(response.csrfToken));
                $(`#${parent}`).html(response.view);
                $(elem).siblings('.tabs-item').removeClass('active');
                $(elem).addClass('active');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
            }
        })
    }
    // Mobile view appbar extend search bar
    function search(elem, stype, type) {
        var oldClass = "col-3",
            newClass = "col-12";

        if (type != 'open') {
            oldClass = "col-12";
            newClass = "col-3";
            $(elem).siblings('#form-search').fadeOut('slow', function() {
                renewClass($(elem).parent().parent(), oldClass, newClass);
                setTimeout(() => {
                    $(elem).fadeIn('slow');
                    $(elem).siblings('button').fadeIn('slow')
                    $(elem).parentsUntil('.appbar').siblings('.back').fadeIn('slow')
                    $(elem).parentsUntil('.appbar').siblings('.titles').fadeIn('slow')
                }, 150);
            })
        } else {
            $(elem).parentsUntil('.appbar').siblings('.back').fadeOut('fast', function() {
                $(elem).parentsUntil('.appbar').siblings('.titles').fadeOut('slow', function() {
                    $(elem).fadeOut('fast', function() {
                        $(elem).siblings('button').fadeOut('fast', function() {
                            renewClass($(elem).parent().parent(), oldClass, newClass);
                            $(elem).siblings('#form-search').fadeIn('fast', function() {
                                $('#search').focus();
                            });
                        })
                    })
                })
            });
        }
        $('#form-search').attr('data-s', stype)
    }

    function renewClass(elem, old, cnew) {
        $(elem).removeClass(old);
        $(elem).addClass(cnew);
    }

    // Modal func
    function close_modal(modalid) {
        $("#" + modalid).modal('hide');
        if (modalid == 'modaldel') {
            $("#modaldel-assets").html("");
            $("#modaldel-title").text("")
            // regenerate_dropdown()
        } else if (modalid == 'modaldetail') {
            $("#modaldetail-size").removeClass("modal-lg modal-md modal-sm modal-xl");
            $("#btn-close-modaldetail").removeClass('lost-elem')
            $("#modaldetail-title").text("")
            $("#modaldetail-form").html("")
        } else if (modalid == 'modalrel') {
            $("#modalrel-assets").html("");
        }
        // console.log($("#modaldetail-form").html())   
    }

    function modalglobal(id, link, title, btn, size) {
        $.ajax({
            url: link,
            type: 'post',
            data: {
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
                id: id,
            },
            dataType: 'json',
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken))
                $('#modaldetail-size').removeClass();
                $('#modaldetail-id').val(id);
                $('#modaldetail-link').val(link);
                $('#modaldetail-size').addClass(`modal-dialog ${size}`)
                $('#modaldetail-title').text(title);
                $('#modaldetail').modal('show')
                if (btn == null) {
                    $('#modaldetail-btn').css('display', 'none');
                } else {
                    $('#modaldetail-btn').html(btn);
                }
                $('#modaldetail-form').html(res.view);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
            }
        })
    }

    function modalForm(title, size, link, datas = {}) {
        datas["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
        let elem = datas['identifier']
        delete datas["identifier"];
        let old_html = $(elem).html();
        $(elem).html('<i class="bx bx-loader bx-spin"></i>');
        $(elem).attr('disabled', 'disabled');
        $("button.btn").attr('disabled', 'disabled');
        $.ajax({
            url: link,
            type: 'post',
            data: datas,
            dataType: 'json',
            success: function(res) {
                $(elem).html(old_html);
                $(elem).removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
                $("#csrf_token").val(encrypter(res.csrfToken));
                if (res.error != undefined) {
                    showError(res.error);
                } else {
                    $('#modaldetail-title').html("");
                    $(`#modaldetail-size`).removeClass("modal-lg", "modal-sm", "modal-md", "modal-xl");
                    $("#btn-close-modaldetail").removeClass('lost-elem')
                    if (res.nonclose != undefined) {
                        $("#btn-close-modaldetail").addClass('lost-elem')
                    }
                    $(`#modaldetail-size`).addClass(size)
                    $("#modaldetail-title").html(`<h4>${title}</h4>`);
                    $("#modaldetail-form").html(res.view)
                    $("#modaldetail-form script").each(function() {
                        eval($(this).text());
                    });
                    $(`#modaldetail`).modal("toggle");
                    dp('#modaldetail')
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
                $(elem).html(old_html);
                $(elem).removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
            }
        })
    }

    function modalFormTwo(title, size, link, datas = {}) {
        datas["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
        let elem = datas['identifier']
        if (elem != undefined) {
            delete datas["identifier"];
            let old_html = $(elem).html();
            $(elem).html('<i class="bx bx-loader bx-spin"></i>');
            $(elem).attr('disabled', 'disabled');
            $("button.btn").attr('disabled', 'disabled');
        }
        // console.log(datas);
        $.ajax({
            url: link,
            type: 'post',
            data: datas,
            dataType: 'json',
            success: function(res) {
                if (elem != undefined) {
                    $(elem).html(old_html);
                    $(elem).removeAttr('disabled');
                    $("button.btn").removeAttr('disabled');
                }
                $("#csrf_token").val(encrypter(res.csrfToken));
                if (res.error != undefined) {
                    showError(res.error);
                } else {
                    $('#modaldetailtwo-title').html("");
                    $(`#modaldetailtwo-size`).removeClass("modal-lg", "modal-sm", "modal-md", "modal-xl");
                    $("#btn-close-modaldetailtwo").removeClass('lost-elem')
                    if (res.nonclose != undefined) {
                        $("#btn-close-modaldetailtwo").addClass('lost-elem')
                    }
                    $(`#modaldetailtwo-size`).addClass(size)
                    $("#modaldetailtwo-title").html(`<h4>${title}</h4>`);
                    $("#modaldetailtwo-form").html(res.view)
                    $("#modaldetailtwo-form script").each(function() {
                        eval($(this).text());
                    });
                    $(`#modaldetailtwo`).modal("toggle");
                    dp('#modaldetailtwo')
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
                if (elem != undefined) {
                    $(elem).html(old_html);
                    $(elem).removeAttr('disabled');
                    $("button.btn").removeAttr('disabled');
                }
            }
        })
    }

    function modalDelete(title, datas) {
        $("#modaldel-title").html(`<h4>${title}</h4>`);
        $(`#modaldel`).modal("show");
        $("#modaldel-assets").html("");
        let keys = Object.keys(datas);
        // console.log(keys)
        for (let x of keys) {
            if (x == 'plus-message') {
                $('.plus-message').html(datas[x]);
                continue;
            }
            $("#modaldel-assets").append(`<span class="re-set" key="${x}" vals="${datas[x]}"></span>`);
        }
    }

    function modalDeleteSec(title, datas) {
        $("#modaldeltwo-title").html(`<h4>${title}</h4>`);
        $(`#modaldeltwo`).modal("show");
        $("#modaldeltwo-assets").html("");
        let keys = Object.keys(datas);
        // console.log(keys)
        for (let x of keys) {
            if (x == 'plus-message') {
                $('.plus-message').html(datas[x]);
                continue;
            }
            $("#modaldeltwo-assets").append(`<span class="re-set" key="${x}" vals="${datas[x]}"></span>`);
        }
    }

    function modalSynchronize(title, datas = {}) {
        $("#sync-title").html(`<h4>${title}</h4>`);
        $(`#modalsync`).modal("show");
        $("#sync-assets").html("");
        let keys = Object.keys(datas);
        for (let x of keys) {
            if (x == 'plus-message') {
                $('.plus-message').html(datas[x]);
                continue;
            }
            $("#sync-assets").append(`<span class="re-set" key="${x}" vals="${datas[x]}"></span>`);
        }
    }

    function loadButton(element) {
        let oldHtml = $(element).html();
        $(element).html(`<i class='bx bx-loader-alt bx-spin'></i>`);
        $(element).attr('disabled', true);
        return oldHtml;
    }

    function unloadButton(element, oldHtml) {
        $(element).removeAttr('disabled');
        $(element).html(oldHtml);
    }

    // Navigate basic page
    function toPage(url, blank = '') {
        window.location.href = url;
        if (blank != '') {
            window.open(url, '_blank');
            return false;
        }
    }

    function printPage(url) {
        window.open(url, 'blank');
    }
    var table = $('.datatable').DataTable({
        // serverSide: true,
        // destroy: true,
        // ajax: {
        //     url: $(this).data('link'),
        //     type: 'post',
        //     dataType: 'json',
        //     data: function(param) {
        //         return param;
        //     }
        // }
    })
    var tbl = $('.table-master').DataTable({
        serverSide: true,
        destroy: true,
        autoWidth: false,
        ajax: {
            url: '<?= current_url(true) ?>/table',
            type: 'post',
            dataType: 'json',
            data: function(param) {
                param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                return param;
            },
            "deferRender": true,
            dataSrc: function(json) {
                let gt = json.tambahan.grandtotal;
                if (gt != undefined) {
                    $("#text-grandtotal").text(gt);
                }
                $("#csrf_token").val(encrypter(json.csrfToken));
                return json.data
            }
        }
    })

    let tbl_history = $('.tbl-history').DataTable({
        serverSide: true,
        destroy: true,
        autoWidth: false,
        ajax: {
            url: '<?= getURL('loghistory/table_history') ?>',
            type: 'post',
            dataType: 'json',
            data: function(param) {
                param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                param['parameter'] = $("#param-history").val();
                param['ids'] = $("#data_ids").val();
                param['dtids'] = $("#list_dtids").val();
                return param;
            },
            dataSrc: function(json) {
                $("#csrf_token").val(encrypter(json.csrfToken));
                return json.data
            }
        }
    })

    function swipeTab(direction) {
        if (direction == 'left') {
            document.getElementById('tabs-content').scrollLeft -= 150;
        } else {
            document.getElementById('tabs-content').scrollLeft += 150;
        }
    }
    // Log out func
    function logOut(type = '') {
        if (type == '') {
            $('#modalout').modal('show');
        } else {
            showSuccess('Logging out . . .')
            setTimeout(() => {
                window.location.href = '<?= getURL('User/logOut') ?>';
            }, 150);
        }
    }

    function getTotal() {
        $('.istats').each(function() {
            var elm = $(this);
            var status = $(this).data('status');
            $.ajax({
                url: '<?= getURL('ticket/getcountstatus') ?>',
                type: 'post',
                dataType: 'json',
                data: {
                    reload: 'true',
                    status: status,
                    <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
                },
                success: function(res) {
                    $("#csrf_token").val(encrypter(res.csrfToken));
                    elm.text(res.total);
                }
            })
        })
    }
    // Prevent Triple CLick Selection
    document.addEventListener('DOMContentLoaded', function() {
        let clickCount = 0;
        let clickTimer;

        document.body.addEventListener('click', function(event) {
            clickCount++;
            clearTimeout(clickTimer);

            clickTimer = setTimeout(function() {
                clickCount = 0;
            }, 400); // Waktu reset clickCount (misal: 400ms)

            if (clickCount === 3) {
                window.getSelection().removeAllRanges();
                clickCount = 0;
            }
        });
    });

    // Ckeditor Init
    const {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font
    } = CKEDITOR;

    function initializeEditor() {
        const editorElement = document.querySelector('#editor');
        if (!editorElement) {
            console.warn('Editor element with ID "editor" not found.');
            return;
        }

        ClassicEditor
            .create(editorElement, {
                licenseKey: 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3MzQ1NjYzOTksImp0aSI6IjM2YTk2MzNhLTExMjgtNGQzOS1hNGU3LTM2ODU5OTRmM2RkOSIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiLCJzaCJdLCJ3aGl0ZUxhYmVsIjp0cnVlLCJsaWNlbnNlVHlwZSI6InRyaWFsIiwiZmVhdHVyZXMiOlsiKiJdLCJ2YyI6IjM0MzgxMDhkIn0.MWBXuBAhdRKCfBEUUQnFEYmLcyoGuil9rsCWyaso-30DcQ9GUB9tRWjhRfeGjlaOiVHQl8FxX9t-SUZoBtWfeA',
                plugins: [Essentials, Paragraph, Bold, Italic, Font],
                toolbar: [
                    'undo', 'redo', '|', 'bold', 'italic', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
                ]
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    }


    $(document).ready(function() {
        let editorInstance = null;

        dp();
        // load_company();
        // sideHasParent();
        sidebarEvent();
        // load_notification();
        // Sidebar Navigate
        // Appbar search focus out
        $('#search:not(.notif)').focusout(function() {
            var stype = $('#form-search').data('s');
            search($('.icon-search'), stype, 'hide');
        })
        // new tab
        $('.btn-newtab').each(function() {
            $(this).click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                var urls = $(this).data('link');
                window.open(urls, '_blank')
            })
        })
        // new window
        $('.btn-copylink').each(function() {
            $(this).click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                var url = $(this).children('#copys').val();
                var tempVal = $('<input>');
                $('body').append(tempVal);
                tempVal.val(url).select();
                document.execCommand('copy');
                tempVal.remove();
                showSuccess('Link Copied!');
            })
        })
        $('.sidebar .sidebar-nav a.no-parent').each(function() {
            $(this).on('click', function(evt) {
                evt.preventDefault();
                evt.stopImmediatePropagation();
                if (evt.ctrlKey) {
                    window.open($(this).attr('href'), '_blank')
                } else {
                    window.location.href = $(this).attr('href')
                }
            })
        })
        // Nested Sidebar
        $('.sub-item.haveSub').each(function(i, e) {
            $(this).click(function(e) {
                e.preventDefault()
                e.stopPropagation();
                let ch = $(this).next('.childSub');
                ch.slideToggle(100);
                $(this).children().children('.navicon').toggleClass('open')
            })
        })
        // Show LG nested sidebar
        $('.haveChild').each(function() {
            $(this).hover(
                function() {
                    let pr = $(this).next('#listSub').children('.subChild');
                    if (pr.hasClass('opened')) {
                        pr.removeClass('opened');
                    } else {
                        pr.addClass('opened');
                    }
                },
            )
        })
        // Breadcrumb
        $('.breadcrumb .breadcrumb-item:not(:last-of-type)').each(function(i, e) {
            $(this).after(`<span class="breadcrumb-item icon"><i class="bx bx-chevron-right"></i></span>`);
        })
        // Delete Button
        $("#confirm-delete").on('click', function() {
            let link = "";
            let id = "";
            let pagetype = "";
            let reloadpage = "";
            let reloadurl = "";
            let table_cls = "";
            $(".re-set").each(function() {
                let k = $(this).attr('key');
                let v = $(this).attr('vals');
                if (k == 'link') {
                    link = v
                } else if (k == 'id') {
                    id = v;
                } else if (k == 'pagetype') {
                    pagetype = v;
                } else if (k == 'reloadpage') {
                    reloadpage = v;
                } else if (k == 'reloadurl') {
                    reloadurl = v;
                } else if (k == 'table-id') {
                    table_cls = v;
                }
            })

            $.ajax({
                url: link,
                type: 'post',
                data: {
                    id: id,
                    <?= csrf_token() ?>: decrypter($("#csrf_token").val())
                },
                dataType: 'json',
                success: function(res) {
                    $("#csrf_token").val(encrypter(res.csrfToken));
                    if (res.sukses != '0' || res.sukses != 0) {
                        close_modal('modaldel');
                        var pesan = (res.pesan !== undefined ? res.pesan : 'Data Berhasil dihapus');
                        showSuccess(pesan);
                        if (pagetype == 'pages') {
                            $('#' + reloadpage).load(reloadurl, function() {
                                // dp('#' + reloadpage);
                            })
                        } else if (pagetype == 'table') {
                            if (table_cls == 'detailsTable') {
                                if (typeof detailsTbl !== 'undefined') {
                                    detailsTbl.ajax.reload(null, false);
                                }
                            } else {
                                tbl.ajax.reload();
                                if (typeof tbls !== 'undefined') {
                                    tbls.ajax.reload();
                                }
                            }
                        } else if (pagetype == 'tablespecific') {
                            if (table_cls == 'tbl_sub') {
                                tbl_sub.ajax.reload(function() {
                                    $("#grand_total").text($("#span-gt").data('gt'));
                                });
                            }
                        } else if (pagetype == 'tablecredit') {
                            if (typeof tbl_dt !== undefined) {
                                tbl_dt.ajax.reload()
                            }
                        } else if (pagetype == 'tabledetail') {
                            if (typeof tbl_sub !== undefined) {
                                tbl_sub.ajax.reload();
                            }
                        } else if (pagetype == 'profiletab') {
                            $('#profile-wrap').load('<?= getURL('myprofile/getview') ?>');
                            fileArr = '';
                            fileAttach = '';
                        } else if (pagetype == 'tablebom') {
                            if (typeof table_bank !== undefined) {
                                tbl_bom.ajax.reload();
                            }
                        } else if (pagetype == 'tablefam') {
                            if (typeof tblRelation !== undefined) {
                                tblRelation.ajax.reload();
                            }
                        } else if (pagetype == 'tablestatuskar') {
                            if (typeof tblStatus !== undefined) {
                                tblStatus.ajax.reload();
                            }
                        } else if (pagetype == 'tablesp') {
                            if (typeof tblSp !== undefined) {
                                tblSp.ajax.reload();
                            }
                        } else if (pagetype == 'tablebnk') {
                            if (typeof tblBank !== undefined) {
                                tblBank.ajax.reload();
                            }
                        } else if (pagetype == 'tablerip') {
                            if (typeof tblEdu !== undefined) {
                                tblEdu.ajax.reload();
                            }
                        } else if (pagetype == 'tableripker') {
                            if (typeof tblJob !== undefined) {
                                tblJob.ajax.reload();
                            }
                        } else if (pagetype == 'tableemer') {
                            if (typeof tblContact !== undefined) {
                                tblContact.ajax.reload();
                            }
                        } else if (pagetype == 'tabledocs') {
                            if (typeof tblDok !== undefined) {
                                tblDok.ajax.reload();
                            }
                        } else if (pagetype == 'reloademp') {
                            reloadCalendar();
                        }
                    } else {
                        var msg = (res.pesan !== undefined ? res.pesan : 'Data gagal dihapus');
                        showError(msg);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    showError(thrownError);
                }
            })
        })

        // CKEDitor Reinitialize
        $('#modaldetail').on('shown.bs.modal', function() {
            if (editorInstance) {
                editorInstance.destroy().then(() => {
                    initializeEditor();
                }).catch(error => {
                    console.error('Error destroying editor:', error);
                });
            } else {
                initializeEditor();
            }
        });

        $('#modaldetail').on('hidden.bs.modal', function() {
            if (editorInstance) {
                editorInstance.destroy().then(() => {
                    editorInstance = null;
                }).catch(error => {
                    console.error('Error destroying editor:', error);
                });
            }
        });

        // Synchronize Button
        $("#confirm-sync").on('click', function() {
            let link = "";
            let id = "";
            let ip = "";
            let port = "";
            $(".re-set").each(function() {
                let k = $(this).attr('key');
                let v = $(this).attr('vals');
                if (k == 'link') {
                    link = v
                } else if (k == 'id') {
                    id = v;
                } else if (k == 'ip') {
                    ip = v;
                } else if (k == 'port') {
                    port = v;
                }
            })

            let old_body = $('#modalsync .modal-body > div:first-child').html();
            let loading = `<div class="dflex justify-center" style="padding-block: 2rem;"><div style="align-items: center; justify-content: center; width: 2.2em; height: 2.2em; margin: 0 1.875em; animation: swal2-rotate-loading 1.5s linear 0s infinite normal; border-width: .25em; border-style: solid; border-radius: 100%; border-color: #2778c4 rgba(0,0,0,0) #2778c4 rgba(0,0,0,0);"></div></div>`;
            $('#modalsync .modal-body > div:first-child').html(loading);
            $('#modalsync .modal-header').css('display', 'none');
            $('#modalsync .modal-footer').css('display', 'none');
            // swal.showLoading()
            $.ajax({
                url: '<?= getURL('attmachine/sync') ?>',
                type: 'post',
                data: {
                    id: id,
                    ip: ip,
                    port: port
                },
                dataType: 'json',
                success: function(res) {
                    $("#csrf_token").val(encrypter(res.csrfToken));
                    let row = res.row;
                    let machineid = row['id'];
                    let machinename = row['machinename'];
                    let branchname = row['branchname'];
                    // swal.close()
                    $('#modalsync .modal-body > div:first-child').html(old_body);
                    $('#modalsync .modal-header').css('display', 'flex');
                    $('#modalsync .modal-footer').css('display', 'flex');
                    close_modal('modalsync')
                    tbl.ajax.reload()
                    if (res.sukses == 1) {
                        showSuccess(res.pesan)
                    } else {
                        showError(res.pesan)
                    }
                    custom_notyf.open({
                        duration: 99999999999,
                        position: {
                            x: 'right',
                            y: 'bottom',
                        },
                        type: 'process',
                        dismissible: true,
                        message: `<span style="margin-left: 10px;">Collecting attendance data on ${machinename} at the ${branchname} branch</span><input type='hidden' class='hidden-machineids' value='${machineid}'>`
                    })
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    showError(thrownError);
                    $('#modalsync .modal-body > div:first-child').html(old_body);
                    $('#modalsync .modal-header').css('display', 'flex');
                    $('#modalsync .modal-footer').css('display', 'flex');
                    close_modal('modalsync')
                    // swal.close()
                }
            })
        })
    })

    function load_notification() {
        $("#list-notifikasi, #list-company-hp").html("");
        $.ajax({
            url: "<?= base_url('servers/getnotif') ?>",
            type: 'post',
            data: {
                <?= csrf_token() ?>: decrypter($("#csrf_token").val())
            },
            dataType: 'json',
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                let arr = res.notif;
                let unread = 0;
                for (let x = 0; x < arr.length; x++) {
                    var dateStr = arr[x]['notifdate'];
                    var date = new Date(dateStr).toString();
                    var exp = date.split(' ');
                    let tanggal = exp[2] + ' ' + exp[1] + ' ' + exp[3] + ' ' + exp[4];
                    let style_warna = ''
                    if (arr[x]['readdate'] == null) {
                        style_warna = 'style="background-color: #B8E2F2;"'
                        unread++;
                    }
                    let link = arr[x]['link'];
                    let splitlink = link.split('--');
                    let getlink = splitlink[0]
                    $("#list-notifikasi, #list-company-hp").append(`
                    <div class="dropdown-item align-start" ${style_warna} url="${getlink}" type="single" notif="${arr[x]['notifid']}" onclick="read_notif(this)">
    <img src="<?= base_url('images/avatar/avatar-1.png') ?>" style="width: 30px; height: 100%; object-fit: cover" alt="avatar" class="img-profile margin-r-3" />
    <div class="row" style="width: 100%">
        <div style="width: 100%; margin: 0; display: flex">
            <span class="fw-semibold" style="font-size: 12px !important">${arr[x]['fromname']}</span>
            <span class="fw-normal text-success" style="font-size: 8px !important; margin-left: auto">${tanggal}</span>
        </div>
        <p class="fw-normal" style="font-size: 11px !important; text-align: justify; margin: 0px">
            ${arr[x]['description']}
        </p>
    </div>
</div>
                    `);
                }
                if (arr.length > 0) {
                    let firstlink = arr[0]['link'];
                    let linksplit = firstlink.split('--');
                    if (linksplit[0] == 'attmachine') {
                        // custom_notyf.dismissAll();
                        let macid = linksplit[1];
                        $(".hidden-machineids").each(function() {
                            if ($(this).val() == macid) {
                                $(this).parent('.notyf__message').parent('.notyf__wrapper').find('.notyf__dismiss').children('.notyf__dismiss-btn').trigger('click');
                                let dmac = $("#dashboardmachines").val();
                                if (dmac == 'true') {
                                    loadListMachine();
                                }
                            }
                        });
                    }
                }
                $(".badge-notif").text(unread)

            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
            }
        })
    }

    function read_notif(elem) {
        let url = $(elem).attr('url');
        let type = $(elem).attr('type');
        let notif = $(elem).attr('notif');
        $.ajax({
            url: "<?= base_url('servers/readnotif') ?>",
            type: 'post',
            data: {
                type: type,
                notifid: notif,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
            },
            dataType: 'json',
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                if (res.type == 'single') {
                    window.location.href = "<?= base_url() ?>" + url
                } else {
                    load_notification()
                }
            },
        })
    }

    function load_company() {
        $.ajax({
            url: "<?= base_url('personal/getcompany') ?>",
            type: 'post',
            data: {
                <?= csrf_token() ?>: decrypter($("#csrf_token").val())
            },
            dataType: 'json',
            success: function(res) {
                let arr = res.data;
                $("#csrf_token").val(encrypter(res.csrfToken));
                $("#list-company").html("");
                $("#list-company-hp").html("");
                for (let y = 0; y < arr.length; y++) {
                    $("#list-company").append(`
                        <div class="dropdown-item dflex align-start" thecompany="${arr[y]['companyid']}" onclick="change_company(this)">
                            <i class='bx bx-chevron-right-circle bx-xs margin-r-3'></i><span class="fs-7set text-dark">${arr[y]['companyname']}</span>
                        </div>
                        `);
                    $("#list-company-hp").append(`
                        <div class="dropdown-item dflex align-start" thecompany="${arr[y]['companyid']}" onclick="change_company(this)">
                            <i class='bx bx-chevron-right-circle bx-xs margin-r-3'></i><span class="fs-7set text-dark">${arr[y]['companyname']}</span>
                        </div>
                    `);
                }
            },
        })
    }

    function change_company(elem) {
        let coid = $(elem).attr('thecompany');
        $.ajax({
            url: "<?= base_url('personal/changecompany') ?>",
            data: {
                coid: coid,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val())
            },
            type: 'post',
            dataType: 'json',
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                window.location.reload()
            },
        })
    }

    function penyebut(nilai) {
        nilai = Math.abs(nilai);
        var huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        var temp = "";
        if (nilai < 12) {
            temp = " " + huruf[nilai];
        } else if (nilai < 20) {
            temp = penyebut(nilai - 10) + " belas";
        } else if (nilai < 100) {
            temp = penyebut(Math.floor(nilai / 10)) + " puluh" + penyebut(nilai % 10);
        } else if (nilai < 200) {
            temp = " seratus" + penyebut(nilai - 100);
        } else if (nilai < 1000) {
            temp = penyebut(Math.floor(nilai / 100)) + " ratus" + penyebut(nilai % 100);
        } else if (nilai < 2000) {
            temp = " seribu" + penyebut(nilai - 1000);
        } else if (nilai < 1000000) {
            temp = penyebut(Math.floor(nilai / 1000)) + " ribu" + penyebut(nilai % 1000);
        } else if (nilai < 1000000000) {
            temp = penyebut(Math.floor(nilai / 1000000)) + " juta" + penyebut(nilai % 1000000);
        } else if (nilai < 1000000000000) {
            temp = penyebut(Math.floor(nilai / 1000000000)) + " milyar" + penyebut(nilai % 1000000000);
        } else if (nilai < 1000000000000000) {
            temp = penyebut(Math.floor(nilai / 1000000000000)) + " triliun" + penyebut(nilai % 1000000000000);
        }
        return temp;
    }


    function price_keyup(elem) {
        let value = $(elem).val();
        $(elem).val(formatRupiah(value));
    }

    function price_fixed_keyup(elem) {
        let value = $(elem).val();
        $(elem).val(formatRupiahFixed(value));
    }

    function exp_number(number) {
        let exp_imp = number.split(',');
        let number_update = '';
        for (let n = 0; n < exp_imp.length; n++) {
            number_update += exp_imp[n];
        }
        let exp_dua = number_update.split('.');
        let number_updatedua = '';
        for (let j = 0; j < exp_dua.length; j++) {
            if (j == 0) {
                number_updatedua += exp_dua[j];
            } else {
                number_updatedua += '.' + exp_dua[j];
            }
        }
        return number_updatedua * 1;
    }

    function checkValidateDate(elem) {

    }

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    function formatRupiah(angka) {
        // var ux = toNumeric(angka)
        var val = angka.toString();
        val = val.replace(/[^0-9\.-]/g, '');

        if (val != "") {
            // console.log(val);
            valArr = val.split('.');
            // console.log(valArr);
            valArr[0] = (parseInt(valArr[0], 10)).toLocaleString("en-US");
            val = valArr.join('.');
        }

        var rupiah = val;
        return rupiah;
    }

    function formatRupiahFixed(angka) {
        var ux = toNumeric(angka)
        var val = ux.toString();
        val = val.replace(/[^0-9\.]/g, '');

        if (val != "") {
            let isi = (val * 1).toFixed(2);
            val = isi.toString();
            let valArr = val.split('.');
            valArr[0] = (parseInt(valArr[0], 10)).toLocaleString("en-US");
            val = valArr[0];
        }

        var rupiah = val;
        return rupiah;
    }

    function toNumeric(angka) {
        var val = angka.toString();
        val = val.replace(/[^0-9\.]/g, '');
        let number = val * 1;
        return number;
    }

    function generateSelect2(
        element,
        dparent = '',
        link = '',
        placeholder = '',
        width = '',
        minimumResultsForSearch = 0,
        allowClear = true,
        datas = {},
        ismultiple = false,
    ) {
        setting_select = {
            allowClear: allowClear,
            multiple: ismultiple,
        };
        if (dparent != '') {
            setting_select.dropdownParent = $(dparent)
        }
        if (placeholder == '') {
            placeholder = 'Choose data';
        }
        setting_select.placeholder = placeholder;
        if (width != '') {
            setting_select.width = width;
        }
        if (minimumResultsForSearch != 0) {
            setting_select.minimumResultsForSearch = minimumResultsForSearch;
        }
        if (link != '') {
            setting_select.ajax = {
                url: link,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    datas.searchTerm = params.term;
                    datas.<?= csrf_token() ?> = decrypter($("#csrf_token").val());
                    return datas;
                },
                processResults: function(response) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    return {
                        results: response.data,
                    };
                },
                cache: true,
            };
        }
        $(element).select2(setting_select);
        $(element).on('select2:open', function() {
            $(this).select2('focus');
        });
    }

    function load_approval() {
        $.ajax({
            url: "<?= base_url('leavetime/approvalview') ?>",
            data: {
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
                menuid: $("#mids").val(),
                hdid: $("#hdids").val(),
                accesscode: $("#accesscode").val()
            },
            type: 'post',
            dataType: 'json',
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                $("#tab-approval").html(res.view);
            },
        })
    }

    function load_files(noact = false) {
        $.ajax({
            url: '<?= base_url("datafile/view") ?>',
            type: 'post',
            dataType: 'json',
            data: {
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
                transtypeid: $("#trtyid").val(),
                transid: $("#data_ids").val(),
                reltype: $("#reltype").val(),
                invoice: $('#isinvoice').val(),
                btaction: noact,
            },
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken));
                $("#tab-file").html(res.view);
            }
        })
    }

    function loadButton(element) {
        let oldHtml = $(element).html();
        $(element).html(`<i class='bx bx-loader-alt bx-spin'></i>`);
        $(element).attr('disabled', true);
        return oldHtml;
    }

    function unloadButton(element, oldHtml) {
        $(element).removeAttr('disabled');
        $(element).html(oldHtml);
    }

    function formRequired(form) {
        var reqField = [];
        $(`${form} input, ${form} select, ${form} textarea`).each(function(i, e) {
            if ($(e).attr('required') !== undefined) {
                var fieldName = $(e).attr('fieldname');
                if ($(e).val() == null || $(e).val() == "" || $(e).val() == '[]') {
                    reqField.push(fieldName);
                }
            }
        })
        if (reqField.length === 0) {
            return 'nol';
        } else {
            var returnField = reqField.join(', ');
            return returnField;
        }
    }

    let FormsBuilder = {
        initSelect2: function(options = {}) {
            let selector = options.selector !== undefined ? options.selector : '[data-toggle="select2"]';

            let $el = $(this);
            if (this.select2 === undefined) $el = $(selector);

            let allowClear = options.allowClear !== undefined ? options.allowClear : true;
            let dropdownParent = options.dropdownParent !== undefined ? options.dropdownParent : null;

            let placeholder = options.placeholder !== undefined ? options.placeholder : 'Choose option';
            if ($el.data('placeholder') !== undefined) placeholder = $el.data('placeholder');

            let theme = options.theme != undefined ? options.theme : 'default';
            if ($el.data('theme') !== undefined) theme = $el.data('theme');

            let width = options.width !== undefined ? options.width : null;
            if ($el.data('width') !== undefined) width = $el.data('width');

            let config = {
                allowClear: allowClear,
                placeholder: placeholder,
                width: width,
                dropdownParent: dropdownParent,
                theme: theme,
            };

            let url = options.url !== undefined ? options.url : null;
            if ($el.data('url') !== undefined) $el.data('url');

            if (url !== null) {

                let cache = options.cache !== undefined ? options.cache : true;
                let processResults = options.processResults !== undefined ? options.processResults : (response) => {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                    return {
                        results: response.data
                    };
                };
                let data = (params) => {
                    params['searchTerm'] = params.term;
                    params['<?= csrf_token() ?>'] = decrypter($("#csrf_token").val());

                    if (options.data !== undefined &&
                        typeof options.data === 'function') return options.data(params);

                    return params;
                };


                config.ajax = {
                    url: url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    cache: cache,
                    processResults: processResults,
                };
            }

            $el.select2(config);

            $el.on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        },
        filterPills: function(options = {}, value = null) {
            let $elements = $(this);
            if (options.selector !== undefined) $elements = $(options.selector);

            if (options == 'data') {
                if ($elements.data('pillStatus') !== undefined) {
                    let currentOptions = $elements.data('pillStatus');

                    if (value == null) {
                        let children = $elements.children('.pill-label');

                        if (!(currentOptions.multiple ?? false)) {
                            let indexActive = children.get().findIndex((child) => $(child).hasClass('active'));

                            return $(children.get(indexActive)).data('value');
                        }

                        return children.filter((i, child) => $(child).hasClass('active'))
                            .map((i, child) => $(child).data('value'))
                            .get();
                    }
                }

                return;
            } else if (options == 'reInit') {
                if ($elements.data('pillStatus') !== undefined) {
                    return $elements.filterPills($elements.data('pillStatus'));
                }

                return;
            } else if (options == 'clear') {
                if ($elements.data('pillStatus') !== undefined) {
                    let currentOptions = $elements.data('pillStatus');

                    $elements.each((i, el) => {
                        let $el = $(el);
                        let children = $el.children('.pill-label');
                        children.removeClass('active');
                    });

                    $elements.filterPills('reInit');
                }

                return;
            }

            let showAll = options.showAll !== undefined ? options.showAll : true;
            let multiple = options.multiple !== undefined ? options.multiple : false;
            let url = options.url !== undefined ? options.url : null;
            let data = options.data !== undefined ? options.data : {};
            if (typeof data === 'function') data = data({});

            let onChange = options.onChange !== undefined ? options.onChange : () => {};
            let processResults = options.processResults !== undefined ? options.processResults : (res) => res;


            let $pillElement = $('<div>', {
                class: 'label label-md pill-label pill-primary'
            });
            let $skeletonElement = $('<div>').addClass('label pill-label margin-l-3 skeleton-box');
            let $allPill = $pillElement.clone()
                .data('value', 'all')
                .text('All');

            $elements.each((i, el) => {
                let $el = $(el);

                let children = $el.children('.pill-label');
                let indexActive = children.get().findIndex((child) => $(child).hasClass('active'));

                $el.empty();
                if (showAll) {

                    if (indexActive == -1) $allPill.addClass('active');

                    $el.append($allPill);
                }

                for (let i = 0; i < 3; i++) {
                    $el.append($skeletonElement.clone());
                }

                $.ajax({
                    url: url,
                    type: 'post',
                    data: $.extend(data, {
                        '<?= csrf_token() ?>': decrypter($("#csrf_token").val()),
                    }),
                    dataType: 'json',
                }).done(res => {
                    $el.find('.skeleton-box').remove();
                    processResults(res).forEach((status, i) => {

                        let $pillStatus = $pillElement.clone()
                            .addClass('margin-l-3')
                            .text(status.text)
                            .data('value', status.id);

                        let currentIndex = i;
                        if (showAll) currentIndex = i + 1;

                        if (currentIndex == indexActive)
                            $pillStatus.addClass('active');

                        if (status.count_results > 0)
                            $pillStatus.append(
                                $('<span>').addClass('pill-badge')
                                .text(status.count_results <= 99 ? status.count_results : '99+')
                            );

                        $pillStatus.on('click', () => {

                            if (multiple === false)
                                $el.find('.pill-label').removeClass('active');

                            if (multiple) {

                                if ($pillStatus.hasClass('active')) $pillStatus.removeClass('active');
                                else $pillStatus.addClass('active');

                                if (showAll) {
                                    let activeChildren = $el.find('.pill-label').filter('.active');

                                    if (activeChildren.length > 0) $allPill.removeClass('active');
                                    else $allPill.addClass('active');
                                }
                            } else $pillStatus.addClass('active');

                            onChange($pillStatus);
                        });

                        $el.append($pillStatus);
                    });
                }).fail((xhr) => {
                    $el.find('.skeleton-box').remove();

                    if (xhr.responseJSON !== undefined) {
                        if (xhr.responseJSON.pesan !== undefined) $el.html($('<small>', {
                            class: 'text-danger'
                        }).html('Invalid response from server'));
                        else $el.html(xhr.responseJSON.pesan);


                        if (xhr.responseJSON.redirect !== undefined) {
                            setTimeout(() => window.location.href = xhr.responseJSON.redirect, 1000);
                        }
                    } else $el.html($('<small>', {
                        class: 'text-danger'
                    }).html('Invalid response from server'));
                });

                $el.find('.pill-label').on('click', (e) => {

                    $el.find('.pill-label').removeClass('active');
                    $(e.currentTarget).addClass('active');

                    onChange($(e.currentTarget));
                });

                $el.data('pillStatus', options);
            });
        }
    };

    $.fn.initSelect2 = FormsBuilder.initSelect2;
    $.fn.filterPills = FormsBuilder.filterPills;

    let relElement = {
        modal: $('#modalrel'),
        modaltitle: $('#modalrel-title'),
        typerelease: $('#type-release'),
        confirm: $('#confirm-release'),
    };

    function onRelease(config) {
        relElement.modal.data('config', config);

        let url = config.url ?? null;
        let title = config.title ?? null;
        let type = config.type ?? null;
        let data = config.data ?? {};

        relElement.modal.modal('show');
        relElement.modaltitle.text(title);
        relElement.typerelease.text(type);
        relElement.modal.on('shown.bs.modal', () => {
            if (url === null) {
                relElement.modal.modal('hide');
                alert('Relase action url is not defined');
            }
        });
    }

    relElement.confirm.on('click', () => {
        let config = relElement.modal.data('config');

        if (config === undefined) return;

        let url = config.url ?? null;
        let title = config.title ?? null;
        let type = config.type ?? null;

        let onConfirm = config.onConfirm ?? null;
        let onConfirmSuccess = config.onConfirmSuccess ?? null;

        let btnHtml = relElement.confirm.html();
        relElement.confirm.attr('disabled', 'disabled');
        relElement.confirm.html('Processing ...');

        $.ajax({
            url: url,
            type: 'post',
            data: {
                <?= csrf_token() ?>: decrypter($('#csrf_token').val()),
            },
            dataType: 'json',
        }).done((response) => {
            $("#csrf_token").val(encrypter(response.csrfToken));
            relElement.confirm.html(btnHtml);
            relElement.confirm.removeAttr('disabled');

            let pesan = response.pesan;
            let notif = 'success'
            if (response.sukses != 1) {
                notif = 'error';
            }

            if (response.pesan != undefined) {
                pesan = response.pesan;
            }

            showNotif(notif, pesan);

            if (onConfirmSuccess !== null)
                onConfirmSuccess(response, element);
        });
    });

    function ucwords(string) {
        if (string != null && string != '') {
            return string.charAt(0).toUpperCase() + string.slice(1);
        } else {
            return '';
        }
    }

    function cutText(text, long) {
        if (text.length > long) {
            trunct = text.substr(0, long) + "...";
        } else {
            trunct = text;
        }
        return trunct;
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function addZeroFront(number, panjang) {
        return String(number).padStart(panjang, '0');
    }

    function generateTypeahead(input, url, hiddenId, width = '', datas = {}, editable = 'f') {
        if (width == '') {
            width = 'auto'
        }
        $(input).typeahead({
            items: 10,
            source: function(query, result) {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        searchTerm: query,
                        datas: datas,
                        <?= csrf_token() ?>: decrypter($('#csrf_token').val())
                    },
                    dataType: "json",
                    success: function(data) {
                        result($.map(data['data'], function(item) {
                            return {
                                id: item.id,
                                name: item.text,
                                stocktype: item.stocktype
                            };
                        }));
                        $('.typeahead-form .dropdown-menu').css('width', width);
                    }
                })
            },
            afterSelect: function(item) {
                if (datas.stocktype !== 'undefined') {
                    $(datas.stocktype).val(item.stocktype)
                    $(datas.stocktype).trigger('change')
                }
                $(hiddenId).val(item.id + '(-|-)' + item.name)
                $(hiddenId).trigger('change')
                if (editable == 'f') {
                    $(input).prop('readonly', true)
                }
                $(input + '-clear').css('display', 'block')
            }
        });
    }

    function resetTypeahead(element, input, inputid = '') {
        if (inputid != '') {
            $(inputid).val('')
        } else {
            $(input + '-id').val('')
        }
        $(input).prop('readonly', false)
        $(input).val('')
        $(element).css('display', 'none')
    }

    function clickLink(href, target) {
        window.open(href, target);
    }

    function setupTransdateInput() {
        $(".transaction-date").each(function() {
            $(this).attr("onblur", "validatePeriod(this)");
        });
    }

    function validatePeriod(elem) {
        let isi = $(elem).val();
        let elemtext = $(elem).data('errordateid');
        if (isi != null && isi != '') {
            $.ajax({
                url: '<?= base_url('generalledger/posting/checkclosing') ?>',
                type: 'post',
                dataType: 'json',
                data: {
                    tanggal: isi,
                    <?= csrf_token() ?>: decrypter($("#csrf_token").val())
                },
                success: function(res) {
                    $("#csrf_token").val(encrypter(res.csrfToken));
                    if (res.iserror == 'true') {
                        $(elem).closest('div.form-group').find('small.transaction-date-error').html(`Date <b>${res.tanggal}</b> is not valid. Periode ${res.periode} is closed`);
                        $(elem).val("")
                    } else {
                        $(elem).closest('div.form-group').find('small.transaction-date-error').html("");
                    }
                }
            })
        }
    }

    function triggerTransdate() {
        $(".transaction-date").trigger('blur');
    }

    function arraySum(arr) {
        let total = arr.reduce((accumulator, currentValue) => {
            return accumulator + currentValue
        }, 0);
        return total;
    }

    function sebutKoma(angka) {
        let huruf = ["nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
        let ledak = (angka + '').split('');
        let teks = " koma ";
        for (let y = 0; y < ledak.length; y++) {
            teks += huruf[ledak[y]] + " ";
        }
        return teks;
    }
</script>