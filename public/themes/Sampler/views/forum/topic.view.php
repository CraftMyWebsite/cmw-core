<?php
/** @var \CMW\Model\Forum\ResponseModel $responseModel */

/** @var \CMW\Entity\Forum\TopicEntity $topic
 */

use CMW\Manager\Security\SecurityManager;

$title = "Titre de la page";
$description = "Description de votre page";
?>
<section>
    <div class="container">
        <h2><?= "{$topic->getId()}. {$topic->getName()}" ?></h2>
        <p>
            <?= $topic->getContent() ?>
        </p>
    </div>
    <div class="container">
        <h3>Réponses :</h3>
        <?php if (!$responseModel->countResponseInTopic($topic->getId())): ?>
            <h4 style="text-align: center">Aucune réponse...</h4>
        <?php endif; ?>
        <?php foreach ($responseModel->getResponseByTopic($topic->getId()) as $response) : ?>
            <h4><?= $response->getContent() ?></h4>
            <span><?= $response->getUser()->getUsername() ?></span>
            <?php if ($response->isSelfReply()): ?>
                <a href="<?= $response->deleteLink() ?>">Supprimer ma réponse</a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <hr>
</section>

<?php if($topic->isDisallowReplies()): ?>

<section>
    Les réponses sont désactivés sur ce topic.
</section>

<?php else: ?>

<section>
    <form action="" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <label style="display:block;" for="topicResponse">Votre réponse : </label>
        <input hidden type="text" name="topicId" value="<?= $topic->getId() ?>">
        <textarea required name="topicResponse" id="topicResponse" cols="30" rows="10"></textarea>
        <input type="submit" value="Envoyer !">
    </form>
</section>

<?php endif; ?>

<?php if ($topic->getTags() === []): ?>

<div class="container" style="margin-top: 25px">
    <h5>Ce topic ne possède pas de tags</h5>
</div>

<?php else: ?>

<div class="container" style="margin-top: 25px">
    Voici les tags de ce topic:

    <ul>
        <?php foreach ($topic->getTags() as $tag): ?>
            <li>#<?= $tag->getContent() ?></li>
        <?php endforeach; ?>
    </ul>

</div>
<?php endif; ?>