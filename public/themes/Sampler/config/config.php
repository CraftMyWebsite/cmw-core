<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\ThemeModel;

?>

<div class="row">
    <div class="col-12">
        <form action="" method="post"> <!-- IMPORTANT, KEEP THE FORM -->
            <div class="card card-primary">
                <div class="card-body">

                    <!-- CONFIG TABS -->

                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="config-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="config-tabs-colors-tab" data-toggle="pill"
                                       href="#config-tabs-colors" role="tab" aria-controls="config-tabs-colors"
                                       aria-selected="true">Couleurs</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="config-tabs-test-tab" data-toggle="pill"
                                       href="#config-tabs-test" role="tab"
                                       aria-controls="config-tabs-test" aria-selected="false">Test</a>
                                </li>
                            </ul>
                        </div>

                        <!-- CONFIG TABS CONTENT -->

                        <div class="card-body">
                            <div class="tab-content" id="config-tabs-colors-tabContent">
                                <!-- COLORS -->
                                <div class="tab-pane fade active show" id="config-tabs-colors" role="tabpanel"
                                     aria-labelledby="config-tabs-colors-tab">
                                    <!-- CONTENT -->
                                    <div class="form-group">
                                        <label for="primaryColor">Couleur principale</label>
                                        <input type="color" id="primaryColor" name="primaryColor"
                                               value="<?= ThemeModel::fetchConfigValue('primaryColor') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="secondaryColor">Couleur secondaire</label>
                                        <input type="color" id="secondaryColor" name="secondaryColor"
                                               value="<?= ThemeModel::fetchConfigValue('secondaryColor') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="backgroundColor">Couleur d'arrière plan</label>
                                        <input type="color" id="backgroundColor" name="backgroundColor"
                                               value="<?= ThemeModel::fetchConfigValue('backgroundColor') ?>">
                                    </div>
                                </div>

                                <!-- TEST -->
                                <div class="tab-pane fade" id="config-tabs-test" role="tabpanel"
                                     aria-labelledby="config-tabs-test-tab">
                                    <!-- CONTENT -->
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit"
                            class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                </div>
            </div>
        </form>
    </div>
</div>