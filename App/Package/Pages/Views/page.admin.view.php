<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Website;

$title = LangManager::translate("pages.list.title");
$description = LangManager::translate("pages.list.desc"); ?>

<div class="page-title">
    <h3><i class="fa-solid fa-file-lines"></i> <?= LangManager::translate("pages.list.sub_title") ?></h3>
    <a href="pages/add" class="btn-primary"><?= LangManager::translate("pages.creation.add") ?></a>
</div>

<div class="card">
    <div class="table-container">
        <table id="table1">
            <thead>
            <tr>
                <th><?= LangManager::translate("pages.title") ?></th>
                <th><?= LangManager::translate("pages.link") ?></th>
                <th><?= LangManager::translate("pages.author") ?></th>
                <th><?= LangManager::translate("pages.draft") ?></th>
                <th><?= LangManager::translate("pages.creation.update") ?></th>
                <th><?= LangManager::translate("pages.creation.date") ?></th>
                <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php /** @var \CMW\Entity\Pages\PageEntity[] $pagesList */
            foreach ($pagesList as $page) : ?>
                <tr>
                    <td>
                        <?= $page->getTitle() ?>
                    </td>
                    <td>
                        <a href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . $page->getSlug() ?>"
                           target="_blank"><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . $page->getSlug() ?></a>
                    </td>
                    <td class="flex items-center gap-2">
                        <img class="avatar-rounded w-6 h-6" src="<?= $page->getUser()->getUserPicture()->getImage() ?>" alt="user picture">
                        <?= $page->getUser()->getPseudo() ?>
                    </td>
                    <td>
                        <?php if ($page->getState() === 1): ?> <?= LangManager::translate("pages.list.yes") ?><?php else: ?> <?= LangManager::translate("pages.list.no") ?><?php endif; ?>
                    </td>
                    <td>
                        <?= $page->getEdited() ?>
                    </td>
                    <td>
                        <?= $page->getCreated() ?>
                    </td>
                    <td class="text-center">
                        <a class="font-medium text-blue-600 dark:text-blue-500"
                           href="pages/edit/<?= $page->getSlug() ?>">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <button data-modal-toggle="modal-<?= $page->getId() ?>" class="font-medium text-red-400 ml-2"
                                type="button"><i class="fa-solid fa-trash"></i></button>
                        <div id="modal-<?= $page->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6><?= LangManager::translate("pages.delete.button") ?> <?= $page->getTitle() ?>
                                        ?</h6>
                                    <button type="button" data-modal-hide="modal-<?= $page->getId() ?>"><i
                                            class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <p><?= LangManager::translate("pages.delete.message") ?></p>
                                </div>
                                <div class="modal-footer">
                                    <a href="pages/delete/<?= $page->getId() ?>" class="btn-danger">
                                        <span class=""><?= LangManager::translate("pages.delete.button") ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>