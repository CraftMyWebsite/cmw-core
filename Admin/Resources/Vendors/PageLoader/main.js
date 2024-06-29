// script.js
document.addEventListener("DOMContentLoaded", function() {
    // Sélectionner l'élément contenant
    const pageLoader = document.querySelector('.page-loader');

    // Créer l'élément loader
    const loader = document.createElement('div');
    loader.className = 'loader';
    pageLoader.appendChild(loader);

    // Créer un conteneur pour le contenu
    const contentContainer = document.createElement('div');
    contentContainer.className = 'content-loader';

    // Déplacer les enfants existants dans le conteneur de contenu
    while (pageLoader.firstChild && pageLoader.firstChild !== loader) {
        contentContainer.appendChild(pageLoader.firstChild);
    }

    pageLoader.appendChild(contentContainer);

    window.onload = function() {
        loader.style.display = 'none';
        contentContainer.style.display = 'block';
    };
});
