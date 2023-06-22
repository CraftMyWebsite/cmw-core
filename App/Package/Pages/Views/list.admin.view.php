<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Website;

$title = LangManager::translate("pages.list.title");
$description = LangManager::translate("pages.list.desc"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-file-lines"></i> <span class="m-lg-auto"><?= LangManager::translate("pages.list.sub_title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("pages.list.title") ?></h4>
        </div>
        <div class="card-body">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th class="text-center"><?= LangManager::translate("pages.title") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.link") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.author") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.draft") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.creation.update") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.creation.date") ?></th>
                    <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                </tr>
                </thead>
                <tbody class="text-center">
                    <?php /** @var \CMW\Entity\Pages\PageEntity[] $pagesList */ foreach ($pagesList as $page) : ?>
                    <tr>
                        <td><?= $page->getTitle() ?></td>
                        <td><a href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "p/" . $page->getSlug() ?>" target="_blank"><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "p/" . $page->getSlug() ?></a></td>
                        <td><?= $page->getUser()->getPseudo() ?></td>
                        <td><?php if ($page->getState() === 2): ?> <?= LangManager::translate("pages.list.yes") ?> <?php else: ?> <?= LangManager::translate("pages.list.no") ?> <?php endif; ?></td>
                        <td><?= $page->getEdited() ?></td>
                        <td><?= $page->getCreated() ?></td>
                        <td>
                            <a class="me-3" href="../pages/edit/<?= $page->getSlug() ?>">
                                <i class="text-primary fa-solid fa-gears"></i>
                            </a>
                            <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $page->getId() ?>">
                                <i class="text-danger fas fa-trash-alt"></i>
                            </a>
                            <div class="modal fade text-left" id="delete-<?= $page->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                            <h5 class="modal-title white" id="myModalLabel160"><?= LangManager::translate("pages.delete.button") ?> <?= $page->getTitle() ?> ?</h5>
                                        </div>
                                        <div class="modal-body text-left">
                                            <?= LangManager::translate("pages.delete.message") ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                            </button>
                                            <a href="../Pages/delete/<?= $page->getId() ?>" class="btn btn-danger">
                                                <span class=""><?= LangManager::translate("pages.delete.button") ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="button text-end">
                    <a href="add" class="btn btn-primary">
                        <?= LangManager::translate("pages.creation.add") ?>
                    </a>
            </div>

        </div>
    </div>
</section>