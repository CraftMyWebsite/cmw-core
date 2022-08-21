<?php

use CMW\Manager\Lang\LangManager;

/* @var $currentTheme \CMW\Entity\Core\ThemeEntity */
/* @var $installedThemes \CMW\Entity\Core\ThemeEntity[] */

$title = LangManager::translate("core.theme.config.title", lineBreak: true);
$description = LangManager::translate("core.theme.config.desc", lineBreak: true); ?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("core.dashboard.title") ?>:</h3>
                        </div>
                        <div class="card-body">

                            <!-- SELECT THE CURRENT THEME -->


                            <div class="form-group">
                                <label><?= LangManager::translate("core.theme.config.select", lineBreak: true) ?></label>
                                <select class="form-control" name="theme">
                                    <?php foreach ($installedThemes as $theme): ?>
                                        <option value="<?= $theme->getName() ?>" <?= $theme->getName() === $currentTheme->getName() ? "selected" : "" ?>>
                                            <?= $theme->getName() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>