<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Connexion - 2FA');
Website::setDescription('Double authentification');
?>
<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Double facteur</h1>
        </div>
    </div>
</section>


<section class="flex items-center justify-center text-white py-8">
    <div class="w-full max-w-md bg-[#1E1E1E] p-8 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-center mb-6">Double facteur</h2>
        <hr class="border-[#E63A5C] mb-6 w-24 mx-auto">

        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login/validate/tfa' ?>" method="post" class="space-y-6">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>

            <div>
                <label for="code" class="block text-sm font-medium mb-1">Code d'authentification</label>
                <input type="text" id="code" name="code" maxlength="7" required
                       class="w-full px-4 py-2 bg-[#2A2A2A] text-white rounded focus:ring-2 focus:ring-[#E63A5C] focus:outline-none"
                       placeholder="123456">
            </div>

            <div>
                <button type="submit"
                        style="background: <?= ThemeModel::getInstance()->fetchConfigValue('buttonColor') ?>;"
                        class="w-full text-white font-semibold py-2 rounded transition hover:opacity-90">
                    Connexion
                </button>
            </div>
        </form>
    </div>
</section>
