<?php
$title = USERS_ROLE_ADD_TITLE;
$description = USERS_ROLE_ADD_DESC;

?>

<?php ob_start(); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?= USERS_ROLE_ADD ?> :</h3>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control" placeholder="<?= USERS_ROLE ?>"
                                           required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                    </div>
                                    <input type="text" name="description" class="form-control"
                                           placeholder="<?= USERS_ROLE_DESCRIPTION ?>" required>
                                </div>

                                <!-- PERMISSIONS -->
                                <h3 class="mt-4"><?= USERS_ROLE_PERMISSIONS_LIST ?></h3>
                                <hr>
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <?php /* @var $permissionsList */
                                        foreach ($permissionsList as $perms):
                                            foreach ($perms as $permName => $permCode): ?>
                                                <div class="col-lg-3 col-md-3">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                   id="<?= ($permCode == "*" ? "*" : "perms[$permName][$permCode]") ?>"
                                                                   name="<?= ($permCode == "*" ? "*" : "perms[$permName][$permCode]") ?>"
                                                                   value="<?= $permCode ?>">
                                                            <label for="<?= ($permCode == "*" ? "*" : "perms[$permName][$permCode]") ?>" class="custom-control-label">
                                                                <?= $permName ?>
                                                                </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= USERS_LIST_BUTTON_SAVE ?></button>
                        </div>
                        </div>
                </form>
            </div>
        </div>
    </div>

<!-- Trigger perm * and disabled all others perms checkbox -->
    <script>
        const checkbox = document.getElementById("*");

        checkbox.addEventListener('change', (event) => {
            if (event.currentTarget.checked) {
                $(':checkbox').each(function () {
                    if(this.id !== "*") {
                        this.disabled = true;
                        this.checked = false;
                    }
                });
            } else {
                $(':checkbox').each(function () {
                    this.disabled = false;
                });
            }
        })
    </script>

<?php $content = ob_get_clean(); ?>