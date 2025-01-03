<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle('Mot de passe oublié');
Website::setDescription('Retrouvez votre mot de passe');
?>

<section class="page-section">
    <div class="container">

        <h1 class="text-center">Mot de passe oublié</h1>
        <form action="" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="input-group mb-3">
                <input type="email" class="form-control" name="mail"
                       placeholder="<?= LangManager::translate('users.users.mail') ?>">

            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit"
                            class="btn btn-primary btn-block"><?= LangManager::translate('users.login.forgot_password.btn') ?></button>
                </div>

            </div>
        </form>
    </div>

</section>
