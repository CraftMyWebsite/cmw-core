<?php

use CMW\Manager\Views\View;

require_once("Include/header.php") ?>

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

<?php require_once("Include/footer.php") ?>

<script src="installation/Views/Assets/Js/loader.js"></script>
