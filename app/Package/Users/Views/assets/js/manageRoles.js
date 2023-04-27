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

            const editModalDOM = modalEditData(roleId);

            const editModalContainer     = document.createElement('div');
            editModalContainer.id        = "editModalContainer";
            editModalContainer.innerHTML = editModalDOM;

            const appElement = document.getElementById("app");
            if (!appElement) return;

            appElement.insertAdjacentElement("beforeend", editModalContainer);

            const modalContainer = document.getElementById("roleEditModal"),
                  modalElement   = bootstrap.Modal.getOrCreateInstance(modalContainer)

            modalElement.show()

            modalContainer.addEventListener('hidden.bs.modal', () => editModalContainer.remove());
        })
}