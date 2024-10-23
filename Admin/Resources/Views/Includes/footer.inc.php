<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;

$isUpToDate = UpdatesManager::checkNewUpdateAvailable()

?>

</section>
<footer>
    <div class="md:flex justify-between mt-10">
        <p><?= LangManager::translate('core.footer.left') ?></p>
        <?php if ($isUpToDate): ?>
            <p class="text-center">
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/updates/cms">
                    <span><?= LangManager::translate('core.footer.upgrade') . "<span class='text-success font-bold'> " . UpdatesManager::getCmwLatest()['value'] ?></span>!
                </a>
            </p>
        <?php else: ?>
            <p>
                <?= LangManager::translate('core.footer.right') . ' ' . UpdatesManager::getVersion() ?>
            </p>
        <?php endif; ?>
    </div>
</footer>

<script>
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
    }

    const themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }
    });
</script>

<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Choices.js/choices.js"></script>
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/flowbite.js"></script>
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/cmw.js"></script>
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Fontawesome-picker/main.js"></script>
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Izitoast/iziToast.min.js"></script>