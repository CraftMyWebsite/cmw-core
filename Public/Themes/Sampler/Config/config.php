<?php use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel; ?>
<!-------------->
<!--Navigation-->
<!-------------->
<div class="tab-menu">
    <ul class="tab-horizontal" data-tabs-toggle="#tab-content-config">
        <li>
            <button type="button" data-tabs-target="#tab1" role="tab">Réglages</button>
        </li>
    </ul>
</div>

<!--------------->
<!----CONTENT---->
<!--------------->
<div id="tab-content-1">
    <div class="tab-content" id="tab1">
        <div>
            <label for="buttonColor">Couleur des boutons :</label>
            <input type="color" id="buttonColor" name="buttonColor"
                   value="<?= ThemeModel::getInstance()->fetchConfigValue('buttonColor') ?>">
        </div>
        <div>
            <label for="backgroundColor">Image de fond :</label>
            <div class="grid-3">
                <img width="100%" class="col-span-2"
                     src='<?= ThemeModel::getInstance()->fetchImageLink('background') ?>'>
                <div class="drop-img-area" data-input-name="background"></div>
            </div>
        </div>
    </div>
</div>