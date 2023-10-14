<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;

$title = "Connexion - 2Fa";
$description = "Description de votre page";
?>

<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-lg-8 col-xl-6 text-center">
                <h2 class="mt-0">Double facteur</h2>
                <hr class="divider">
            </div>
        </div>
        <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
            <div class="col-lg-6">
                <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login/validate/tfa' ?>"
                      method="post" class="mb-4">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="form-floating mb-3">
                        <input class="form-control" name="code" type="text" placeholder="123456" maxlength="7" required>
                        <label for="name">Code d'authentification</label>
                    </div>
                    <div class="d-grid">
                        <button style="background: <?= ThemeModel::fetchConfigValue('buttonColor') ?>"
                                class="btn btn-xl" type="submit">
                            Connexion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>