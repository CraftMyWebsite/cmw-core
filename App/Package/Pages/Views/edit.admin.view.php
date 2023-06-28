<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Website;

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
                              id="inputGroup-sizing-default"><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "p/" ?></span>
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

            <div class="card-in-card" id="editorjs"></div>

            <div class="text-center mt-2">

                <button id="saveButton" type="submit"
                        class="btn btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
            </div>
        </div>
    </div>
</section>

<!-- Initialization -->
<script>
    /**
     * Check inpt befor send
     */
    let input_title = document.querySelector("#title");
    let input_slug = document.querySelector("#slug");
    let button = document.querySelector("#saveButton");
    input_title.addEventListener("change", stateHandle);
    input_slug.addEventListener("change", stateHandle);

    function stateHandle() {
        if (document.querySelector("#title").value != "" && document.querySelector("#slug").value != "") {
            button.disabled = false;
            button.innerHTML = "<?= LangManager::translate("core.btn.add") ?>";
        } else {
            button.disabled = true;
            button.innerHTML = "<i class='fa-solid fa-spinner fa-spin-pulse'></i> <?= LangManager::translate("pages.add.create") ?>";
        }
    }
    
    let editor = new EditorJS({
        placeholder: "<?= LangManager::translate("pages.editor.start") ?>",
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
                            return fetch("<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER")?>cmw-admin/Pages/uploadImage/edit", {
                                method: "POST",
                                body: formData
                            }).then(res => res.json())
                                .then(response => {
                                    return {
                                        success: 1,
                                        file: {
                                            url: "<?= EnvManager::getInstance()->getValue("PATH_URL")?>Public/Uploads/Editor/" + response
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
        if (document.getElementById("draft").checked) {
            page_state = 2;
        }
        editor.save()
            .then((savedData) => {

                let formData = new FormData();
                formData.append('news_id', document.getElementById("page_id").value);
                formData.append('news_title', document.getElementById("title").value);
                formData.append('news_slug', document.getElementById("slug").value);
                formData.append('news_content', JSON.stringify(savedData));
                formData.append('page_state', page_state.toString());

                fetch("<?= EnvManager::getInstance()->getValue("PATH_URL") ?>cmw-admin/pages/edit", {
                    method: "POST",
                    body: formData
                })

                button.disabled = true;
                button.innerHTML = "<i class='fa-solid fa-spinner fa-spin-pulse'></i> Enregistrement en cours ...";
                setTimeout(() => {
                            button.innerHTML = "<i style='color: #16C329;' class='fa-solid fa-check fa-shake'></i> Ok !";
                        }, 850);
                setTimeout(() => {
                            document.location.replace("<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . 'cmw-admin/pages/list'?>");
                        }, 1000);
                
            })
            .catch((error) => {
                alert("Error : " + error);
            });
    });
</script>