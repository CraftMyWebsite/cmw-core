<?php

/* @var \CMW\Entity\Pages\PageEntity $page */
/* @var \CMW\Model\Pages\PagesModel $pages */
/* @var \CMW\Controller\CoreController $core */
/* @var \CMW\Controller\Menus\MenusController $menu */

$title = ucfirst($page->getTitle());
$description = "Description de votre page";
ob_start(); ?>


    <section>
        <div class="container">
            <?= $page->getConverted() ?>
        </div>
    </section>


<?php $content = ob_get_clean(); ?>