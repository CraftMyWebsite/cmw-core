<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate('users.settings.blacklisted.pseudo.title');
$description = LangManager::translate('users.settings.blacklisted.pseudo.description');

/* @var \CMW\Entity\Users\BlacklistedPseudoEntity[] $pseudos */

?>


<section class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate('users.settings.blacklisted.pseudo.title') ?></h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>

                    <div class="form-group position-relative has-icon-left">
                        <input type="text" name="pseudo" class="form-control" id="pseudoInput"
                               placeholder="Teyir" required>
                        <div class="form-control-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="mt-2 ">
                        <button type="submit" class="btn btn-success">
                            <?= LangManager::translate('users.settings.blacklisted.pseudo.btn') ?>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<section class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Liste des pseudos blacklist</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($pseudos as $pseudo) : ?>
                        <tr>
                            <td><?= $pseudo->getPseudo() ?></td>
                            <td><?= $pseudo->getDateBlacklistedFormatted() ?></td>
                            <td>
                                <a class="me-3" href="pseudo/edit/<?= $pseudo->getId() ?>">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </a>
                                <a class="me-3" href="pseudo/delete/<?= $pseudo->getId() ?>">
                                    <i class="text-danger fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>