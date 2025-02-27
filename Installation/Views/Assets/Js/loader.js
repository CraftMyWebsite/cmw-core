/* document.onreadystatechange = function () {
    if (document.readyState !== "complete") {
        document.querySelector("body").style.visibility = "hidden";
        document.querySelector("#loader").style.visibility = "visible";
    } else {
        document.querySelector("#loader").style.display = "none";
        document.querySelector("body").style.visibility = "visible";
    }
};*/


const launchLoader = (event) => {
    const form = document.getElementById('mainForm');

    // Vérifie si le formulaire est valide
    if (!form.checkValidity()) {
        event.preventDefault(); // Empêche l'envoi du formulaire si invalide
        form.reportValidity(); // Affiche les messages d'erreur natifs
        return;
    }

    let loader = document.getElementById('loader')
    let body = document.getElementById('body')

    loader.classList.remove('hidden')
    body.classList.add("hidden")
}

const btn = document.getElementById('formBtn')

btn.addEventListener('click', launchLoader)
