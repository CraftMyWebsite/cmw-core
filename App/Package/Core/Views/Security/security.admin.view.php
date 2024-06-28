<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.security.title");
$description = LangManager::translate("core.security.description");
/* @var string $captcha */
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
                        onclick="generateCaptchaInputs()">
                    <option value="captcha-none" <?= $captcha === "none" ? "selected" : "" ?>>
                        <?= LangManager::translate("core.security.no_captcha") ?>
                    </option>
                    <option value="captcha-hcaptcha" <?= $captcha === "hcaptcha" ? "selected" : "" ?>>
                        HCaptcha
                    </option>
                    <option value="captcha-recaptcha" <?= $captcha === "recaptcha" ? "selected" : "" ?>>
                        RECaptcha
                    </option>
                </select>
                <div id="security-content-wrapper" class="mt-3"></div>
            </form>
        </div>
        <div class="card">
            <h6>Double authentification</h6>
            <p>Pour gérer la double authentification obligatoire rendez-vous dans les <a class="link" href="users/settings">paramètres utilisateur.</a></p>
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

<script>

    const generateHcaptchaInputs = (parent = null) => {
        if (parent === null) {
            parent = document.getElementById("security-content-wrapper");
        }

        let divWrapper = document.createElement("div");
        divWrapper.setAttribute("class", "grid-2");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-md-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-md-6");

        let divInfoCaptcha = document.createElement("p");
        divInfoCaptcha.innerHTML += '<?= LangManager::translate('core.security.free_key') ?> <a class="link" href="https://www.hcaptcha.com/" target="_blank">https://www.hcaptcha.com/</a>';

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerHTML += "<label>Site Key :</label>";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerHTML += "<label>Secret Key :</label>";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '<?= EnvManager::getInstance()->getValue("HCAPTCHA_SITE_KEY") ?>');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_hcaptcha_site_key");
        inputSiteKey.setAttribute("class", "input");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '<?= EnvManager::getInstance()->getValue("HCAPTCHA_SECRET_KEY") ?>');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_hcaptcha_secret_key");
        inputSecretKey.setAttribute("class", "input");
        inputSecretKey.setAttribute("required", "true");


        parent.append(divWrapper);

        divWrapper.append(divPrepend);
        divPrepend.append(divFormGroupSiteKey);
        divFormGroupSiteKey.append(labelSiteKey);
        divFormGroupSiteKey.append(inputSiteKey);

        divWrapper.append(divPrepend2);
        divPrepend2.append(divFormGroupSecretKey);
        divFormGroupSecretKey.append(labelSecreteKey);
        divFormGroupSecretKey.append(inputSecretKey);

        parent.append(divInfoCaptcha);

    }

    const generateRecaptchaInputs = (parent = null) => {


        if (parent === null) {
            parent = document.getElementById("security-content-wrapper");
        }

        let divWrapper = document.createElement("div");
        divWrapper.setAttribute("class", "grid-2");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-md-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-md-6");

        let divInfoCaptcha = document.createElement("p");
        divInfoCaptcha.innerHTML += '<?= LangManager::translate('core.security.free_key') ?> <a class="link" href="https://www.google.com/recaptcha/" target="_blank">https://www.google.com/recaptcha/</a>';

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerHTML += "<label>Site Key :</label>";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerHTML += "<label>Secret Key :</label>";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '<?= EnvManager::getInstance()->getValue("RECAPTCHA_SITE_KEY") ?>');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_recaptcha_site_key");
        inputSiteKey.setAttribute("class", "input");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '<?= EnvManager::getInstance()->getValue("RECAPTCHA_SECRET_KEY") ?>');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_recaptcha_secret_key");
        inputSecretKey.setAttribute("class", "input");
        inputSecretKey.setAttribute("required", "true");


        parent.append(divWrapper);

        divWrapper.append(divPrepend);
        divPrepend.append(divFormGroupSiteKey);
        divFormGroupSiteKey.append(labelSiteKey);
        divFormGroupSiteKey.append(inputSiteKey);

        divWrapper.append(divPrepend2);
        divPrepend2.append(divFormGroupSecretKey);
        divFormGroupSecretKey.append(labelSecreteKey);
        divFormGroupSecretKey.append(inputSecretKey);

        parent.append(divInfoCaptcha);

    }
</script>