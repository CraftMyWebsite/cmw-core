<?php

/* @var \CMW\Entity\Pages\PageEntity $page */
/* @var \CMW\Model\Pages\PagesModel $pages */

use CMW\Utils\Website;

Website::setTitle(ucfirst($page->getTitle()));
Website::setDescription($page->getContentPreview());
?>
<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl"><?= $page->getTitle() ?></h1>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="max-w-6xl mx-auto py-8">
        <?= $page->getConverted() ?>
    </div>
</section>

