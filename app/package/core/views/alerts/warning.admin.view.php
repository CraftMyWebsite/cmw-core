<?php
/** @var Alert $alert */

use CMW\Manager\Response\Alert;

?>
<link rel="stylesheet" href="https://izitoast.marcelodolza.com/css/iziToast.min.css">
<script src="https://izitoast.marcelodolza.com/js/iziToast.min.js"></script>
<script>
    iziToast.show(
        {
            title  : "<?= $alert->getTitle() ?>",
            message: "<?= $alert->getMessage() ?>",
            color: "orange"
        });
</script>