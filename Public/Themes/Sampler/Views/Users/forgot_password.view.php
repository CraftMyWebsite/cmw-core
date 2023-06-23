<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
$title = "Mot de passe oublié";
$description = "C'est pas bien";
?>

<section class="page-section">
    <div class="container">

            <h1 class="text-center">Mot de passe oublié</h1>
            <form action="" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="mail" placeholder="<?= LangManager::translate("users.users.mail") ?>">

                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block"><?= LangManager::translate("users.login.forgot_password.btn") ?></button>
                    </div>

                </div>
            </form>
        </div>

</section>
