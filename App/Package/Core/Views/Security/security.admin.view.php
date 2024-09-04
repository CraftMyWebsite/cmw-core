<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.security.title");
$description = LangManager::translate("core.security.description");
/* @var string $currentCaptcha */
/* @var \CMW\Interface\Core\ICaptcha[] $availablesCaptcha */
?>

<div class="page-title">
    <h3><i class="fa-solid fa-shield-halved"></i> <?= LangManager::translate("core.security.title") ?></h3>
    <button class="btn-primary" form="captchaConfig"
            type="submit"><?= LangManager::translate("core.btn.save") ?></button>
</div>

<div class="center-flex">
    <div class="flex-content-lg space-y-3">
        <div class="card">
            <h6><?= LangManager::translate("core.security.captcha.title") ?></h6>
            <form id="captchaConfig" action="security/edit/captcha" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <select id="captcha" name="captcha" required
                        onchange="generateCaptchaInputs()">
                    <option value="none" <?= $currentCaptcha === "none" ? "selected" : "" ?>>
                        <?= LangManager::translate("core.security.no_captcha") ?>
                    </option>

                    <?php foreach ($availablesCaptcha as $captcha): ?>
                        <option
                            value="<?= $captcha->getCode() ?>" <?= $currentCaptcha === $captcha->getCode() ? "selected" : "" ?>>
                            <?= $captcha->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="security-content-wrapper" class="mt-3">
                    <?php foreach ($availablesCaptcha as $captcha): ?>
                        <?php $captcha->adminForm(); ?>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        <div class="card">
            <h6>Double authentification</h6>
            <p>Pour gérer la double authentification obligatoire rendez-vous dans les <a class="link"
                                                                                         href="users/settings">paramètres
                    utilisateur.</a></p>
        </div>
        <div class="card">
            <h6><?= LangManager::translate('core.security.healthReport.title') ?></h6>
            <?= LangManager::translate('core.security.healthReport.content') ?>
            <div class="flex justify-between">
                <a class="btn-danger" href="security/delete/report/health">
                    <?= LangManager::translate('core.btn.delete') ?>
                </a>
                <a class="btn-success " href="security/generate/report/health">
                    <?= LangManager::translate('core.btn.generate') ?>
                </a>
            </div>
        </div>
    </div>
</div>