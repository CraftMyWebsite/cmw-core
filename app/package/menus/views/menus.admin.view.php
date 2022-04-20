<?php
$title = MENUS_TITLE;
$description = MENUS_DESC;
?>

<?php $styles = '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.css">'; ?>

<?php $scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/dragula/dragula.js"></script>
    
    <script>
    function getChildren(elem) {
        let parent = [];
        jQuery(elem).children("ul").children("li").each(function() {
            if(jQuery(this).data("id")) {
                parent.push(jQuery(this).data("id"));
            }
            if(jQuery(this).children("ul").children("li").length) {
                parent.push(getChildren(this));
            }
        });
      return parent;
    }
    
    jQuery(document).ready(function() {
        let menu = dragula(jQuery(".menu-drag ul").toArray(), {
            mirrorContainer: jQuery(".menu-drag")[0]
        });
        menu.on("drop", function(el, target, source, sibling) {
            console.log(getChildren(jQuery(".menu-drag")));
        });
    });
    </script>'; ?>

<?php ob_start(); ?>
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
                        <div class="card-body">
                            <style>
                                .menu-drag {
                                    margin: 0 auto;
                                }

                                .menu-drag > ul {
                                    padding: 0;
                                }

                                .menu-drag > ul > li {
                                    padding-left: 0;
                                }

                                .menu-drag ul {
                                    padding: 0 0 0 20px;
                                    margin: 0;
                                    min-height: 10px;
                                }

                                .menu-drag li > span {
                                    cursor: move;
                                    background: #fff;
                                    display: block;
                                    padding: 10px 15px;
                                    border-radius: 3px;
                                    box-shadow: 0 2px 6px rgba(170, 185, 200, 0.4);
                                }

                                .menu-drag li:first-child {
                                    padding-top: 10px;
                                }

                                .menu-drag li {
                                    list-style: none;
                                }
                            </style>

                            <div class="menu-drag">
                                <ul>
                                    <li data-id="1">
                                        <span>Item 1</span>
                                        <ul>
                                            <li data-id="4">
                                                <span>Item 4</span>
                                                <ul>
                                                    <li data-id="7">
                                                        <span>Item 7</span>
                                                        <ul></ul>
                                                    </li>
                                                    <li data-id="8">
                                                        <span>Item 8</span>
                                                        <ul></ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li data-id="5">
                                                <span>Item 5</span>
                                                <ul></ul>
                                            </li>
                                            <li data-id="6">
                                                <span>Item 6</span>
                                                <ul></ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li data-id="2">
                                        <span>Item 2</span>
                                        <ul></ul>
                                    </li>
                                    <li data-id="3">
                                        <span>Item 3</span>
                                        <ul></ul>
                                    </li>
                                </ul>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.main-content -->
<?php $content = ob_get_clean(); ?>