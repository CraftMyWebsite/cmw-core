/* DELETE ROLE */

const deleteRole = (roleId) => {

    document.getElementById('app').innerHTML += modalDeleteRole(roleId)
    let modalEl = document.getElementById('roleEditModal')

    const myModal = bootstrap.Modal.getOrCreateInstance(modalEl)
    myModal.show()

    modalEl.addEventListener('hidden.bs.modal', function (event) {
        modalEl.remove()
    })
}

const storeRoleId = (roleId) => {
    document.cookie = 'editRoleId=' + roleId
}



/* EDIT ROLE */
const fillEditModal = (roleId) => {

    fetch(`getRole/${roleId}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            document.getElementById('app').innerHTML += modalEditData(data)
            let modalEl = document.getElementById('roleEditModal')

            const myModal = bootstrap.Modal.getOrCreateInstance(modalEl)
            myModal.show()

            modalEl.addEventListener('hidden.bs.modal', function (event) {
                modalEl.remove()
            })
        })


}