<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Utils\Utils;

?>
</div>
<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p><?= LangManager::translate("core.footer.left") ?></p>
        </div>
        <?php if (UpdatesManager::checkNewUpdateAvailable()): ?>
            <div class="float-end">
                <p class="text-center">
                    <a href="/cmw-Admin/updates/cms">
                        <span><?= LangManager::translate("core.footer.used") . "<span class='text-danger font-bold'>" . UpdatesManager::getVersion() ?></span>!
                        <br>
                        <span><?= LangManager::translate("core.footer.upgrade") . "<span class='text-success font-bold'>" . UpdatesManager::getLatestVersion() ?></span>!
                    </a>
                </p>
            </div>
        <?php else: ?>
            <div class="float-end">
                <p>
                    <?= LangManager::translate("core.footer.right") . " " . UpdatesManager::getVersion() ?>
                </p>
            </div>
        <?php endif; ?>


    </div>
</footer>
<!--IMPORTANT : Fermetures des DIV de sidebar et contenue-->
</div>
</div>
</div>
<!--IMPORTANT : Fermetures des DIV de sidebar et contenue-->


<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/bootstrap.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/app.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/tooltip.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Choices.js/Public/Assets/Scripts/choices.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/Pages/form-element-select.js"></script>