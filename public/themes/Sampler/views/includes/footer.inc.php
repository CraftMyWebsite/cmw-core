<?php use CMW\Controller\CoreController;
/** @var CoreController $core */ ?>

<footer>
   <?= $core->cmwFooter() ?>
</footer>

<?php
/* INCLUDE AFTER SCRIPTS*/
if (!empty($includes))
    includeFiles($includes, "scriptsAfter");
?>
