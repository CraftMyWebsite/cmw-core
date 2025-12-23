<?php

use CMW\Controller\Core\PackageController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Utils\Website;

/* @var $themesList */

Website::setTitle(LangManager::translate('core.theme.config.title'));
Website::setDescription(LangManager::translate('core.theme.config.description'));
?>

<h3><i class="fa-solid fa-palette"></i> <?= LangManager::translate('core.theme.market') ?></h3>

<?php if (UpdatesManager::isTestAPI()):?>
    <h6 class="text-warning mb-2">Votre site est en mode test API.</h6>
<?php endif; ?>

<div class="grid-4">
    <?php foreach ($themesList as $theme): ?>
        <?php if (!ThemeLoader::getInstance()->isThemeInstalled($theme['name'])): ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= ($theme['version_status']) === 1 && UpdatesManager::isTestAPI() ? '<span class="text-warning">En attente : </span>' : '' ?><?= $theme['market_name'] ?></p>
                    <button data-modal-toggle="modal-<?= $theme['id'] ?>" class="btn-primary-sm" type="button"><?= LangManager::translate('core.theme.details') ?></button>
                </div>
                <div class="relative">
                    <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme['icon'] ?>"
                         alt="Icon <?= $theme['name'] ?>">
                </div>

                <div class="pb-2">
                    <?php if ((float)$theme['price'] === 0.0): ?>
                        <?php if ($theme['version_status'] === 1 && UpdatesManager::isTestAPI()): ?>
                            <div class="flex justify-center gap-2">
                                <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <input type="hidden" name="resId" value="<?= $theme['id'] ?>">
                                    <input type="hidden" name="status" value="online">
                                    <button type="submit" class="btn-success-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?></button>
                                </form>
                                <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <input type="hidden" name="resId" value="<?= $theme['id'] ?>">
                                    <input type="hidden" name="status" value="test">
                                    <button type="submit" class="btn-warning-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?> <?= $theme['version_name'] ?></button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center gap-2">
                                <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <input type="hidden" name="resId" value="<?= $theme['id'] ?>">
                                    <input type="hidden" name="status" value="online">
                                    <button type="submit" class="btn-success-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?></button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($theme['version_status'] === 1 && UpdatesManager::isTestAPI()): ?>
                            <div class="flex justify-center  gap-2">
                                <button data-modal-toggle="modal-install-<?= $theme['id'] ?>" class="btn-success-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?></button>
                                <button data-modal-toggle="modal-install-<?= $theme['id'] ?>-test" class="btn-warning-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?> <?= $apiPackages['version_name'] ?></button>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-center  gap-2">
                                <button data-modal-toggle="modal-install-<?= $theme['id'] ?>" class="btn-success-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.theme.install') ?></button>
                            </div>
                        <?php endif; ?>
                        <div id="modal-install-<?= $theme['id'] ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header">
                                    <h6>Installation de <?= $theme['market_name'] ?></h6>
                                    <button type="button" data-modal-hide="modal-install-<?= $theme['id'] ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <form action="install" autocomplete="off" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                    <div class="modal-body">
                                        <p><?= $theme['market_name'] ?> est un thème payant, pour l'installer, vous devez l'avoir acheté</p>
                                        <p>Si vous l'avez déjà acheter rendez-vous sur <a target="_blank" class="link" href="https://craftmywebsite.fr/market/manage/purchases">craftmywebsite.fr</a> pour récupérer votre clé d'achat<br>
                                            Assurez-vous d'avoir bien enregistré le domaine <b><?= $_SERVER['SERVER_NAME'] ?></b> pour l'activer ici !</p>
                                        <p>Si vous ne l'avez pas encore acheter vous pouvez le faire en vous rendant sur la <a class="link" target="_blank" href="https://craftmywebsite.fr/market/details/<?= $theme['name'] ?>">page de l'article</a></p>
                                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                        <input type="hidden" name="resId" value="<?= $theme['id'] ?>">
                                        <input type="hidden" name="status" value="online">
                                        <label for="input-group">Clé d'activation :</label>
                                        <div class="input-group">
                                            <i class="fa-solid fa-key"></i>
                                            <input type="text" id="input-group" name="activationKey" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn-success"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div id="modal-install-<?= $theme['id'] ?>-test" class="modal-container">
                            <div class="modal">
                                <div class="modal-header">
                                    <h6>Installation de <?= $theme['market_name'] ?></h6>
                                    <button type="button" data-modal-hide="modal-install-<?= $theme['id'] ?>-test"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <form action="install" autocomplete="off" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                    <div class="modal-body">
                                        <p><?= $theme['market_name'] ?> est un package payant, pour l'installer, vous devez l'avoir acheté</p>
                                        <p>Si vous l'avez déjà acheter rendez-vous sur <a target="_blank" class="link" href="https://craftmywebsite.fr/market/manage/purchases">craftmywebsite.fr</a> pour récupérer votre clé d'achat<br>
                                            Assurez-vous d'avoir bien enregistré le domaine <b><?= $_SERVER['SERVER_NAME'] ?></b> pour l'activer ici !</p>
                                        <p>Si vous ne l'avez pas encore acheter vous pouvez le faire en vous rendant sur la <a class="link" target="_blank" href="https://craftmywebsite.fr/market/details/<?= $theme['name'] ?>">page de l'article</a></p>
                                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                        <input type="hidden" name="resId" value="<?= $theme['id'] ?>">
                                        <input type="hidden" name="status" value="test">
                                        <label for="input-group">Clé d'activation :</label>
                                        <div class="input-group">
                                            <i class="fa-solid fa-key"></i>
                                            <input type="text" id="input-group" name="activationKey" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn-success"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-xl overflow-auto">
                    <div class="modal-header">
                        <h6><?= $theme['market_name'] ?></h6>
                    </div>
                    <div class="modal-body grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= $theme['icon'] ?>"
                                 alt="img <?= $theme['name'] ?>">
                            <div>
                                <p><?= PackageController::getInstance()->renderStars($theme['rate']) ?> <?= $theme['rate'] ? $theme['rate'] . '/5' : '' ?></p>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.author') ?>
                                    <a
                                        href="https://craftmywebsite.fr/market/user/<?= $theme['author_pseudo'] ?>"
                                        target="_blank" class="link"><?= $theme['author_pseudo'] ?>
                                    </a>
                                </p>
                                <p>
                                    <?= LangManager::translate('core.theme.downloads') ?>
                                    <i><b><?= PackageController::getInstance()->formatDownloads((int) $theme['downloads']) ?></b></i>
                                </p>
                                <p>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.themeVersion') ?>
                                    <i><b><?= $theme['version_name'] ?></b></i><br>
                                    <?= LangManager::translate('core.theme.CMWVersion') ?>
                                    <i><b><?= $theme['version_cmw'] ?></b></i>
                                </p>
                                <div class="flex gap-3">
                                    <?php if (isset($theme['demo'])): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['demo'] ?>" target="_blank"><i
                                                class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate('core.theme.demo') ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($theme['code_link']): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['code_link'] ?>" target="_blank"><i
                                                class="fa-brands fa-github"></i> GitHub</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate('core.theme.description') ?></b>
                            </p>
                            <?= htmlspecialchars_decode($theme['description']) ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme['id'] ?>" type="button" class="btn-danger">Fermer</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('button[type="submit"]');

        buttons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const form = this.closest('form');

                buttons.forEach(b => b.disabled = true);

                this.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Installation en cours';

                form.submit();
            });
        });
    });
</script>