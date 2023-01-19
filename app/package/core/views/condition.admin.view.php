<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("core.config.title");
$description = LangManager::translate("core.config.desc");

/* @var CMW\Entity\Core\ConditionEntity $cgv */
/* @var CMW\Entity\Core\ConditionEntity $cgu */
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gavel"></i> <span class="m-lg-auto">Condition general</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <form action="" method="post" enctype="multipart/form-data">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h4>Condition général de vente (CGV)</h4>

                        <input type="text" name="conditionId" value="<?= $cgv->getConditionId() ?>" hidden>
                        <div class="form-check-reverse form-switch">
                            <label class="form-check-label" for="conditionState">Activer les CGV</label>
                            <input class="form-check-input" type="checkbox" id="conditionState"
                                   name="conditionState" <?= $cgv->isConditionState() ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6>Contenue :</h6>
                    <textarea name="conditionContent" id="summernote-2"><?= $cgv->getConditionContent() ?></textarea>
                    <p>Mis à jour par <?= $cgv->getConditionAuthor()->getUsername() ?>
                        le <?= $cgv->getConditionUpdate() ?></p>
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-primary float-right">
                            <?= LangManager::translate("core.btn.save") ?>
                        </button>
                    </div>
            </form>
        </div>
    </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <form action="" method="post" enctype="multipart/form-data">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h4>Condition général d'utilisation (CGU)</h4>
                        <input type="text" name="conditionId" value="<?= $cgu->getConditionId() ?>" hidden>
                        <div class="form-check-reverse form-switch">
                            <label class="form-check-label" for="conditionState">Activer les CGU</label>
                            <input class="form-check-input" type="checkbox" id="conditionState"
                                   name="conditionState" <?= $cgu->isConditionState() ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6>Contenue :</h6>
                    <textarea name="conditionContent" id="summernote-2"><?= $cgu->getConditionContent() ?></textarea>
                    <p>Mis à jour par <?= $cgu->getConditionAuthor()->getUsername() ?>
                        le <?= $cgu->getConditionUpdate() ?></p>
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-primary float-right">
                            <?= LangManager::translate("core.btn.save") ?>
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</section>