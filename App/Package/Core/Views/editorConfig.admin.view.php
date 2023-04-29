<?php

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\EditorController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.editor.title");
$description = LangManager::translate("core.editor.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto"><?= LangManager::translate("core.editor.title") ?></span></h3>
</div>

<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <h6><?= LangManager::translate("core.editor.style") ?> :</h6>
                    <select class="choices form-select" name="style" required>
                        <?php foreach ($installedStyles as $style): ?>
                            <option value="<?= $style ?>" <?= $style === $currentStyle ? "selected" : "" ?>>
                                <?= $style ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small><?= LangManager::translate("core.editor.preview") ?></small>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>











