<?php use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;use CMW\Model\Core\ThemeModel;use CMW\Manager\Security\SecurityManager;?>
<!-------------->
<!--Navigation-->
<!-------------->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab" aria-selected="true">Réglages 1</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab" aria-selected="false">Réglages 2</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="setting3-tab" data-bs-toggle="tab" href="#setting3" role="tab" aria-selected="false">Réglages 3</a>
    </li>
</ul>

<!--------------->
<!----CONTENT---->
<!--------------->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel" aria-labelledby="setting1-tab">
        Vos options 1 :
        <div class="form-group">
            <label for="primaryColor">Couleur principale</label>
            <input type="color" id="primaryColor" name="primaryColor"value="<?= ThemeModel::fetchConfigValue('primaryColor') ?>">
        </div>
        <div class="form-group">
            <label for="secondaryColor">Couleur secondaire</label>
            <input type="color" id="secondaryColor" name="secondaryColor" value="<?= ThemeModel::fetchConfigValue('secondaryColor') ?>">
        </div>
        <div class="form-group">
            <label for="backgroundColor">Couleur d'arrière plan</label>
            <input type="color" id="backgroundColor" name="backgroundColor" value="<?= ThemeModel::fetchConfigValue('backgroundColor') ?>">
        </div>
        <div class="form-group">
            <label for="backgroundColor">Image 1</label>
            <input type="file" id="img1" name="img1" value="<?= ThemeModel::fetchConfigValue('img1') ?>">
            <img src='<?= ThemeModel::fetchImageLink("img1") ?>' height="150px" width="150px">
        </div>
        <div class="form-group">
            <label for="backgroundColor">Image 2</label>
            <input type="file" id="img2" name="img2" value="<?= ThemeModel::fetchConfigValue('img2') ?>">
            <img src='<?= ThemeModel::fetchImageLink("img2") ?>'  height="150px" width="150px">
        </div>
    </div>
    <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
        Vos options 2
    </div>
    <div class="tab-pane fade py-2" id="setting3" role="tabpanel" aria-labelledby="setting3-tab">
        Vos options 3
    </div>
</div>