<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
use CMW\Utils\Response;

$title = LangManager::translate("pages.edit.title");
$description = LangManager::translate("pages.edit.desc");

/* @var \CMW\Entity\Pages\PageEntity $page
 */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-file-lines"></i> <span
                class="m-lg-auto"><?= LangManager::translate("pages.edit.title") ?> : <?= $page->getTitle() ?></span>
    </h3>
</div>

<section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h6><?= LangManager::translate("pages.title") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="hidden" id="page_id" name="page_id" value="<?= $page->getId() ?>">
                        <input type="text" class="form-control" id="title" name="title" required
                               placeholder="<?= LangManager::translate("pages.title") ?>" maxlength="255"
                               value="<?= $page->getTitle() ?>">
                        <div class="form-control-icon">
                            <i class="fas fa-heading"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <h6>URL :</h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                              id="inputGroup-sizing-default"><?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "p/" ?></span>
                        <input type="text" value="<?= $page->getSlug() ?>" id="slug" class="form-control"
                               aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
                    </div>

                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="draft"
                       name="draft" <?= $page->getState() === 2 ? "checked" : "" ?>>
                <label class="form-check-label" for="draft"><h6><?= LangManager::translate("pages.draft") ?></h6>
                </label>
            </div>
            <h6><?= LangManager::translate("pages.creation.content") ?> :</h6>

            <div id="editorjs"></div>

            <div class="text-center mt-2">

                <button id="saveButton" type="submit"
                        class="btn btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
            </div>
        </div>
    </div>
</section>

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
                            formData.append("image", file);
                            return fetch("<?= Utils::getEnv()->getValue("PATH_SUBFOLDER")?>cmw-admin/pages/uploadImage/edit", {
                                method: "POST",
                                body: formData
                            }).then(res => res.json())
                                .then(response => {
                                    return {
                                        success: 1,
                                        file: {
                                            url: "<?= Utils::getEnv()->getValue("PATH_URL")?>public/uploads/editor/" + response
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
            code: editorjsCodeflask,
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
        data: <?= $page->getContent() ?>,
        onReady: function () {
            new Undo({editor});
            const undo = new Undo({editor});
            new DragDrop(editor);
        },
        onChange: function () {
        }
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
                    url: "<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>//cmw-admin/pages/edit",
                    type: "POST",
                    data: {
                        "news_id": jQuery("#page_id").val(),
                        "news_title": jQuery("#title").val(),
                        "news_slug": jQuery("#slug").val(),
                        "news_content": JSON.stringify(savedData),
                        "page_state": page_state
                    },
                    success: function (data) {
                        console.log("Id :" + jQuery("#page_id").val());
                        console.log("Titre :" + jQuery("#title").val());
                        console.log("Slug :" + jQuery("#slug").val());
                        console.log("Content :" + JSON.stringify(savedData));
                        console.log("State :" + page_state);
                        saveButton.innerHTML = "<i style='color: #16C329;' class='fa-solid fa-check fa-shake'></i> Ok !";
                        setTimeout(() => {
                            saveButton.innerHTML = "<?= LangManager::translate("core.btn.edit") ?>";
                        }, 1000);

                    }
                });
            })
            .catch((error) => {
                alert("Page capoute");
            });
    });
</script>