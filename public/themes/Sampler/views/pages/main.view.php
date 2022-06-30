<?php

/* @var \CMW\Entity\Pages\pageEntity $page */
/* @var \CMW\Model\Pages\pagesModel $pages */
/* @var \CMW\Controller\coreController $core */
/* @var \CMW\Controller\Menus\menusController $menu */

$title = ucfirst($page->getTitle());
$description = "Description de votre page";
ob_start(); ?>


    <section>
        <div class="container">
            <?= $page->getConverted() ?>
        </div>
    </section>


<?php $content = ob_get_clean(); ?>