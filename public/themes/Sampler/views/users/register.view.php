<?php


use CMW\Controller\Core\SecurityController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Inscription";
$description = "Description de votre page"; ?>

<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-lg-8 col-xl-6 text-center">
                <h2 class="mt-0">Inscription</h2>
                <hr class="divider">
            </div>
        </div>
        <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
            <div class="col-lg-6">
                <form action="" method="post" class="mb-4">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="form-floating mb-3">
                        <input name="register_email" type="email" class="form-control" placeholder="<?= LangManager::translate("users.users.mail") ?>">
                        <label for="name">E-Mail</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="register_pseudo" type="text" class="form-control" placeholder="<?= LangManager::translate("users.users.pseudo") ?>">
                        <label for="name">Pseudo</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="register_password" class="form-control" placeholder="<?= LangManager::translate("users.users.pass") ?>">
                        <label for="email">Mot de passe</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="register_password_verify" class="form-control" placeholder="<?= LangManager::translate("users.users.repeat_pass") ?>">
                        <label for="email">Mot de passe</label>
                    </div>
                    <?php SecurityController::getPublicData(); ?>
                    <div class="d-grid"><button class="btn btn-primary btn-xl" type="submit"><?= LangManager::translate("users.login.register") ?></button></div>
                </form>
            </div>
        </div>
    </div>
</section>