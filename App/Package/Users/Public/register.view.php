<?php

use CMW\Controller\Core\SecurityController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Inscription');
Website::setDescription('Inscrivez-vous');

/* @var \CMW\Interface\Users\IUsersOAuth[] $oAuths */
?>

<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1 style="text-align: center">Inscription</h1>
<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">
    <form class="space-y-6" action="" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <input hidden name="previousRoute" type="text" value="<?= $_SERVER['HTTP_REFERER'] ?>">
        <div>
            <label for="email">Mail</label>
            <input name="register_email" type="email" style="display: block; width: 100%" placeholder="mail@craftmywebsite.fr" required>
        </div>
        <div style="margin-top: 10px">
            <label for="email">Pseudo / Nom d'affichage</label>
            <input name="register_pseudo" type="text" style="display: block; width: 100%" placeholder="<?= LangManager::translate('users.users.pseudo') ?>" required>
        </div>
        <div style="margin-top: 10px">
            <label for="passwordInput" >Mot de passe</label>
            <div style="display:flex;">
                <input type="password" name="register_password" id="passwordInput" placeholder="••••••••"
                       style="display: block; width: 100%" required>
                <div onclick="showPassword()">AFFICHER</div>
            </div>
        </div>
        <div style="margin-top: 10px">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Confirmation</label>
            <div style="display:flex;">
                <input id="passwordInputV" type="password" name="register_password_verify"
                       placeholder="<?= LangManager::translate('users.users.pass') ?>"
                       style="display: block; width: 100%" required>
                <div onclick="showPasswordV()">AFFICHER</div>
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
        </div>
        <?php SecurityController::getPublicData(); ?>
        <button type="submit" style="display: block; width: 100%; margin-top: 15px">
            M'inscrire
        </button>
    </form>
    <hr>
    <h5 style="text-align: center">M'enregistrer avec :</h5>
    <div style="display: flex; justify-content: center; gap: .8rem">
        <?php foreach ($oAuths as $oAuth): ?>
            <a href="oauth/<?= $oAuth->methodIdentifier() ?>" class="hover:text-blue-600"
               aria-label="<?= $oAuth->methodeName() ?>">
                <img src="<?= $oAuth->methodeIconLink() ?>"
                     alt="<?= $oAuth->methodeName() ?>" width="32" height="32"/>
            </a>
        <?php endforeach; ?>
    </div>
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

    function showPasswordV() {
        var x = document.getElementById("passwordInputV");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>