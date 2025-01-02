<?php

use CMW\Controller\Core\SecurityController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle('Nouveau mot de passe');
Website::setDescription('Nouveau mot de passe');
?>

<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<h1 style="text-align: center">Nouveau mot de passe</h1>
<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">
    <form action="" method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <input hidden name="previousRoute" type="text" value="<?= $_SERVER['HTTP_REFERER'] ?>">
        <div style="margin-top: 10px">
            <label for="passwordInput" >Mot de passe</label>
            <div style="display:flex;">
                <input type="password" name="reset_password" id="passwordInput" placeholder="••••••••"
                       style="display: block; width: 100%" required>
                <div onclick="showPassword()">AFFICHER</div>
            </div>
        </div>
        <div style="margin-top: 10px">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Confirmation</label>
            <div style="display:flex;">
                <input id="passwordInputV" type="password" name="reset_password_verify"
                       placeholder="<?= LangManager::translate('users.users.pass') ?>"
                       style="display: block; width: 100%" required>
                <div onclick="showPasswordV()">AFFICHER</div>
            </div>
        </div>
        <?php SecurityController::getPublicData(); ?>
        <button type="submit" style="display: block; width: 100%; margin-top: 15px">
            Réinitialiser
        </button>
    </form>
</section>
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