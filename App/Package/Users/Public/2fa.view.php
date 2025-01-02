<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Double facteur');
Website::setDescription('Activer le double facteur');

?>
<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<h1 style="text-align: center">Code d'authentification</h1>

<section style="border: 1px #b4aaaa solid; border-radius: 9px; padding: .5rem; max-width: 50%; margin: auto">
    <form class="space-y-6"
          action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login/validate/tfa' ?>"
          method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div>
            <label for="code"></label>
            <input type="text" name="code" id="email" style="display:block; width: 100%;" required>
        </div>
        <div style="margin-top: 20px">
            <button type="submit" style="display: block; width: 100%;">Connexion</button>
        </div>
    </form>
</section>
</section>
