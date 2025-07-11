<?php

/* @var \CMW\Entity\Users\UserEntity $user */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

Website::setTitle('Double facteur obligatoire');
Website::setDescription("Merci d'activer le 2fa !");
?>
<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Double facteur</h1>
        </div>
    </div>
</section>

<section class=" flex items-center justify-center text-white py-8">
    <div class="w-full max-w-md bg-[#1E1E1E] p-8 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-center mb-6">Double authentification requise</h2>
        <p class="text-sm text-center text-gray-300 mb-6">
            <b>Veuillez activer le double facteur pour pouvoir vous connecter</b>
        </p>

        <div class="text-center mb-6">
            <img src="<?= $user->get2Fa()->getQrCode(250) ?>" alt="QR Code double authentification" class="mx-auto w-48 mb-2">
            <p class="text-sm text-gray-400"><?= $user->get2Fa()->get2FaSecretDecoded() ?></p>
        </div>

        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/2fa/toggle" method="post" class="space-y-5">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <input type="hidden" name="enforce_mail" value="<?= $user->getMail() ?>">

            <div>
                <label for="secret" class="block mb-1 text-sm">Code d'authentification</label>
                <input type="text" name="secret" id="secret" required
                       class="w-full px-4 py-2 bg-[#2A2A2A] text-white rounded focus:ring-2 focus:ring-[#E63A5C] focus:outline-none"
                       placeholder="Entrez le code à 6 chiffres">
            </div>

            <button type="submit"
                    class="w-full bg-[#E63A5C] hover:bg-[#c22d4c] transition text-white font-semibold py-2 rounded">
                Activer
            </button>
        </form>
    </div>
</section>
