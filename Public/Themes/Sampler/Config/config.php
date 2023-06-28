<?php use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;use CMW\Model\Core\ThemeModel;use CMW\Manager\Security\SecurityManager;?>
<!-------------->
<!--Navigation-->
<!-------------->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab" aria-selected="true">Réglages</a>
    </li>
</ul>

<!--------------->
<!----CONTENT---->
<!--------------->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel" aria-labelledby="setting1-tab">
        <div class="form-group">
            <label for="buttonColor">Couleur des boutons :</label>
            <input type="color" id="buttonColor" name="buttonColor" value="<?= ThemeModel::fetchConfigValue('buttonColor') ?>">
        </div>
        <hr>
        <div class="form-group">
            <h4 for="backgroundColor">Image de fond :</h4>
            <input type="file" id="background" name="background" value="<?= ThemeModel::fetchConfigValue('background') ?>">
            <img width="100%" src='<?= ThemeModel::fetchImageLink("background") ?>'>
        </div>
    </div>
</div>