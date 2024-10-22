<?php

/**
 * @var Alert $alert
 */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;

?>
<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.css' ?>">
<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.js' ?>"></script>
<script>
    iziToast.show(
        {
            titleSize: '14',
            messageSize: '12',
            icon: 'fa-solid fa-info',
            title: "<?= $alert->getTitle() ?>",
            message: "<?= $alert->getMessage() ?>",
            color: "#4296da",
            iconColor: '#ffffff',
            titleColor: '#ffffff',
            messageColor: '#ffffff',
            balloon: false,
            close: true,
            pauseOnHover: true,
            position: 'topCenter',
            timeout: 4000,
            animateInside: false,
            progressBar: true,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOut',
        });

</script>