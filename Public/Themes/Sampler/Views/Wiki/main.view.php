<?php use CMW\Manager\Env\EnvManager;

$title = "Wiki";
$description = "Wiki";

 ?>

<section class="page-section">
    <h1 class="text-center">Wiki</h1>
    <div class="container">
        <div class="row">
            <div class="card p-4 col-lg-3">
                <div class="mb-2">
                    <?php foreach ($categories as $categorie): ?>
                        <h5><i class="<?= $categorie->getIcon() ?>"></i> <?= $categorie->getName() ?></h5>
                    <ul style="list-style: none">
                        <?php foreach ($categorie?->getArticles() as $menuArticle): ?>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "wiki/" . $categorie->getSlug() . "/" . $menuArticle->getSlug() ?>">
                                    <i class="<?= $menuArticle->getIcon() ?>"></i> <?= $menuArticle->getTitle() ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php endforeach; ?>
                </div>

            </div>
            <div class="card p-4 col-lg-9">
                <?php if($article !== null): ?>
                <i class="<?= $article->getIcon() ?>"></i><?= $article->getTitle() ?>
                    <?= $article->getContent() ?>
                    <?= date("d/m/Y", strtotime($article->getDateCreate())) ?>
                    <?= $article->getAuthor()->getPseudo() ?>
                    <?= date("d/m/Y", strtotime($article->getDateUpdate())) ?>
                <?php elseif($firstArticle === null && $article !== null): ?>
                    You haven't started creating your Wiki yet!
                <?php else: ?>
                <i class="<?= $firstArticle->getIcon() ?>"></i><?= $firstArticle->getTitle() ?>
                    <?= $firstArticle->getContent() ?>
                    <?= date("d/m/Y", strtotime($firstArticle->getDateCreate())) ?>
                    <?= $firstArticle->getAuthor()->getPseudo() ?>
                    <?= date("d/m/Y", strtotime($firstArticle->getDateUpdate())) ?>
                <?php endif; ?>
            </div>
        </div>




    </div>
</section>

