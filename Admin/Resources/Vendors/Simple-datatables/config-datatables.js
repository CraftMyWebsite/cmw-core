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
            initializeSliders(); // Initialize sliders
        });

        // Réinitialiser les événements de Flowbite et TableCheckable après le tri et la pagination
        dataTable.on("datatable.page", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
            initializeSliders(); // Initialize sliders
        });

        dataTable.on("datatable.sort", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
            initializeSliders(); // Initialize sliders
        });

        dataTable.on("datatable.perpage", function() {
            attachFlowbiteModalHandlers(tableElement);
            if (tableElement.classList.contains('table-checkeable')) {
                initializeTableCheckable();  // Initialize checkboxes only if table is checkeable
            }
            initializeSliders(); // Initialize sliders
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

function initializeSliders() {
    const sliderContainers = document.querySelectorAll(".slider-container");

    sliderContainers.forEach((sliderContainer, sliderIndex) => {
        const images = sliderContainer.querySelectorAll("img");

        // Get height from data attribute
        const imageHeight = sliderContainer.getAttribute("data-height");

        // Apply hidden class and height to all images
        images.forEach(img => {
            img.classList.add("hidden", "bg-contain", "w-full");
            img.style.height = imageHeight;
        });

        // Create slides container
        const slidesContainer = document.createElement("div");
        slidesContainer.classList.add("slides", "overflow-hidden", "relative");
        sliderContainer.appendChild(slidesContainer);

        // Create indicators container
        const indicatorsContainer = document.createElement("div");
        indicatorsContainer.classList.add("indicators", "absolute", "bottom-4", "left-1/2", "transform", "-translate-x-1/2", "flex", "space-x-2");
        sliderContainer.appendChild(indicatorsContainer);

        // Create Prev button
        const prevButton = document.createElement("button");
        prevButton.classList.add("prev", "absolute", "left-0", "top-1/2", "transform", "-translate-y-1/2", "bg-gray-500", "text-white", "py-2", "rounded-r");
        prevButton.innerHTML = `
        <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
        </svg>`;
        sliderContainer.appendChild(prevButton);

        // Create Next button
        const nextButton = document.createElement("button");
        nextButton.classList.add("next", "absolute", "right-0", "top-1/2", "transform", "-translate-y-1/2", "bg-gray-500", "text-white", "py-2", "rounded-l");
        nextButton.innerHTML = `
        <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>`;
        sliderContainer.appendChild(nextButton);

        let currentIndex = 0;
        let slideInterval;

        function renderSlides() {
            images.forEach((img, index) => {
                const slideDiv = document.createElement("div");
                slideDiv.classList.add("slide", "w-full", "h-auto", "flex", "items-center", "justify-center", "text-white", "text-2xl");
                slideDiv.style.display = index === 0 ? "block" : "none";
                if (index === 0) {
                    img.classList.remove("hidden");
                }
                slideDiv.appendChild(img);
                slidesContainer.appendChild(slideDiv);

                const indicator = document.createElement("div");
                indicator.classList.add("indicator", "w-3", "h-3", "bg-white", "rounded-full", "cursor-pointer", index === 0 ? "bg-blue-700" : "bg-white");
                indicator.dataset.index = index;
                indicator.addEventListener("click", () => goToSlide(index));
                indicatorsContainer.appendChild(indicator);
            });
        }

        function goToSlide(index) {
            const slides = slidesContainer.querySelectorAll(".slide");
            const indicators = indicatorsContainer.querySelectorAll(".indicator");

            slides[currentIndex].style.display = "none";
            indicators[currentIndex].classList.remove("bg-blue-700");
            indicators[currentIndex].classList.add("bg-white");

            currentIndex = index;

            slides[currentIndex].style.display = "block";
            indicators[currentIndex].classList.remove("bg-white");
            indicators[currentIndex].classList.add("bg-blue-700");

            // Remove hidden class from current image
            slides[currentIndex].querySelector("img").classList.remove("hidden");
        }

        function nextSlide() {
            const nextIndex = (currentIndex + 1) % images.length;
            goToSlide(nextIndex);
        }

        function prevSlide() {
            const prevIndex = (currentIndex - 1 + images.length) % images.length;
            goToSlide(prevIndex);
        }

        function startSlideShow() {
            slideInterval = setInterval(nextSlide, 3000); // Change slide every 3 seconds
        }

        function stopSlideShow() {
            clearInterval(slideInterval);
        }

        prevButton.addEventListener("click", prevSlide);
        nextButton.addEventListener("click", nextSlide);

        // Pause slideshow on mouse enter
        sliderContainer.addEventListener("mouseenter", stopSlideShow);

        // Resume slideshow on mouse leave
        sliderContainer.addEventListener("mouseleave", startSlideShow);

        renderSlides();
        startSlideShow(); // Start the slideshow
    });
}

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