<?php

use CMW\Manager\Views\View;

require_once("include/header.php") ?>

<?php
/* INCLUDE SCRIPTS / STYLES*/
/* @var $includes */
View::loadInclude($includes, "beforeScript");
View::loadInclude($includes, "styles");
?>



<?php /* @var string $content */ ?>
<?= $content ?>

<?php

/* INCLUDE SCRIPTS */
View::loadInclude($includes, "afterScript");

?>

<?php require_once("include/footer.php") ?>

<script src="installation/views/assets/js/loader.js"></script>
