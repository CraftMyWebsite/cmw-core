<?php

use CMW\Entity\Core\TermsEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\TermsModel;
use CMW\Type\Core\Enum\TermType;

/** @var TermType[] $types */
/** @var array<string,TermsEntity> $terms */

$title = LangManager::translate('core.config.title');
$description = LangManager::translate('core.config.desc');

function type_label(TermType $t): string {
    return $t->label();
}

// --- NEW: order mandatory first, then by label
$orderedTypes = $types;
usort($orderedTypes, function (TermType $a, TermType $b) {
    // mandatory first
    $cmp = ($b->isMandatory() <=> $a->isMandatory());
    if ($cmp !== 0) return $cmp;
    // then by label
    return strcmp($a->label(), $b->label());
});

$model = TermsModel::getInstance();
?>
<div class="page-title">
    <h3><i class="fa-solid fa-gavel"></i> <?= LangManager::translate('core.terms.title') ?></h3>
    <button form="terms" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<div class="alert-info mb-4">
    <p>Pour obliger les utilisateurs à accepter les conditions avant de créer un compte ou de se connecter, rendez-vous dans <a class="link" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>cmw-admin/users/settings/general" target="_blank">les paramètres des utilisateurs</a> et activez cette option.</p>
</div>

<form id="terms" action="" method="post" enctype="multipart/form-data" class="mt-4">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>

    <div class="grid-2">
        <?php foreach ($orderedTypes as $type):
            $key      = $type->value;
            /** @var \CMW\Entity\Core\TermsEntity|null $entity */
            $entity   = $terms[$key] ?? null;
            $content  = $entity?->getContent() ?? '';
            $slug     = $entity->getSlug() ?? '';
            $requires = $entity?->getRequireAccept() ?? true;
            $published= $entity?->getPublishedAt() ?? '—';
            $editor   = $entity?->getLastEditor()?->getPseudo() ?? '—';
            $hash     = hash('sha256', $content);
            $isActive = $type->isMandatory() ? true : $model->isTypeActive($type);
            ?>
            <div class="card">
                <div class="card-header">
                    <div class="flex justify-between align-center">
                        <h6><?= htmlspecialchars(type_label($type)) ?></h6>
                        <?php if ($isActive): ?><a class="btn-primary" target="_blank" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $slug ?>"><i class="fa-solid fa-eye"></i></a><?php endif;?>
                    </div>
                    <small>
                        <?= LangManager::translate('core.terms.last_published') ?> <?= htmlspecialchars($published) ?> ·
                        <?= LangManager::translate('core.terms.last_editor') ?> <?= htmlspecialchars($editor) ?>
                    </small>
                </div>

                <div class="mt-2">
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate('core.terms.requires_accept') ?></p>
                        <input class="toggle-input" type="checkbox"
                               name="term[<?= $key ?>][requires_accept]" value="1" <?= $requires ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>
                    <br>
                    <small><?= LangManager::translate('core.terms.requires_accept_explain' , ['type' => htmlspecialchars(type_label($type))]) ?></small>
                </div>

                <?php if ($type->isMandatory()): ?>
                    <div class="alert-warning">
                        <p><?= LangManager::translate('core.terms.mandatory') ?></p>
                    </div>
                <?php else: ?>
                    <label class="toggle">
                        <p class="toggle-label"><?= LangManager::translate('core.terms.use') ?></p>
                        <input class="toggle-input" type="checkbox"
                               name="term[<?= $key ?>][is_active]" value="1"
                            <?= $isActive ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                    </label>
                <?php endif; ?>

                <textarea class="tinymce"
                          name="term[<?= $key ?>][content]"><?= htmlspecialchars($content) ?></textarea>

                <input type="hidden" name="term[<?= $key ?>][current_hash]" value="<?= $hash ?>">
            </div>
        <?php endforeach; ?>
    </div>
</form>
