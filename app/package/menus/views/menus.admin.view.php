<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("menus.menus.title");
$description = LangManager::translate("menus.menus.desc");
?>

    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ajouter au menu</h3>
                        </div>
                        <div class="card-body">
                            <div id="menus-pages">

                            </div>
                            <div id="menus-packages">

                            </div>
                            <div id="menus-custom">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Menu principal</h3>
                        </div>

                        <div id="list1" class="list">
                            <div>Item 1.1
                                <div class="list n1">
                                    <div>Item 2.1</div>
                                    <div>Item 2.2</div>
                                </div>
                            </div>
                            <div>Item 1.2
                                <div class="list n1">
                                    <div>Item 2.1</div>
                                    <div>Item 2.2</div>
                                    <div>Item 2.3</div>
                                </div>
                            </div>
                            <div>Item 1.3</div>
                        </div>

                        <style>
                            .n1 > div {
                                background-color: lightblue;
                            }

                            .list {
                                padding: 50px;
                            }

                        </style>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.main-content -->