<?php

use CMW\Manager\Lang\LangManager;

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
                        <select class="form-select" name="roles[]" id="basicSelect" required>
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
                                <i data-bs-toggle="modal" data-bs-target="#userEditModal"
                                   class="text-primary fa-solid fa-gears"></i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<!-- Modal Edit -->
<div class="modal fade modal-xl" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="roleEditModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleEditModalTitle">Édition de Zomb</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <h6>A propos :</h6>
                                            <p><b>Date de création :</b> 2022-11-14 19:32:25</p>
                                            <p><b>Date de modification :</b> 2022-11-14 19:32:25</p>
                                            <p><b>Dernière visite :</b> 2022-11-14 19:32:25</p>
                                        </div>
                                        <div class="d-lg-flex flex-wrap justify-content-between">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-sm-block">Reset MDP</span>
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                <span class="d-sm-block">Bloquer</span>
                                            </button>
                                            <button type="submit" class="btn btn-danger">
                                                <span class="d-sm-block">Supprimer</span>
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <h6>Image de profil :</h6>
                                            <p>Dernière modification: 2022-11-14 19:32:25</p>
                                            <div class="text-center ">
                                                <img class="w-25 border"
                                                     src="https://theme.voyza.fr/public/uploads/users/default/defaultImage.jpg"
                                                     alt="Image introuvable !">
                                            </div>

                                        </div>
                                        <input class="form-control w-75 mx-auto form-control-sm" type="file"
                                               id="formFile">
                                        <div class="text-center mt-1">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="d-sm-block">Réinitialiser l'image</span>
                                            </button>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6>E-Mail :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="email" class="form-control" placeholder="E-Mail">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                            </div>
                            <h6>Prénom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Prénom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                            </div>
                            <h6>Rôles :</h6>
                            <fieldset class="form-group">
                                <select class="form-select" id="basicSelect">
                                    <option>Sampler</option>
                                    <option>Wipe</option>
                                    <option>Vega</option>
                                </select>
                            </fieldset>
                            <h6>Mot de passe :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" class="form-control" placeholder="••••">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6>Pseudo :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Pseudo">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                            <h6>Nom :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" placeholder="Nom">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-signature"></i>
                                </div>
                            </div>
                            <h6>Repeter mot de passe :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="password" class="form-control" placeholder="••••">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-unlock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-sm-block">Annuler</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-sm-block">Confirmer</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>