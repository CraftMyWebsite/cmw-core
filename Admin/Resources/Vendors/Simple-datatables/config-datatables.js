// Fonction pour initialiser TableCheckable
function initializeTableCheckable() {
    const tables = document.querySelectorAll('table.table-checkeable');

    tables.forEach((table, index) => {
        const formAction = table.getAttribute('data-form-action');
        const thead = table.querySelector('thead tr');
        const tbodyRows = table.querySelectorAll('tbody tr');

        // Create a form and append to table container
        let form = table.closest('form');
        if (!form) {
            form = document.createElement('form');
            form.id = `checkbox-form-${index + 1}`;
            form.method = 'POST';
            form.action = formAction;
            table.parentNode.insertBefore(form, table);
            form.appendChild(table);
        }

        // Add checkbox to thead mass-selector
        const thMassSelector = thead.querySelector('th.mass-selector');
        if (thMassSelector) {
            thMassSelector.classList.add('flex', 'justify-center');
            thMassSelector.style.cssText = '';
            thMassSelector.innerHTML = `
                <div class="checkbox">
                    <input id="checkbox-all-${index + 1}" type="checkbox" value="1">
                </div>
            `;
        }

        const checkboxAll = thMassSelector ? thMassSelector.querySelector(`#checkbox-all-${index + 1}`) : null;

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
        if (checkboxAll) {
            checkboxAll.addEventListener('change', () => {
                const checkboxes = table.querySelectorAll('tbody .checkbox-item');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkboxAll.checked;
                });
            });
        }
    });

    // Event listener for the delete buttons
    const deleteButtons = document.querySelectorAll('.btn-mass-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const targetTableIndex = button.getAttribute('data-target-table');
            const form = document.getElementById(`checkbox-form-${targetTableIndex}`);

            if (form) {
                const formData = new FormData(form);

                // Get the selected checkboxes
                const selectedItems = formData.getAll('selectedIds[]');
                if (selectedItems.length > 0) {
                    form.submit();
                } else {
                    iziToast.show(
                        {
                            titleSize: '16',
                            messageSize: '14',
                            icon: 'fa-solid fa-xmark',
                            title  : "Erreur",
                            message: "Vous n'avez rien sélectionner !",
                            color: "#41435F",
                            iconColor: '#DE2B59',
                            titleColor: '#DE2B59',
                            messageColor: '#fff',
                            balloon: false,
                            close: false,
                            position: 'bottomRight',
                            timeout: 5000,
                            animateInside: false,
                            progressBar: false,
                            transitionIn: 'fadeInLeft',
                            transitionOut: 'fadeOutRight',
                        });
                    setTimeout(function() {
                        location.reload();
                    }, 5000);
                }
            } else {
                iziToast.show(
                    {
                        titleSize: '16',
                        messageSize: '14',
                        icon: 'fa-solid fa-xmark',
                        title  : "Erreur",
                        message: "Impossible de trouver le formulaire de suppression !",
                        color: "#41435F",
                        iconColor: '#DE2B59',
                        titleColor: '#DE2B59',
                        messageColor: '#fff',
                        balloon: false,
                        close: false,
                        position: 'bottomRight',
                        timeout: 5000,
                        animateInside: false,
                        progressBar: false,
                        transitionIn: 'fadeInLeft',
                        transitionOut: 'fadeOutRight',
                    });
                setTimeout(function() {
                    location.reload();
                }, 5000);
            }
        });
    });
}

// Initialiser DataTable et réinitialiser les checkbox après chaque manipulation
document.addEventListener('DOMContentLoaded', () => {
    for (let i = 1; i < 20; i++) {
        let tableElement = document.getElementById("table" + i);
        if (tableElement == null) {
            continue;
        }

        // Lire la valeur de l'attribut data-load-per-page
        let perPageValue = tableElement.getAttribute('data-load-per-page') || 5;

        let dataTable = new simpleDatatables.DataTable(tableElement, {
            perPage: parseInt(perPageValue),
            deferRender: false,
        });

        function adaptPageDropdown() {
            const selector = dataTable.wrapper.querySelector(".dataTable-selector");
            selector.parentNode.parentNode.insertBefore(selector, selector.parentNode);
        }

        // Patch "per page dropdown" and pagination after table rendered
        dataTable.on("datatable.init", function () {
            adaptPageDropdown();
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
        });

        // Réinitialiser les événements de Flowbite et TableCheckable après le tri et la pagination
        dataTable.on("datatable.page", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
        });

        dataTable.on("datatable.sort", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
        });

        dataTable.on("datatable.perpage", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
        });
    }
});

// Fonction pour réinitialiser les événements de Flowbite
function attachFlowbiteModalHandlers(tableElement) {
    // Sélectionner tous les boutons dans le tbody de la table donnée
    tableElement.querySelectorAll('tbody button[data-modal-toggle]').forEach(button => {
        button.addEventListener('click', function() {
            let modalId = button.getAttribute('data-modal-toggle');
            let modalElement = document.getElementById(modalId);
            if (modalElement) {
                // Utiliser Flowbite pour afficher le modal
                const modal = new Modal(modalElement);
                modal.show();
            }
        });
    });

    // Gestion de la fermeture des modals
    document.querySelectorAll('div.modal-container button[data-modal-hide]').forEach(button => {
        button.addEventListener('click', function() {
            var modalId = button.getAttribute('data-modal-hide');
            var modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.hide();
            }
        });
    });
}
