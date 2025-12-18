<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= getURL('css/template.css') ?>">
    <link rel="stylesheet" href="<?= getURL('css/login.css') ?>">
    <link rel="stylesheet" href="<?= getURL('css/boxicons.css') ?>">
    <link rel="stylesheet" href="<?= getURL('css/notyf.css') ?>">
</head>

<body>
    <div class="content">
        <div class="container-fluid sc-sm p-x">
            <div class="container-fluid sc-sm p-x">
                <div class="login-logo">
                </div>
                <div class="content-log">
                    <div class="head-log">
                        <h4>Login Now</h4>
                        <p>Log In to Continue Our App</p>
                    </div>
                    <div class="form-log">
                        <form id="form-login-sm" method="POST">
                            <div class="form-group">
                                <label>Username</label>
                                <div class="form-append">
                                    <i class="bx bx-at text-dark form-append-leading"></i>
                                    <input type="text" name="username-sm" id="username-sm" class="form-input uname" placeholder="ex: usernum811" autofocus>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 25px; margin-bottom: 70px">
                                <label>Password</label>
                                <div class="form-append">
                                    <i class="bx bx-lock text-dark form-append-leading"></i>
                                    <input type="password" name="password-sm" id="password-sm" class="form-input" placeholder="••••••••••••••">
                                    <i class="bx bx-hide text-dark form-append-trailing show-pass" show="n" show-size="sm"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary d-flex align-center justify-center" id="btn-login-sm">
                                    <i class="bx bx-door-open margin-r-2"></i>
                                    <span class="fw-normal fs-7">Login</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid sc-lg p-x login-bg">
            <div class="login-card">
                <label class="login-label">Login</label>
                <form id="form-login" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="form-append">
                            <i class="bx bx-at text-dark"></i>
                            <input type="text" name="username" id="username" class="form-input" style="padding-left: 0px;" placeholder="@ex: usernum811" autofocus>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 25px; margin-bottom: 70px">
                        <label>Password</label>
                        <div class="form-append">
                            <i class="bx bx-lock text-dark"></i>
                            <input type="password" name="password" id="password" class="form-input" style="padding-left: 0px;" placeholder="••••••••••••••">
                            <i class="bx bx-hide text-dark show-pass" show="n" show-size="lg"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary d-flex align-center justify-center" id="btn-login">
                            <i class="bx bx-door-open margin-r-2"></i>
                            <span class="fw-normal fs-7">Login</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="dflex align-center justify-center margin-t-3">
                <span class="fw-semibold fs-7 text-primary">Copyright © 2024 PT Hyperdata Solusindo Mandiri</span>
            </div>
        </div>
    </div>
    <input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">
</body>

</html>
<script src="<?= getURL('js/jquery.js') ?>"></script>
<script src="<?= getURL('js/bootstrap.js') ?>"></script>
<script src="<?= getURL('js/editor.js') ?>"></script>
<script src="<?= getURL('js/notyf.js') ?>"></script>
<script src="<?= getURL('js/script.js') ?>"></script>
<script>
    var notif = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        }
    });

    function toPage(url) {
        window.location.href = url;
    }

    function showPass(item, val, scsm) {
        $(item).removeClass();
        let vale = '',
            icn = '';
        if (val == 'y') {
            vale = 'n';
            icn = 'bx bx-hide';
            if (scsm == 'sm') {
                $(item).siblings('#password-sm').attr('type', 'password');
            } else {
                $(item).siblings('#password').attr('type', 'password');
            }
        } else {
            vale = 'y';
            icn = 'bx bx-show';
            if (scsm == 'sm') {
                $(item).siblings('#password-sm').attr('type', 'text');
            } else {
                $(item).siblings('#password').attr('type', 'text');
            }
        }
        $(item).attr('show', vale);
        $(item).addClass(`${icn} text-dark form-append-trailing show-pass`);
    }

    function showSuccess(msg) {
        notif.success(msg);
    }

    function showError(msg) {
        notif.error(msg);
    }

    function showNotif(type, msg) {
        notif.open({
            type: type,
            message: msg,
        });
    }

    function resetButton() {
        setTimeout(() => {
            $('#btn-login').removeAttr('disabled');
            $('#btn-login-sm').removeAttr('disabled');
            $('#btn-login').html(`
                <i class="bx bx-door-open margin-r-2"></i>
                <span class="fw-normal fs-7">Login</span>
            `)
            $('#btn-login-sm').html(`
                <i class="bx bx-door-open margin-r-2"></i>
                <span class="fw-normal fs-7">Login</span>
            `)
        }, 1000);
    }

    function clearForm() {
        $('#username').val("");
        $('#username-sm').val("");
        $('#password').val("");
        $('#password-sm').val("");
    }

    function login(link, username, password) {
        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                username: username,
                password: password,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val()),
            },
            success: function(res) {
                $("#csrf_token").val(encrypter(res.csrfToken))
                if (res.sukses == 1) {
                    showSuccess(res.pesan)
                    setTimeout(() => {
                        window.location.href = res.link
                    }, 100);
                } else {
                    showError(res.pesan)
                    $('#username').focus();
                    $('#username-sm').focus();
                }
            },
            error: function(xhr, ajaOptions, thrownError) {
                showError(thrownError)
            }
        })
        resetButton();
        clearForm();
    }

    // Prevent Triple Click Selection
    document.addEventListener('DOMContentLoaded', function() {
        let clickCount = 0;
        let clickTimer;

        document.body.addEventListener('click', function(event) {
            clickCount++;
            clearTimeout(clickTimer);

            clickTimer = setTimeout(function() {
                clickCount = 0;
            }, 400);

            if (clickCount === 3) {
                window.getSelection().removeAllRanges();
                clickCount = 0;
            }
        });
    });

    $(document).ready(function() {
        $('.show-pass').click(function() {
            var vlue = $(this).attr('show'),
                scsm = $(this).attr('show-size');
            showPass($(this), vlue, scsm);
        })
        // login submit
        $('#form-login').submit(function(e) {
            e.preventDefault();
            var spin = `
            <i class="bx bx-loader-alt bx-spin">
            `;
            $('#btn-login').html(spin);
            $('#btn-login').attr('disabled', true);
            $('#btn-log-in').html(spin);
            $('#btn-log-in').attr('disabled', true);
            var link = '<?= getURL('login/auth') ?>',
                username = $('#username').val(),
                password = $('#password').val();

            login(link, username, password);
        })
        $('#form-login-sm').submit(function(evt) {
            evt.preventDefault();
            var spin = `
            <i class="bx bx-loader-alt bx-spin">
            `;
            $('#btn-login-sm').html(spin);
            $('#btn-login-sm').attr('disabled', true);
            var link = '<?= getURL('login/auth') ?>',
                username = $('#username-sm').val(),
                password = $('#password-sm').val();

            login(link, username, password);
        })
    })
</script>