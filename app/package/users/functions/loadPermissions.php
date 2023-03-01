<?php

use CMW\Entity\USers\PermissionEntity;
use CMW\Entity\Users\RoleEntity;
use CMW\Model\USers\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Utils\Utils;

function generateCheckBox(PermissionEntity $permission, string $codeValue, bool $checked = false): string
{
    $check = $checked ? "checked" : "";
    return <<<HTML
            <ul style="list-style-type: none">
                <li>
                    <div class="form-switch">
                        <input class="me-1 form-check-input" type="checkbox" id="{$permission->getId()}" name="perms[{$permission->getId()}]" value="{$permission->getId()}" $check>
                        <label class="form-check-label" for="{$permission->getId()}">$codeValue</label>
                    </div>
                </li>
            </ul>
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
        $packageTranslate = ucfirst($p->getCode());
        echo " <div class='col'>";
        if (!$hasParent) {
            echo "<b>$packageTranslate </b><ul style='list-style-type: none'>
                                <li>
                                    <div class='form-switch'><input class='me-1 form-check-input' type='checkbox' id=''><label
                                                class='form-check-label' for=''>{$p->getCode()}.*</label></div>";
        }

        $hasRole = !is_null($rolesModel) && !is_null($roleEntity) && $rolesModel->roleHasPermission($roleEntity->getId(), $permissionModel->getFullPermissionCodeById($p->getId()));

        $codeValue = $p->getCode() . (($hasChild) ? ".*" : "");

        echo generateCheckBox($p, $codeValue, $hasRole);

        if ($hasChild) {
            showPermission($permissionModel, $permissionModel->getPermissionByParentId($p->getId()));
        }
        echo "</div>";

    }

}
