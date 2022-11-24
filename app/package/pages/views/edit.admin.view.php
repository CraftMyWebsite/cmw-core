<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("pages.edit.title");
$description = LangManager::translate("pages.edit.desc");

/* @var \CMW\Entity\Pages\PageEntity $page
 */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-file-lines"></i> <span class="m-lg-auto"><?= LangManager::translate("pages.edit.title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("pages.title") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="hidden" id="page_id" name="page_id" value="<?= $page->getId() ?>">
                                <input type="text" class="form-control" name="title" required placeholder="<?= LangManager::translate("pages.title") ?>" maxlength="255" value="<?= $page->getTitle() ?>">
                                <div class="form-control-icon">
                                    <i class="fas fa-heading"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6>URL :</h6>
                            <div class="input-group mb-3">
                            <span class="input-group-text" id="inputGroup-sizing-default"><?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "p/" ?></span>
                            <input type="text" value="<?= $page->getSlug() ?>" id="slug" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
                          </div>

                        </div>
                    </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="draft" name="draft" <?= $page->getState() === 2 ? "checked" : ""; ?>>
                                <label class="form-check-label" for="draft"><h6><?= LangManager::translate("pages.draft") ?></h6></label>
                            </div>
                <h6><?= LangManager::translate("pages.creation.content") ?> :</h6>
                <textarea name="content" id="summernote-1"></textarea>

                <div class="text-center mt-2">
                    <a class="btn btn-danger" href="../delete/<?= $page->getId() ?>"><?= LangManager::translate("core.btn.delete") ?></a>
                    <button id="saveButton" type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
                </div>
        </div>
    </div>
</section>