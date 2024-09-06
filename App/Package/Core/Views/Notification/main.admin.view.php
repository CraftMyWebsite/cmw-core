<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Notification\NotificationModel;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Manager\Notification\NotificationEntity[] $notifications */
/* @var \CMW\Manager\Package\IPackageConfig[] $packages */
/* @var string $showOnDiscord */
/* @var string $webhookDiscord */
/* @var string $sendMail */
/* @var string $mailReceiver */
/* @var array $refusedPackages */

$title = 'Notifications';
$description = 'Notifications'
?>

<div class="page-title">
    <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
    <div>
        <button type="button" class="btn-mass-delete btn-success" data-target-table="1">Marquer la séléction comme lue</button>
        <button data-modal-toggle="modal" class="btn-primary">Paramètres</button>
    </div>
</div>


<div class="table-container">
    <table id="table1" class="table-checkeable" data-form-action="notification/readSelected" data-load-per-page="10">
        <thead>
        <tr>
            <th class="mass-selector"></th>
            <th>Package</th>
            <th>État</th>
            <th>Titre</th>
            <th>Message</th>
            <th>Date</th>
            <th class="text-center">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($notifications as $notification): ?>
            <tr <?php if (!$notification->isRead()): ?>style="background-color: rgba(87,161,146,0.21)"<?php endif; ?>>
                <td class="item-selector" data-value="<?= $notification->getId() ?>"></td>
                <td><b><?= $notification->getPackage() ?></b></td>
                <td>
                    <?php if ($notification->isRead()): ?>
                        <i class="text-warning fa-solid fa-eye-slash"></i> <?php if ($notification->isReadSilence()): ?> Notification silencieuse <?php else: ?> Lue <?php endif; ?>
                    <?php else: ?>
                        <b><i class="text-success fa-solid fa-eye"></i> Non lue</b>
                    <?php endif; ?>
                </td>
                <td><?= mb_strimwidth($notification->getTitle(), 0, 30, '...') ?></td>
                <td><?= mb_strimwidth($notification->getMessage(), 0, 80, '...') ?></td>
                <td><?= $notification->getCreatedAt() ?></td>
                <td class="text-center space-x-2">
                    <?php if ($notification->getSlug()): ?>
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $notification->getSlug() ?>" class="text-info" title="S'y rendre"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    <?php endif; ?>
                    <?php if ($notification->isRead()): ?>
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/unRead/<?= $notification->getId() ?>" class="text-warning" title="Marqué comme non lue"><i class="fa-solid fa-eye-slash"></i></a>
                    <?php else: ?>
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/read/<?= $notification->getId() ?>" class="text-success" title="Marqué comme lue"><i class="fa-solid fa-eye"></i></a>
                    <?php endif; ?>
                    <button data-modal-toggle="modal-details-<?= $notification->getId() ?>" class="text-info" type="button" title="Voir en détail"><i class="fa-solid fa-envelope-open-text"></i></button>
                    <div id="modal-details-<?= $notification->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header">
                                <h6><?= $notification->getPackage() ?></h6>
                                <button type="button" data-modal-hide="modal-details-<?= $notification->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body" style="text-align: left">
                                <h6><?= $notification->getTitle() ?></h6>
                                <p><?= $notification->getMessage() ?></p>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <?php if ($notification->getSlug()): ?>
                                        <a type="button" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $notification->getSlug() ?>" class="btn-info">S'y rendre</a>
                                    <?php endif; ?>
                                    <?php if ($notification->isRead()): ?>
                                        <a type="button" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/unRead/<?= $notification->getId() ?>" class="btn-warning">Marqué comme non lue</a>
                                    <?php else: ?>
                                        <a type="button" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/read/<?= $notification->getId() ?>" class="btn-success">Marqué comme lue</a>
                                    <?php endif; ?>
                                    <button data-modal-hide="modal-details-<?= $notification->getId() ?>" type="button" class="btn-primary">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modal" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Paramètres de notifications</h6>
            <button type="button" data-modal-hide="modal"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
        <div class="modal-body">
            <!--DISCORD-->
            <div>
                <label class="toggle">
                    <span class="toggle-label">Webhook Discord</span>
                    <input type="checkbox" class="toggle-input" name="show_on_discord" <?= $showOnDiscord ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
                <div class="input-group">
                    <i class="fa-brands fa-discord"></i>
                    <input type="text" id="discord_webhook_url" name="discord_webhook_url" placeholder="https://discord.com/api/webhooks/XXXXXXX" value="<?= $webhookDiscord ?>">
                </div>
            </div>

            <!--MAILS-->
            <div>
                <label class="toggle">
                    <span class="toggle-label">Recevoir un mail</span>
                    <input type="checkbox" class="toggle-input" name="send_mail" <?= $sendMail ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="text" id="discord_webhook_url" name="mail_receiver" placeholder="your@mail.com" value="<?= $mailReceiver ?>">
                </div>
            </div>

            <!--PACKAGES-->
            <label for="select">Notification silencieuse pour les packages :</label>
            <select id="select" name="refused_package[]" class="choices" multiple>
                <?php foreach ($packages as $package): ?>
                    <option
                        <?php foreach ($refusedPackages as $refusedPackage): ?>
                        <?= $refusedPackage === $package->name() ? 'selected' : '' ?>
                        <?php endforeach ?>
                        value="<?= $package->name() ?>"><?= $package->name() ?></option>
                <?php endforeach; ?>
            </select>
            <small>Lorsque vous ajoutez des notifications silencieuses sur des paquets, celles-ci continuent d'émettre des notifications. Cependant, vous n'êtes ni notifié ni alerté. Néanmoins, vous pourrez toujours les consulter sur cette page, mais elles seront automatiquement marquées comme lues.
            </small>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-primary">Sauvegarder</button>
        </div>
        </form>
    </div>
</div>