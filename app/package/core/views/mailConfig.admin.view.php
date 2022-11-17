<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("core.mail.config.title");
$description = LangManager::translate("core.mail.config.description");

/* @var \CMW\Entity\Core\MailConfigEntity $config */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-envelope"></i> <span class="m-lg-auto">Mails</span></h3>
    <div class="buttons">
        <button form="smtpConfig" type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
    </div>
</div>
<section class="row">
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between">
                    <h4><?= LangManager::translate("core.mail.config.title") ?></h4>
                    <form id="smtpConfig" action="" method="post">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="form-check-reverse form-switch">
                        <label class="form-check-label" for="enableSMTP">SMTP</label>
                        <input class="form-check-input" type="checkbox" id="enableSMTP" name="enableSMTP" value="<?= $config?->isEnable() ?>" <?= $config?->isEnable() ? 'checked' : '' ?>>
                    </div>
                </div>
            </div>
            <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.senderMail") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" id="mail" name="mail" value="<?= $config?->getMail() ?>"
                                           placeholder="contact@monsite.fr" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.replyMail") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" id="mailReply" name="mailReply" class="form-control"
                                           value="<?= $config?->getMailReply() ?>"
                                           placeholder="reply@monsite.fr" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.serverSMTP") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" id="addressSMTP" name="addressSMTP" class="form-control" value="<?= $config?->getAddressSMTP() ?>" placeholder="smtp.google.com" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-server"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.portSMTP") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" type="number" id="port" name="port" class="form-control" value="<?= $config?->getPort() ?>" placeholder="465" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-network-wired"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.userSMTP") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" id="user" name="user" class="form-control" value="<?= $config?->getUser() ?>" placeholder="admin@monsite.fr" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.mail.config.passwordSMTP") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" id="password" name="password" class="form-control" value="<?= $config?->getPassword() ?>" placeholder="••••" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between justify-content-end">
                        <div>
                            <label for="protocol"><?= LangManager::translate("core.mail.config.protocol") ?></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="protocol" value="tls" <?= $config?->getProtocol() === "tls" ? "checked" : "" ?>>
                                <label class="form-check-label" for="flexRadioDefault1">TLS (default)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="ssl" name="protocol" <?= $config?->getProtocol() === "ssl" ? "checked" : "" ?>>
                                <label class="form-check-label" for="flexRadioDefault2">SSL</label>
                            </div>
                        </div>
                        <div class="buttons align-self-end mt-2">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#testModal"
                                    class="btn btn-primary"><?= LangManager::translate("core.mail.config.test.btn") ?>
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4>Mise en forme</h4>
            </div>
            <div class="card-body">
                    <h6>Nom d'affichage :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" placeholder="Nom de votre site">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("core.mail.config.footer") ?> :</h6>
                    <textarea name="footer" id="summernote"><?= $config?->getFooter() ?></textarea>
                </form>
            </div>
        </div>
    </div>
</section>









<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle"><?= LangManager::translate("core.mail.config.test.title") ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <p><?= LangManager::translate("core.mail.config.test.warning") ?></p>
                </div>
                <p>
                    <?= LangManager::translate("core.mail.config.test.description") ?>
                </p>
                <form id="sendMail" action="test" method="post">
                <?php (new SecurityService())->insertHiddenToken() ?>
                    <h6><?= LangManager::translate("core.mail.config.test.receiverMail") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="email" class="form-control" id="receiver" name="receiver" placeholder="<?= LangManager::translate('core.mail.config.test.receiverMailPlaceholder') ?>" required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-at"></i>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                </button>
                <button form="sendMail" type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.send") ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
