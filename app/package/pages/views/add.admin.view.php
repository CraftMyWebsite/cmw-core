<?php $title = PAGES_ADD_TITLE;
$description = PAGES_ADD_DESC; ?>

<?php $scripts = '<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script><!-- Header -->

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script><!-- Image -->

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script><!-- Delimiter -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script><!-- List -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script><!-- Quote -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest"></script><!-- Code -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script><!-- Table -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script><!-- Link -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script><!-- Warning -->

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script><!-- Embed -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script><!-- Marker -->
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script><!-- Underline -->

    <script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/editorjs/plugins/drag-drop.js"></script><!-- DragDrop -->
    <script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/editorjs/plugins/undo.js"></script><!-- Undo -->


    <!-- Load Editor.js Core -->
    <script src="'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/editorjs/editor.js"></script>
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
                    placeholder: "Entrer un titre",
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
                        return fetch("'.getenv("PATH_SUBFOLDER").'admin/resources/vendors/editorjs/upload_file.php", {
                            method:"POST",
                            body:formData
                        }).then(res=>res.json())
                            .then(response => {
                                return {
                                    success: 1,
                                    file: {
                                        url: "'.getenv("PATH_SUBFOLDER").'public/uploads/"+response
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
                    url : "'.getenv("PATH_SUBFOLDER").'cms-admin/pages/edit",
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
                        $(document).Toasts("create", {
                              title: "Page mise à jour !",
                              body: "Votre contenu a bien été enregistré.",
                              class: "body-success"
                        })
                    }
                });
            }
            else {
                $.ajax({
                    url : "'.getenv("PATH_SUBFOLDER").'cms-admin/pages/add",
                    type : "POST",
                    data : {
                        "news_title" : jQuery("#title").val(),
                        "news_slug" : jQuery("#slug").val(),
                        "news_content" : JSON.stringify(savedData),
                        "page_state" : page_state
                    },
                    success: function (data) {
                        jQuery("#page_id").val(data);
                        $(document).Toasts("create", {
                              title: "Page enregistrée !",
                              body: "Votre page a bien été enregistrée.",
                              class: "body-success"
                        })
                    }
                });
            }
        })
        .catch((error) => {
            $(document).Toasts("create", {
                  title: "Erreur",
                  body: "Une erreur est survenue, veuillez re-essayer",
                  class: "body-danger"
            })
        });
    });
    </script>'; ?>

<?php ob_start(); ?>
<style>
    .ce-block__content, .ce-toolbar__content {
        max-width: 80%!important;
    }
    .page-title {
        width: 80%;
        margin: 0 10%;
        font-size: 30px;
        border: 0;
    }
    .page-slug {
        width: 80%;
        margin: 0 10%;
    }
    input:focus{
        outline: none;
    }
</style>
    <!-- main-content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-9">
                    <div class="card card-primary">
                        <div class="card-body">
                            <input type="hidden" id="page_id" name="page_id">
                            <input class="page-title" type="text" id="title" placeholder="Titre de la page">
                            <p class="page-slug text-blue mb-3 d-flex"><?php echo "http://" . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER"); ?> <input class="border-0 text-blue p-0 w-100 page-slug-input" type="text" id="slug"></p>
                            <div>
                                <div id="editorjs"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Publication de la page</h3>
                        </div>
                        <div class="card-body">
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="draft" name="draft">
                                <label class="custom-control-label" for="draft">Brouillon</label>
                            </div>
                            <div class="btn btn-block btn-primary" id="saveButton">
                                <?=BUTTON_SAVE?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
    <script>
        $('#title').on('keyup', function() {
            let val = $(this).val();
            val=val.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            val=val.replace(/[^\w\s]/gi, '');
            val=val.replace(/ /g,"-");
            $('#slug').val(val);
        });
    </script>

    <!-- /.main-content -->
<?php $content = ob_get_clean(); ?>
