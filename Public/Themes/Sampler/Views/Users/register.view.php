<?php

use CMW\Controller\Core\SecurityController;
use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var IUsersOAuth[] $oAuths */

Website::setTitle('Inscription');
Website::setDescription('Inscrivez-vous sur le site ' . Website::getWebsiteName());

?>

<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Inscription</h1>
        </div>
    </div>
</section>

<section class="page-section  text-white py-20 flex items-center justify-center">
    <div class="w-full max-w-lg bg-[#1E1E1E] p-8 rounded-xl shadow-lg">
        <form action="" method="post" class="">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>

            <div>
                <label for="register_email" class="block text-sm font-semibold mb-1">E-Mail</label>
                <input name="register_email" type="email"
                       class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       placeholder="<?= LangManager::translate('users.users.mail') ?>" required>
            </div>

            <div class="mt-6">
                <label for="register_pseudo" class="block text-sm font-semibold mb-1">Pseudo</label>
                <input name="register_pseudo" type="text"
                       class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       placeholder="<?= LangManager::translate('users.users.pseudo') ?>" required>
            </div>

            <div class="mt-6">
                <label for="register_password" class="block text-sm font-semibold mb-1">Mot de passe</label>
                <input type="password" name="register_password"
                       class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       placeholder="<?= LangManager::translate('users.users.pass') ?>" required>
            </div>

            <div class="mt-6">
                <label for="register_password_verify" class="block text-sm font-semibold mb-1">Répéter le mot de passe</label>
                <input type="password" name="register_password_verify"
                       class="w-full px-4 py-2 rounded bg-[#2a2a2a] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       placeholder="<?= LangManager::translate('users.users.repeat_pass') ?>" required>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <?php foreach ($oAuths as $oAuth): ?>
                    <a href="oauth/<?= $oAuth->methodIdentifier() ?>" class="p-2 rounded-full border border-[#E63A5C] hover:bg-[#E63A5C22] transition" aria-label="<?= $oAuth->methodeName() ?>">
                        <img src="<?= $oAuth->methodeIconLink() ?>" alt="<?= $oAuth->methodeName() ?>" width="32" height="32"/>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php SecurityController::getPublicData(); ?>

            <div class="d-grid mt-6">
                <button type="submit"
                        class="w-full py-3 bg-[#E63A5C] hover:bg-[#c22d4c] text-white rounded text-lg font-semibold transition">
                    <?= LangManager::translate('users.login.register') ?>
                </button>
            </div>
            <p class="mt-6">Déjà un compte ? <a class="text-[#E63A5C] hover:underline" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">Se connecter</a></p>
        </form>
    </div>
</section>
