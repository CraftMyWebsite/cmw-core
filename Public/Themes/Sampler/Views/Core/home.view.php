<?php

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

Website::setTitle('Accueil');
Website::setDescription("page d'accueil de CraftMyWebsite");
?>

<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Bienvenue <?= UsersSessionsController::getInstance()->getCurrentUser()?->getPseudo() ?? '' ?> !</h1>
            <p class="max-w-2xl mb-6 font-light lg:mb-8 md:text-lg lg:text-xl">Ceci est le thème par défaut. Installer un nouveau depuis le panneau d’administration.</p>
            <?php if (UsersController::isAdminLogged()): ?>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin" target="_blank" class="inline-flex items-center justify-center py-3 mr-3 text-base font-medium text-center rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                Commencer
                <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
            <?php else: ?>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login" class="inline-flex items-center justify-center py-3 mr-3 text-base font-medium text-center rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                    Connexion
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            <?php endif; ?>
        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            <img data-cmw-attr="src:home-hero:image" data-cmw-style="width:home-hero:image_width" alt="mockup">
        </div>
    </div>
</section>

<section style="background-color: var(--bg-color);" class=" py-16 text-center">
    <h2 class="text-3xl font-bold mb-12">Que faire maintenant ?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-6xl mx-auto">

        <div>
            <i class="fa-solid fa-code text-4xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold">Créer votre site</h3>
            <p class="text-[#ccc] mt-2">Gérez facilement vos pages, utilisateurs et contenus.</p>
        </div>

        <div>
            <i class="fa-solid fa-cube text-4xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold">Installer des packages</h3>
            <p class="text-[#ccc] mt-2">Ajoutez des fonctionnalités via notre marketplace.</p>
        </div>

        <div>
            <i class="fa-solid fa-palette text-4xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold">Personnaliser un thème</h3>
            <p class="text-[#ccc] mt-2">Adaptez l'apparence à votre communauté ou projet.</p>
        </div>

    </div>
</section>

<section data-cmw-style="background:global:bg-color-secondary" class="py-16  text-center">
    <h2 class="text-3xl font-bold mb-12">Commencer avec CraftMyWebsite</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">

        <a target="_blank" href="https://craftmywebsite.fr/docs/fr/users/tableau-de-bord/installer-un-package" class="block bg-[#1E1E1E] hover:bg-[#292929] p-6 rounded-xl shadow-md transition">
            <i class="fa-solid fa-box text-3xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Installer un package</h3>
            <p class="text-[#ccc] text-sm">Ajoute des fonctionnalités à ton site facilement.</p>
        </a>

        <a target="_blank" href="https://craftmywebsite.fr/docs/fr/users/tableau-de-bord/installer-un-theme" class="block bg-[#1E1E1E] hover:bg-[#292929] p-6 rounded-xl shadow-md transition">
            <i class="fa-solid fa-palette text-3xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Changer de thème</h3>
            <p class="text-[#ccc] text-sm">Personnalise ton site avec des thèmes variés.</p>
        </a>

        <a target="_blank" href="https://craftmywebsite.fr/docs/fr/users/tableau-de-bord/envoyer-des-mails" class="block bg-[#1E1E1E] hover:bg-[#292929] p-6 rounded-xl shadow-md transition">
            <i class="fa-solid fa-envelope text-3xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Envoyer des mails</h3>
            <p class="text-[#ccc] text-sm">Configure l’envoi de mails automatiques facilement.</p>
        </a>

    </div>
</section>


<section style="background-color: var(--bg-color);" class="py-16 text-center">
    <h2 class="text-3xl font-bold mb-12">Contribue à l'écosystème</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">

        <a target="_blank" href="https://craftmywebsite.fr/docs/fr/technical/creer-un-package/introduction" class="block bg-[#1E1E1E] hover:bg-[#292929] p-6 rounded-xl shadow-md transition">
            <i class="fa-solid fa-cubes text-3xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Créer un package</h3>
            <p class="text-[#ccc] text-sm">Ajoute des fonctionnalités et partage-les avec la communauté.</p>
        </a>

        <a target="_blank" href="https://craftmywebsite.fr/docs/fr/technical/creer-un-theme/introduction" class="block bg-[#1E1E1E] hover:bg-[#292929] p-6 rounded-xl shadow-md transition">
            <i class="fa-solid fa-brush text-3xl text-[#E63A5C] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Créer un thème</h3>
            <p class="text-[#ccc] text-sm">Propose des designs uniques pour les autres utilisateurs.</p>
        </a>

    </div>
</section>
