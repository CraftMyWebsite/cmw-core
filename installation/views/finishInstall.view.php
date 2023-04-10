<?php

use CMW\Controller\Installer\InstallerController;
use CMW\Utils\Utils;

?>
<div class="card-body text-center">
    <p class="text-[140px]">🎉</p>
    <p class=" text-4xl lg:text-7xl">Félicitations !</p>
    <p>Votre site est maintenant prêt ! <br>Rendez-vous dessus pour commencer sa configuration.</p>
    <div class="card-actions justify-end mt-16">
        <a href="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>installer/finish" class="btn btn-primary">Aller sur mon site</a>
    </div>
</div>
