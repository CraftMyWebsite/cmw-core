<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

Website::setTitle('News');
Website::setDescription('Affichage de toutes vos news');

/* @var \CMW\Entity\News\NewsEntity[] $newsList */
/* @var \CMW\Model\News\NewsModel $newsModel => $newsModel->getSomeNews(3, 'DESC') */
?>

<section class="page-section">
    <h1 class="text-center">NEWS</h1>
    <div class="container">
        <?php foreach ($newsList as $news): ?>
            <div class="card mb-4 p-4">
                <div class="text-center"><img style="max-width: 200px" src="<?= $news->getImageLink() ?>" alt="..."/>
                </div>
                <h3 class="text-center"><a
                        href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>news/<?= $news->getSlug() ?>"><?= $news->getTitle() ?></a>
                </h3>

                <p>Contenue : <?= $news->getDescription() ?></p>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>news/<?= $news->getSlug() ?>"></a>
                <?= $news->getLikes()->getTotal() ?>

                <!--YOU CAN CHECK LIKE THIS FOR LIKES -->
                <?php if ($news->getLikes()->userCanLike()): ?>
                    <?php if (UsersController::isUserLogged()) {
                        echo 'You already love!';
                    } else {
                        echo 'Log in to like!';
                    } ?>
                <?php else: ?>
                    <a href="<?= $news->getLikes()->getSendLike() ?>">You will like</a>
                <?php endif; ?>
                <small>Par : <?= $news->getAuthor()->getPseudo() ?> le <?= $news->getDateCreated() ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</section>
