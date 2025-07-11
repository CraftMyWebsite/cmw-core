<?php

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

Website::setTitle('Hello World');
Website::setDescription('CraftMyWebsite is wonderful.');
?>

<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Hello !</h1>
        </div>
    </div>
</section>

<script>
    console.log("CraftMyWebsite v2.0 was build with love by the CMW Team.");
    console.log("Our original heroes are: @Teyir, @Zomb, @Emilien52, @Axel.V, @BadiiiX")
</script>