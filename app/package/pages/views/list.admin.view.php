<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("pages.list.title");
$description = LangManager::translate("pages.list.desc"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-file-lines"></i> <span class="m-lg-auto"><?= LangManager::translate("pages.list.sub_title") ?></span></h3>
</div>

<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate("news.list.list") ?></h4>
        </div>
        <div class="card-body">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th class="text-center"><?= LangManager::translate("pages.title") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.link") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.author") ?></th>
                    <th class="text-center"><?= LangManager::translate("pages.creation.date") ?></th>
                    <th class="text-center"><?= LangManager::translate("core.btn.edit") ?></th>
                </tr>
                </thead>
                <tbody class="text-center">
                    <?php /** @var \CMW\Entity\News\NewsEntity[] $newsList */ foreach ($pagesList as $page) : ?>
                    <tr>
                        <td><?= $page->getTitle() ?></td>
                        <td><a href="<?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "p/" . $page->getSlug() ?>" target="_blank"><?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "p/" . $page->getSlug() ?></a></td>
                        <td><?= $page->getUser()->getUsername() ?></td>
                        <td><?= $page->getCreated() ?></td>
                        <td>
                            <a href="../pages/edit/<?= $page->getSlug() ?>">
                                <i class="text-primary fa-solid fa-gears"></i>
                            </a>
                            <a href="../pages/delete/<?= $page->getId() ?>">
                                <i class="ms-2 text-danger fa-solid fa-trash"></i>
                            </a>
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