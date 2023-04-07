<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

use CMW\Manager\Lang\LangManager;

?>

<div class="lg:flex flex-wrap mb-2">
    <div class="lg:w-1/2 lg:pr-2">
        <label class="label">
            <span class="label-text">Rechercher :</span>
        </label>
        <label class="input-group">
            <span><i class="fa-solid fa-magnifying-glass"></i></span>
            <input id="searchInput" onkeyup="searchFunction()" type="text" placeholder="Rechercher"
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
                <img class="w-7 mr-2" src="/public/img/bundle/other.png">
                <span class="font-medium text-lg">Personnalisé</span>
            </div>
        </div>

        <div class="bg-gray-800 rounded-b-2xl">
            <div class="p-4">
                <p>Personnaliser votre installation vous même.</p>
                <p>Ceci ne veut pas dire qu'il n'est pas possible de le personnaliser avec d'autres bundle.</p>
            </div>
            <form action="installer/submit" method="post" id="mainForm">
                <button id="formBtn" type="submit" class="btn btn-primary">
                    <?= LangManager::translate("core.btn.next") ?>
                </button>
            </form>
        </div>
    </li>

    <!-- Bundle -->

    <li class="lg:w-1/3 lg:px-2 mb-4 h-fit">
        <div class="font-bold text-lg bg-gray-700 rounded-t-2xl p-1">
            <div class="flex flex-wrap">
                <img class="w-7 mr-2" src="/public/img/bundle/community.png">
                <span class="font-medium text-lg">Communautaire</span>
            </div>
        </div>

        <div class="bg-gray-800 rounded-b-2xl">
            <div class="p-4">
                <p>Parfait pour bla bla bla</p>
                <p class="mt-2">Ce bundle inclus les packages :</p>
                <ul style="list-style: inside;">
                    <li>Contact</li>
                    <li>Forum</li>
                </ul>
            </div>
            <form action="installer/submit" method="post">
                <button type="submit" class="btn btn-primary" onclick="customLaunchLoader()">
                    <?= LangManager::translate("core.btn.next") ?>
                </button>
            </form>
        </div>
    </li>

    <!-- /Bundle -->

</ul>

<script src="installation/views/assets/js/search.js"></script>