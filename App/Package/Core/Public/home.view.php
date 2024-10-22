<?php


use CMW\Utils\Website;


/* TITRE ET DESCRIPTION */
Website::setTitle(Website::getWebsiteName());
Website::setDescription(Website::getWebsiteDescription());
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1>Bienvenue sur votre site !</h1>