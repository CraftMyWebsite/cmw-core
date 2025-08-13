<?php


use CMW\Utils\Website;

/** @var \CMW\Entity\Core\TermsEntity $term */

/* TITRE ET DESCRIPTION */
Website::setTitle( htmlspecialchars($term->getType()->label()));
Website::setDescription(htmlspecialchars($term->getType()->label()) . ' ' . htmlspecialchars($term->getPublishedAt()));
?>

<style>
    .class-4541232-wrap{width:50%;margin: 20px auto 40px auto;padding:1.25rem}
    .class-4541232-head{margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid}
    .class-4541232-title{margin:0}
    .class-4541232-meta{font-size:.9rem;opacity:.8;margin:.25rem 0 0 0}
    .class-4541232-content{line-height:1.7;word-break:break-word;overflow-wrap:anywhere}
    .class-4541232-content img, .class-4541232-content video{max-width:100%;height:auto}
    .class-4541232-content h2, .class-4541232-content h3{margin-top:1.25rem}
    .class-4541232-content ul, .class-4541232-content ol{padding-left:1.25rem}
</style>

<article class="class-4541232-wrap">
    <header class="class-4541232-head">
        <h1 class="class-4541232-title"><?= htmlspecialchars($term->getType()->label()) ?></h1>
        <p class="class-4541232-meta">Dernière mise à jour : <?= htmlspecialchars($term->getPublishedAt()) ?></p>
    </header>

    <div class="class-4541232-content">
        <?= $term->getContent() ?>
    </div>
</article>