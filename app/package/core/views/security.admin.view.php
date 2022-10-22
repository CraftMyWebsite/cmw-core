<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

$title = LangManager::translate("core.security.title");
$description = LangManager::translate("core.security.description");
/* @var string $captcha */
?>

<div class="content">

    <div class="container-fluid">
        <div class="row">

            <!-- Add new rewards -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("core.security.captcha.title") ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="security/edit/captcha" method="post">
                            <?php (new SecurityService())->insertHiddenToken() ?>

                            <label><?= LangManager::translate("core.security.captcha.type") ?></label>
                            <select id="captcha" name="captcha" class="form-control" required
                                    onclick="generateCaptchaInputs()">
                                <option value="captcha-none" <?= $captcha === "none" ? "selected" : "" ?>>
                                    Pas de catpcha
                                </option>
                                <option value="captcha-hcaptcha" <?= $captcha === "hcaptcha" ? "selected" : "" ?>>
                                    HCaptcha
                                </option>
                                <option value="captcha-recaptcha" <?= $captcha === "recaptcha" ? "selected" : "" ?>>
                                    RECaptcha
                                </option>
                            </select>

                            <!-- JS container -->
                            <div id="security-content-wrapper" class="mt-3"></div>

                            <input type="submit" value="Sauvegarder" class="btn btn-primary float-right">
                        </form>
                    </div>
                </div>

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
        divWrapper.setAttribute("class", "row");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-sm-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-sm-6");

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerText = "Site Key";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerText = "Secret Key";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '<?= Utils::getEnv()->getValue("HCAPTCHA_SITE_KEY") ?>');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_hcaptcha_site_key");
        inputSiteKey.setAttribute("class", "form-control");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '<?= Utils::getEnv()->getValue("HCAPTCHA_SECRET_KEY") ?>');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_hcaptcha_secret_key");
        inputSecretKey.setAttribute("class", "form-control");
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

    }

    const generateRecaptchaInputs = (parent = null) => {


        if (parent === null) {
            parent = document.getElementById("security-content-wrapper");
        }

        let divWrapper = document.createElement("div");
        divWrapper.setAttribute("class", "row");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-sm-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-sm-6");

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerText = "Site Key";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerText = "Secret Key";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '<?= Utils::getEnv()->getValue("RECAPTCHA_SITE_KEY") ?>');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_recaptcha_site_key");
        inputSiteKey.setAttribute("class", "form-control");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '<?= Utils::getEnv()->getValue("RECAPTCHA_SECRET_KEY") ?>');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_recaptcha_secret_key");
        inputSecretKey.setAttribute("class", "form-control");
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

    }
</script>