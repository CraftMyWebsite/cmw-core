<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

global $_UTILS;
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= INSTALL_ADMIN_TITLE ?></h3>
    </div>
    <!-- form start -->
    <form action="" role="form" method="post" id="formThirdInstall" name="formThirdInstall">
        <div class="card-body">
            <div class="form-group">
                <label for="email"><?= INSTALL_ADMIN_EMAIL ?></label>
                <input type="email" name="email" class="form-control" id="email"
                       placeholder="example@craftmywebsite.fr"
                       autocomplete="email"
                       required>
            </div>
            <div class="form-group">
                <label for="username"><?= INSTALL_ADMIN_USERNAME ?></label>
                <input type="text" name="username" class="form-control" id="username"
                       placeholder="Pseudo"
                       autocomplete="username"
                       required>
            </div>

            <div class="form-group">
                <label for="password"><?= INSTALL_ADMIN_PASS ?></label>
                <div class="input-group" id="showHidePassword">
                    <input type="password" name="password" class="form-control" id="password"
                           placeholder="********"
                           autocomplete="current-password"
                           required>
                    <div class="input-group-append">
                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash"
                                                                aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" name="submitThirdInstall" id="submitThirdInstall"
                    class="btn btn-primary"><?= INSTALL_SAVE ?>
            </button>
        </div>
    </form>
</div>

<script>

    const formSubmit = document.getElementById("submitThirdInstall"),
          formRaw    = document.getElementById("formThirdInstall");

    formRaw.onsubmit = e => {
        e.preventDefault();

        const formData   = new FormData(formRaw);

        fetch(`${window.location.pathname}/submitThirdInstall`, {
            method: "post",
            body  : formData,
        }).then(v => v.text())
            .then(res => {
                if (+res > 0) {
                    console.log("ok")
                    window.location.reload();
                } else {
                    //Todo, Alert system
                    console.log(`ERROR CODE : ${res}`)
                }

            })
    }

</script>