<?php


use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = "Inscription";
$description = "Description de votre page"; ?>


<section>
    <div class="container">

        <form action="" method="post" class="mb-4">
            <?php (new SecurityService())->insertHiddenToken() ?>
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <input name="register_email" type="email" class="form-control"
                           placeholder="<?= LangManager::translate("users.users.mail") ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <input name="register_pseudo" type="text" class="form-control"
                           placeholder="<?= LangManager::translate("users.users.pseudo") ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-3" id="showHidePassword">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="register_password" class="form-control"
                           placeholder="<?= LangManager::translate("users.users.pass") ?>">
                    <div class="input-group-append">
                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group mb-3" id="showHidePassword">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="register_password_verify" class="form-control"
                           placeholder="<?= LangManager::translate("users.users.repeat_pass") ?>">
                    <div class="input-group-append">
                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <button type="submit"
                            class="btn btn-primary btn-block"><?= LangManager::translate("users.login.register") ?></button>
                </div>
            </div>
        </form>

    </div>
</section>
