<?php

/* @var \CMW\Entity\Users\UserEntity $user */

$title = "Profil - " . $user->getUsername();
$description = "Profil de " . $user->getUsername(); ?>

<main role="main">
    <div class="container">
        <div class="content">
            <p class="text-center">
                Bonjour, <strong><?= $user->getUsername() ?></strong>
            </p>

            <p>Upload ton image de profile ici:</p>

            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" id="pictureProfile" name="pictureProfile" accept=".png, .jpg, .jpeg, .webp, .gif" required>

                <button type="submit">Sauvegarder</button>
            </form>

            <?php if (!is_null($user->getUserPicture()->getImageName())): ?>
                <img src="<?= getenv('PATH_SUBFOLDER') ?>public/uploads/users/<?= $user->getUserPicture()->getImageName() ?>"
                     alt="Image de profil de <?= $user->getUsername() ?>">
            <?php endif; ?>


            <p class="text-center">
                Si tu souhaites te déconnecter clique <a
                        href="<?= getenv('PATH_SUBFOLDER') ?>logout">ici</a>
            </p>
        </div>
    </div>
</main>