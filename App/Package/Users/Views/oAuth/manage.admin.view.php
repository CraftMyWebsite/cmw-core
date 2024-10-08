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
    <div>
        <a type="button" class="btn-success" href="https://craftmywebsite.fr/docs/fr/users/oauth-connexion-externe/explication" target="_blank"><i class="fa-solid fa-up-right-from-square"></i> Explication</a>
        <button class="btn-primary" form="oauth-config" type="submit">
            <?= LangManager::translate('core.btn.save') ?>
        </button>
    </div>

</div>


<form method="post" id="oauth-config">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <h5><?= LangManager::translate('users.oauth.manage.enabled') ?> : </h5>
    <div class="grid-3">
        <?php foreach ($implementations as $implementation):
        if (UsersOAuthModel::getInstance()->isMethodEnabled($implementation->methodIdentifier())): ?>
        <div class="card">
            <div class="flex flex-row justify-between mb-2">
                <div class="flex flew-row">
                    <img alt="<?= $implementation->methodeName() ?>"
                         src="<?= $implementation->methodeIconLink() ?>"
                         height="40" width="40" class="mr-2">
                    <h5 style="align-self: center"><?= $implementation->methodeName() ?></h5>
                </div>

                <label class="toggle" style="align-self: center">
                    <p class="toggle-label"></p>
                    <input type="checkbox" name="oauth_enabled[<?= $implementation->methodIdentifier() ?>]"
                           class="toggle-input" value="1"
                        <?= UsersOAuthModel::getInstance()->isMethodEnabled($implementation->methodIdentifier()) ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <?php $implementation->adminForm() ?>
        </div>
        <?php endif;
endforeach; ?>
    </div>
    <hr>
    <h5 class="mt-4"><?= LangManager::translate('users.oauth.manage.disabled') ?> : </h5>
    <div class="grid-3">
        <?php
        foreach ($implementations as $implementation):
            if (!UsersOAuthModel::getInstance()->isMethodEnabled($implementation->methodIdentifier())):
                $methodId = $implementation->methodIdentifier(); ?>
                <div class="card">
                    <div class="flex flex-row justify-between mb-2">
                        <div class="flex flew-row">
                            <img alt="<?= $implementation->methodeName() ?>"
                                 src="<?= $implementation->methodeIconLink() ?>"
                                 height="40" width="40" class="mr-2">
                            <h5 style="align-self: center"><?= $implementation->methodeName() ?></h5>
                        </div>

                        <label class="toggle"  style="align-self: center">
                            <input id="toggle_<?= $methodId ?>" type="checkbox" name="oauth_enabled[<?= $implementation->methodIdentifier() ?>]"
                                   class="toggle-input" value="1"
                                <?= UsersOAuthModel::getInstance()->isMethodEnabled($implementation->methodIdentifier()) ? 'checked' : '' ?>>
                            <div class="toggle-slider"></div>
                        </label>
                    </div>
                    <div id="form_<?= $methodId ?>" style="display: <?= UsersOAuthModel::getInstance()->isMethodEnabled($methodId) ? 'block' : 'none' ?>;">
                        <?php $implementation->adminForm() ?>
                    </div>
                </div>
            <?php endif;
        endforeach;
        ?>
    </div>
</form>
<script>
    document.querySelectorAll('.toggle-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let methodId = this.id.replace('toggle_', ''); // Récupère l'identifiant de la méthode
            let form = document.getElementById('form_' + methodId); // Associe le formulaire correspondant

            // Si le checkbox est coché, on affiche le formulaire, sinon on le masque
            if (this.checked) {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    });
</script>
