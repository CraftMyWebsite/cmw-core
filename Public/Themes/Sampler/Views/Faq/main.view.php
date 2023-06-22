<?php
$title = "FAQ";
$description = "Affichage de toutes les faq";

/* @var \CMW\Entity\Faq\FaqEntity[] $faqList */

?>

<section class="page-section">
    <h1 class="text-center">F.A.Q</h1>
    <div class="container">
        <?php foreach ($faqList as $faq) : ?>
        <div class="card mb-4">
            <p><b>Question : <?= $faq->getQuestion() ?></b></p>
            <p>Réponse : <?= $faq->getResponse() ?></p>
            <p><small>Auteur : <?= $faq->getAuthor()->getPseudo() ?></small></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>