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