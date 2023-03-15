
let roles;

window.onload = async function () {
    roles = await getRolesWeights()
}

const getRolesWeights = async () => {
    let url = await fetch(`getRoles`)
    return await url.json()
}


const checkIfWeightsIsAlreadyTaken = async (weight) => {
    if (weight === ""){
        return
    }

    let roleData = Object.values(roles).find(role => role.weight === parseInt(weight))

    launchAlert(roleData.name, weight)
}

const launchAlert = (roleName, weight) => {
    iziToast.show(
        {
            titleSize: '16',
            messageSize: '14',
            icon: 'fa-solid fa-info',
            title  : "",
            message: `Rôle ${roleName} a déjà un poids de ${weight}`,
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
}

