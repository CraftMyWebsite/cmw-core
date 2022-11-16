<?php


use CMW\Controller\Core\SecurityController;
use CMW\Utils\SecurityService;

$title = "Connexion";
$description = "Description de votre page"; ?>
<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-lg-8 col-xl-6 text-center">
                <h2 class="mt-0">Connexion</h2>
                <hr class="divider">
            </div>
        </div>
        <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
            <div class="col-lg-6">
                <form action="" method="post" class="mb-4">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="login_email" type="email" placeholder="Votre mail" required>
                        <label for="name">E-Mail</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" name="login_password" placeholder="****" required>
                        <label for="email">Mot de passe</label>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="icheck-primary">
                            <input type="checkbox" id="login_keep_connect" name="login_keep_connect">
                            <label for="login_keep_connect">Rester connecter</label>
                        </div>
                    </div>
                    <?php SecurityController::getPublicData(); ?>
                    <div class="d-grid"><button class="btn btn-primary btn-xl" type="submit">Connexion</button></div>
                </form>
            </div>
        </div>
    </div>
</section>