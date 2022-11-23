const storeUserId = (userId) => {
    localStorage.setItem("cmwUserEditId", userId)
}

const getStoreUserId = () => {
    return localStorage.getItem("cmwUserEditId")
}

const clearEditUserId = () => {
    localStorage.removeItem("cmwUserEditId")
}


const fillEditModal = () => {


    let userId = getStoreUserId()

    fetch(`../users/getUser/${userId}`)
        .then((response) => response.json())
        .then((data) => {
            document.getElementById('app').innerHTML += modalData(data)
            let modalEl = document.getElementById('userEditModal')

            const myModal = bootstrap.Modal.getOrCreateInstance(modalEl)
            myModal.show()

            modalEl.addEventListener('hidden.bs.modal', function (event) {
                clearEditUserId()
                modalEl.remove()
            })
        })
}


const generatePassword = (inputId, passwordLength = 15) => {
    const chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()-_ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    let password = "";

    for (let i = 0; i <= passwordLength; i++) {
        let randomNumber = Math.floor(Math.random() * chars.length);
        password += chars.substring(randomNumber, randomNumber + 1);
    }

    document.getElementById(inputId).value = password;

    navigator.clipboard.writeText(password);
}