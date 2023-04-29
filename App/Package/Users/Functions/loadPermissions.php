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

        if (is_null($rolesModel) || is_null($roleEntity)) {
            $hasRole = false;
        } else {
            $hasRole = $rolesModel->roleHasPermission($roleEntity->getId(), $permissionModel->getFullPermissionCodeById($p->getId()));
        }

        $check = $hasRole ? "checked" : "";

        echo " <div class='col'>";
        if (!$hasParent) {
            echo "<b>$packageTranslate </b><ul style='list-style-type: none'>
                                <li>
                                    <div class='form-switch'>
                                        <input class='me-1 form-check-input permission-input' type='checkbox' id='{$p->getId()}' 
                                        value='{$p->getId()}' name='perms[{$p->getId()}]' $check>
                                        
                                        <label class='form-check-label' for=''>{$p->getCode()}.*</label>
                                    </div>";
        }

        $codeValue = $p->getCode() . (($hasChild) ? ".*" : "");

        $hiddeChilds = !$hasParent && $hasRole ? 'd-none' : '';

        echo "<div class='perms-child $hiddeChilds'>";
        if ($p->hasParent()) {
            echo generateCheckBox($p, $codeValue, $hasRole);
        }

        if ($hasChild) {
            showPermission($permissionModel, $permissionModel->getPermissionByParentId($p->getId()), $rolesModel, $roleEntity);
        }
        echo "</div></div>";

    }

}
