<?php

/* @var \CMW\Entity\Pages\PageEntity $page */


use CMW\Utils\Website;

Website::setTitle(ucfirst($page->getTitle()));
Website::setDescription(ucfirst($page->getTitle()));
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1><?= ucfirst($page->getTitle()) ?></h1>

<?= $page->getConverted() ?>


