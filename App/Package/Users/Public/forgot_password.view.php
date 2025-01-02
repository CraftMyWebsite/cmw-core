<?php

use CMW\Controller\Core\SecurityController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* TITRE ET DESCRIPTION */
Website::setTitle('Mot de passe oublié');
Website::setDescription("C'est pas très bien d'oublié son mot de passe ...");
?>
<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<h1 style="text-align: center">Mot de passe oublié</h1>
<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">
    <form class="space-y-6" action="" method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div>
            <label for="email" >Mail</label>
            <input name="mail" id="email" type="email" style="display: block; width: 100%" placeholder="mail@craftmywebsite.fr" required>
        </div>
        <div style="display:flex; justify-content: space-between">
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>register" class="text-sm text-blue-700 hover:underline dark:text-blue-500">S'inscrire</a>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login" class="text-sm text-blue-700 hover:underline dark:text-blue-500">Connexion</a>
        </div>
        <?php SecurityController::getPublicData(); ?>
        <button type="submit" style="display: block; width: 100%; margin-top: 10px">Envoyer</button>
    </form>
</section>
</section>