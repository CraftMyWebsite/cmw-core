<?php
/** @var Alert $alert */

use CMW\Manager\Response\Alert;

?>
<link rel="stylesheet" href="https://izitoast.marcelodolza.com/css/iziToast.min.css">
<script src="https://izitoast.marcelodolza.com/js/iziToast.min.js"></script>
<script>
    iziToast.show(
        {
            titleSize: '16',
            messageSize: '14',
            icon: 'fa-solid fa-check',
            title  : "<?= $alert->getTitle() ?>",
            message: "<?= $alert->getMessage() ?>",
            color: "#41435F",
            iconColor: '#22E445',
            titleColor: '#22E445',
            messageColor: '#fff',
            balloon: false,
            close: false,
            position: 'bottomRight',
            timeout: 5000,
            animateInside: false,
            progressBar: false,
            transitionIn: 'fadeInLeft',
            transitionOut: 'fadeOutRight',
        });

</script>