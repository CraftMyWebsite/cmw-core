/* DELETE ROLE */
const deleteRole = (roleId) => {
    const deleteModalDOM = modalDeleteRole(roleId);

    const deleteModalContainer     = document.createElement('div');
    deleteModalContainer.id        = "deleteModalContainer";
    deleteModalContainer.innerHTML = deleteModalDOM;

    const appElement = document.getElementById("app");
    if (!appElement) return;

    appElement.insertAdjacentElement("beforeend", deleteModalContainer);

    const modalContainer = document.getElementById("roleDeleteModal"),
          modalElement   = bootstrap.Modal.getOrCreateInstance(modalContainer)

    modalElement.show()

    modalContainer.addEventListener('hidden.bs.modal', () => deleteModalContainer.remove());
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