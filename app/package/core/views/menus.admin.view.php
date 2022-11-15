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

                            <div class="list-group-item nested-1">
                                <i class="fas fa-arrows-alt handle"></i>
                                <p class="content-editable" contenteditable="true">Dropdown</p>

                                <div class="list-group nested-sortable">
                                    <div class="list-group-item nested-2">
                                        <i class="fas fa-arrows-alt handle"></i>
                                        <input type="hidden" value="1" name="id[]" hidden>
                                        <p class="content-editable" contenteditable="true">Dropdown 2.1</p>
                                    </div>
                                    <div class="list-group-item nested-2">
                                        <i class="fas fa-arrows-alt handle"></i>
                                        <input type="hidden" value="2" name="id[]" hidden>
                                        <p class="content-editable" contenteditable="true">Dropdown 2.2</p>
                                    </div>
                                    <div class="list-group-item nested-2">
                                        <i class="fas fa-arrows-alt handle"></i>
                                        <input type="hidden" value="1" name="id[]" hidden>
                                        <p class="content-editable" contenteditable="true">Dropdown 2.3</p>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item nested-1">
                                <i class="fas fa-arrows-alt handle"></i>
                                <input type="hidden" value="4" name="id[]" hidden>
                                <p class="content-editable" contenteditable="true">Item</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script async>
    /* Prevent enter on content editable*/
    document.querySelectorAll('.content-editable').forEach(item => {
        item.addEventListener('keypress', (evt) => {
            if (evt.key === 'Enter') {
                evt.preventDefault();
            }
        })
    })
</script>