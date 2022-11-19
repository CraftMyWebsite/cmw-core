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
