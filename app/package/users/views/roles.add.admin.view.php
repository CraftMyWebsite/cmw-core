<?php

/**@var \CMW\Controller\Permissions\PermissionsController $permissionController */

/**@var \CMW\Model\Permissions\PermissionsModel $permissionModel */

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("users.role.add_title");
$description = LangManager::translate("users.role.add_desc");

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.role.add") ?> :</h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control" placeholder="<?= LangManager::translate("users.users.role") ?>"
                                       required>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                </div>
                                <input type="text" name="description" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.role_description") ?>" required>
                            </div>

                            <input type="number" name="weight" class="form-control"
                                   placeholder="<?= LangManager::translate("users.users.weight") ?>"
                                   required>

                            <!-- PERMISSIONS -->
                            <h3 class="mt-4"><?= LangManager::translate("users.role.permission_list") ?></h3>
                            <hr>
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <?php showPermission($permissionModel, $permissionController->getParents()) ?>
                                </div>

                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
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
