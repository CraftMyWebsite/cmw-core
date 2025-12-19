<?php
use CMW\Entity\Core\TermsEntity;
use CMW\Manager\Security\SecurityManager;
use CMW\Type\Core\Enum\TermType;
use CMW\Utils\Website;

/** @var TermType[] $types */
/** @var array<string,TermsEntity> $terms */

Website::setTitle('Mise à jour des termes et conditions');
Website::setDescription('Veuillez les accepter pour continuer');
?>
<style>
    .class-781220-wrap{width:50%;margin: 20px auto 40px auto;padding:1.25rem}
    .class-781220-head{margin:0 0 1rem 0;padding:0 0 .5rem 0;border-bottom:1px solid}
    .class-781220-list a{text-decoration:none;border:1px solid;padding:.25rem .5rem;border-radius:.5rem}
    .class-781220-sec{margin:1.25rem 0;padding:1rem;border:1px solid;border-radius:.5rem}
    .class-781220-meta{font-size:.9rem;opacity:.8;margin:.25rem 0 1rem 0}
    .class-781220-content{line-height:1.7;word-break:break-word;overflow-wrap:anywhere}
    .class-781220-content img,.class-781220-content video{max-width:100%;height:auto}
    .class-781220-actions{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:1rem}
    .class-781220-checkbox{display:flex;align-items:center;gap:.5rem}
    .class-781220-submit{padding:.5rem .9rem;border:1px solid;border-radius:.5rem;cursor:pointer}
</style>

<div class="class-781220-wrap" id="top">
    <header class="class-781220-head">
        <h1><?= count($types) ? 'Nos termes et conditions !' : 'Conditions d’utilisation' ?></h1>
        <p>Veuillez-les accepter pour continuer à utiliser le service !</p>
    </header>

    <form method="post">
        <?php SecurityManager::getInstance()->insertHiddenToken(); ?>

        <?php foreach ($types as $t): $key=$t->value; $term=$terms[$key] ?? null; if(!$term) continue; ?>
            <section class="class-781220-sec">
                <h2><?= htmlspecialchars($t->label()) ?></h2>
                <p class="class-781220-meta">Dernière mise à jour : <?= htmlspecialchars($term->getPublishedAt()) ?></p>
                <div class="class-781220-content"><?= $term->getContent(); ?></div>
            </section>
        <?php endforeach; ?>

        <div class="class-781220-actions">
            <label class="class-781220-checkbox">
                <input type="checkbox" name="accept_all" value="1" >
                <span>J’ai lu et j’accepte l’ensemble des documents ci-dessus</span>
            </label>
            <button type="submit" class="class-781220-submit">Continuer</button>
        </div>
    </form>
</div>
