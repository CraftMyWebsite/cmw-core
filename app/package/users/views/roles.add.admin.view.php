<?php

/**@var \CMW\Controller\Permissions\PermissionsController $permissionController */

/**@var \CMW\Model\Permissions\PermissionsModel $permissionModel */

$title = USERS_ROLE_ADD_TITLE;
$description = USERS_ROLE_ADD_DESC;

?>

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

                                <input type="number" name="weight" class="form-control"
                                       placeholder="<?= USERS_WEIGHT ?>"
                                       required>

                                <!-- PERMISSIONS -->
                                <h3 class="mt-4"><?= USERS_ROLE_PERMISSIONS_LIST ?></h3>
                                <hr>
                                <div class="container-fluid">
                                    <div class="row justify-content-center">
                                        <?php showPermission($permissionModel, $permissionController->getParents()) ?>
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
    </div>

    <!-- Trigger perm * and disabled all others perms checkbox -->
    <script>
        const inputs = document.getElementsByClassName("permission-input")


        const checkChild = (parentElement) => {
            const group = parentElement.parentElement.parentElement.parentElement.parentElement
            const els   = group.getElementsByClassName("permission-input")
            for (const item of els) {
                item.parentElement.parentElement.parentElement.classList.toggle("d-none")
            }
            parentElement.parentElement.parentElement.parentElement.classList.toggle("d-none")
        }

        for (const inp of inputs) {

            inp.onchange = () => checkChild(inp);

        }


    </script>
