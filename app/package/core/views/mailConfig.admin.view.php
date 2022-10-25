<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("core.mail.config.title");
$description = LangManager::translate("core.mail.config.description");

/* @var \CMW\Entity\Core\MailConfigEntity $config */

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("core.mail.config.title") ?> :</h3>
                        </div>
                        <div class="card-body">

                            <!-- GENERAL CONFIG SECTION -->

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="enableSMTP"
                                           name="enableSMTP" value="<?= $config?->isEnable() ?>"
                                        <?= $config?->isEnable() ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="enableSMTP">
                                        <?= LangManager::translate("core.mail.config.enableSMTP") ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email"><?= LangManager::translate("core.mail.config.senderMail") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    </div>
                                    <input type="email" id="mail" name="mail" class="form-control"
                                           value="<?= $config?->getMail() ?>"
                                           placeholder="contact@monsite.fr" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="emailReply"><?= LangManager::translate("core.mail.config.replyMail") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    </div>
                                    <input type="email" id="mailReply" name="mailReply" class="form-control"
                                           value="<?= $config?->getMailReply() ?>"
                                           placeholder="reply@monsite.fr" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addressSMTP"><?= LangManager::translate("core.mail.config.serverSMTP") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                    </div>
                                    <input type="text" id="addressSMTP" name="addressSMTP" class="form-control"
                                           value="<?= $config?->getAddressSMTP() ?>"
                                           placeholder="smtp.google.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="user"><?= LangManager::translate("core.mail.config.userSMTP") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                    </div>
                                    <input type="text" id="user" name="user" class="form-control"
                                           value="<?= $config?->getUser() ?>"
                                           placeholder="admin@monsite.fr" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password"><?= LangManager::translate("core.mail.config.passwordSMTP") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" class="form-control"
                                           value="<?= $config?->getPassword() ?>"
                                           placeholder="*******" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="port"><?= LangManager::translate("core.mail.config.portSMTP") ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                    </div>
                                    <input type="number" id="port" name="port" class="form-control"
                                           value="<?= $config?->getPort() ?>"
                                           placeholder="465" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="protocol"><?= LangManager::translate("core.mail.config.protocol") ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="protocol" value="tls"
                                        <?= $config?->getProtocol() === "tls" ? "checked" : "" ?>>
                                    <label class="form-check-label">TLS (Par default)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="ssl" name="protocol"
                                        <?= $config?->getProtocol() === "ssl" ? "checked" : "" ?>>
                                    <label class="form-check-label">SSL</label>
                                </div>
                            </div>

                            <label for="footer"
                                   class="mt-3"><?= LangManager::translate("core.mail.config.footer") ?></label>
                            <div class="input-group mb-3">
                                <textarea id="footer" name="footer" class="form-control" required>
                                    <?= $config?->getFooter() ?>
                                </textarea>
                            </div>


                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <a data-toggle="modal" data-target="#testConfig" class="btn btn-primary float-left">
                                <?= LangManager::translate("core.mail.config.test.btn") ?>
                            </a>

                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>


<!-- Modal test config -->
<div class="modal fade" id="testConfig" tabindex="-1" role="dialog" aria-labelledby="testConfigLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="testConfigLabel"><?= LangManager::translate("core.mail.config.test.title") ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="test" method="post">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <p>
                            <?= LangManager::translate("core.mail.config.test.description") ?>
                        </p>

                        <label for="receiver"><?= LangManager::translate("core.mail.config.test.receiverMail") ?></label>
                        <input type="email" class="form-control" id="receiver" name="receiver"
                               placeholder="<?= LangManager::translate('core.mail.config.test.receiverMailPlaceholder') ?>"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= LangManager::translate("core.btn.close") ?></button>
                    <button type="submit"
                            class="btn btn-primary"><?= LangManager::translate("core.btn.send") ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
