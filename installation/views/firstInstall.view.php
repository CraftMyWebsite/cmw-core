<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= INSTALL_BDD_TITLE ?></h3>
    </div>
    <!-- form start -->
    <form action="" role="form" id="formFirstInstall" name="formFirstInstall">
        <div class="card-body">
            <div class="form-group">
                <label for="bdd_name"><?= INSTALL_BDD_NAME ?></label>
                <input type="text" name="bdd_name" class="form-control" id="bdd_name"
                       placeholder="craftmywebsite"
                       required>
                <small class="text-muted"><?= INSTALL_BDD_NAME_ABOUT ?></small>
            </div>
            <div class="form-group">
                <label for="bdd_login"><?= INSTALL_BDD_USER ?></label>
                <input type="text" name="bdd_login" class="form-control" id="bdd_login"
                       placeholder="root"
                       required>
                <small class="text-muted"><?= INSTALL_BDD_USER_ABOUT ?></small>
            </div>
            <div class="form-group">
                <label for="bdd_pass"><?= INSTALL_BDD_PASS ?></label>
                <div class="input-group" id="showHidePassword">
                    <input type="password" name="bdd_pass" class="form-control"
                           placeholder="********"
                           id="bdd_pass">
                    <div class="input-group-append">
                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash"
                                                                aria-hidden="true"></i></a>
                    </div>
                </div>
                <small class="text-muted"><?= INSTALL_BDD_PASS_ABOUT ?></small>
            </div>
            <div class="form-group">
                <label for="bdd_address"><?= INSTALL_BDD_ADDRESS ?></label>
                <input type="text" name="bdd_address" class="form-control" id="bdd_address"
                       placeholder="localhost"
                       value="localhost" required>
                <small class="text-muted"><?= INSTALL_BDD_ADDRESS_ABOUT ?></small>
            </div>
            <hr>
            <h2><?= INSTALL_SITE_TITLE ?></h2>
            <div class="form-group">
                <label for="install_folder"><?= INSTALL_SITE_FOLDER ?></label>
                <input type="text" name="install_folder" class="form-control"
                       id="install_folder"
                       placeholder="/"
                       value="/" required>
                <small class="text-muted"><?= INSTALL_SITE_FOLDER_ABOUT ?></small>
            </div>
            <div class="form-check">
                <input type="checkbox" name="dev_mode" class="form-check-input"
                       id="dev_mode">
                <label class="form-check-label"
                       for="dev_mode"><?= INSTALL_DEVMODE_NAME ?></label>
                <br>
                <small class="text-muted"><?= INSTALL_DEVMODE_NAME_ABOUT ?></small>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" id="submitFirstInstall" name="submitFirstInstall"
                    class="btn btn-primary"><?= INSTALL_SAVE ?></button>
        </div>
    </form>
</div>

<script>

    const formSubmit = document.getElementById("submitFirstInstall"),
          formRaw    = document.getElementById("formFirstInstall");

    formRaw.onsubmit = e => {
        e.preventDefault();

        const formData   = new FormData(formRaw);

        fetch(`${window.location.pathname}/submit`, {
            method: "post",
            body  : formData,
        }).then(v => v.text())
            .then(res => {
                if (+res > 0) {
                    console.log("ok")
                    window.location.reload();
                } else {
                    //Todo, Alert system
                    console.log(`ERROR CODE : ${res}`)
                }


            })
    }

</script>