document.addEventListener("DOMContentLoaded", function () {
    let theme = localStorage.getItem("color-theme");
    if (theme === "dark") {
        theme = "theme-dark";
    } else {
        theme = "theme-light";
    }

    const images_upload_handler = (blobInfo) => new Promise((success, failure) => {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        xhr.open('POST', '/editor/upload/image');
        const imgElement = tinymce.activeEditor.selection.getNode();

        xhr.onload = function() {
            if (xhr.status === 200) {
                let json = JSON.parse(xhr.responseText);

                if (json && typeof json.location === 'string') {
                    success(json.location);
                } else {
                    failure('Réponse JSON invalide');
                    tinymce.activeEditor.dom.remove(imgElement);
                }
            } else {
                failure('Erreur lors de l\'upload : ' + xhr.status);
                tinymce.activeEditor.dom.remove(imgElement);
            }
        };

        xhr.onerror = function() {
            failure('Erreur réseau ou problème d\'accès au serveur');
            tinymce.activeEditor.dom.remove(imgElement);
        };

        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    });

    function initTinyMCE(skin) {
        document.querySelectorAll('.tinymce').forEach(function(textarea) {
            const minHeight = textarea.getAttribute('data-tiny-height') || 350;

            tinymce.init({
                selector: `.tinymce`,
                skin: skin,
                content_css: skin,
                promotion: false,
                toolbar_sticky: true,
                toolbar_mode: 'sliding',
                plugins: ['emoticons', 'image', 'autoresize', 'wordcount', 'advlist', 'lists', 'charmap', 'codesample', 'code', 'directionality', 'fullscreen', 'link', 'insertdatetime', 'media', 'pagebreak', 'nonbreaking', 'preview', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars'],
                toolbar:
                    'undo redo | ' +
                    'formatpainter casechange blocks fontsizeselect | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bold italic strikethrough | ' +
                    'forecolor backcolor removeformat |' +
                    'bullist numlist outdent indent | ' +
                    'table | ' +
                    'visualchars visualblocks ltr rtl | ' +
                    'searchreplace nonbreaking pagebreak|' +
                    'link media image insertdatetime |' +
                    'emoticons charmap |' +
                    'wordcount codesample code |' +
                    'preview fullscreen help',
                menubar: false,
                images_file_types: 'jpg,svg,webp',
                file_picker_types: 'file image media',
                statusbar: false,
                relative_urls: false,
                remove_script_host: false,
                images_upload_handler:images_upload_handler,
                setup: function(editor) {
                    editor.on('init', function() {
                        const currentTextarea = editor.getElement();
                        const minHeight = currentTextarea.getAttribute('data-tiny-height') || 350;
                        editor.editorContainer.style.minHeight = `${minHeight}px`;
                    });
                }
            });
        });
    }

    initTinyMCE(theme);

    document.getElementById('theme-toggle').addEventListener('click', function () {
        let newTheme = localStorage.getItem("color-theme");
        if (newTheme === "dark") {
            newTheme = "theme-dark";
        } else {
            newTheme = "theme-light";
        }
        tinymce.remove('.tinymce');
        initTinyMCE(newTheme);
    });
});
