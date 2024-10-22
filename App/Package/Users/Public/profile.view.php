<?php

/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Votre profil');
Website::setDescription('Éditez votre profil');
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1 style="text-align: center"><?= $user->getPseudo() ?></h1>

<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; margin: auto">
    <h5 style="text-align: center">Informations personnel</h5>
    <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'profile/update' ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Votre mail</label>
                <input type="email" name="email" id="email" style="display: block; width: 100%" value="<?= $user->getMail() ?>" required>
            </div>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Pseudo / Nom d'affichage</label>
                <input type="text" name="pseudo" id="pseudo" style="display: block; width: 100%" value="<?= $user->getPseudo() ?>" required>
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Mot de passe</label>
                <input type="password" name="password" id="password" placeholder="********" style="display: block; width: 100%" required>
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Confirmation</label>
                <input type="password" name="passwordVerif" id="passwordVerif" placeholder="********" style="display: block; width: 100%" required>
            </div>
        <div style="margin-top: 10px">
            <button type="submit" style="display: block; width: 100%">Appliquer les modifications</button>
        </div>
    </form>
</section>

<div style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-top: 20px">
    <div style="flex: 0 0 48%; border: solid 1px #b4aaaa; border-radius: 5px; padding: 9px;">
        <h5 style="text-align: center">
            <?php if ($user->get2Fa()->isEnabled()): ?>
                <span style="color: #188c1a;">Sécurité <i class="fa-solid fa-check"></i></span>
            <?php else: ?>
                <span style="color: #bc2015;">Sécurité <i class="fa-solid fa-triangle-exclamation"></i></span>
            <?php endif; ?>
        </h5>
        <?php if (!$user->get2Fa()->isEnabled()): ?>
            <p>Pour activer l'authentification à double facteur scannez le QR code dans une application d'authentification (GoogleAuthenticator, Aegis ...)</p>
        <?php endif; ?>
        <div>
            <div>
                <img height="50%" width="50%" src='<?= $user->get2Fa()->getQrCode(250) ?>'
                     alt="QR Code double authentification">
                <span><?= $user->get2Fa()->get2FaSecretDecoded() ?></span>
            </div>
            <form
                action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/2fa/toggle"
                method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="mt-2">
                    <label for="secret">Code d'authentification</label>
                    <input type="text" name="secret" id="secret" style="display: block; width: 100%" required>
                </div>
                <div class="text-center mt-2">
                    <button type="submit" style="display: block; width: 100%;"><?= $user->get2Fa()->isEnabled() ? 'Désactiver' : 'Activer' ?></button>
                </div>
            </form>
        </div>
    </div>
    <div style="flex: 0 0 48%; border: solid 1px #b4aaaa; border-radius: 5px; padding: 9px;">
        <h5 style="text-align: center">Identité visuelle</h5>
        <?php if (!is_null($user->getUserPicture()?->getImage())): ?>
        <div style="text-align: center; margin: auto; ">
            <img style="width: 50%" src="<?= $user->getUserPicture()->getImage() ?>" alt="Image de profil de <?= $user->getPseudo() ?>">
        </div>
        <?php endif; ?>
        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/update/picture" method="post" enctype="multipart/form-data">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <label >Changer votre image :</label>
            <input style="display: block; width: 100%" type="file" id="pictureProfile" name="pictureProfile" accept=".png, .jpg, .jpeg, .webp, .gif" required>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, WEBP, GIF (MAX. 400px400px).</p>
            <button style="display: block; width: 100%; margin-top: 10px" type="submit">Sauvegarder</button>
        </form>
    </div>
</div>

<div style="border: solid 1px #b4aaaa; border-radius: 5px; padding: 9px; margin-top: 20px">
    <h5>Vous nous quittez ?</h5>
    <p class="mb-2">Nous sommes triste de vous voir partir !</p>
    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/delete/<?= $user->getId() ?>" style="color: red">Supprimer mon compte</a>
</div>