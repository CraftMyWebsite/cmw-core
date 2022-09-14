<?php

use CMW\Manager\Lang\LangManager;

/* @var $currentTheme \CMW\Entity\Core\ThemeEntity */
/* @var $installedThemes \CMW\Entity\Core\ThemeEntity[] */
/* @var $themesList */

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


                <!--- LIST THEMES -->
                <div class="col-md-12 col-xl-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <?= LangManager::translate("core.theme.config.list.title") ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <div class="text-center">
                                        <p>
                                            <i class="fas fa-info-circle"></i> <?= LangManager::translate("core.theme.config.list.info") ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= LangManager::translate("core.theme.config.list.name") ?></th>
                                    <th class="text-center"><?= LangManager::translate("core.theme.config.list.version") ?></th>
                                    <th class="text-center"><?= LangManager::translate("core.theme.config.list.cmw_version") ?></th>
                                    <th class="text-center"><?= LangManager::translate("core.theme.config.list.downloads") ?></th>
                                    <th class="text-center"><?= LangManager::translate("core.theme.config.list.download") ?></th>

                                </tr>
                                </thead>
                                <!-- Get availables themes -->
                                <?php foreach ($themesList as $theme): ?>
                                    <tbody>
                                    <td class="text-center"><?= $theme->title ?></td>
                                    <td class="text-center"><?= $theme->version ?></td>
                                    <td class="text-center"><?= $theme->version_cmw ?></td>
                                    <td class="text-center"><?= $theme->downloads ?></td>
                                    <td class="text-center"><a href="install/<?= $theme->id ?>"
                                                               class="btn btn-primary"><?= LangManager::translate("core.theme.config.list.download") ?></a>
                                    </td>
                                    </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <div class="card-footer">

                            <div class="row">

                                <div class="offset-md-4"></div>
                                <div class="col-md-4">

                                    <div class="d-flex justify-content-center">

                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination">
                                                <li class="page-item">
                                                    <button class="page-link" onclick="lessIndex();" aria-hidden="true"
                                                            id="left">
                                                        <span aria-hidden="true">&laquo;</span>
                                                        <span class="sr-only"><?= LangManager::translate("core.datatables.list.previous") ?></span>
                                                    </button>
                                                </li>
                                                <input min="0" step="1" class="text-center inputwithoutarrow" max="9999"
                                                       onchange="setIndex();"
                                                       id="block" type="number" value="0"/>

                                                <li class="page-item">
                                                    <button class="page-link" onclick="moreIndex();" aria-hidden="true"
                                                            id="right">
                                                        <span aria-hidden="true">&raquo;</span>
                                                        <span class="sr-only"><?= LangManager::translate("core.datatables.list.next") ?></span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </nav>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- /.row -->
    </div>
</div>