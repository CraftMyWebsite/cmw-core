<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Utils\Date;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate('core.Package.title');
$description = LangManager::translate('core.Package.desc');
?>

<h3><i class="fa-solid fa-puzzle-piece"></i> <?= LangManager::translate('core.Package.market') ?></h3>

<?php if (UpdatesManager::isTestAPI()):?>
    <h6 class="text-warning mb-2">Votre site est en mode test API.</h6>
<?php endif; ?>

<div class="grid-2">
    <?php foreach ($packagesList as $apiPackages): ?>
            <div class="card relative h-full" style="overflow: hidden;">
                <div class="flex justify-between">
                    <img class="rounded-lg" style="height: 140px; width: 140px;"
                         src="<?= $apiPackages['icon'] ?>"
                         alt="img">
                    <div class="pl-4 w-full">
                        <div class="flex justify-between">
                            <h6><?= ($apiPackages['version_status']) === 1 && UpdatesManager::isTestAPI() ? '<span class="text-warning">En attente : </span>' : '' ?><?= $apiPackages['name'] ?></h6>
                            <div>
                                <?php if ((float)$apiPackages['price'] === 0.0): ?>
                                    <?php if ($apiPackages['version_status'] === 1 && UpdatesManager::isTestAPI()): ?>
                                    <div class="flex gap-2">
                                        <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                            <input type="hidden" name="resId" value="<?= $apiPackages['id'] ?>">
                                            <input type="hidden" name="status" value="online">
                                            <button type="submit" class="btn-success-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                        </form>
                                        <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                            <input type="hidden" name="resId" value="<?= $apiPackages['id'] ?>">
                                            <input type="hidden" name="status" value="test">
                                            <button type="submit" class="btn-warning-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?> <?= $apiPackages['version_name'] ?></button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <div class="flex gap-2">
                                        <button data-modal-toggle="modal-<?= $apiPackages['id'] ?>" class="btn-primary-sm" type="button"><?= LangManager::translate('core.Package.details') ?></button>
                                        <form action="install" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                            <input type="hidden" name="resId" value="<?= $apiPackages['id'] ?>">
                                            <input type="hidden" name="status" value="online">
                                            <button type="submit" class="btn-success-sm"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($apiPackages['version_status'] === 1 && UpdatesManager::isTestAPI()): ?>
                                        <div class="flex gap-2">
                                            <button data-modal-toggle="modal-install-<?= $apiPackages['id'] ?>" class="btn-success-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                            <button data-modal-toggle="modal-install-<?= $apiPackages['id'] ?>-test" class="btn-warning-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?> <?= $apiPackages['version_name'] ?></button>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex gap-2">
                                            <button data-modal-toggle="modal-<?= $apiPackages['id'] ?>" class="btn-primary-sm" type="button"><?= LangManager::translate('core.Package.details') ?></button>
                                            <button data-modal-toggle="modal-install-<?= $apiPackages['id'] ?>" class="btn-success-sm" type="button"><i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?></button>
                                        </div>
                                    <?php endif; ?>
                                    <div id="modal-install-<?= $apiPackages['id'] ?>" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header">
                                                <h6>Installation de <?= $apiPackages['name'] ?></h6>
                                                <button type="button" data-modal-hide="modal-install-<?= $apiPackages['id'] ?>"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <form action="install" autocomplete="off" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                            <div class="modal-body">
                                                <p><?= $apiPackages['name'] ?> est un package payant, pour l'installer, vous devez l'avoir acheté</p>
                                                <p>Si vous l'avez déjà acheter rendez-vous sur <a target="_blank" class="link" href="https://craftmywebsite.fr/market/manage/purchases">craftmywebsite.fr</a> pour récupérer votre clé d'achat<br>
                                                    Assurez-vous d'avoir bien enregistré le domaine <b><?= $_SERVER['SERVER_NAME'] ?></b> pour l'activer ici !</p>
                                                <p>Si vous ne l'avez pas encore acheter vous pouvez le faire en vous rendant sur la <a class="link" target="_blank" href="https://craftmywebsite.fr/market/details/<?= $apiPackages['name'] ?>">page de l'article</a></p>
                                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                                <input type="hidden" name="resId" value="<?= $apiPackages['id'] ?>">
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
                                    <div id="modal-install-<?= $apiPackages['id'] ?>-test" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header">
                                                <h6>Installation de <?= $apiPackages['name'] ?></h6>
                                                <button type="button" data-modal-hide="modal-install-<?= $apiPackages['id'] ?>-test"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <form action="install" autocomplete="off" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                                <div class="modal-body">
                                                    <p><?= $apiPackages['name'] ?> est un package payant, pour l'installer, vous devez l'avoir acheté</p>
                                                    <p>Si vous l'avez déjà acheter rendez-vous sur <a target="_blank" class="link" href="https://craftmywebsite.fr/market/manage/purchases">craftmywebsite.fr</a> pour récupérer votre clé d'achat<br>
                                                        Assurez-vous d'avoir bien enregistré le domaine <b><?= $_SERVER['SERVER_NAME'] ?></b> pour l'activer ici !</p>
                                                    <p>Si vous ne l'avez pas encore acheter vous pouvez le faire en vous rendant sur la <a class="link" target="_blank" href="https://craftmywebsite.fr/market/details/<?= $apiPackages['name'] ?>">page de l'article</a></p>
                                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                                    <input type="hidden" name="resId" value="<?= $apiPackages['id'] ?>">
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
                        <div>
                            <p>
                                <?= mb_strimwidth($apiPackages['description_short'], 0, 280, '...') ?>
                            </p>
                            <p>
                                <?= LangManager::translate('core.Package.author') ?>
                                <a
                                    href="https://craftmywebsite.fr/market/user/<?= $apiPackages['author_pseudo'] ?>"
                                    target="_blank" class="link"><?= $apiPackages['author_pseudo'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="flex justify-between">
                    <p><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                            class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                            class="fa-regular fa-star"></i> (0)
                    </p>
                    <p><?= Date::formatDate($apiPackages['date_release']) ?></p>
                </div>
                <div class="flex justify-between">
                    <p>Téléchargé <b><?= $apiPackages['downloads'] ?></b> fois</p>
                    <p>Compatible avec <b><?= $apiPackages['version_cmw'] ?></b></p>
                </div>
            </div>
            <!--Details modal -->
            <div id="modal-<?= $apiPackages['id'] ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $apiPackages['name'] ?></h6>
                    </div>
                    <div class="modal-body">
                        <div class="flex justify-between">
                            <img class="rounded-lg bg-contain" style="height: 200px; width: 200px;"
                                 src="<?= $apiPackages['icon'] ?>"
                                 alt="img">
                            <div class="px-4 w-full">
                                <p><b><?= LangManager::translate('core.Package.description') ?></b></p>
                                <?= htmlspecialchars_decode($apiPackages['description']) ?>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.author') ?>
                                    <a
                                        href="https://craftmywebsite.fr/market/user/<?= $apiPackages['author_pseudo'] ?>"
                                        target="_blank" class="link"><?= $apiPackages['author_pseudo'] ?>
                                    </a>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.version') ?>
                                    <i><b><?= $apiPackages['version_name'] ?></b></i><br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $apiPackages['id'] ?>" type="button"
                                class="btn-primary"><?= LangManager::translate('core.Package.close') ?></button>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
</div>