<?php

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

Website::setTitle('Hello World');
Website::setDescription('CraftMyWebsite is wonderful.');
?>

<section class="page-section">
   <div class="container">
       <div class="text-center">
           <h1>Hello World !!</h1>
           <img src="<?= EnvManager::getInstance()->getValue('PATH_URL') . 'Admin/Resources/Assets/Img/logo_dark.png' ?>"
                alt="CraftMyWebsite Logo" class="img-fluid mt-5">
       </div>
   </div>
</section>

<script>
    console.log("CraftMyWebsite v2.0 was build with love by the CMW Team.");
    console.log("Our original heroes are: @Teyir, @Zomb, @Emilien52, @Axel.V, @BadiiiX")
</script>