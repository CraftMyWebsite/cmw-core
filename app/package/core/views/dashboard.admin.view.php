<?php use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
$title = LangManager::translate("core.dashboard.title");
$description = LangManager::translate("core.dashboard.desc"); 
?>

<h3>
    <span class="m-lg-auto">Tableau de bord</span>
</h3>

<!-- EXTENSION: Apexcharts -->
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>admin/resources/vendors/apexcharts/apexcharts.min.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>admin/resources/assets/js/pages/dashboard.js"></script>
