<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>

<h2 class="text-2xl font-medium text-center"><?= LangManager::translate('Installation.packages.title') ?>.</h2>
<p class="text-center"><?= LangManager::translate('Installation.packages.sub_title') ?>.</p>
<form action="installer/submit" method="post" id="mainForm">
    <div class="lg:grid grid-cols-2 gap-4 mb-2">
        <div class="">
            <label class="label">
                <span class="label-text"><?= LangManager::translate('Installation.packages.search') ?> :</span>
            </label>
            <label class="input-group">
                <span><i class="fa-solid fa-magnifying-glass"></i></span>
                <input id="searchInput" onkeyup="searchFunction()" type="text"
                       placeholder="<?= LangManager::translate('Installation.packages.search') ?>"
                       name="search"
                       class="input input-bordered input-sm w-full">
            </label>
        </div>

        <!-- TODO Tags -->
        <div class="">
            <div class="form-control  w-full">
                <label class="label">
                    <span class="label-text"><?= LangManager::translate('Installation.packages.tags') ?> :</span>
                </label>
                <select onchange="tagsFunction()" id="selectedTag" class="select select-sm select-bordered" disabled>
                    <option selected>-- SOON --</option>
                </select>
            </div>

        </div>
    </div>

    <div class="lg:grid grid-cols-2 gap-4">
        <ul id="mySearch">
            <div class="mb-4 bg-gray-700 rounded-2xl">
                <h2 class="font-bold text-lg text-center p-1"><?= LangManager::translate('Installation.packages.list_title') ?></h2>
                <div class="px-3 pb-3">

                    <!-- List packages -->
                    <?php foreach (PublicAPI::getData("resources/getResources&resource_type=1&Lang=" . Utils::getEnv()->getValue("locale")) as $package): ?>
                        <?php
                        $tags = [];

                        foreach ($package['tags'] as $tag) {
                            $tags[] = $tag['value'];
                        }

                        $tags = implode(",", $tags);
                        ?>
                        <li onmouseenter="showInfoPackage('<?= $package['name'] ?>','<?= $package['description'] ?>', '<?= $tags ?>',
                                '<?= $package['author_pseudo'] ?>', '<?= $package['price'] ?>', '<?= $package['downloads'] ?>',
                                '<?= $package['version_name'] ?>', '<?= $package['code_link'] ?>', '<?= $package['demo'] ?>')"
                            class="p-1 bg-cmw-gray-sec hover:bg-gray-800 mb-px">
                            <input class="hidden" id="package_<?= $package['id'] ?>" type="checkbox" name="packages[]"
                                   value="<?= $package['id'] ?>">
                            <label class="label flex justify-start gap-2" for="package_<?= $package['id'] ?>">
                                <img src='<?= PublicAPI::getUrl() . '/' . $package["icon"] ?>'
                                     style="max-height: 32px; max-width: 32px"
                                     class="rounded" alt="Logo <?= $package['name'] ?>">
                                <span class="font-medium text-lg"><?= $package['name'] ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>

                </div>
            </div>
        </ul>

        <!-- Help card -->
        <div class="rounded-2xl bg-gray-800  h-fit sticky top-10">
            <h2 id="PackageName" class="font-bold text-lg text-center bg-gray-700 rounded-t-2xl p-1">
                <?= LangManager::translate("Installation.packages.help.title") ?>
            </h2>
            <div class="p-4">
                <p id="PackageInfo" class="mb-2">
                    <?= LangManager::translate("Installation.packages.help.content") ?>
                </p>
                <div id="Author"></div>
                <div id="Download"></div>
                <div id="PackageVersion"></div>
                <div id="CodeLink"></div>
                <div id="DemoLink"></div>
                <div id="Tags"></div>
            </div>
            <div id="Price" class="text-lg text-center bg-gray-700 rounded-b-2xl p-1">
                <small class="mt-2">
                    <?= LangManager::translate("Installation.packages.help.footer") ?>
                </small>
            </div>
        </div>
    </div>

    <div class="card-actions justify-end mt-4">
        <button id="formBtn" type="submit" class="btn btn-primary">
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>
<script src="installation/Views/Assets/Js/changeLang.js"></script>
<script>
    /*
    * Info package
    * */
    let titleParent = document.getElementById("PackageName");
    let descriptionParent = document.getElementById("PackageInfo");
    let tagsParent = document.getElementById("Tags");
    let authorParent = document.getElementById("Author");
    let priceParent = document.getElementById("Price");
    let downloadParent = document.getElementById("Download");
    let packageVersionParent = document.getElementById("PackageVersion");
    let codeLinkParent = document.getElementById("CodeLink")
    let demoLinkParent = document.getElementById("DemoLink")

    function showInfoPackage(title, description, tags, author, price, downloads, version, codeLink, demoLink) {
        /* * * * * * * * TAGS SYS * * * * * * */
        if (tags !== '') {
            const tagsList = tags.split(",");
            tagsParent.innerHTML = "";
            let i = 0;
            while (i < tagsList.length) {
                let div = document.createElement('small');
                div.className = 'px-1 bg-primary rounded mr-2';
                let text = document.createTextNode(tagsList[i]);
                div.appendChild(text);
                tagsParent.appendChild(div)
                i++;
            }
        } else {
            tagsParent.innerHTML = "";
        }
        /* * * * * * * * * * * * * * * * * * * * */

        titleParent.innerHTML = title;
        descriptionParent.innerHTML = description;
        authorParent.innerHTML = "<i class='fa-solid fa-at'></i><i> " + author + "</i>";
        if (price > 0) {
            priceParent.innerHTML = new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR'}).format(price);
        } else {
            priceParent.innerHTML = "<b><?= LangManager::translate('Installation.packages.free') ?></b>";
        }
        downloadParent.innerHTML = "<i class='fa-solid fa-download'></i> " + downloads;
        packageVersionParent.innerHTML = "<?= LangManager::translate('Installation.packages.version') ?>: " + version;

        if (codeLink !== "") {
            codeLinkParent.innerHTML = "<a href='" + codeLink + "' target='_blank'><i class='fa-brands fa-github'></i></a>"
        } else {
            codeLinkParent.innerHTML = ""
        }

        if (demoLink !== "") {
            demoLinkParent.innerHTML = "<a href='" + codeLink + "' target='_blank'><?= LangManager::translate('Installation.packages.demo') ?></a>"
        } else {
            demoLinkParent.innerHTML = ""
        }
    }

    //TODO Tags
    function tagsFunction() {
        let selected = document.getElementById("selectedTag");
        let value = selected.value;
        if (value === "all") {
            console.log("tout est select")
        }
    }

    /*
    *
    * Search bar
    *
    */
    let input, filter, ul, li, span, i, txtValue;

    function searchFunction() {
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("mySearch");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            span = li[i].getElementsByTagName("span")[0];
            txtValue = span.textContent || span.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
</script>