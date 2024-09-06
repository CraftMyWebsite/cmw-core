<?php

use CMW\Entity\USers\PermissionEntity;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Model\USers\PermissionsModel;
use CMW\Model\Users\RolesModel;

/**
 * @param \CMW\Entity\Users\PermissionEntity[] $permissionList
 */
function showPermission(PermissionsModel $permissionModel, array $permissionList, ?RolesModel $rolesModel = null, ?RoleEntity $roleEntity = null, int $depth = 0): void
{
    foreach ($permissionList as $p) {
        $hasChild = $permissionModel->hasChild($p->getId());
        $hasParent = $p->hasParent();
        $packageTranslate = ucfirst($p->getCode());

        if (is_null($rolesModel) || is_null($roleEntity)) {
            $hasRole = false;
            $isAdminRole = false;
        } else {
            $hasRole = $rolesModel->roleHasPermission($roleEntity->getId(), $permissionModel->getFullPermissionCodeById($p->getId()));
            if ($roleEntity->getId() === 5) {
                $isAdminRole = true;
            } else {
                $isAdminRole = false;
            }
        }

        $disabled = $isAdminRole ? 'hidden' : '';
        $check = $hasRole ? 'checked' : '';

        $paddingClass = $depth > 0 ? 'pl-6' : '';
        $operatorClass = $packageTranslate === 'Operator' ? 'operator-permission' : 'permission-item';
        $dataParentId = $hasParent ? "data-parent-id='{$p->getParent()->getId()}'" : '';

        if (!$hasParent) {
            echo "<div class='$operatorClass $paddingClass'>";
            echo "<div class='$disabled rounded-t-lg border p-4'>";
            echo "<div class='checkbox'>";
            echo "<input id='{$p->getId()}' type='checkbox' value='{$p->getId()}' name='perms[{$p->getId()}]' $check $dataParentId>";
            echo "<label for='{$p->getId()}'><h6>$packageTranslate</h6></label>";
            echo '</div></div>';

            if ($packageTranslate === 'Operator') {
                if ($isAdminRole) {
                    echo "<div class='alert-warning'>";
                    echo "<p><i class='fa-solid fa-circle-info'></i>" . LangManager::translate('users.roles.perms.admin_warning') . '</p>';
                    echo '</div>';
                } else {
                    echo "<div class='rounded-b-lg border-b border-l border-r p-4'>";
                    echo '<p>' . LangManager::translate('users.roles.perms.operator') . '</p>';
                }
            } else {
                echo "<div class='rounded-b-lg border-b border-l border-r p-4'>";
            }
        }

        if ($hasParent) {
            echo "<div class='$operatorClass $paddingClass'>";
            echo generateCheckBox($p, $hasRole, $p->getParent()->getId());
        }

        if ($hasChild) {
            showPermission($permissionModel, $permissionModel->getPermissionByParentId($p->getId()), $rolesModel, $roleEntity, $depth + 1);
        }

        if ($hasParent) {
            echo '</div>';
        }

        if (!$hasParent) {
            echo '</div></div>';
        }
    }
}

function generateCheckBox(PermissionEntity $permission, bool $checked = false, ?int $parentId = null): string
{
    $check = $checked ? 'checked' : '';
    $dataParentId = $parentId ? "data-parent-id='{$parentId}'" : '';
    return <<<HTML
                <div class="checkbox permission-item">
                    <input id="{$permission->getId()}" type="checkbox" name="perms[{$permission->getId()}]" value="{$permission->getId()}" $check $dataParentId>
                    <label for="{$permission->getId()}">{$permission->getDescription()}</label>
                </div>
        HTML;
}
