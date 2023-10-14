document.getElementById("sendMail").addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche le rechargement de la page

    const button = document.getElementById("testButton");
    button.disabled = true;
    const originalText = button.innerHTML;
    button.innerHTML = originalText + '<i class="fa-solid fa-spinner fa-spin"></i>';

    // Récupérez l'adresse e-mail du formulaire
    const receiver = document.getElementById("receiver").value;

    // Créez une instance de XMLHttpRequest
    const xhr = new XMLHttpRequest();

    // Configurez la requête
    xhr.open("POST", "/cmw-admin/mail/test", true);

    // Configurez le gestionnaire d'événements pour la réponse réussie
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            iziToast.show(
                {
                    titleSize: '16',
                    messageSize: '14',
                    icon: 'fa-solid fa-check',
                    title: "Mail",
                    message: "Le mail de test à été envoyé, veuillez verifier la reception sur " + receiver,
                    color: "#41435F",
                    iconColor: '#22E445',
                    titleColor: '#22E445',
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
            button.disabled = false;
            button.innerHTML = originalText;
        } else {
            iziToast.show(
                {
                    titleSize: '16',
                    messageSize: '14',
                    icon: 'fa-solid fa-xmark',
                    title  : "Mail",
                    message: "Impossible de réaliser le test !",
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
            button.disabled = false;
            button.innerHTML = originalText;
            console.error(xhr.statusText);
        }
    };

    // Configurez le gestionnaire d'événements pour les erreurs
    xhr.onerror = function() {
        console.error("Erreur de réseau lors de la requête.");
        iziToast.show(
            {
                titleSize: '16',
                messageSize: '14',
                icon: 'fa-solid fa-xmark',
                title  : "Mail",
                message: "Erreur de réseau lors de la requête.",
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
        button.disabled = false;
        button.innerHTML = originalText;
    };

    // Préparez les données à envoyer
    const data = "receiver=" + receiver;

    // Définissez les en-têtes pour la requête POST
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Envoyez la requête avec les données
    xhr.send(data);
});