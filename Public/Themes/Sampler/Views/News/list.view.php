<?php

use CMW\Manager\Security\SecurityManager;

$title = "News";
$description = "Affichage de toutes vos news";
/* @var \CMW\Entity\News\NewsEntity[] $newsList */
/* @var \CMW\Model\News\NewsModel $newsModel => $newsModel->getSomeNews(3, 'DESC') */
?>

<main>
    <?php foreach ($newsList as $news): ?>
        <hr>
        <div style="text-align: center">
            <h4><?= $news->getTitle() ?></h4>
            <img src="<?= $news->getImageLink() ?>" height="250" width="250">
            <br>
            <p><?= $news->getContent() ?></p>
            <p>- <?= $news->getAuthor()->getUsername() ?></p>
            <p>Nombre de likes: <?= $news->getLikes()->getTotal() ?></p>

            <?php if ($news->getLikes()->userCanLike()): ?>
                <a href="#">Vous avez déjà liké cet article</a>
            <?php else: ?>
                <a href="<?= $news->getLikes()->getSendLike() ?>">Liker l'article</a>
            <?php endif; ?>

            <br>
            <small>Lire la news en cliquant <a href="news/<?= $news->getSlug() ?>">ici</a></small>

            <h5>-- Commenter --</h5>
            <form method="post" action="<?= $news->sendComments() ?>">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <textarea name="comments"></textarea>
                <button type="submit">Envoyer</button>
            </form>

            <h6>Liste des commentaires: </h6>
            <?php foreach ($news->getComments() as $comment): ?>

            <img src="<?= $comment->getUser()->getUserPicture()->getImageLink() ?>" alt="cc">

                <p><?= $comment->getContent() ?></p>

                <?php if ($comment->userCanLike()): ?>
                    <a href="#">Vous avez déjà liké ce commentaire</a>
                <?php else: ?>
                    <a href="<?= $comment->getSendLike() ?>">Liker ce commentaire</a>
                <?php endif; ?>


                <p>Likes: <?= $comment->getLikes()->getTotal() ?></p>

            <?php endforeach; ?>

        </div>
        <hr>
    <?php endforeach; ?>
</main>
