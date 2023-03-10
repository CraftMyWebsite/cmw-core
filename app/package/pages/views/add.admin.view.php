<?php

use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("pages.add.title");
$description = LangManager::translate("pages.add.desc");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-file-lines"></i> <span
                class="m-lg-auto"><?= LangManager::translate("pages.add.title") ?></span></h3>
</div>

    
<section>
    <div class="card">
        <div class="card-body">
            <form>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h6><?= LangManager::translate("pages.title") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="hidden" id="page_id" name="page_id">
                        <input type="text" class="form-control" id="title" required
                               placeholder="<?= LangManager::translate("pages.title") ?>" maxlength="255">
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
                        <input type="text" id="slug" class="form-control" placeholder="<?= LangManager::translate("pages.link") ?>"
                               aria-label="Slug" aria-describedby="inputGroup-sizing-default" name="news_slug" required>
                    </div>

                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="draft" name="draft">
                <label class="form-check-label" for="draft"><h6><?= LangManager::translate("pages.draft") ?></h6>
                </label>
            </div>
            <h6><?= LangManager::translate("pages.creation.content") ?> :</h6>

            <div>
                <div class="card-in-card" id="editorjs"></div>
            </div>

            <div class="text-center mt-2">
                <button id="saveButton" type="submit" disabled="disabled" class="btn btn-primary"><i class="fa-solid fa-spinner fa-spin-pulse"></i> <?= LangManager::translate("pages.add.create") ?></button>
            </div>
        </form>
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
     if (document.querySelector("#title").value !="" && document.querySelector("#slug").value !="") {
      button.disabled = false;
      button.innerHTML = "<?= LangManager::translate("core.btn.add") ?>";
     }
     else {
      button.disabled = true;
      button.innerHTML = "<i class='fa-solid fa-spinner fa-spin-pulse'></i> <?= LangManager::translate("pages.add.create") ?>";
     }
    }
    /**
    * EditorJS
    */
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
                        return fetch("<?php getenv("PATH_SUBFOLDER") ?>admin/resources/vendors/editorjs/upload_file.php", {
                            method:"POST",
                            body:formData
                        }).then(res=>res.json())
                            .then(response => {
                                return {
                                    success: 1,
                                    file: {
                                        url: "<?php getenv("PATH_SUBFOLDER")?>public/uploads/"+response
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
        data: {},
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
            if(jQuery("#page_id").val()) {
                $.ajax({
                    url : "<?php getenv("PATH_SUBFOLDER") ?>/cmw-admin/pages/edit",
                    type : "POST",
                    data : {
                        "news_id" : jQuery("#page_id").val(),
                        "news_title" : jQuery("#title").val(),
                        "news_slug" : jQuery("#slug").val(),
                        "news_content" : JSON.stringify(savedData),
                        "page_state" : page_state
                    },
                    success: function (data) {
                        console.log ("Id :" + jQuery("#page_id").val());
                        console.log ("Titre :" + jQuery("#title").val());
                        console.log ("Slug :" + jQuery("#slug").val());
                        console.log ("Content :" + JSON.stringify(savedData));
                        console.log ("State :" + page_state);
                    }
                });
            }
            else {
                $.ajax({
                    url : "<?php getenv("PATH_SUBFOLDER") ?>/cmw-admin/pages/add",
                    type : "POST",
                    data : {
                        "news_title" : jQuery("#title").val(),
                        "news_slug" : jQuery("#slug").val(),
                        "news_content" : JSON.stringify(savedData),
                        "page_state" : page_state
                    },
                    success: function (data) {
                        jQuery("#page_id").val(data);
                        console.log ("Titre :" + jQuery("#title").val());
                        console.log ("Slug :" + jQuery("#slug").val());
                        console.log ("Content :" + JSON.stringify(savedData));
                        console.log ("State :" + page_state);
                    }
                });
            }
        })
        .catch((error) => {
            alert("Page capoute");
        });
    });
    </script>