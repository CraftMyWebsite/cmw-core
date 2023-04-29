<?php

use CMW\Model\Users\usersModel;
use CMW\Utils\Utils;

$title = "One-Dream | Voter";
$description = "Votez pour le serveur One-Dream et gagnez des récompenses uniques!";

/* @var \CMW\Entity\Votes\VotesSitesEntity[] $sites */
/* @var $topCurrent */
/* @var $topGlobal */

//Convert minutes to dates
function convertDate($minutes): string
{
    $t = $minutes;
    $h = floor($t / 60) ? floor($t / 60) . 'h ' : '';
    $m = $t % 60 ? $t % 60 . 'm' : '';

    return $h && $m ? $h . $m : $h . $m;
}

?>

<main role="main">
    <div class="container">
        <div class="content">

            <?php if (usersModel::getLoggedUser() === -1): ?>
                <!-- Si le joueur n'est pas connecté -->
                <div class="panel">
                    <div class="panel__heading">
                        <h1>Connectez-vous</h1>
                    </div>
                    <div class="panel__body">

                        <!-- Information -->
                        <div class="">
                            <p class="text-center">Pour pouvoir voter et donc récupérer vos récompenses vous devez être
                                connecté
                                sur le site, alors n'attendez plus pour obtenir des <strong>récompenses uniques</strong>
                                !

                                <br>

                                <strong>Connectez-vous</strong> dès maintenant en cliquant <a
                                    href="<?= getenv('PATH_SUBFOLDER') ?>connexion">ici</a>
                            </p>


                        </div>

                    </div>
                </div>

            <?php else: ?>

                <div class="panel">
                    <div class="panel__heading">
                        <h1>Votez</h1>
                    </div>
                    <div class="panel__body">

                        <!-- Information -->
                        <div class="panel__description">
                            <p>Les votes nous permettent de faire connaitre le serveur plus facilement,
                                en contre partie nous vous offrons entre 0 et 3 VotePoints par votes. <br>

                                Les VotePoints sont dépensables en jeux avec la commande <strong>/voteshop</strong>
                            </p>
                            <br>
                            <p>
                                Le système de votes étant en cours de refonte quelques buggs peuvent survenir.<br>
                                Merci de votre compréhension.
                            </p>
                        </div>


                        <div class="category category--list">
                            <!-- LIST SITES -->

                            <?php foreach ($sites as $site): ?>
                                <input type="hidden" id="idSite" value="<?= $site->getIdUnique() ?>" hidden>
                                <input type="hidden" id="urlSite" value="<?= $site->getUrl() ?>" hidden>
                                <input type="hidden" id="token" value="<?= $_SESSION['votes']['token'] ?>" hidden>

                                <div class="package">
                                    <div class="package__info">
                                        <h3><?= $site->getTitle() ?></h3>
                                        <div class="package__tags">
                                            <span class="tag tag--left tag--700">1 à 3 VotePoints</span>
                                            <span class="tag tag--danger"><i
                                                    class="fas fa-stopwatch"></i><?= convertDate($site->getTime()) ?></span>
                                        </div>
                                    </div>
                                    <div class="package__buttons package__buttons--outBasket">
                                        <button type="button" rel="noopener noreferrer"
                                                class="btn btn--primary cursorAura"
                                                name="btnVote" value="<?= $site->getUrl() ?>">Voter
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<section>
    <div class="container">
        <div class="content">

            <div class="panel">
                <div class="panel__heading">
                    <h1>Classement</h1>
                </div>
                <div class="panel__body">

                    <!-- Information -->
                    <div class="panel__description">
                        <p>Dans cette section retrouvez le classement des meilleurs voteurs du mois en cours ! <br><br>
                            <span><em>Liste des récompenses:</em></span><br><br>
                            1er → <strong>350 Tokens + 4 SkyBoxUltimate</strong><br>
                            2ème → <strong>150 Tokens + 3 SkyBoxUltimate</strong><br>
                            3ème → <strong>100 Tokens + 2 SkyBox</strong><br>
                        </p>
                    </div>


                    <div class="panel__heading">
                        <h3>Top 10 du mois</h3>
                    </div>
                    <div class="category category--list">
                        <!-- TOP VOTES CE MOIS-CI -->

                        <div class="table-wrapper">
                            <table class="fl-table">
                                <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Pseudo</th>
                                    <th>Votes</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php $i = 0;
                                foreach ($topCurrent as $top): $i++; ?>

                                    <tr>
                                        <td>#<?= $i ?></td>
                                        <td><?= $top['pseudo'] ?></td>
                                        <td><?= $top['votes'] ?></td>
                                    </tr>

                                <?php endforeach; ?>


                                <tbody>
                            </table>
                        </div>

                    </div>

                    <div class="panel__heading">
                        <h3>Top 10 global</h3>
                    </div>

                    <div class="category category--list">
                        <!-- TOP VOTES TOTAUX -->

                        <div class="table-wrapper">
                            <table class="fl-table">
                                <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Pseudo</th>
                                    <th>Votes</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php $i = 0;
                                foreach ($topGlobal as $top): $i++; ?>

                                    <tr>
                                        <td>#<?= $i ?></td>
                                        <td><?= $top['pseudo'] ?></td>
                                        <td><?= $top['votes'] ?></td>
                                    </tr>

                                <?php endforeach; ?>


                                <tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>
</section>

<div id="snackbar"></div>