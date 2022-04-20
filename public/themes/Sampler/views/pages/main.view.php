<?php
/* @var  $page*/

$title = ucfirst($page->pageTitle);
$description = "Description de votre page";
ob_start();?>

<section>
    <div class="container">
            <?= $page->pageContentTranslated ?>
    </div>
</section>




<?php $content = ob_get_clean(); ?>