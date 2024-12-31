/*
* ACCORDION
* */
document.addEventListener('DOMContentLoaded', function () {
    const accordions = document.querySelectorAll('.accordion');

    accordions.forEach(accordion => {
        const button = accordion.querySelector('.accordion-btn');
        const content = accordion.querySelector('.accordion-content');

        // Get classes to be applied from data-active attributes
        const buttonActiveClasses = button.getAttribute('data-active') ? button.getAttribute('data-active').split(' ') : [];
        const contentActiveClasses = content.getAttribute('data-active') ? content.getAttribute('data-active').split(' ') : [];

        // Add styles and arrow icon
        button.classList.add('w-full', 'flex', 'justify-between', 'items-center', 'py-3', 'focus:outline-none');
        button.innerHTML += '<span class="transform transition-transform duration-300">&#x25BC;</span>';
        content.classList.add('hidden');

        button.addEventListener('click', () => {
            const icon = button.querySelector('span');
            const isActive = content.classList.contains('hidden');

            if (isActive) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                buttonActiveClasses.forEach(cls => button.classList.add(cls));
                contentActiveClasses.forEach(cls => content.classList.add(cls));
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
                buttonActiveClasses.forEach(cls => button.classList.remove(cls));
                contentActiveClasses.forEach(cls => content.classList.remove(cls));
            }
        });
    });
});

/*
* SLIDER
* */
document.addEventListener("DOMContentLoaded", () => {
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
                slideDiv.classList.add("slide", "w-full", "h-auto", "items-center", "justify-center", "text-white", "text-2xl");
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
});


/*
* DROPDOWN
* */
document.addEventListener("DOMContentLoaded", function() {
    // Sélectionner tous les dropdowns et ajouter la classe hidden par défaut
    const allDropdownContents = document.querySelectorAll('.dropdown-content');
    allDropdownContents.forEach(dropdownContent => {
        dropdownContent.style.display = 'none';
    });

    // Gestion du dropdown par hover
    const hoverDropdowns = document.querySelectorAll('.dropdown-hover');
    hoverDropdowns.forEach(dropdownHover => {
        const dropdownOpener = dropdownHover.querySelector('.dropdown-opener');
        const dropdownContent = dropdownHover.querySelector('.dropdown-content');
        let timeoutId;

        dropdownHover.addEventListener('mouseenter', function() {
            clearTimeout(timeoutId);
            dropdownContent.style.display = 'block';
        });

        dropdownHover.addEventListener('mouseleave', function() {
            timeoutId = setTimeout(function() {
                dropdownContent.style.display = 'none';
            }, 300); // Délai de 300ms avant de masquer le dropdown
        });
    });

    // Gestion du dropdown par clic
    const clickDropdowns = document.querySelectorAll('.dropdown');
    clickDropdowns.forEach(dropdown => {
        const dropdownOpener = dropdown.querySelector('.dropdown-opener');
        const dropdownContent = dropdown.querySelector('.dropdown-content');

        dropdownOpener.addEventListener('click', function(event) {
            event.stopPropagation();
            if (dropdownContent.style.display === 'none' || dropdownContent.style.display === '') {
                dropdownContent.style.display = 'block';
            } else {
                dropdownContent.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            if (!dropdown.contains(event.target)) {
                dropdownContent.style.display = 'none';
            }
        });

        dropdownContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
});

/*
* CHOICES
* */
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('.choices');
    elements.forEach(element => {
        new Choices(element, {
            removeItemButton: true,
            searchEnabled: true,
            placeholderValue: 'Choisissez en plusieurs !',
            loadingText: 'Chargement...',
            noResultsText: 'Aucun résultat trouvé',
            itemSelectText: 'Cliquez pour sélectionner',
            shouldSort: false
        });
    });
});

/*
* LOADING BTN
* */
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.loading-btn');

    buttons.forEach(button => {
        button.addEventListener('click', function(event) {
            if (!button.classList.contains('loading')) {
                button.classList.add('loading');
                button.disabled = true;
                button.dataset.originalText = button.textContent;
                button.innerHTML = `${button.getAttribute('data-loading-btn')} <span class="loading-icon"></span>`;

                // Get the form and submit it
                const form = button.closest('form');
                if (form) {
                    form.submit();
                }
            } else {
                button.classList.remove('loading');
                button.textContent = button.dataset.originalText;
            }
        });
    });
});

/*
* AUTO CHECKBOX
* */
document.addEventListener('DOMContentLoaded', (event) => {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (checkbox.value === "" || checkbox.getAttribute('value') === null) {
            checkbox.value = "1";
        }
    });
});
