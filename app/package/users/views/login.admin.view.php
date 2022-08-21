<?php use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("user.login.title", lineBreak: true);
$description = LangManager::translate("user.login.desc", lineBreak: true);

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/js/main.js"></script>';

?>
<?php $noBody = 1; ?>

    <body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo mb-4">
        <img src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/images/identity/logo_compact.png" alt="<?= LangManager::translate("core.alt.logo", lineBreak: true) ?>"
             width="100px">
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><?= LangManager::translate("user.login.title", lineBreak: true) ?></p>
            <form action="" method="post" class="mb-4">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <input name="login_email" type="email" class="form-control" placeholder="<?= LangManager::translate("user.login.mail", lineBreak: true) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3" id="showHidePassword">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="login_password" class="form-control"
                               placeholder="<?= LangManager::translate("user.login.password", lineBreak: true) ?>">
                        <div class="input-group-append">
                            <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="icheck-primary">
                            <input type="checkbox" id="login_keep_connect" name="login_keep_connect">
                            <label for="login_keep_connect"><?= LangManager::translate("user.login.remember", lineBreak: true) ?></label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block"><?= LangManager::translate("user.login.signin", lineBreak: true) ?></button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mb-1">
                <a href=""><?= LangManager::translate("user.login.lost_password", lineBreak: true) ?></a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>