<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;

$isUpToDate = UpdatesManager::checkNewUpdateAvailable()

?>

    </section>


    <script>
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        const themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>

    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Choices.js/choices.js"></script>
    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/flowbite.js"></script>
    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/cmw.js"></script>
    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Fontawesome-picker/main.js"></script>
    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Izitoast/iziToast.min.js"></script>

    <script
        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ace.js' ?>"></script>
    <script
        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ext-language_tools.js' ?>"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const langTools = ace.require("ace/ext/language_tools");

            document.querySelectorAll('.html-editor-container').forEach(container => {
                const editorDiv = container.querySelector('.html-editor');
                const editorId = editorDiv.id;
                const inputHidden = document.getElementById(`input-${editorId.split('-')[1]}`);

                if (!editorDiv) {
                    console.warn("⚠️ Aucun .html-editor trouvé dans", container);
                    return;
                }

                const editor = ace.edit(editorId, {
                    mode: "ace/mode/php",
                    selectionStyle: "text"
                });

                editor.setOptions({
                    autoScrollEditorIntoView: true,
                    enableBasicAutocompletion: true,
                    enableLiveAutocompletion: true,
                    enableSnippets: false
                });

                if (localStorage.getItem('theme') === 'theme-dark') {
                    editor.setTheme("ace/theme/cmw_dark");
                } else {
                    editor.setTheme("ace/theme/cmw_light");
                }

                editor.container.style.height = "14.7vh";
                editor.container.style.width = "100%";
                editor.resize();
                editor.session.setUseWrapMode(true);
                editor.setShowPrintMargin(false);
                editor.session.mergeUndoDeltas = true;

                editor.setValue(inputHidden.value || "", -1);

                editor.session.on('change', function () {
                    const value = editor.getValue().trim();
                    inputHidden.value = value;

                    const name = inputHidden.name;
                    if (!name) return;

                    configValues[name] = value;

                    // Mise à jour dans l'iframe
                    updateThemePreview(name);

                    // Auto-scroll vers l'élément modifié dans l'iframe
                    const iframe = document.getElementById("previewFrame");
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

                    const selector = [
                        `[data-cmw="${name.replace('_', ':')}"]`,
                    ].join(",");

                    const target = iframeDoc.querySelector(selector);
                    if (target) {
                        target.scrollIntoView({ behavior: "smooth", block: "center" });
                    }

                });

            });
        });
    </script>


<?php
require_once ('Admin/Resources/Assets/Php/imageDropper.php');
?>