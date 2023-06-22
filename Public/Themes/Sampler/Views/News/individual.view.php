<?php
/* @var \CMW\Entity\News\NewsEntity $news */

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;

$title = "News - " . $news->getTitle();
$description = "Affichage de la news " . $news->getTitle();
?>


<section class="page-section">
    <h1 class="text-center">NEWS : <?= $news->getTitle() ?></h1>
    <div class="container">
        <a href="/news"><< Revenir aux news</a>
        <div class="card p-5">
            <h3></h3>
            <img src="<?= $news->getImageLink() ?>" height="250" width="250">
            <br>
            <p>Contenue : <?= $news->getContent() ?></p>
            <?= $news->getLikes()->getTotal() ?>
            <?php if ($news->getLikes()->userCanLike()): ?>
                <?php if (UsersController::isUserLogged()) {
                    echo "You already love!";
                } else {
                    echo "Log in to like!";
                } ?>
            <?php else: ?>
                <a href="<?= $news->getLikes()->getSendLike() ?>">You will like</a>
            <?php endif; ?>
            <small>Par : <?= $news->getAuthor()->getPseudo() ?> le <?= $news->getDateCreated() ?></small>
        </div>

        <h2 class="text-center mt-2">Espace commentaire</h2>
        <?php foreach ($news->getComments() as $comment): ?>
            <div class="card mb-2">
                <div class="row">
                    <div class="col-lg-2 text-center">
                        <img style="max-width: 150px" src="<?= $comment->getUser()->getUserPicture()->getImageLink() ?>"
                             alt="...">
                    </div>
                    <div class="col-lg-9">
                        <p><?= $comment->getUser()->getPseudo() ?> :
                            <?= $comment->getContent() ?></p>
                        <small><?= $comment->getDate() ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="card">
            <form method="post" action="<?= $news->sendComments() ?>" class="">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <h4>Votre commentaire :</h4>
                <textarea style="width: 100%" name="comments" placeholder="Bonjour," required></textarea>
                <div class="text-center mt-4">
                    <?php if (UsersController::isUserLogged()): ?>
                        <button type="submit">Commenter</button>
                    <?php else: ?>
                        <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>login">Commenter</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

    </div>
</section>