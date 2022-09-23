<?php

use CMW\Entity\USers\PermissionEntity;
use CMW\Entity\Users\RoleEntity;
use CMW\Model\USers\PermissionsModel;
use CMW\Model\Users\RolesModel;

function generateCheckBox(PermissionEntity $permission, string $codeValue, bool $checked = false): string
{
    $check = $checked ? "checked" : "";
    return <<<HTML
            <li>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input permission-input" id="{$permission->getId()}" name="perms[{$permission->getId()}]" value="{$permission->getId()}" $check>
                        <label for="{$permission->getId()}" class="custom-control-label">
                            {$codeValue}
                        </label>
                    </div>
                </div>
            </li>
        HTML;
}

/**
 * @param \CMW\Entity\Users\PermissionEntity[] $permissionList
 */
function showPermission(PermissionsModel $permissionModel, array $permissionList, ?RolesModel $rolesModel = null, ?RoleEntity $roleEntity = null): void
{

    foreach ($permissionList as $p) {
        $hasChild = $permissionModel->hasChild($p->getId());
        $hasParent = $p->hasParent();
        echo "<ul>";
        if (!$hasParent) {
            echo "<div class='mb-2 mr-5'> <span>Package: {$p->getCode()} </span> <hr>";
        }

        $hasRole = !is_null($rolesModel) && !is_null($roleEntity) && $rolesModel->roleHasPermission($roleEntity->getId(), $permissionModel->getFullPermissionCodeById($p->getId()));

        $codeValue = $p->getCode() . (($hasChild) ? ".*" : "");

        echo generateCheckBox($p, $codeValue, $hasRole);

        if ($hasChild) {
            showPermission($permissionModel, $permissionModel->getPermissionByParentId($p->getId()));
        }
        echo "</ul>";

    }

}
