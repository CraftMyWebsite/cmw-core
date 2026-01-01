<?php use CMW\Manager\Lang\LangManager; ?>
<h2 class="text-2xl font-medium text-center"><?= LangManager::translate('Installation.administrator.title') ?></h2>
<form action="installer/submit" method="post" id="mainForm" name="mainForm">
    <div class="lg:grid grid-cols-2 gap-8">
        <div class="form-control">
            <p><?= LangManager::translate('users.users.pseudo') ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-user"></i></span>
                <input type="text" placeholder="cmw" name="pseudo" class="input input-bordered input-sm w-full"
                       required>
            </label>
        </div>
        <div class="form-control">
            <p><?= LangManager::translate('users.users.mail') ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-at"></i></i></span>
                <input type="email" placeholder="contact@craftmywebsite.fr" name="email"
                       class="input input-bordered input-sm w-full" required>
            </label>
        </div>
    </div>
    <div class="lg:grid grid-cols-2 gap-8">
        <div class="form-control">
            <p><?= LangManager::translate('users.users.password') ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-unlock"></i></span>
                <input onblur="checkPasswordIsSame()" type="password"
                       placeholder="<?= LangManager::translate('users.users.pass') ?>"
                       id="password" name="password"
                       class="input input-bordered input-sm w-full" required>
                <div onclick="showPassword()"
                     class="cursor-pointer py-1 px-2 text-sm font-medium text-white bg-cmw-pink rounded-r-lg"><i
                        class="fa fa-eye-slash"></i></div>
            </label>
        </div>
        <div class="form-control">
            <p><?= LangManager::translate('users.users.password_confirm') ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-unlock"></i></span>
                <input onblur="checkPasswordIsSame()" type="password"
                       placeholder="<?= LangManager::translate('users.users.pass') ?>"
                       id="passwordCheck" name="passwordCheck"
                       class="input input-bordered input-sm w-full" required>
                <div onclick="showPasswordV()"
                     class="cursor-pointer py-1 px-2 text-sm font-medium text-white bg-cmw-pink rounded-r-lg"><i
                        class="fa fa-eye-slash"></i></div>
            </label>
        </div>
    </div>
        <div class="">
            <progress class="w-64" style="height: 10px; margin-left: 50px" max="100" value="0" id="meter"></progress>
        </div>
    <div id="passwordTextAlert"></div>
    <div class="card-actions justify-end">
        <button id="formBtn" type="submit" class="btn btn-primary" disabled>
            <?= LangManager::translate('core.btn.next') ?>
        </button>
    </div>
</form>

<script>

    function showPassword() {
        const x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function showPasswordV() {
        const x = document.getElementById("passwordCheck");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    const formBtn = document.getElementById("formBtn");
    const textAlert = document.getElementById("passwordTextAlert");

    const pass1Input = document.getElementById("password");
    const pass2Input = document.getElementById("passwordCheck");

    const strengthbar = document.getElementById("meter");

    /**
     * Vérifie si les mots de passe sont identiques
     */
    function checkPasswordsMatch() {
        const pass1 = pass1Input.value;
        const pass2 = pass2Input.value;

        textAlert.style.display = 'none';
        formBtn.disabled = true;

        if (!pass2) return;

        if (pass1 === pass2) {
            formBtn.disabled = false;
        } else {
            textAlert.style.display = 'inline-block';
            textAlert.innerHTML = "<p class='text-cmw-pink'><?= LangManager::translate('Installation.password.notmatch') ?></p>";
        }
    }

    /**
     * Vérifie la force du mot de passe
     */
    function checkPasswordStrength(password) {
        let strength = 0;

        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[$@#&!]/.test(password)) strength++;

        const values = [0, 15, 35, 70, 100];
        strengthbar.value = values[strength] ?? 0;
    }

    /**
     * Événements
     */
    pass1Input.addEventListener("input", () => {
        checkPasswordStrength(pass1Input.value);
        checkPasswordsMatch();
    });

    pass2Input.addEventListener("input", checkPasswordsMatch);

</script>