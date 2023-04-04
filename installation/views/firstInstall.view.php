<h2 class="text-2xl font-medium text-center"><?= INSTALL_CONFIG_TITLE ?></h2>
<form action="installer/submit" method="post">
    <div class="lg:grid grid-cols-2 gap-8">
        <div>
            <h2 class="text-lg font-medium text-center"><?= INSTALL_CONFIG_DB ?></h2>
            <div class="grid grid-cols-6 gap-4 mb-2">
                <div class="col-span-4">
                    <p class="font-light"><?= INSTALL_CONFIG_DB_ADDRESS ?> :</p>
                    <label class="input-group input-group">
                        <span><i class="fa-solid fa-server"></i></span>
                        <input type="text" value="localhost" placeholder="localhost" id="bdd_address" name="bdd_address"
                               class="input input-bordered input-sm w-full" required>
                    </label>
                </div>
                <div class="col-span-2">
                    <p class="font-light"><?= INSTALL_CONFIG_DB_PORT ?> :</p>
                    <label class="input-group input-group">
                        <span><i class="fa-solid fa-server"></i></span>
                        <input type="text" value="3306" placeholder="3306" id="bdd_port" name="bdd_port"
                               class="input input-bordered input-sm w-full" required>
                    </label>
                </div>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= INSTALL_CONFIG_DB_NAME ?>:</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-database"></i></span>
                    <input type="text" placeholder="craftmywebsite" id="bdd_name" name="bdd_name"
                           class="input input-bordered input-sm w-full" required>
                </label>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= INSTALL_CONFIG_DB_USER ?> :</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-user"></i></span>
                    <input type="text" placeholder="webmaster" id="bdd_login" name="bdd_login"
                           class="input input-bordered input-sm w-full" required>
                </label>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= INSTALL_CONFIG_DB_PASS ?> :</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-unlock"></i></span>
                    <input type="password" placeholder="••••" id="bdd_pass" name="bdd_pass"
                           class="input input-bordered input-sm w-full">
                </label>
            </div>
            <div class="text-center">
                <button type="button" onclick="testDb()" class="btn btn-primary"><?= INSTALL_BTN_TEST ?></button>
            </div>
        </div>
        <div>
            <h2 class="text-lg font-medium text-center"><?= INSTALL_CONFIG_SETTINGS ?></h2>
            <div class="mb-2">
                <p class="font-light"><?= INSTALL_CONFIG_SITE_FOLDER ?>:</p>
                <label class="input-group input-group">
                    <span><i class="fa-regular fa-folder-open"></i></span>
                    <input type="text" placeholder="/" value="/" name="install_folder"
                           class="input input-bordered input-sm w-full" required>
                </label>
                <small><?= INSTALL_CONFIG_SITE_FOLDER_ABOUT ?></small>
            </div>
            <div class="mt-4">
                <p class="font-light"><?= INSTALL_CONFIG_DEVMODE ?> :</p>
                <input id="devmode" type="checkbox" name="dev_mode" class="checkbox checkbox-info checkbox-sm"/>
                <label for="devmode"><?= INSTALL_CONFIG_DEVMODE_ABOUT ?></label>
            </div>
        </div>
    </div>
    <div class="card-actions justify-end">
        <button type="submit" class="btn btn-primary"><?= INSTALL_BTN_NEXT ?></button>
    </div>
</form>

<script src="installation/views/assets/js/testDb.js"></script>