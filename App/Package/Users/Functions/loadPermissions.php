<?php

use CMW\Entity\USers\PermissionEntity;
use CMW\Entity\Users\RoleEntity;
use CMW\Model\USers\PermissionsModel;
use CMW\Model\Users\RolesModel;
/**
 * @param \CMW\Entity\Users\PermissionEntity[] $permissionList
 */
function showPermission(PermissionsModel $permissionModel, array $permissionList, ?RolesModel $rolesModel = null, ?RoleEntity $roleEntity = null, int $depth = 0): void
{
    // Ajout de la fonction JavaScript pour gérer l'affichage des permissions et l'auto-coche des enfants
    echo <<<JS
    <script>
        function togglePermissions(operatorChecked) {
            const permissions = document.querySelectorAll('.permission-item');
            permissions.forEach(permission => {
                if (!permission.classList.contains('operator-permission')) {
                    permission.style.display = operatorChecked ? 'none' : '';
                }
            });
        }

        function toggleChildren(parentId) {
            const parentCheckbox = document.getElementById(parentId);
            if (parentCheckbox) {
                const childCheckboxes = document.querySelectorAll('input[data-parent-id="' + parentId + '"]');
                childCheckboxes.forEach(childCheckbox => {
                    childCheckbox.checked = parentCheckbox.checked;
                    toggleChildren(childCheckbox.id); // Recursively toggle children of children
                });
            }
        }

        function uncheckMasterParentIfNeeded(childId) {
            const childCheckbox = document.getElementById(childId);
            if (childCheckbox && !childCheckbox.checked) {
                // Find the master parent by traversing up the DOM tree
                let currentCheckbox = childCheckbox;
                while (currentCheckbox) {
                    const parentId = currentCheckbox.getAttribute('data-parent-id');
                    if (!parentId) break; // Stop if no more parents

                    const parentCheckbox = document.getElementById(parentId);
                    if (parentCheckbox && !parentCheckbox.getAttribute('data-parent-id')) {
                        // This is the master parent, uncheck it
                        parentCheckbox.checked = false;
                        break;
                    }
                    currentCheckbox = parentCheckbox;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const operatorCheckbox = document.querySelector('.operator-permission input[type="checkbox"]');
            if (operatorCheckbox) {
                operatorCheckbox.addEventListener('change', function () {
                    togglePermissions(this.checked);
                });
                togglePermissions(operatorCheckbox.checked);
            }

            const parentCheckboxes = document.querySelectorAll('.permission-item input[type="checkbox"]');
            parentCheckboxes.forEach(parentCheckbox => {
                parentCheckbox.addEventListener('change', function () {
                    toggleChildren(this.id);
                    uncheckMasterParentIfNeeded(this.id);
                });
            });
        });
    </script>
JS;

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

        $paddingClass = $depth > 0 ? "pl-6" : "";
        $operatorClass = $packageTranslate === "Operator" ? "operator-permission" : "permission-item";
        $dataParentId = $hasParent ? "data-parent-id='{$p->getParent()->getId()}'" : "";

        if (!$hasParent) {
            echo "<div class='$operatorClass $paddingClass'>";
            echo "<div class='rounded-t-lg border p-4'>";
            echo "<div class='checkbox'>";
            echo "<input id='{$p->getId()}' type='checkbox' value='{$p->getId()}' name='perms[{$p->getId()}]' $check $dataParentId>";
            echo "<label for='{$p->getId()}'><h6>$packageTranslate</h6></label>";
            echo "</div></div>";
            echo "<div class='rounded-b-lg border-b border-l border-r p-4'>";

            if ($packageTranslate === "Operator") {
                echo "<p>Cette permission est la plus importante et donne tous les accès sans exception.</p>";
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
            echo "</div>";
        }

        if (!$hasParent) {
            echo "</div></div>";
        }
    }
}

function generateCheckBox(PermissionEntity $permission, bool $checked = false, ?int $parentId = null): string
{
    $check = $checked ? "checked" : "";
    $dataParentId = $parentId ? "data-parent-id='{$parentId}'" : "";
    return <<<HTML
        <div class="checkbox permission-item">
            <input id="{$permission->getId()}" type="checkbox" name="perms[{$permission->getId()}]" value="{$permission->getId()}" $check $dataParentId>
            <label for="{$permission->getId()}">{$permission->getDescription()}</label>
        </div>
HTML;
}
