<?php

/* @var \CMW\Entity\Pages\PageEntity $page */


use CMW\Utils\Website;

Website::setTitle(ucfirst($page->getTitle()));
Website::setDescription(ucfirst($page->getTitle()));
?>
<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<h1><?= ucfirst($page->getTitle()) ?></h1>

<?= $page->getConverted() ?>
</section>


