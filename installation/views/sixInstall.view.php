<?php use CMW\Manager\Lang\LangManager; ?>

<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("installation.administrator.title") ?></h2>
<form action="installer/submit" method="post" id="mainForm">
    <div class="lg:grid grid-cols-2 gap-8">
        <div class="form-control">
            <p><?= LangManager::translate("users.users.pseudo") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-user"></i></span>
                <input type="text" placeholder="cwm" name="pseudo" class="input input-bordered input-sm w-full" required>
            </label>
        </div>
        <div class="form-control">
            <p><?= LangManager::translate("users.users.mail") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-at"></i></i></span>
                <input type="text" placeholder="contact@craftmywebsite.fr" name="email"
                       class="input input-bordered input-sm w-full" required>
            </label>
        </div>
    </div>
    <div class="lg:grid grid-cols-2 gap-8">
        <div class="form-control">
            <p><?= LangManager::translate("users.users.password") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-unlock"></i></span>
                <input type="password" placeholder="<?= LangManager::translate("users.users.pass") ?>"
                       id="password" name="password"
                       class="input input-bordered input-sm w-full" required>
            </label>
        </div>
        <div class="form-control">
            <p><?= LangManager::translate("users.users.password_confirm") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-unlock"></i></span>
                <input type="password" placeholder="<?= LangManager::translate("users.users.pass") ?>"
                       id="passwordCheck" name="passwordCheck"
                       class="input input-bordered input-sm w-full" required>
            </label>
        </div>
    </div>
    <div class="card-actions justify-end">
        <button id="formBtn" type="submit" class="btn btn-primary">
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>