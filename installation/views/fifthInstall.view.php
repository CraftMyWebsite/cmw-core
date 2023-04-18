<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

?>

<h2 class="text-2xl font-medium text-center"><?= LangManager::translate('installation.themes.title') ?>.</h2>
<p class="text-center"><?= LangManager::translate('installation.themes.sub_title') ?>.</p>

<form action="installer/submit" method="post" id="mainForm">
    <div class="lg:flex flex-wrap mb-2">
        <div class="lg:w-1/2 lg:pr-2">
            <label class="label">
                <span class="label-text"><?= LangManager::translate('installation.packages.search') ?> :</span>
            </label>
            <label class="input-group">
                <span><i class="fa-solid fa-magnifying-glass"></i></span>
                <input id="searchInput" onkeyup="searchFunction()" type="text" name="search"
                       placeholder="<?= LangManager::translate('installation.packages.search') ?>"
                       class="input input-bordered input-sm w-full">
            </label>
        </div>

        <div class="lg:pl-2 lg:w-1/2">
            <div class="form-control  w-full">
                <label class="label">
                    <span class="label-text">
                        <?= LangManager::translate('installation.themes.compatibility') ?> :
                    </span>
                </label>
                <select class="select select-sm select-bordered" disabled>
                    <option selected>-- SOON --</option>
                </select>
            </div>

        </div>
    </div>

    <ul id="mySearch" class="lg:flex flex-wrap">

        <?php foreach (PublicAPI::getData("resources/getResources&resource_type=0&lang=" . Utils::getEnv()->getValue("locale")) as $theme): ?>

            <li class="lg:w-1/3 lg:px-2 mb-4">
                <input class="hidden" id="theme_<?= $theme['id'] ?>" type="radio" name="theme"
                       value="<?= $theme['id'] ?>">
                <label class="label" for="theme_<?= $theme['id'] ?>">
                    <h2 class="font-bold text-lg text-center bg-gray-700 rounded-t-2xl p-1"><?= $theme['name'] ?></h2>
                    <div class="bg-gray-800">
                        <figure><img src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="Shoes"/></figure>
                        <div class="p-2">
                            <p><?= $theme['description'] ?></p>
                            <?php foreach ($theme['tags'] as $tag): ?>
                                <small class="px-1 bg-primary rounded mr-2"><?= $tag['value'] ?></small>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </label>
                <div tabindex="0" class="collapse collapse-arrow bg-primary rounded-b-box">
                    <div class="collapse-title font-medium">
                        <?= LangManager::translate("installation.themes.more") ?>
                    </div>
                    <div class="collapse-content bg-gray-800">
                        <div class="pt-4">
                            <p><i class='fa-solid fa-at'></i><i> <?= $theme['author_pseudo'] ?> </i></p>
                            <p><i class='fa-solid fa-download'></i> <?= $theme['downloads'] ?></p>
                            <p>
                                <?= LangManager::translate("installation.packages.version") ?> :
                                <?= $theme['version_name'] ?>
                            </p>

                            <?= $theme['code_link'] !== "" ?
                                "<p><a href='" . $theme['code_link'] . "' target='_blank'><i class='fa-brands fa-github'></i></a></p>" : "" ?>
                            <?= $theme['demo'] !== "" ?
                                "<p><a href='" . $theme['demo'] . "' target='_blank'> " . LangManager::translate('installation.packages.demo') . "</a></p>" : "" ?>
                        </div>
                    </div>
                </div>
            </li>

        <?php endforeach; ?>

    </ul>

    <div class="card-actions justify-end mt-4">
        <button id="formBtn" type="submit" class="btn btn-primary">
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>

<script>
    function searchFunction() {

        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("mySearch");
        li = ul.getElementsByTagName("li");
        for (let i = 0; i < li.length; i++) {
            span = li[i].getElementsByTagName("h2")[0];
            txtValue = span.textContent || span.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
</script>