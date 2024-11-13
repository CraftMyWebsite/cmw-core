<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var \CMW\Entity\Users\UserEntity $user */

Website::setTitle('Profil ' . $user->getPseudo());
Website::setDescription("Découvrez le profil de l'utilisateur " . $user->getPseudo());

?>


<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <h1 class="text-center"><?= $user->getPseudo() ?></h1>
        <div class="row">
            <div class="col-lg-6 p-2">
                <div class="card p-2">
                    <h4 class="text-center">Informations personnel</h4>
                    <form class="space-y-6" action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'profile/update' ?>" method="post">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="login_email" type="email"
                                           value="<?= $user->getMail() ?>" required>
                                    <label for="name">E-Mail</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="login_email" type="password" required>
                                    <label for="name">Mot de passe</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="login_email" type="text"
                                           value="<?= $user->getPseudo() ?>" required>
                                    <label for="name">Pseudo</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="login_email" type="password" required>
                                    <label for="name">Confirmation</label>
                                </div>
                            </div>

                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary btn" type="submit">Appliquer les modifications</button>
                        </div>
                    </form>
                </div>

                <div class="card p-2 mt-2">
                    <h4 class="text-center">Sécurité :
                        <small>
                            <?php if ($user->get2Fa()->isEnabled()): ?>
                                <span style="color: green">Actif !</span>
                            <?php else: ?>
                                <span style="color: red">Inactif !</span>
                            <?php endif; ?>
                        </small>
                    </h4>
                    <?php if (!$user->get2Fa()->isEnabled()): ?>
                        <p>Pour activer l'authentification à double facteur scannez le QR code dans une application
                            d'authentification (GoogleAuthenticator, Aegis ...)</p>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="text-center">
                                <img height="50%" width="50%" src='<?= $user->get2Fa()->getQrCode(250) ?>'
                                     alt="QR Code double authentification">
                                <span><?= $user->get2Fa()->get2FaSecretDecoded() ?></span>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center mt-4">
                            <form class="space-y-6"
                                  action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/2fa/toggle"
                                  method="post">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <div class="form-floating mb-3">
                                    <input class="form-control" name="secret" type="number" maxlength="7" required>
                                    <label for="name">Code d'authentification</label>
                                </div>
                                <button class="btn btn-primary btn" type="submit">
                                    <?= $user->get2Fa()->isEnabled() ? 'Désactiver' : 'Activer' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 p-2">
                <div class="card p-2">
                    <h4 class="text-center">Identité visuel</h4>
                    <?php if (!is_null($user->getUserPicture()?->getImage())): ?>
                        <!--RECUPERER L'iMAGE-->
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Votre image :</label>
                        <img class="mx-auto rounded-lg border border-gray-300 shadow-xl"
                             src="<?= $user->getUserPicture()->getImage() ?>"
                             height="50%" width="50%" alt="Image de profil de <?= $user->getPseudo() ?>">
                    <?php endif; ?>
                    <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/update/picture" method="post"
                          enctype="multipart/form-data">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Changer votre image
                            :</label>
                        <div class="flex">
                            <input type="file" id="pictureProfile" name="pictureProfile"
                                   accept=".png, .jpg, .jpeg, .webp, .gif" required>
                            <button class="btn btn-primary" type="submit">Sauvegarder</button>
                        </div>
                        <p id="file_input_help">PNG, JPG, JPEG, WEBP, GIF (MAX. 400px400px).</p>
                    </form>
                </div>
            </div>

            <div class="card text-center">

                <h2>Vous nous quittez ?</h2>

                <p class="mb-2">Nous somme triste de vous voir partir !</p>
                <a style="margin: auto"
                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/delete/<?= $user->getId() ?>"
                   class="btn btn-primary">Supprimer mon compte</a>
            </div>
        </div>
    </div>
</section>

