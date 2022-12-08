<?php

echo <<<HTML
        <script>
            const editor = new EditorJS({
                placeholder: "Commencez à taper ou cliquez sur le \"+\" pour choisir un bloc à ajouter...",
                logLevel: "ERROR",
                readOnly: false,
                holder: "editorjs",
                autofocus: false
               
            });
        </script>
HTML;