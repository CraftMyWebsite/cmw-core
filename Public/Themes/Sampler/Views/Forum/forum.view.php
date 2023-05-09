<?php
/** @var \CMW\Entity\Forum\ForumEntity $forum */

/** @var \CMW\Model\Forum\TopicModel $topicModel */

/** @var \CMW\Model\Forum\ForumModel $forumModel */

use CMW\Manager\Env\EnvManager;

$title = "Titre de la page";
$description = "Description de votre page";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php if ($forumModel->hasSubForums($forum->getId())): ?>
    <h1>Sous-Forums</h1>
    <div class="container">
        <?php foreach ($forumModel->getForumByParent($forum->getId(), true) as $forumEntity): ?>
            <h3><?= $forumEntity->getId() . ". " . $forumEntity->getName() ?></h3>
            <a href="/<?= $forumEntity->getLink() ?>">Aller vers ce Forum</a>
        <?php endforeach; ?>
    </div>
<?php endif ?>
<h1>Topics</h1>
<div class="container">
    <a href="<?= $forum->getSlug() ?>/add" class="btn">Ajouter un topic</a>
    <?php foreach ($topicModel->getTopicByForum($forum->getId()) as $topic): ?>

        <h3 <?= $topic->isImportant() ? " style='color: red'" : "" ?>>
            <?= $topic->getId() . ". " . $topic->getName() ?> <?= $topic->isPinned() ? " - épinglé" : "" ?>
        </h3>
        <a href="/<?= $topic->getLink() ?>">Aller vers ce Topic</a>
        =>
        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?><?= $topic->getPinnedLink() ?>">
            <?= $topic->isPinned() ? " Désépingler ce topic" : " Épingler ce topic" ?>
        </a>
    <?php endforeach; ?>
</div>

