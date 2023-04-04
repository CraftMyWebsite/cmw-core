<?php /* @var $lang String */ ?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>

<h2 class="text-2xl font-medium text-center"><?= INSTALL_WELCOME_TITLE ?></h2>
<p class="text-center"><?= INSTALL_WELCOME_SUBTITLE ?></p>
<p><?= INSTALL_WELCOME_CONFIG_TITLE ?> :</p>
<div class="overflow-x-auto">
    <table class="table w-full">
        <!-- head -->
        <thead>
        <tr class="text-center">
            <th>WEB</th>
            <th>PHP</th>
            <th>HTTPS</th>
            <th>Configuration</th>
            <th>Extension</th>
        </tr>
        </thead>
        <tbody>
        <!-- row 1 -->
        <tr class="text-center">
            <td><i class="text-green-500 fa-solid fa-check"></i> Apache 2</td>
            <td><i class="text-red-500 fa-solid fa-xmark"></i> 8.1</td>
            <td><i class="text-green-500 fa-solid fa-check"></i> Actif</td>
            <td><i class="text-green-500 fa-solid fa-check"></i> AllowOverride All</td>
            <td><i class="text-green-500 fa-solid fa-check"></i> zip, pdo, curl</td>
        </tr>
        </tbody>
    </table>
</div>

<?= INSTALL_WELCOME_CONTENT ?>

<small>TODO => établir les pré-requis avant l'installation.</small>

<div class="card-actions justify-end">
    <form action="installer/submit" method="post">
        <button type="submit" class="btn btn-primary"><?= INSTALL_BTN_NEXT ?></button>
    </form>
</div>

<script src="installation/views/assets/js/changeLang.js"></script>