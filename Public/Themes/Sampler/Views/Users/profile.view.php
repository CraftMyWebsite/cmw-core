<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var \CMW\Entity\Users\UserEntity $user */

Website::setTitle('Profil ' . $user->getPseudo());
Website::setDescription("Découvrez le profil de l'utilisateur " . $user->getPseudo());

?>

<section class="hero-gradiant">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-8 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl">Bonjour <?= $user->getPseudo() ?></h1>
        </div>
    </div>
</section>

<section class="page-section bg-[#121212] text-white py-8">
    <div class="lg:grid grid-cols-2 px-4 px-lg-5 gap-8">
            <!-- Infos personnelles -->

                <div class="bg-[#1E1E1E] rounded-xl p-5 shadow-md">
                    <h4 class="text-center text-xl font-semibold mb-4">Informations personnelles</h4>
                    <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'profile/update' ?>" method="post" class="space-y-4">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>

                        <div class="row">
                            <div class="col-md-6 space-y-3">
                                <div>
                                    <label class="block text-sm mb-1">E-Mail</label>
                                    <input class="w-full px-3 py-2 bg-[#2A2A2A] text-white rounded" name="login_email" type="email" value="<?= $user->getMail() ?>" required>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Mot de passe</label>
                                    <input class="w-full px-3 py-2 bg-[#2A2A2A] text-white rounded" name="login_email" type="password" required>
                                </div>
                            </div>
                            <div class="col-md-6 space-y-3">
                                <div>
                                    <label class="block text-sm mb-1">Pseudo</label>
                                    <input class="w-full px-3 py-2 bg-[#2A2A2A] text-white rounded" name="login_email" type="text" value="<?= $user->getPseudo() ?>" required>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Confirmation</label>
                                    <input class="w-full px-3 py-2 bg-[#2A2A2A] text-white rounded" name="login_email" type="password" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center pt-2">
                            <button class="px-5 py-2 rounded bg-[#E63A5C] hover:bg-[#c22d4c] transition text-white">Appliquer les modifications</button>
                        </div>
                    </form>
                </div>

                <!-- 2FA -->
                <div class="bg-[#1E1E1E] rounded-xl p-5 shadow-md mt-4">
                    <h4 class="text-center text-xl font-semibold mb-2">Sécurité :</h4>
                    <p class="text-center text-sm">
                        <?php if ($user->get2Fa()->isEnabled()): ?>
                            <span class="text-green-400">Actif ✅</span>
                        <?php else: ?>
                            <span class="text-red-500">Inactif ❌</span>
                        <?php endif; ?>
                    </p>

                    <?php if (!$user->get2Fa()->isEnabled()): ?>
                        <p class="text-sm mt-2 text-center">Scannez ce QR code dans Google Authenticator, Aegis, etc.</p>
                    <?php endif; ?>

                    <div class="row mt-3">
                        <div class="col-md-6 text-center">
                            <img src="<?= $user->get2Fa()->getQrCode(250) ?>" alt="QR Code 2FA" class="mx-auto mb-2" width="140">
                            <span class="text-sm"><?= $user->get2Fa()->get2FaSecretDecoded() ?></span>
                        </div>
                        <div class="col-md-6 text-center">
                            <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/2fa/toggle" method="post" class="space-y-3">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <input class="w-full px-3 py-2 bg-[#2A2A2A] text-white rounded" name="secret" type="number" maxlength="7" required placeholder="Code d'authentification">
                                <button class="w-full py-2 bg-[#E63A5C] hover:bg-[#c22d4c] text-white rounded">
                                    <?= $user->get2Fa()->isEnabled() ? 'Désactiver' : 'Activer' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>


            <!-- Image de profil -->
            <div class="">
                <div class="bg-[#1E1E1E] rounded-xl p-5 shadow-md">
                    <h4 class="text-center text-xl font-semibold mb-4">Identité visuelle</h4>

                    <?php if (!is_null($user->getUserPicture()?->getImage())): ?>
                        <div class="text-center mb-4">
                            <img class="mx-auto rounded-lg border border-gray-600 shadow-xl" src="<?= $user->getUserPicture()->getImage() ?>" width="150" alt="Image de profil de <?= $user->getPseudo() ?>">
                        </div>
                    <?php endif; ?>

                    <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/update/picture" method="post" enctype="multipart/form-data" class="space-y-4">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <div>
                            <input type="file" id="pictureProfile" name="pictureProfile" accept=".png, .jpg, .jpeg, .webp, .gif" class="text-sm" required>
                            <p class="text-xs mt-1 text-gray-400">PNG, JPG, JPEG, WEBP, GIF (MAX. 400x400px).</p>
                        </div>
                        <button class="px-5 py-2 rounded bg-[#E63A5C] hover:bg-[#c22d4c] text-white">Sauvegarder</button>
                    </form>
                </div>
            </div>

            <!-- Supprimer compte -->
            <div class="mt-4">
                <div class="bg-[#1E1E1E] text-center p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-semibold mb-2">Vous nous quittez ?</h2>
                    <p class="text-sm text-gray-400 mb-4">Nous sommes tristes de vous voir partir !</p>
                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile/delete/<?= $user->getId() ?>"
                       class="px-5 py-2 rounded bg-red-600 hover:bg-red-700 text-white transition">
                        Supprimer mon compte
                    </a>
                </div>
            </div>
    </div>
</section>
