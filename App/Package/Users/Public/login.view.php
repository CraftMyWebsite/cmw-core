<?php

use CMW\Controller\Core\SecurityController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Connexion');
Website::setDescription('Connectez-vous sur ' . Website::getWebsiteName());

/* @var \CMW\Interface\Users\IUsersOAuth[] $oAuths */

?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1 style="text-align: center">Connexion</h1>
<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">

    <form class="space-y-6" action="" method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <input hidden name="previousRoute" type="text" value="<?= $_SERVER['HTTP_REFERER'] ?>">
        <div>
            <label for="email">Mail</label>
            <input name="login_email" type="email" style="display: block; width: 100%" placeholder="mail@craftmywebsite.fr" required>
        </div>
        <div style="margin-top: 10px">
            <label for="password" >Mot de passe</label>
            <div style="display:flex;">
                <input type="password" name="login_password" id="passwordInput" placeholder="••••••••"
                       style="display: block; width: 100%" required>
                <div onclick="showPassword()">AFFICHER</div>
            </div>
        </div>
        <div style="display:flex; justify-content: space-between; margin-top: 10px">
            <div style="display: flex; align-items: center">
                <div class="flex items-center h-5">
                    <input id="login_keep_connect" name="login_keep_connect" type="checkbox" value=""
                           class="w-4 h-4 bg-gray-50 rounded border border-gray-300 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
                </div>
                <label for="login_keep_connect"
                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Se souvenir de
                    moi</label>
            </div>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login/forgot"
               class="text-sm text-blue-700 hover:underline dark:text-blue-500">Mot de passe oublié ?</a>
        </div>
        <?php SecurityController::getPublicData(); ?>
        <button type="submit" style="display: block; width: 100%; margin-top: 15px">
            Connexion
        </button>
    </form>
    <hr>
    <h5 style="text-align: center">Se connecter avec :</h5>
    <div style="display: flex; justify-content: center; gap: .8rem">
        <?php foreach ($oAuths as $oAuth): ?>
            <a href="oauth/<?= $oAuth->methodIdentifier() ?>" class="hover:text-blue-600"
               aria-label="<?= $oAuth->methodeName() ?>">
                <img src="<?= $oAuth->methodeIconLink() ?>"
                     alt="<?= $oAuth->methodeName() ?>" width="32" height="32"/>
            </a>
        <?php endforeach; ?>
    </div>
    <label class="block text-sm text-gray-900 mt-4">Pas encore de comtpe, <a
            href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>register" class="text-blue-500">s'enregistrer</a></label>
</section>


<script>
    function showPassword() {
        var x = document.getElementById("passwordInput");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>