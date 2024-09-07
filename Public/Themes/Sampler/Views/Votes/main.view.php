<?php

use CMW\Manager\Env\EnvManager;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

Website::setTitle('Voter');
Website::setDescription('Votez dès maintenant pour le site ' . Website::getWebsiteName());

/* @var \CMW\Entity\Votes\VotesSitesEntity[] $sites */
/* @var \CMW\Entity\Votes\VotesPlayerStatsEntity[] $topCurrent */
/* @var \CMW\Entity\Votes\VotesPlayerStatsEntity[] $topGlobal */
?>


<section class="page-section">
    <h1 class="text-center">Votes</h1>
    <div class="container">
        <div class="row">
            <div class="card col-lg-4">
                <?php if (UsersModel::getCurrentUser()?->getId() === -1): ?>
                    <!-- Si le joueur n'est pas connecté -->
                    <div>
                        <h1>Connectez-vous</h1>
                        <p class="text-center">Pour pouvoir voter et donc récupérer vos récompenses vous devez être
                            connecté sur le site, alors n'attendez plus pour obtenir des <strong>récompenses
                                uniques</strong>!
                            <br>
                            <strong>Connectez-vous</strong> dès maintenant en cliquant <a
                                href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">ici</a>
                        </p>
                    </div>
                <?php else: ?>
                    <div>
                        <h1 class="text-center">Votez</h1>
                        <p>Les votes nous permettent de faire connaitre le serveur plus facilement,
                            en contre partie nous vous offrons entre 0 et 3 VotePoints par votes. <br>
                            Les VotePoints sont dépensables en jeux avec la commande <strong>/voteshop</strong>
                        </p>
                        <?php foreach ($sites as $site): ?>
                            <hr>
                            <h3 class="text-center"><?= $site->getTitle() ?></h3>
                            <span>1 à 3 VotePoints</span>
                            <span><?= $site->getTimeFormatted() ?></span>
                            <button onclick="sendVote('<?= $site->getSiteId() ?>', this)" class="p-2">Voter</button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card col-lg-8">
                <h1>Classement</h1>
                <h3>Top 10 du mois</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Position</th>
                        <th>Pseudo</th>
                        <th>Votes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0;
                    foreach ($topCurrent as $top):
                        $i++; ?>
                        <tr>
                            <td>#<?= $i ?></td>
                            <td><?= $top->getUser()->getPseudo() ?></td>
                            <td><?= $top->getVotes() ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tbody>
                </table>
            </div>
        </div>
        <div class="card mt-4">
            <h3>Top 10 global</h3>
            <table>
                <thead>
                <tr>
                    <th>Position</th>
                    <th>Pseudo</th>
                    <th>Votes</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($topGlobal as $top):
                    $i++; ?>
                    <tr>
                        <td>#<?= $i ?></td>
                        <td><?= $top->getUser()->getPseudo() ?></td>
                        <td><?= $top->getVotes() ?></td>
                    </tr>
                <?php endforeach; ?>
                <tbody>
            </table>
        </div>

    </div>
</section>
