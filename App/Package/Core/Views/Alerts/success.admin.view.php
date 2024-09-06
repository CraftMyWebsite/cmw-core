<?php

/**
 * @var Alert $alert
 */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;

?>
<link rel="stylesheet" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.css' ?>">
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.js' ?>"></script>
<script>
    iziToast.show(
        {
            title  : "<?= $alert->getTitle() ?>",
            message: "<?= $alert->getMessage() ?>",
            color: "green"
        });
</script>