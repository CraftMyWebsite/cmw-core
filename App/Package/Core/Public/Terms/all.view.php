<?php


use CMW\Entity\Core\TermsEntity;
use CMW\Type\Core\Enum\TermType;
use CMW\Utils\Website;

/** @var TermType[] $types */
/** @var array<string,TermsEntity> $terms */

/* TITRE ET DESCRIPTION */
Website::setTitle('Termes et conditions');
Website::setDescription('Termes et conditions');
?>
    <style>
        .class-4541231-wrap{width:50%;margin: 20px auto 40px auto;padding:1.25rem}
        .class-4541231-list a{text-decoration:none;padding:.25rem .5rem;border:1px solid;border-radius:.5rem}
        .class-4541231-sec{scroll-margin-top:4rem;margin:2rem 0;padding:1rem;border:1px solid;border-radius:.75rem}
        .class-4541231-meta{font-size:.875rem;opacity:.8;margin:.25rem 0 1rem 0}
        .class-4541231-content{line-height:1.7;word-break:break-word;overflow-wrap:anywhere}
        .class-4541231-content img, .class-4541231-content video{max-width:100%;height:auto}
    </style>

<div class="class-4541231-wrap">

    <?php foreach ($types as $t):
        $key=$t->value; if (!isset($terms[$key])) continue; $term=$terms[$key]; ?>
        <section class="class-4541231-sec">
            <h2><?= htmlspecialchars($t->label()) ?></h2>
            <p class="class-4541231-meta">Dernière mise à jour : <?= htmlspecialchars($term->getPublishedAt()) ?></p>
            <div class="class-4541231-content"><?= $term->getContent(); ?></div>
        </section>
    <?php endforeach; ?>
</div>