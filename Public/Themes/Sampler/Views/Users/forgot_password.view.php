<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle('Mot de passe oublié');
Website::setDescription('Retrouvez votre mot de passe');
?>
<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Mot de passe oublié</h1>
        </div>
    </div>
</section>

<section class=" flex items-center justify-center  py-8">
    <div class="w-full max-w-md bg-[#1E1E1E] p-8 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-center mb-6">Mot de passe oublié</h1>

        <form action="" method="post" class="space-y-6">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>

            <div>
                <label for="mail" class="block text-sm mb-1"><?= LangManager::translate('users.users.mail') ?></label>
                <input type="email" name="mail" id="mail" required
                       class="w-full px-4 py-2 bg-[#2A2A2A] text-white rounded focus:outline-none focus:ring-2 focus:ring-[#E63A5C]"
                       placeholder="<?= LangManager::translate('users.users.mail') ?>">
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-[#E63A5C] hover:bg-[#c22d4c] transition text-white font-semibold py-2 rounded">
                    <?= LangManager::translate('users.login.forgot_password.btn') ?>
                </button>
            </div>
        </form>
    </div>
</section>