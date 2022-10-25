<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\SecurityService;

$title = LangManager::translate("users.settings.title");
$description = LangManager::translate("users.settings.desc"); ?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.settings.title") ?> :</h3>
                        </div>
                        <div class="card-body">

                            <!-- CONFIG SECTION -->

                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="defaultPicture"  accept=".png, .jpg, .jpeg, .webp, .gif"
                                           name="defaultPicture">
                                    <label class="custom-file-label" for="defaultPicture">
                                        <?= LangManager::translate("users.settings.default_picture") ?>
                                    </label>
                                </div>
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