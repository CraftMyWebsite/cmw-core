<style>
    .main-content {
        padding: 0 !important;
        margin-top: 2.5rem !important;
    }
    .preview-container {
        display: flex;
        justify-content: center;
        height: calc(100vh - 2.5rem);
        position: relative;
        overflow: hidden;
    }

    #previewFrame {
        transition: all 0.6s ease-in-out;
    }

    .mode-btn {
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.6s ease;
    }

    .mode-btn.active {
        color: #3d72dd; /* Couleur du bouton actif */
    }
</style>
<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Views\View;

include_once (EnvManager::getInstance()->getValue('DIR') . 'Admin/Resources/Views/Includes/head.inc.php');

/* INCLUDE SCRIPTS / STYLES */
/* @var $includes */
/* @var $content */
View::loadInclude($includes, 'beforeScript');
View::loadInclude($includes, 'styles');

include_once (EnvManager::getInstance()->getValue('DIR') . 'App/Package/Core/Views/Theme/Editor/Includes/header.inc.php');

echo $content;

include_once (EnvManager::getInstance()->getValue('DIR') . 'App/Package/Core/Views/Theme/Editor/Includes/footer.inc.php');
/* INCLUDE SCRIPTS */
View::loadInclude($includes, 'afterScript');
?>
<script>
    function setIframeWidth(mode, btn) {
        const iframe = document.getElementById("previewFrame");
        const buttons = document.querySelectorAll(".mode-btn");

        // Supprime la classe active de tous les boutons
        buttons.forEach(button => button.classList.remove("active"));

        // Ajoute la classe active au bouton cliqué
        btn.classList.add("active");

        switch (mode) {
            case 'mobile':
                iframe.style.width = '430px';
                iframe.style.height = '667px';
                break;
            case 'tablet':
                iframe.style.width = '768px';
                iframe.style.height = '667px';
                break;
            case 'desktop':
                iframe.style.width = '98%';
                iframe.style.height = '100%';
                break;
        }
    }
</script>

</body>
</html>