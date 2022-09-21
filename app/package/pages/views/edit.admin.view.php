<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("pages.edit.title");
$description = LangManager::translate("pages.edit.desc");

/* @var \CMW\Entity\Pages\PageEntity $page
 */

?>

<?php $scripts = '
    <!-- Initialization -->
    <script>
    let editor = new EditorJS({
        placeholder: "Commencez à taper ou cliquez sur le \"+\" pour choisir un bloc à ajouter...",
        logLevel: "ERROR",
        readOnly: false,
        holder: "editorjs",
        /**
         * Tools list
         */
        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: "Entrez un titre",
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }  
            },
            image: {
                class: ImageTool,
                config: {
                    uploader: {
                        uploadByFile(file) {
                        let formData = new FormData();
                        formData.append("file", file, file["name"]);
                        return fetch("' . getenv("PATH_SUBFOLDER") . 'admin/resources/vendors/editorjs/upload_file.php", {
                            method:"POST",
                            body:formData
                        }).then(res=>res.json())
                            .then(response => {
                                return {
                                    success: 1,
                                    file: {
                                        url: "' . getenv("PATH_SUBFOLDER") . 'public/uploads/"+response
                                    }
                                }
                            })
                        }
                    }
                }
            },
            list: List,
            quote: {
                class: Quote,
                config: {
                    quotePlaceholder: "",
                    captionPlaceholder: "Auteur",
                },
            },
            warning: Warning,
            code: CodeTool,
            delimiter: Delimiter,
            table: Table,
            embed: {
                class: Embed,
                config: {
                    services: {
                        youtube: true,
                        coub: true
                    }
                }
            },
            Marker: Marker,
            underline: Underline,
        },
        defaultBlock: "paragraph",

        /**
         * Initial Editor data
         */
         
        data: ' . $page->getContent() . ',
        onReady: function(){
            new Undo({ editor });
            const undo = new Undo({ editor });
            new DragDrop(editor);
        },
        onChange: function() {}
    });

    /**
     * Saving button
     */
    const saveButton = document.getElementById("saveButton");

    /**
     * Saving action
     */
    saveButton.addEventListener("click", function () {
        let page_state = 1;
        if (jQuery("#draft").is(":checked")) {
            page_state = 2;
        }
        editor.save()
        .then((savedData) => {
            $.ajax({
                url : "' . getenv("PATH_SUBFOLDER") . 'cmw-admin/pages/edit",
                type : "POST",
                data : {
                    "news_id" : jQuery("#page_id").val(),
                    "news_title" : jQuery("#title").val(),
                    "news_slug" : jQuery("#slug").val(),
                    "news_content" : JSON.stringify(savedData),
                    "page_state" : page_state
                },
                success: function (data) {
                    console.log(data)
                    jQuery(document).Toasts("create", {
                          title: "Page mise à jour !",
                          body: "Votre contenu a bien été enregistré.",
                          class: "body-success"
                    })
                }
            });
        })
        .catch((error) => {
            jQuery(document).Toasts("create", {
                  title: "Erreur",
                  body: "Une erreur est survenue, veuillez re-essayer",
                  class: "body-danger"
            })
        });
    });
    </script>'; ?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-9">
                <div class="card card-primary">
                    <div class="card-body">

                        <input type="hidden" id="page_id" name="page_id" value="<?= $page->getId() ?>">
                        <input class="page-title" type="text" id="title" placeholder="<?= LangManager::translate("pages.title") ?>"
                               value="<?= $page->getTitle() ?>">
                        <p class="page-slug text-blue mb-3 d-flex"><?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "p/" ?>
                            <input class="border-0 text-blue p-0 w-100 page-slug-input" type="text" id="slug"
                                   value="<?= $page->getSlug() ?>"></p>

                        <div>
                            <div id="editorjs"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("pages.publish") ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="draft"
                                   name="draft" <?= $page->getState() === 2 ? "checked" : ""; ?>>
                            <label class="custom-control-label" for="draft"><?= LangManager::translate("pages.draft") ?></label>
                        </div>
                        <div class="btn btn-block btn-primary" id="saveButton">
                            <?= LangManager::translate("core.btn.save") ?>
                        </div>

                        <a href="../delete/<?= $page->getId() ?>" class="mt-3 btn btn-danger btn-block"
                           id="deleteButton">
                            <?= LangManager::translate("core.btn.delete") ?>
                        </a>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>

<!-- /.main-content -->