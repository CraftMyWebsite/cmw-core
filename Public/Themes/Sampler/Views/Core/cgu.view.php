<?php

use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgu */

Website::setTitle('CGU');
Website::setDescription(Website::getWebsiteName() . ' CGU');
?>
<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">C.G.U</h1>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?= $cgu->getContent() ?><br>
        <?= $cgu->getLastEditor()->getPseudo() ?>
        <?= $cgu->getUpdate() ?>
    </div>
</section>
