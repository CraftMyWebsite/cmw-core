<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\UsersOAuthModel;

$title = LangManager::translate('users.oauth.manage.title');
$description = LangManager::translate('users.oauth.manage.description');

/* @var \CMW\Interface\Users\IUsersOAuth[] $implementations */

?>

<div class="page-title">
    <h3>
        <i class="fa-solid fa-shield-halved"></i> <?= LangManager::translate('users.oauth.manage.title') ?>
    </h3>
</div>

<div class="center-flex">
    <div class="flex-content-lg space-y-3">
        <div class="card">
            <div class="flex flex-row justify-between">
                <h6><?= LangManager::translate('users.oauth.manage.subtitle') ?> </h6>
                <button class="btn-primary" form="oauth-config" type="submit">
                    <?= LangManager::translate('core.btn.save') ?>
                </button>
            </div>
            <form method="post" id="oauth-config">
                <?php (new SecurityManager())->insertHiddenToken() ?>

                <?php foreach ($implementations as $implementation): ?>
                    <hr>
                    <div>
                        <div class="flex flex-row justify-between mb-2">
                            <div class="flex flew-row">
                                <img alt="<?= $implementation->methodeName() ?>"
                                     src="<?= $implementation->methodeIconLink() ?>"
                                     height="48" width="48">
                                <h5 style="align-self: center"><?= $implementation->methodeName() ?></h5>
                            </div>

                            <label class="toggle" style="align-self: center">
                                <p class="toggle-label">Activer</p>
                                <input type="checkbox" name="oauth_enabled[<?= $implementation->methodIdentifier() ?>]"
                                       class="toggle-input" value="1"
                                    <?= UsersOAuthModel::getInstance()->isMethodEnabled($implementation->methodIdentifier()) ? 'checked' : '' ?>>
                                <div class="toggle-slider"></div>
                            </label>
                        </div>

                        <?php $implementation->adminForm() ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </form>
        </div>
    </div>
</div>
