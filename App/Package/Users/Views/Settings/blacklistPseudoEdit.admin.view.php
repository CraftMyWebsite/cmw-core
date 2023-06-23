<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate('users.settings.blacklisted.pseudo.edit.title');
$description = LangManager::translate('users.settings.blacklisted.pseudo.edit.description');

/* @var \CMW\Entity\Users\BlacklistedPseudoEntity $pseudo */

?>

<section class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate('users.settings.blacklisted.pseudo.edit.label', ['pseudo' => $pseudo->getPseudo()]) ?></h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>

                    <div class="form-group position-relative has-icon-left">
                        <input type="text" name="pseudo" class="form-control" id="pseudoInput"
                               value="<?= $pseudo->getPseudo() ?>"
                               placeholder="Teyir" required>
                        <div class="form-control-icon">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="mt-2 ">
                        <button type="submit" class="btn btn-success">
                            Modifier
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

