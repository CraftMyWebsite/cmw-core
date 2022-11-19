<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

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
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <h6><?= LangManager::translate("users.users.mail") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="email" class="form-control" name="email" required
                               placeholder="<?= LangManager::translate("users.users.mail") ?>">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-at"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.pseudo") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="pseudo" required
                               placeholder="<?= LangManager::translate("users.users.pseudo") ?>">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.firstname") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="firstname"
                               placeholder="<?= LangManager::translate("users.users.firstname") ?>">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.surname") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="surname"
                               placeholder="<?= LangManager::translate("users.users.surname") ?>">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                    </div>
                    <h6><?= LangManager::translate("users.users.role") ?> :</h6>
                    <fieldset class="form-group">
                        <select class="choices choices__list--multiple" name="roles[]" multiple required>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                    <h6><?= LangManager::translate("users.users.password") ?>:</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="password" class="form-control" name="password" placeholder="••••" required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-unlock"></i>
                        </div>
                    </div>
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
                            <td><?= $user->getUsername() ?></td>
                            <td><?= $user->getHighestRole()?->getName() ?></td>
                            <td><?= $user->getCreated() ?></td>
                            <td><?= $user->getLastConnection() ?></td>
                            <td>
                                <button class="btn-clear" onclick="storeUserId(<?= $user->getId() ?>); fillEditModal()">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>

    const modalData = (data) => {
        return `<div class="modal fade modal-xl" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="roleEditModalTitle"
             aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleEditModalTitle"><?= LangManager::translate("users.manage.edit.title") ?> ${data.username}</h5>\n                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                        <div class="row">
                                            <h6><?= LangManager::translate("users.manage.edit.about") ?> :</h6>
                                            <p><b><?= LangManager::translate("users.users.creation") ?> :</b> ${data.dateCreated}</p>
                                            <p><b><?= LangManager::translate("users.users.last_edit") ?> :</b> ${data.dateUpdated}</p>
                                            <p><b><?= LangManager::translate("users.users.last_connection") ?> :</b> ${data.lastConnection}</p>
                                        </div>
                                        <div class="d-lg-flex flex-wrap justify-content-between">
                                            <form method="post"
                                                action="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>login/forgot">
                                                <?php (new SecurityService())->insertHiddenToken() ?>
                                                <input type="hidden" value="${data.mail}" name="mail">
                                                <button type="submit" class="btn btn-warning">
                                                    <span class="d-sm-block"><?= LangManager::translate("users.edit.reset_password") ?></span>
                                                </button>
                                            </form>
                                            <a href="state/${data.id}/${data.state}" class="btn btn-${data.state ? 'warning' : 'success'}">
                                                <span class="d-sm-block">
                                                <?='${data.state}' ? LangManager::translate("users.edit.disable_account") :
            LangManager::translate("users.edit.activate_account") ?>
                                                </span>
                                            </a>
                                            <a href="delete/${data.id}" class="btn btn-danger">
                                                <span class="d-sm-block"><?= LangManager::translate("core.btn.delete") ?></span>
                                            </a>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <form action="picture/edit/${data.id}" method="post" enctype="multipart/form-data">
                                    <?php (new SecurityService())->insertHiddenToken() ?>
                                        <div class="row">
                                            <h6><?= LangManager::translate("users.users.image.title") ?> :</h6>
                                            <p><?= LangManager::translate("users.users.image.last_update") ?>: ${data.pictureLastUpdate}</p>
                                            <div class="text-center ">
                                                <img class="w-25 border"
                                                     src="${data.pictureLink}"
                                                     alt="<?= LangManager::translate("users.users.image.title") ?>">
                                            </div>

                                        </div>
                                        <input class="form-control w-75 mx-auto form-control-sm" type="file"
                                                name="profilePicture"
                                                id="formFile">
                                        <div class="text-center mt-1">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-sm-block">
                                                    <?= LangManager::translate("users.users.image.reset") ?>
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                 <form action="edit/${data.id}" method="post">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("users.users.mail") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="email" class="form-control"
                                value="${data.mail}"
                                name="email"
                                placeholder="<?= LangManager::translate("users.users.mail") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                            </div>
                            <h6><?= LangManager::translate("users.users.firstname") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control"
                                 value="${data.firstName}"
                                 name="name"
                                 placeholder="<?= LangManager::translate("users.users.firstname") ?>">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                            </div>
                            <h6><?= LangManager::translate("users.users.roles") ?> :</h6>
                            <fieldset class="form-group">
                                <select class="choices choices__list--multiple" name="roles[]" multiple required>
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                            <h6><?= LangManager::translate("users.users.password") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" name="password" class="form-control" placeholder="••••">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("users.users.pseudo") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="pseudo" class="form-control"
                                value="${data.username}"
                                placeholder="<?= LangManager::translate("users.users.pseudo") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div

                            <h6><?= LangManager::translate("users.users.surname") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control"
                                 value="${data.lastName}"
                                 value="lastname"
                                 placeholder="<?= LangManager::translate("users.users.surname") ?>">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-signature"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-sm-block"><?= LangManager::translate("core.btn.edit") ?></span>
                        </button>
                    </div>
                 </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
`
    }

</script>