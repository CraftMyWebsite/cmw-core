<?php

use CMW\Entity\Users\BlacklistedPseudoEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var BlacklistedPseudoEntity[] $pseudos */

$title = LangManager::translate('users.settings.title');
$description = LangManager::translate('users.settings.desc');
?>

<h3>
    <i class="fa-solid fa-gears"></i> <?= LangManager::translate('users.settings.title') ?>
    - <?= LangManager::translate('users.pages.settings.blacklist.menu') ?>
</h3>

<div class="space-y-4 mt-6">
    <div class="card">
        <div class="card-title">
            <h6><?= LangManager::translate('users.blacklist.title') ?></h6>
            <button type="button" class="btn-danger btn-mass-delete loading-btn" data-loading-btn="Chargement"
                    data-target-table="1">
                <?= LangManager::translate('core.btn.mass_delete') ?>
            </button>
        </div>
        <div class="table-container">
            <table class="table-checkeable" data-form-action="pseudo/delete/bulk" id="table1">
                <thead>
                <tr>
                    <th class="mass-selector"></th>
                    <th><?= LangManager::translate('users.blacklist.table.pseudo') ?></th>
                    <th><?= LangManager::translate('users.blacklist.table.date') ?></th>
                    <th class="text-center">
                        <?= LangManager::translate('users.blacklist.table.action') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pseudos as $pseudo): ?>
                    <tr>
                        <td class="item-selector" data-value="<?= $pseudo->getId() ?>"></td>
                        <td><?= $pseudo->getPseudo() ?></td>
                        <td><?= $pseudo->getDateBlacklistedFormatted() ?></td>
                        <td class="text-center space-x-2">
                            <button data-modal-toggle="modal-edit-<?= $pseudo->getId() ?>" class="text-info"
                                    type="button">
                                <i class="fa-solid fa-gears"></i>
                            </button>
                            <button data-modal-toggle="modal-<?= $pseudo->getId() ?>" class="text-danger" type="button">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>

                        <!--MODAL DELETE-->
                        <div id="modal-<?= $pseudo->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6><?= LangManager::translate('users.blacklist.delete.title') ?><?= $pseudo->getPseudo() ?></h6>
                                    <button type="button" data-modal-hide="modal-<?= $pseudo->getId() ?>"><i
                                            class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate('users.blacklist.delete.content') ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="pseudo/delete/<?= $pseudo->getId() ?>" type="button" class="btn-danger">
                                        <?= LangManager::translate('core.btn.delete') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--MODAL - EDIT-->
                        <div id="modal-edit-<?= $pseudo->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header">
                                    <h6><?= LangManager::translate('users.blacklist.edit.title') ?><?= $pseudo->getPseudo() ?></h6>
                                    <button type="button" data-modal-hide="modal-edit-<?= $pseudo->getId() ?>">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <form action="pseudo/edit/<?= $pseudo->getId() ?>" method="post">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <div class="modal-body">
                                        <div class="input-group">
                                            <i class="fas fa-user"></i>
                                            <input type="text" id="pseudo" name="pseudo"
                                                   value="<?= $pseudo->getPseudo() ?>" placeholder="BadUserName"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn-primary">
                                            <?= LangManager::translate('core.btn.edit') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h6><?= LangManager::translate('users.settings.blacklisted.pseudo.title') ?></h6>
        <form method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="pseudo" name="pseudo" placeholder="BadUserName" required>
            </div>
            <button type="submit" class="btn-primary btn-center">
                <?= LangManager::translate('users.settings.blacklisted.pseudo.btn') ?>
            </button>
        </form>
    </div>

</div>