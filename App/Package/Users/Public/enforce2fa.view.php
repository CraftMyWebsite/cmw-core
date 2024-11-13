<?php

/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Double facteur obligatoire');
Website::setDescription("Merci d'activer le 2fa !");
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1 style="text-align: center">Veuillez activer le double facteur pour pouvoir vous connecter</h1>

<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">
    <div style="margin: auto; text-align: center">
        <img class="mx-auto" width="50%" src='<?= $user->get2Fa()->getQrCode(250) ?>'
             alt="QR Code double authentification">
        <span><?= $user->get2Fa()->get2FaSecretDecoded() ?></span>
    </div>
    <form
        action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/2fa/toggle"
        method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <input type="text" hidden="" name="enforce_mail" value="<?= $user->getMail() ?>">
        <div class="mt-2">
            <label for="secret">Code d'authentification</label>
            <input type="text" name="secret" id="secret" style="display: block; width: 100%" required>
        </div>
        <div style="margin-top: 20px">
            <button type="submit" style="display: block; width: 100%;">Activer</button>
        </div>
    </form>
</section>