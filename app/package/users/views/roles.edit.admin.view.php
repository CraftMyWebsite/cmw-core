<?php

use CMW\Controller\Users\UsersController;

$title = USERS_ROLE_EDIT_TITLE;
$description = USERS_ROLE_EDIT_DESC;
/** @var \CMW\Entity\Roles\RoleEntity $role */
/** @var \CMW\Model\Roles\RolesModel $rm */
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
                                           value="<?= $role->getName() ?>" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                    </div>
                                    <input type="text" name="description" class="form-control"
                                           value="<?= $role->getDescription() ?>"
                                           placeholder="<?= USERS_ROLE_DESCRIPTION ?>" required>
                                </div>

                                <input type="number" name="weight" class="form-control"
                                       placeholder="<?= USERS_WEIGHT ?>"
                                       value="<?= $role->getWeight() ?>" required>

                                <!-- PERMISSIONS -->
                                <h3 class="mt-4"><?= USERS_ROLE_PERMISSIONS_LIST ?></h3>
                                <hr>
                                <div class="container-fluid">
                                    <div class="row justify-content-center">
                                        <?php /* @var $permissionsList */


                                        foreach ($permissionsList as $parent):
                                            echo "<div class='mb-2 mr-5'> <span>Package: {$parent['package']} </span> <hr>";

                                            echo $parent['parent_code'];

                                            foreach ($parent['perms_childs'] as $child):?>


                                                <div class="">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox"
                                                                   id="<?= $child['child_code'] ?>"
                                                                   name="perms[<?= $child['child_code'] ?>]"
                                                                   value="<?= $child['child_code'] ?>"
                                                                <?= ($rm->roleHasPermission($role->getId(), $child['child_code'])
                                                                || $rm->roleHasPermission($role->getId(), $parent['parent_code']) ? "checked" : "") ?>>
                                                            <label for="<?= $child['child_code'] ?>"
                                                                   class="custom-control-label">
                                                                <?= $child['child_desc_value'] ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>


                                            <?php endforeach;
                                            echo "</div>";
                                        endforeach; ?>
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
                    if (this.id !== "*") {
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