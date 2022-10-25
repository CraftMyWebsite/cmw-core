<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("core.menus.title");
$description = LangManager::translate("core.menus.desc");
?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Menu principal</h3>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary float-left" onclick="addMenu()">
                            Ajouter un menu classique
                        </button>

                        <button class="btn btn-primary float-right" onclick="addDropdown()">
                            Ajouter un menu dropdown
                        </button>
                    </div>


                    <!-- Nested items (menus) -->

                    <div id="nested">
                        <div id="menus" class="list-group col nested-sortable">

                            <div class="list-group-item nested-1">Dropdown
                                <div class="list-group nested-sortable">
                                    <div class="list-group-item nested-2">Dropdown 2.1</div>
                                    <div class="list-group-item nested-2">Dropdown 2.3</div>
                                    <div class="list-group-item nested-2">Dropdown 2.4</div>
                                </div>
                            </div>

                            <div class="list-group-item nested-1">Item 1.2</div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>