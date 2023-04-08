<?php
$title = "Profil";
$description = "Description de votre page";
/* @var \CMW\Entity\Users\UserEntity $user */
?>


<section class="page-section" id="contact">
    <div class="container px-4 px-lg-5">
        <?php
            if ($user->isViewerIsCurrentUser()){
                echo "Bienvenue sur votre page de profil";
            } else {
                echo "Vous visitez actuellement le profil de " . $user->getPseudo();
            }
        ?>
    </div>
</section>
