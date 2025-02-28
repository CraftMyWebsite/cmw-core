<?php

use CMW\Manager\Views\View;
use CMW\Utils\Website;

include_once ('Includes/head.inc.php');

/* INCLUDE SCRIPTS / STYLES */
/* @var $includes */
/* @var $content */
View::loadInclude($includes, 'beforeScript');
View::loadInclude($includes, 'styles');

include_once ('Includes/header.inc.php');

echo $content;

include_once ('Includes/footer.inc.php');

/* INCLUDE SCRIPTS */
View::loadInclude($includes, 'afterScript');
?>

<script>
    //Variables global utilisable dans les script JS (admin only)
    const BASE_URL = "<?= Website::getUrl() ?>";
    const WEBSITE_NAME = "<?= Website::getWebsiteName() ?>";
</script>

</body>
</html>