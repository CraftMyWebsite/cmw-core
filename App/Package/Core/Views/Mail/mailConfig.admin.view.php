<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = LangManager::translate('core.mail.config.title');
$description = LangManager::translate('core.mail.config.description');

/* @var \CMW\Entity\Core\MailConfigEntity $config */
/* @var \CMW\Interface\Core\IMailTemplate[] $mailTemplates */

?>
<style>
    .radio-group label {
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .radio-group input[type="radio"] {
        display: none;
    }

    .radio-group img {
        width: 100%;
        height: 100%; /* Force la hauteur à être égale dans chaque case */
        object-fit: contain;
        aspect-ratio: 16/9; /* Ajuste la hauteur selon besoin */
        border: 2px solid #afabab; /* Couleur de sélection */
        transition: 0.3s;
    }

    .radio-group input[type="radio"]:checked + img {
        border: 2px solid #E63A5C; /* Couleur de sélection */
        box-shadow: 0 0 10px rgba(230, 58, 92, 0.5);
        transform: scale(1.05);
    }

</style>

<div class="page-title">
    <h3><i class="fa-solid fa-envelope"></i> SMTP & Mailing</h3>
    <button form="smtpConfig" type="submit" id="submitButton" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="smtpConfig" action="" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="card">
            <div>
                <label class="toggle">
                    <h6 class="toggle-label">SMTP</h6>
                    <input type="checkbox" class="toggle-input" id="enableSMTP" name="enableSMTP"
                           value="<?= $config?->isEnable() ?>" <?= $config?->isEnable() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div class="grid-2">
                <div>
                    <label for="mail"><?= LangManager::translate('core.mail.config.senderMail') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-at"></i>
                        <input type="text" id="mail" name="mail"
                               value="<?= $config?->getMail() ?>"
                               placeholder="contact@monsite.fr" required>
                    </div>
                </div>
                <div>
                    <label for="mailReply"><?= LangManager::translate('core.mail.config.replyMail') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-at"></i>
                        <input type="text" id="mailReply" name="mailReply" value="<?= $config?->getMailReply() ?>"
                               placeholder="reply@monsite.fr" required>
                    </div>
                </div>
            </div>
            <div class="grid-4">
                <div>
                    <label for="addressSMTP"><?= LangManager::translate('core.mail.config.serverSMTP') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-server"></i>
                        <input type="text" id="addressSMTP" name="addressSMTP"
                               value="<?= $config?->getAddressSMTP() ?>" placeholder="smtp.google.com" required>
                    </div>
                </div>
                <div>
                    <label for="port"><?= LangManager::translate('core.mail.config.portSMTP') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-network-wired"></i>
                        <input type="number" id="port" name="port"
                               value="<?= $config?->getPort() ?>" placeholder="587" required>
                    </div>
                </div>
                <div>
                    <label for="user"><?= LangManager::translate('core.mail.config.userSMTP') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="user" name="user"
                               value="<?= $config?->getUser() ?>" placeholder="admin@monsite.fr" required>
                    </div>
                </div>
                <div>
                    <label for="password"><?= LangManager::translate('core.mail.config.passwordSMTP') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password"
                               value="<?= $config?->getPassword() ?>" placeholder="••••" required>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <label><?= LangManager::translate('core.mail.config.protocol') ?></label>
                    <div class="flex items-center gap-2">
                        <input id="flexRadioDefault1" class="form-check-input" type="radio" name="protocol"
                               value="tls" <?= $config?->getProtocol() === 'tls' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="flexRadioDefault1">TLS</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="flexRadioDefault2" type="radio" value="ssl"
                               name="protocol" <?= $config?->getProtocol() === 'ssl' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="flexRadioDefault2">SSL</label>
                    </div>
                </div>
                <button data-modal-toggle="modal" class="btn-warning h-fit"
                        type="button"><?= LangManager::translate('core.mail.config.test.btn') ?></button>
            </div>
        </div>

    <div class="grid-2 mt-8">
        <div class="card">
            <div class="card-title">
                <h6><?= LangManager::translate('core.mail.editor.title') ?></h6>
                <div>
                    <button data-modal-toggle="modal-template" class="btn-primary" type="button"><?= LangManager::translate('core.mail.editor.select') ?></button>
                    <button data-modal-toggle="modal-help" class="btn-primary" type="button"><i class="fa-solid fa-circle-question"></i></button>
                </div>
                <!--MODAL-TEMPLATE-->
                <div id="modal-template" class="modal-container">
                    <div class="modal-lg">
                        <div class="modal-header">
                            <h6><?= LangManager::translate('core.mail.editor.select') ?></h6>
                            <button type="button" data-modal-hide="modal-template"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert-warning">
                                <i class="fa-solid fa-triangle-exclamation"></i> <?= LangManager::translate('core.mail.editor.select_alert') ?>
                            </div>
                            <div class="radio-group grid-2 gap-8 mt-2">
                                <?php foreach ($mailTemplates as $mailTemplate): ?>
                                    <label>
                                        <?= $mailTemplate->getName() ?>
                                        <input data-code="<?= htmlspecialchars($mailTemplate->getCode(), ENT_QUOTES, 'UTF-8') ?>" type="radio" name="preview" value="<?= $mailTemplate->getVarName() ?>">
                                        <img src="<?= $mailTemplate->getPreviewImg() ?>" alt="Preview - <?= $mailTemplate->getName() ?>">
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button data-modal-hide="modal-template" id="applyTemplate" type="button" class="btn-primary"><?= LangManager::translate('core.mail.editor.apply_btn') ?></button>
                            <button data-modal-hide="modal-template" type="button" class="btn-danger"><?= LangManager::translate('core.btn.close') ?></button>
                        </div>
                    </div>
                </div>
                <!--MODAL-HELP-->
                <div id="modal-help" class="modal-container">
                    <div class="modal">
                        <div class="modal-header">
                            <h6><?= LangManager::translate('core.mail.editor.modal_title') ?></h6>
                            <button type="button" data-modal-hide="modal-help"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <p><?= LangManager::translate('core.mail.editor.modal_text_1') ?></p>
                            <p><?= LangManager::translate('core.mail.editor.modal_text_2') ?></p>
                            <p><?= LangManager::translate('core.mail.editor.modal_text_3') ?><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Arial', sans-serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Helvetica', sans-serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Verdana', sans-serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Tahoma', sans-serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Trebuchet MS', sans-serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Times New Roman', serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Georgia', serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Garamond', serif</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Courier New', monospace</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Lucida Console', monospace</code><br>
                                <code style="color: #2b2929; background: #efa1a1; border-radius: 5px; padding: 0 .2rem 0 .2rem">'Consolas', monospace</code><br></p>
                        </div>
                        <div class="modal-footer">
                            <button data-modal-hide="modal-help" type="button" class="btn-danger"><?= LangManager::translate('core.btn.close') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border rounded-lg" style="height: 60vh;" id="editor">
                <div>

                </div>
            </div>
            <input type="hidden" name="body" id="editorTemplate"
                   value="<?= htmlspecialchars($config->getBody()) ?>">
        </div>
        <div class="card">
            <h6><?= LangManager::translate('core.mail.editor.render') ?></h6>
            <iframe class="border rounded-lg" style="height: 60vh" width="100%" id="preview"></iframe>
        </div>
    </div>
</form>


<div id="modal" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate('core.mail.config.test.title') ?></h6>
            <button type="button" data-modal-hide="modal"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="alert-warning">
                <p><?= LangManager::translate('core.mail.config.test.warning') ?></p>
            </div>
            <p>
                <?= LangManager::translate('core.mail.config.test.description') ?>
            </p>
            <form id="sendMail" action="" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <label for="receiver"><?= LangManager::translate('core.mail.config.test.receiverMail') ?> :</label>
                <div class="form-group position-relative has-icon-left">
                    <div class="input-group">
                        <i class="fa-solid fa-at"></i>
                        <input type="email" id="receiver" name="receiver"
                               placeholder="<?= LangManager::translate('core.mail.config.test.receiverMailPlaceholder') ?>"
                               required>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button form="sendMail" id="testButton" type="submit"
                    class="btn-primary"><?= LangManager::translate('core.btn.send') ?></button>
        </div>
    </div>
</div>

<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ace.js' ?>"></script>
<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ext-language_tools.js' ?>"></script>

<script>
    const langTools = ace.require("ace/ext/language_tools");
    const editor = ace.edit("editor", {
        mode: "ace/mode/php",
        selectionStyle: "text",
    });

    const editorInput = document.getElementById('editorTemplate')
    const form = document.getElementById('smtpConfig');
    const submitButton = document.getElementById('submitButton');

    editor.setOptions({
        autoScrollEditorIntoView: true,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: false
    })

    if (localStorage.getItem('theme') === 'theme-dark') {
        editor.setTheme("ace/theme/cmw_dark");
    } else {
        editor.setTheme("ace/theme/cmw_light");
    }

    editor.resize()
    editor.session.setUseWrapMode(true);
    editor.setShowPrintMargin(false);

    editor.session.mergeUndoDeltas = true;

    // Fonction pour mettre à jour le preview
    function updatePreview() {
        let previewContent = editor.getValue();
        let renderedContent = previewContent.replace(/\[MAIL_CONTENT\]/g, "<?= LangManager::translate('core.mail.editor.render_example') ?>");

        document.getElementById('preview').srcdoc = renderedContent;
        editorInput.value = editor.getValue()
    }

    form.addEventListener("submit", function (event) {
        let content = editor.getValue();

        if (!content.includes("[MAIL_CONTENT]")) {
            event.preventDefault();
            iziToast.show(
                {
                    titleSize: '16',
                    messageSize: '14',
                    icon: 'fa-solid fa-xmark',
                    title  : "Intervention",
                    message: "<?= LangManager::translate('core.mail.editor.render_alert') ?>",
                    color: "#ab1b1b",
                    iconColor: '#ffffff',
                    titleColor: '#ffffff',
                    messageColor: '#ffffff',
                    balloon: false,
                    close: true,
                    pauseOnHover: true,
                    position: 'topCenter',
                    timeout: 4000,
                    animateInside: false,
                    progressBar: true,
                    transitionIn: 'fadeInDown',
                    transitionOut: 'fadeOut',
                });
        }
    });

    // Déclencher le preview en temps réel
    editor.session.on('change', updatePreview);

    // Recupération du CODE actuel
    editor.setValue(`<?= $config->getBody() ?>`, -1);

    // Mettre à jour le preview au chargement initial
    updatePreview();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let selectedTemplateCode = "";

        document.querySelectorAll("input[name='preview']").forEach(input => {
            input.addEventListener("change", function () {
                // Récupération du code du template correspondant via une data attribute
                selectedTemplateCode = this.dataset.code;
            });
        });

        // Appliquer le template dans l'éditeur ACE
        document.getElementById("applyTemplate").addEventListener("click", function () {
            if (selectedTemplateCode) {
                editor.setValue(selectedTemplateCode, -1);
            }
        });
    });
</script>
