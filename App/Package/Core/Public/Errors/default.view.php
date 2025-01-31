<?php

use CMW\Utils\Website;

Website::setTitle('Erreur');
Website::setDescription('Une erreur est survenue.');

/* @var $errorCode */
?>

<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">
        Erreur <?= $errorCode ?> !
</section>