<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/** @var \CMW\Entity\Users\UserEntity $user */
/** @var \CMW\Entity\Users\RoleEntity[] $roles */
/** @var \CMW\Entity\Users\UserEntity[] $userList */

$title = LangManager::translate("users.manage.title");
$description = LangManager::translate("users.manage.desc"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span
                class="m-lg-auto"><?= LangManager::translate("users.manage.title") ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.manage.card_title_add") ?></h4>
            </div>
            <div class="card-body">
                <form method="post" action="add">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <h6><?= LangManager::translate("users.users.mail") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="email" class="form-control" name="email" autocomplete="off"
                               placeholder="<?= LangManager::translate("users.users.mail") ?>" required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-at"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.pseudo") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="pseudo" autocomplete="off"
                               placeholder="<?= LangManager::translate("users.users.pseudo") ?>" required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.password") ?> : </h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input name="password" id="password" type="password" class="form-control" autocomplete="off"
                               aria-describedby="button-generate">
                        <button onclick="generatePassword('password')" data-bs-toggle="tooltip" id="button-generate"
                                class="btn btn-primary" type="button"
                                title="<?= LangManager::translate('users.manage.randomPasswordTooltip') ?>">
                            <i class="fa-solid fa-rotate"></i>
                        </button>
                    </div>
                    <h6><?= LangManager::translate("users.users.role") ?> :</h6>
                    <fieldset class="form-group">
                        <select class="choices choices__list--multiple" name="roles[]" multiple required>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                    <div class="text-center">
                        <button type="submit"
                                class="btn btn-primary"><?= LangManager::translate("core.btn.add") ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.manage.card_title_list") ?></h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center"><?= LangManager::translate("users.users.mail") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.users.pseudo") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.users.role") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.users.creation") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.users.last_connection") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($userList as $user) : ?>
                        <tr>
                            <td><?= $user->getMail() ?></td>
                            <td><?= $user->getPseudo() ?></td>
                            <td><?= $user->getHighestRole()?->getName() ?></td>
                            <td><?= $user->getCreated() ?></td>
                            <td><?= $user->getLastConnection() ?></td>
                            <td>
                                <a class="me-3 "
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/users/edit/<?= $user->getId() ?>">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $user->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt"></i>
                                </a>
                                <div class="modal fade text-left" id="delete-<?= $user->getId() ?>" tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title white" id="myModalLabel160">
                                                    Supprimer <?= $user->getPseudo() ?> ?</h5>
                                            </div>
                                            <div class="modal-body text-left">
                                                La suppression de cet utilisateur est définitive !<br>
                                                Aucun retour possible !
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary"
                                                        data-bs-dismiss="modal">
                                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <a href="/cmw-admin/users/delete/<?= $user->getId() ?>"
                                                   class="btn btn-danger">
                                                    <span class=""><?= LangManager::translate("contact.message.delete") ?></span>
                                                </a>
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
        </div>
    </div>
</section>