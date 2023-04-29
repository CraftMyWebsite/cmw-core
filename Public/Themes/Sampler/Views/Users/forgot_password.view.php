<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

?>
<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg"><?= LangManager::translate("users.login.forgot_password.desc") ?></p>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="input-group mb-3">
                <input type="email" class="form-control" name="mail" placeholder="<?= LangManager::translate("users.users.mail") ?>">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block"><?= LangManager::translate("users.login.forgot_password.btn") ?></button>
                </div>

            </div>
        </form>
        <p class="mt-3 mb-1">
            <a href="/login"><?= LangManager::translate("users.login.signin") ?></a>
        </p>
        <p class="mb-0">
            <a href="/register" class="text-center"><?= LangManager::translate("users.login.register") ?></a>
        </p>
    </div>

</div>