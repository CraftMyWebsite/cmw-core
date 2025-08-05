<?php

use CMW\Controller\Core\SecurityController;
use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var IUsersOAuth[] $oAuths */

Website::setTitle('Connexion');
Website::setDescription('Connectez-vous à votre compte ' . Website::getWebsiteName());
?>

<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Connexion</h1>
        </div>
    </div>
</section>

<section class="page-section text-white mt-12 flex items-center justify-center">
    <div class="w-full max-w-md bg-[#1E1E1E] p-8 rounded-xl shadow-lg">

        <form action="" method="post" class="">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <input hidden name="previousRoute" type="text"
                   value="<?= $_SERVER['HTTP_REFERER'] ?? (EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login') ?>">

            <div class="mt-6">
                <label for="login_email" class="block text-sm font-semibold mb-1">E-Mail</label>
                <input class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       name="login_email" type="email" placeholder="Votre mail" required>
            </div>

            <div class="mt-6">
                <label for="login_password" class="block text-sm font-semibold mb-1">Mot de passe</label>
                <input class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       type="password" name="login_password" placeholder="••••••" required>
            </div>

            <div class="flex items-center justify-between text-sm mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="login_keep_connect" name="login_keep_connect" class="accent-[#E63A5C] mr-2">
                    Se souvenir de moi
                </label>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login/forgot" class="text-[#E63A5C] hover:underline">
                    Mot de passe oublié
                </a>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <?php foreach ($oAuths as $oAuth): ?>
                    <a href="oauth/<?= $oAuth->methodIdentifier() ?>" class="p-2 rounded-full border border-[#E63A5C] hover:bg-[#E63A5C22] transition" aria-label="<?= $oAuth->methodeName() ?>">
                        <img src="<?= $oAuth->methodeIconLink() ?>" alt="<?= $oAuth->methodeName() ?>" width="32" height="32"/>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php SecurityController::getPublicData(); ?>

            <button type="submit" class="w-full py-3 bg-[#E63A5C] hover:bg-[#c22d4c] text-white rounded text-lg font-semibold transition">
                Connexion
            </button>

            <p class="mt-6">Pas encore de compte ? <a class="text-[#E63A5C] hover:underline" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>register">S'inscrire</a></p>
        </form>

    </div>
</section>
