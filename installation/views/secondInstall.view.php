<?php
/* @var \CMW\Controller\Installer\InstallerController $install */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= INSTALL_GAME_SELECTION_TITLE ?></h3>
    </div>
    <!-- form start -->
    <form action="" role="form" method="post" id="formSecondInstall" name="formSecondInstall">
        <div class="card-body">

            <div class="row">

                <?php

                foreach ($install->getGameList() as $game): ?>
                    <div class="game-container">

                        <label class="game-label">
                            <input type="radio" name="game" value="<?= $game ?>">

                            <img src='installation/views/assets/img/<?= $game ?>-logo.png'
                                 class="rounded mx-auto"
                                 style="max-height: 150px; max-width: 150px"
                                 alt="Logo <?= $game ?>">
                        </label>

                        <span class="game-name"><?= $game ?></span>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" name="submitSecondInstall" id="submitSecondInstall"
                    class="btn btn-primary"><?= INSTALL_SAVE ?>
            </button>
        </div>
    </form>
</div>

<script>

    const formSubmit = document.getElementById("submitSecondInstall"),
          formRaw    = document.getElementById("formSecondInstall");

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