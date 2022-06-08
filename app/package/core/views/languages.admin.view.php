<?php use CMW\Model\coreModel;

$title = CORE_LANG_TITLE;
$description = CORE_LANG_DESC; ?>

<?php ob_start(); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline card-outline-tabs">

                        <!-- TABS HEADER -->
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <?php $i = 1;
                                foreach (getAllPackagesInstalled() as $package): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $i == 1 ? "active" : "" ?>" id="tabs-<?= $package ?>-tab"
                                           data-toggle="pill" href="#tab-<?= $package ?>" role="tab"
                                           aria-controls="tab-<?= $package ?>"
                                           aria-selected="<?= $i == 1 ? "true" : "" ?>"><?= $package ?></a>
                                    </li>
                                    <?php $i++; endforeach; ?>
                            </ul>
                        </div>

                        <!-- CONTENT -->
                        <div class="card-body">
                            <div class="tab-content" id="tabs-languages">

                                <?php $i = 1;
                                foreach (getAllPackagesInstalled() as $package): ?>
                                    <div class="tab-pane fade <?= $i == 1 ? "active show" : "" ?>"
                                         id="tab-<?= $package ?>" role="tabpanel" aria-labelledby="tab-<?= $package ?>">


                                        <?php foreach (coreModel::getLanguages($package) as $key => $item): ?>

                                            <div class="input-group row mb-3">
                                                <label for="<?= $key ?>"
                                                       class="col-sm-2 col-form-label"><?= $key ?></label>

                                                <input type="text" class="form-control" id="<?= $key ?>"
                                                       placeholder="<?= htmlspecialchars($item) ?>"
                                                       value="<?= htmlspecialchars($item) ?>">

                                                <span class="input-group-append">
                                                    <button type="button" class="btn btn-info btn-flat"><?= CORE_BTN_SAVE ?></button>
                                                </span>
                                            </div>


                                        <?php endforeach; ?>


                                    </div>
                                    <?php $i++; endforeach; ?>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $content = ob_get_clean(); ?>