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
            const modalDataCtx = modalData(data)

            const editModalContainer     = document.createElement('div');
            editModalContainer.id        = "editModelContainer";
            editModalContainer.innerHTML = modalDataCtx;

            const appElement = document.getElementById("App");
            if (!appElement) return;

            appElement.insertAdjacentElement("beforeend", editModalContainer);

            const modalContainer = document.getElementById("userEditModal"),
                  modalElement   = bootstrap.Modal.getOrCreateInstance(modalContainer)

            modalElement.show()

            modalContainer.addEventListener('hidden.bs.modal', () => {
                clearEditUserId()
                editModalContainer.remove()
            });
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