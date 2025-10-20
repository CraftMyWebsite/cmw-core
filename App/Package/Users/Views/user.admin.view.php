<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Users\UserTokenEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\RolesModel;
use CMW\Utils\Date;

/** @var UserEntity $user */
/** @var RoleEntity[] $roles */
/** @var UserTokenEntity[] $tokens */

$title = LangManager::translate('users.edit.title', ['pseudo' => $user->getPseudo()]);
$description = LangManager::translate('users.edit.desc');

?>

<div class="page-title">
    <h3>
        <i class="fa-solid fa-gears"></i> <?= LangManager::translate('users.manage.edit.title', ['pseudo' => $user->getPseudo()]) ?>
    </h3>
    <div class="lg:flex space-x-2">
        <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"
           class="btn btn-<?= ($user->getState()) ? 'danger' : 'success' ?>">
            <i class="fa fa-user-slash"></i>
            <?= ($user->getState())
                ? LangManager::translate('users.edit.disable_account')
                : LangManager::translate('users.edit.activate_account') ?>
        </a>
        <?php if ($user->useNativeLoginMethode()): ?>
            <form method="post" action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login/forgot">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <input type="hidden" value="<?= $user->getMail() ?>" name="mail">
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-arrows-rotate"></i>
                    <?= LangManager::translate('users.edit.reset_password') ?>
                </button>
            </form>
        <?php endif; ?>
        <button type="submit" form="userprofile" class="btn btn-primary">
            <?= LangManager::translate('core.btn.save', lineBreak: true) ?>
        </button>
    </div>
</div>

