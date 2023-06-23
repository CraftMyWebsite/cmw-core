<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;

$title = "Profil";
$description = "Description de votre page";
/* @var \CMW\Entity\Users\UserEntity $user */
?>


<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <h1 class="text-center"><?= $user->getPseudo() ?></h1>
        <div class="row">
            <div class="col-lg-6 p-2">
                <div class="card p-2">
                    <h4 class="text-center">Informations personnel</h4>
                    <form class="space-y-6" action="profile/update" method="post">
                        <?php (new SecurityManager())->insertHiddenToken() ?>
                        <div class="form-floating mb-3">
                            <input class="form-control" name="login_email" type="email" value="<?= $user->getMail() ?>" required>
                            <label for="name">E-Mail</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" name="login_email" type="text" value="<?= $user->getPseudo() ?>" required>
                            <label for="name">Pseudo</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" name="login_email" type="password" required>
                            <label for="name">Mot de passe</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" name="login_email" type="password" required>
                            <label for="name">Confirmation</label>
                        </div>
                        <div class="d-grid"><button class="btn btn-primary btn-xl" type="submit">Appliquer les modifications</button></div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 p-2">
                <div class="card p-2">
                    <h4 class="text-center">Identité visuel</h4>
                    <?php if (!is_null($user->getUserPicture()?->getImageName())): ?>
                        <!--RECUPERER L'iMAGE-->
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Votre image :</label>
                        <img class="mx-auto rounded-lg border border-gray-300 shadow-xl" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Public/Uploads/Users/<?= $user->getUserPicture()->getImageName() ?>" height="50%" width="50%" alt="Image de profil de <?= $user->getPseudo() ?>">
                    <?php endif; ?>
                    <form action="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>profile" method="post" enctype="multipart/form-data">
                        <?php (new SecurityManager())->insertHiddenToken() ?>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Changer votre image :</label>
                        <div class="flex">
                            <input type="file" id="pictureProfile" name="pictureProfile" accept=".png, .jpg, .jpeg, .webp, .gif" required>
                            <button class="btn btn-primary" type="submit">Sauvegarder</button>
                        </div>
                        <p id="file_input_help">PNG, JPG, JPEG, WEBP, GIF (MAX. 400px400px).</p>
                    </form>
                </div>
            </div>

            <div class="card text-center">

                    <h2>Vous nous quittez ?</h2>

                <p class="mb-2">Nous somme triste de vous voir partir !</p>
                <a style="margin: auto" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>profile/delete/<?= $user->getId() ?>" class="btn btn-primary">Supprimer mon compte</a>
            </div>
        </div>
    </div>
</section>

