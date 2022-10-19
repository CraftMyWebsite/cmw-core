<?php

/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Utils\SecurityService;

$title = "Profil - " . $user->getUsername();
$description = "Profil de " . $user->getUsername(); ?>

<main role="main">
    <div class="container">
        <div class="content">
            <p class="text-center">
                Bonjour, <strong><?= $user->getUsername() ?></strong>
            </p>

            <p>Upload ton image de profil ici:</p>

            <form action="" method="post" enctype="multipart/form-data">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <input type="file" id="pictureProfile" name="pictureProfile" accept=".png, .jpg, .jpeg, .webp, .gif"
                       required>

                <button type="submit">Sauvegarder</button>
            </form>

            <?php if (!is_null($user->getUserPicture()?->getImageName())): ?>
                <img src="<?= getenv('PATH_SUBFOLDER') ?>public/uploads/users/<?= $user->getUserPicture()->getImageName() ?>"
                     height="15%" width="15%"
                     alt="Image de profil de <?= $user->getUsername() ?>">
            <?php endif; ?>

            <form action="profile/update" method="post">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <label for="email"> Adresse e-mail</label>
                <input type="email" name="email" id="email" value="<?= $user->getMail() ?>" required>
                <br>
                <label for="pseudo"> Pseudo</label>
                <input type="text" name="pseudo" id="pseudo" value="<?= $user->getUsername() ?>" required>
                <br>
                <label for="password"> Mot de passe</label>
                <input type="password" name="password" id="password" placeholder="********">
                <label for="passwordVerif"> Confirmation mot de passe</label>
                <input type="password" name="passwordVerif" id="passwordVerif" placeholder="********">
                <br>
                <button type="submit">Modifier</button>
            </form>

            <p>
                Supprimez votre compte en cliquant <a
                        href="<?= getenv('PATH_SUBFOLDER') ?>profile/delete/<?= $user->getId() ?>">ici</a>
            </p>


            <p class="text-center">
                Si tu souhaites te déconnecter clique <a
                        href="<?= getenv('PATH_SUBFOLDER') ?>logout">ici</a>
            </p>
        </div>
    </div>
</main>