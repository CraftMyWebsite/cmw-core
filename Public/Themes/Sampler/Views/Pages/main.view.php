<?php

/* @var \CMW\Entity\Pages\PageEntity $page */
/* @var \CMW\Model\Pages\PagesModel $pages */

use CMW\Utils\Website;

Website::setTitle(ucfirst($page->getTitle()));
Website::setDescription($page->getContentPreview());
?>


<section class="page-section">
    <h1 class="text-center"><?= $page->getTitle() ?></h1>
    <div class="container">
        <?= $page->getConverted() ?>
    </div>
</section>

