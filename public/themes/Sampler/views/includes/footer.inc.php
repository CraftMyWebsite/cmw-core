<?php use CMW\Controller\Core\CoreController;
use CMW\Utils\View;

/** @var CoreController $core */
/**@var  array $includes*/?>

<footer>
   <?= $core->cmwFooter() ?>
</footer>

<?php
View::loadInclude($includes, "afterScript");
?>
