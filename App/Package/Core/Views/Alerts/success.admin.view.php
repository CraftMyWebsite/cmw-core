<?php
/** @var Alert $alert */

use CMW\Manager\Flash\Alert;

?>
<link rel="stylesheet" href="https://izitoast.marcelodolza.com/Css/iziToast.min.css">
<script src="https://izitoast.marcelodolza.com/Js/iziToast.min.js"></script>
<script>
    iziToast.show(
        {
            title  : "<?= $alert->getTitle() ?>",
            message: "<?= $alert->getMessage() ?>",
            color: "green"
        });
</script>