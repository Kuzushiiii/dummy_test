<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <div style="width: 100%;display: flex;align-items: center;justify-content: center;flex-direction:column;">
        <h4>Login</h4>
        <div style="margin-top: 1rem;display: flex;flex-direction: column;">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="@ex: your username">
            <label for="password" style="margin-top: 0.5rem;">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••••••">
            <button style="margin-top: 1rem;text-align:center;width:100%" onclick="loginAuth()">
                Log In
            </button>
        </div>
    </div>
</body>

</html>
<script src="<?= base_url('/js/jquery.js') ?>"></script>
<script>
    function loginAuth() {
        let link = '<?= base_url('login/auth') ?>',
            username = $('#username').val(),
            password = $('#password').val();

        $.ajax({
            url: link,
            type: 'post',
            dataType: 'json',
            data: {
                username: username,
                password
            },
            success: function(res) {
                if (res.sukses == '1') {
                    alert(res.pesan);
                    $('#username').val("");
                    $('#password').val("");
                    window.location.href = res.link;
                } else {
                    alert(res.pesan);
                    $('#username').val("");
                    $('#password').val("");
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        })
    }
</script>