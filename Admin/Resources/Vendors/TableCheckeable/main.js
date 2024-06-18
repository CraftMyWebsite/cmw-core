document.addEventListener('DOMContentLoaded', () => {
    const tables = document.querySelectorAll('table.table-checkeable');

    tables.forEach((table, index) => {
        const formAction = table.getAttribute('data-form-action');
        const thead = table.querySelector('thead tr');
        const tbodyRows = table.querySelectorAll('tbody tr');

        // Create a form and append to table container
        const form = document.createElement('form');
        form.id = `checkbox-form-${index + 1}`;
        form.method = 'POST';
        form.action = formAction;
        table.parentNode.insertBefore(form, table);
        form.appendChild(table);

        // Add checkbox to thead mass-selector
        const thMassSelector = thead.querySelector('th.mass-selector');
        if (thMassSelector) {
            thMassSelector.classList.add('flex', 'justify-center');
            thMassSelector.style.cssText = '';
            thMassSelector.innerHTML = `
                <div class="checkbox">
                    <input id="checkbox-all-${index + 1}" type="checkbox" value="">
                </div>
            `;
        }

        const checkboxAll = thMassSelector.querySelector(`#checkbox-all-${index + 1}`);

        // Add checkbox to each tbody item-selector
        tbodyRows.forEach(row => {
            const tdItemSelector = row.querySelector('td.item-selector');
            if (tdItemSelector) {
                const customValue = tdItemSelector.getAttribute('data-value');
                tdItemSelector.classList.add('flex', 'justify-center');
                tdItemSelector.innerHTML = `
                    <div class="checkbox">
                        <input type="checkbox" class="checkbox-item" name="selectedIds[]" value="${customValue}">
                    </div>
                `;
            }
        });

        // Event listener for the checkbox-all to check/uncheck all checkboxes
        checkboxAll.addEventListener('change', () => {
            const checkboxes = table.querySelectorAll('tbody .checkbox-item');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkboxAll.checked;
            });
        });
    });

    // Event listener for the delete buttons
    const deleteButtons = document.querySelectorAll('.btn-danger-xl.loading-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTableIndex = button.getAttribute('data-target-table');
            const form = document.getElementById(`checkbox-form-${targetTableIndex}`);
            const formData = new FormData(form);

            // Get the selected checkboxes
            const selectedItems = formData.getAll('selectedIds[]');

            if (selectedItems.length > 0) {
                console.log('Selected items:', selectedItems);
                // Submit the form
                form.submit();
            } else {
                alert('Aucun objet sélectionner');
            }
        });
    });
});
