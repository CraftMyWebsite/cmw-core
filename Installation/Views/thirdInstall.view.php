<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\EnvManager;

?>
<div class="lg:flex flex-wrap mb-2">
    <div class="lg:w-1/2 lg:pr-2">
        <label class="label">
            <span class="label-text"><?= LangManager::translate("Installation.search") ?> :</span>
        </label>
        <label class="input-group">
            <span><i class="fa-solid fa-magnifying-glass"></i></span>
            <input id="searchInput" onkeyup="searchFunction()" type="text" placeholder="<?= LangManager::translate("Installation.search") ?>"
                   class="input input-bordered input-sm w-full" required>
        </label>
    </div>

    <div class="lg:pl-2 lg:w-1/2">
        <div class="form-control  w-full">
            <label class="label">
                <span class="label-text">Tags :</span>
            </label>
            <select class="select select-sm select-bordered">
                <option disabled selected>Personnalisé</option>
                <option>Minecraft</option>
                <option>Communautaire</option>
                <option>E-Commerce</option>
                <option>Blog</option>
                <option>Portfolio</option>
            </select>
        </div>

    </div>
</div>

<ul id="mySearch" class="lg:flex flex-wrap">

    <li class="lg:w-1/3 lg:px-2 mb-4 h-fit">
        <div class="font-bold text-lg bg-gray-700 rounded-t-2xl p-1">
            <div class="flex flex-wrap">
                <img class="w-7 mr-2" src="Installation/Views/Assets/Img/other.png" alt="Other picture">
                <span class="font-medium text-lg"><?= LangManager::translate("Installation.bundle.custom") ?></span>
            </div>
        </div>

        <div class="bg-gray-800 rounded-b-2xl">
            <div class="p-4">
                <?= LangManager::translate("Installation.bundle.customText") ?>
            </div>
            <form action="installer/submit" method="post" id="mainForm">
                <div class="flex justify-end">
                    <button id="formBtn" type="submit" class="btn btn-primary py-1 px-2 rounded rounded-br-xl">
                        <?= LangManager::translate("core.btn.continue") ?>
                    </button>
                </div>
            </form>
        </div>
    </li>

    <!-- Bundle -->

    <?php foreach (PublicAPI::getData("resources/getBundles&lang=" . EnvManager::getInstance()->getValue("locale")) as $bundle): ?>
        <li class="lg:w-1/3 lg:px-2 mb-4 h-fit">
            <div class="font-bold text-lg bg-gray-700 rounded-t-2xl p-1">
                <div class="flex flex-wrap">
                    <img class="w-7 mr-2" src="<?= $bundle['image'] ?>" alt="Image <?= $bundle['image'] ?>">
                    <span class="font-medium text-lg"><?= $bundle['name'] ?></span>
                </div>
            </div>

            <div class="bg-gray-800 rounded-b-2xl">
                <div class="p-4">
                    <p><?= $bundle['description'] ?></p>
                    <p class="mt-2">Ce bundle inclus les packages :</p>
                    <ul style="list-style: inside;">
                        <?php foreach ($bundle['content'] as $bundleResource): ?>
                            <li>
                                <?= $bundleResource['type'] === 0 ?
                                    '<i class="fa-solid fa-paintbrush"></i> ' :
                                    '<i class="fa-solid fa-box"></i> ' ?>
                                <?= $bundleResource['name'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <form action="installer/submit" method="post">
                    <div class="flex justify-end">
                        <input type="hidden" name="bundleId" value="<?= $bundle['id'] ?>">
                        <button type="submit" class="btn btn-primary py-1 px-2 rounded rounded-br-xl" onclick="customLaunchLoader()">
                            <?= LangManager::translate("core.btn.continue") ?>
                        </button>
                    </div>
                </form>
            </div>
        </li>

    <?php endforeach; ?>

    <!-- /Bundle -->

</ul>
<script src="Installation/Views/Assets/Js/search.js"></script>