<div class="grid-3">
    <form method="post" id="userprofile" class="col-span-2 grid-2">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="card">
            <label for="email"><?= LangManager::translate('users.users.mail') ?> :</label>
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" id="email" name="email" value="<?= $user->getMail() ?>"
                       placeholder="<?= LangManager::translate('users.users.mail') ?>" required>
            </div>
            <div>
                <label for="pseudo"><?= LangManager::translate('users.users.pseudo') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-signature"></i>
                    <input type="text" id="pseudo" name="pseudo" value="<?= $user->getPseudo() ?>"
                           placeholder="<?= LangManager::translate('users.users.pseudo') ?>" required>
                </div>
            </div>
            <div>
                <label for="name"><?= LangManager::translate('users.users.firstname') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" id="name" name="name" value="<?= $user->getFirstName() ?>"
                           placeholder="<?= LangManager::translate('users.users.firstname') ?>">
                </div>
            </div>
            <div>
                <label for="lastname"><?= LangManager::translate('users.users.surname') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" id="lastname" name="lastname"
                           value="<?= $user->getLastName() ?>"
                           placeholder="<?= LangManager::translate('users.users.surname') ?>">
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <?php if ($user->useNativeLoginMethode()): ?>
                    <label for="password-input"><?= LangManager::translate('users.users.password') ?> :</label>
                    <div class="input-btn">
                        <input id="password-input" type="password" name="pass" class="form-control"
                               placeholder="••••••"/>
                        <button type="button" onclick="showPassword('password')">
                            <i class="cursor-pointer fa fa-eye-slash" id="password-icon"></i>
                        </button>
                    </div>
                    <label for="passwordV-input"><?= LangManager::translate('users.users.repeat_pass') ?> :</label>
                    <div class="input-btn">
                        <input id="passwordV-input" type="password" name="passVerif" class="form-control"
                               placeholder="••••••"/>
                        <button type="button" onclick="showPassword('passwordV')">
                            <i class="cursor-pointer fa fa-eye-slash" id="passwordV-icon"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <p><?= LangManager::translate('users.users.login_methode') ?>
                        <b><?= ucfirst($user->getLoginMethode()) ?></b></p>
                <?php endif; ?>
            </div>
            <div class="card">
                <label for="roles">Rôles :</label>
                <select id="roles" class="choices choices__list--multiple" name="roles[]" multiple required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->getId() ?>"
                            <?= (RolesModel::playerHasRole($user->getId(), $role->getId()) ? 'selected' : '') ?>><?= $role->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
    <div class="card">
        <form action="../picture/edit/<?= $user->getId() ?>" method="post" enctype="multipart/form-data">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="grid-2">
                <img class="rounded-lg bg-contain"
                     src="<?= $user->getUserPicture()->getImage() ?>"
                     alt="<?= LangManager::translate('users.users.image.image_alt') . $user->getPseudo() ?>">
                <div class="drop-img-area" data-input-name="profilePicture"></div>
            </div>
            <div class="flex justify-between mt-2">
                <a href="../picture/reset/<?= $user->getId() ?>" type="submit" class="btn-warning"><i
                        class="fa fa-arrows-rotate"></i><?= LangManager::translate('users.users.image.reset') ?>
                </a>
                <button class="btn btn-primary" type="submit" id="profilePicture">
                    <?= LangManager::translate('core.btn.send') ?> <i class="fa-solid fa-upload"></i>
                </button>
            </div>
        </form>
    </div>

    <div>
        <h4>2FA</h4>
        <div class="card">
            <div class="flex flex-col w-1/2">
                <?php if (!$user->get2Fa()->isEnabled()): ?>
                    <b>2FA <?= LangManager::translate('core.btn.disabled') ?></b>
                    <a href="../2fa/status/toggle/<?= $user->getId() ?>" class="btn-danger mt-2">
                        <?= LangManager::translate('core.btn.enable') ?>
                    </a>
                <?php else: ?>
                    <b>2FA <?= LangManager::translate('core.btn.enabled') ?></b>
                    <a href="../2fa/status/toggle/<?= $user->getId() ?>" class="btn-danger mt-2">
                        <?= LangManager::translate('core.btn.disable') ?>
                    </a>
                <?php endif; ?>
                <a href="../2fa/key/regenerate/<?= $user->getId() ?>" class="btn-danger mt-2">
                    <?= LangManager::translate('users.users.2fa.regen_key') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="col-span-3">
        <h4><?= LangManager::translate('users.tokens.title') ?></h4>
        <div class="card">
            <?php if (empty($tokens)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <?= LangManager::translate('users.tokens.no_active_sessions') ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-4">
                    <i class="fa-solid fa-circle-info"></i>
                    <?= LangManager::translate('users.tokens.info', ['count' => count($tokens)]) ?>
                </div>

                <div class="space-y-3">
                    <?php foreach ($tokens as $token): ?>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Device Info -->
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="text-2xl text-blue-600 dark:text-blue-400 mt-1">
                                        <i class="fa-solid fa-<?= $token->getDeviceIcon() ?>"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            <?= $token->getDeviceName() ?>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fa-solid fa-network-wired mr-1"></i>
                                            <code class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-xs">
                                                <?= htmlspecialchars($token->getIpAddress()) ?>
                                            </code>
                                        </div>
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-3 text-xs text-gray-600 dark:text-gray-400">
                                            <div>
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                <span
                                                    class="font-medium"><?= LangManager::translate('users.tokens.table.created') ?>:</span>
                                                <span
                                                    class="block ml-5 mt-1"><?= Date::formatDate($token->getCreatedAt()) ?></span>
                                            </div>
                                            <div>
                                                <i class="fa-solid fa-history mr-1"></i>
                                                <span
                                                    class="font-medium"><?= LangManager::translate('users.tokens.table.last_used') ?>:</span>
                                                <span
                                                    class="block ml-5 mt-1"><?= Date::formatDate($token->getLastUsedAt()) ?></span>
                                            </div>
                                            <div>
                                                <i class="fa-solid fa-hourglass-end mr-1"></i>
                                                <span
                                                    class="font-medium"><?= LangManager::translate('users.tokens.table.expires') ?>:</span>
                                                <span
                                                    class="block ml-5 mt-1"><?= Date::formatDate($token->getExpiresAt()) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="flex-shrink-0">
                                    <a href="../tokens/revoke/<?= $user->getId() ?>/<?= $token->getTokenId() ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('<?= LangManager::translate('users.tokens.confirm_revoke') ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="../tokens/revoke-all/<?= $user->getId() ?>"
                       class="btn btn-danger"
                       onclick="return confirm('<?= LangManager::translate('users.tokens.confirm_revoke_all', ['pseudo' => $user->getPseudo()]) ?>')">
                        <i class="fa-solid fa-power-off"></i>
                        <?= LangManager::translate('users.tokens.revoke_all') ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const showPassword = (type) => {
        const input = document.getElementById(`${type}-input`);
        const icon = document.getElementById(`${type}-icon`);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";

            if (icon.classList.contains("fa-eye")) {
                icon.classList.remove("fa-eye")
            }

            icon.classList.add("fa-eye-slash")
        }
    }
</script>