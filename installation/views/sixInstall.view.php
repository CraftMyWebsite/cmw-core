<?php use CMW\Manager\Lang\LangManager; ?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>
<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("installation.administrator.title") ?></h2>
<form action="installer/submit" method="post" id="mainForm" name="mainForm">
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
                <input onblur="checkPasswordIsSame()" type="password" placeholder="<?= LangManager::translate("users.users.pass") ?>"
                       id="password" name="password"
                       class="input input-bordered input-sm w-full" required>
                       <div onclick="showPassword()" class="cursor-pointer py-1 px-2 text-sm font-medium text-white bg-cmw-pink rounded-r-lg"><i class="fa fa-eye-slash"></i></div>
            </label>
        </div>
        <div class="form-control">
            <p><?= LangManager::translate("users.users.password_confirm") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-unlock"></i></span>
                <input onblur="checkPasswordIsSame()" type="password" placeholder="<?= LangManager::translate("users.users.pass") ?>"
                       id="passwordCheck" name="passwordCheck"
                       class="input input-bordered input-sm w-full" required>
                       <div onclick="showPasswordV()" class="cursor-pointer py-1 px-2 text-sm font-medium text-white bg-cmw-pink rounded-r-lg"><i class="fa fa-eye-slash"></i></div>
            </label>
        </div>
    </div>
    <div class="mt-2"><?= LangManager::translate("installation.password.strenght") ?>
        <div class="">
            <progress class="w-64 min-h-6" max="100" value="0" id="meter"></progress>
                <div class="flex justify-between w-64 mt-1">
                    <i class="fa-regular fa-thumbs-down"></i>
                    <i class="fa-regular fa-thumbs-up"></i>
                </div>
        </div>
    </div>
    <div class="mt-2" id="passwordTextAlert"></div>
    <div class="card-actions justify-end">
        <button id="formBtn" type="submit" class="btn btn-primary" disabled>
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>

<script src="installation/views/assets/js/changeLang.js"></script>
<script>

function showPassword() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
    function showPasswordV() {
        var x = document.getElementById("passwordCheck");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function checkPasswordIsSame() {
        let textAlert = document.getElementById("passwordTextAlert");
        var pass1 = document.forms["mainForm"]["password"].value;
        var pass2 = document.forms["mainForm"]["passwordCheck"].value;
        textAlert.style.display = 'none';
        if (pass2) {
            textAlert.style.display = 'inline-block';
            if (pass1 == pass2) {
                textAlert.style.display = 'none';
                document.getElementById("formBtn").disabled = false;
            } else {
                textAlert.innerHTML = "<p class='text-cmw-pink'><?= LangManager::translate('installation.password.notmatch') ?></p>";
                document.getElementById("formBtn").disabled = true;
            }
        }
    }

    //Check password strenght

    let code = document.getElementById("password");

    let strengthbar = document.getElementById("meter");

    code.addEventListener("keyup", function(){checkpassword(code.value)

    })

    function checkpassword(password)
    {
        let strength=0;
        if (password.match(/[a-z]+/)){
            strength+=1;
        }
        if (password.match(/[A-Z]+/)){
            strength+=1;
        }
        if (password.match(/[0-9]+/)){
            strength+=1;
        }
        if (password.match(/[$@#&!]+/)){
            strength+=1;

        }
        switch(strength){
            case 0:
                strengthbar.value=0;
                break;

            case 1:
                strengthbar.value=15;
                break;

            case 2:
                strengthbar.value=35;
                break;

            case 3:
                strengthbar.value=70;
                break;

            case 4:
                strengthbar.value=100;
                break; }
    }

</script>