<?php

/* @var CMW\Model\Pages\pagesModel $page */
/* @var CMW\Controller\pages\pagesController $slug */


$title = ucfirst($page->getPage($slug)->getPageTitle());
$description = "Description de votre page";
ob_start();?>

<section>
    <div class="container">
            <?= $page->getPage($slug)->getPageContentTranslated() ?>
    </div>
</section>




<?php $content = ob_get_clean(); ?>