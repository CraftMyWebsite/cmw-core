<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.mail.config.title");
$description = LangManager::translate("core.mail.config.description");

/* @var \CMW\Entity\Core\MailConfigEntity $config */

?>

<div class="page-title">
    <h3><i class="fa-solid fa-envelope"></i> SMTP & Mailing</h3>
    <button form="smtpConfig" type="submit" class="btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
</div>

<form id="smtpConfig" action="" method="post">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="grid-2">
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
                    <label for="mail"><?= LangManager::translate("core.mail.config.senderMail") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-at"></i>
                        <input type="text" id="mail" name="mail"
                               value="<?= $config?->getMail() ?>"
                               placeholder="contact@monsite.fr" required>
                    </div>
                </div>
                <div>
                    <label for="mailReply"><?= LangManager::translate("core.mail.config.replyMail") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-at"></i>
                        <input type="text" id="mailReply" name="mailReply" value="<?= $config?->getMailReply() ?>"
                               placeholder="reply@monsite.fr" required>
                    </div>
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <label for="addressSMTP"><?= LangManager::translate("core.mail.config.serverSMTP") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-server"></i>
                        <input type="text" id="addressSMTP" name="addressSMTP"
                               value="<?= $config?->getAddressSMTP() ?>" placeholder="smtp.google.com" required>
                    </div>
                </div>
                <div>
                    <label for="port"><?= LangManager::translate("core.mail.config.portSMTP") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-network-wired"></i>
                        <input type="number" id="port" name="port"
                               value="<?= $config?->getPort() ?>" placeholder="587" required>
                    </div>
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <label for="user"><?= LangManager::translate("core.mail.config.userSMTP") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="user" name="user"
                               value="<?= $config?->getUser() ?>" placeholder="admin@monsite.fr" required>
                    </div>
                </div>
                <div>
                    <label for="password"><?= LangManager::translate("core.mail.config.passwordSMTP") ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-house"></i>
                        <input type="password" id="password" name="password"
                               value="<?= $config?->getPassword() ?>" placeholder="••••" required>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <label><?= LangManager::translate("core.mail.config.protocol") ?></label>
                    <div class="flex items-center gap-2">
                        <input id="flexRadioDefault1" class="form-check-input" type="radio" name="protocol"
                               value="tls" <?= $config?->getProtocol() === "tls" ? "checked" : "" ?>>
                        <label class="form-check-label" for="flexRadioDefault1">TLS</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="flexRadioDefault2" type="radio" value="ssl"
                               name="protocol" <?= $config?->getProtocol() === "ssl" ? "checked" : "" ?>>
                        <label class="form-check-label" for="flexRadioDefault2">SSL</label>
                    </div>
                </div>
                <button data-modal-toggle="modal" class="btn-warning h-fit"
                        type="button"><?= LangManager::translate("core.mail.config.test.btn") ?></button>
            </div>

        </div>
        <div class="card">
            <h6><?= LangManager::translate("core.mail.config.footer") ?></h6>
            <textarea class="tinymce" name="footer" data-tiny-height="305"><?= $config?->getFooter() ?></textarea>
        </div>
    </div>
</form>


<div id="modal" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate("core.mail.config.test.title") ?></h6>
            <button type="button" data-modal-hide="modal"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="alert-warning">
                <p><?= LangManager::translate("core.mail.config.test.warning") ?></p>
            </div>
            <p>
                <?= LangManager::translate("core.mail.config.test.description") ?>
            </p>
            <form id="sendMail" action="" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <label for="receiver"><?= LangManager::translate("core.mail.config.test.receiverMail") ?> :</label>
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
            <button form="sendMail" id="testButton" type="submit" class="btn-primary"><?= LangManager::translate("core.btn.send") ?></button>
        </div>
    </div>
</div>