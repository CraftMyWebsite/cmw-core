const passwordInput = (...inputId) => {
    for (const IdName of inputId) {

        const inputElement  = document.querySelector(`#${IdName} input`),
              buttonElement = document.querySelector(`#${IdName} a`),
              iElement      = document.querySelector(`#${IdName} i`);

        if (inputElement === null || iElement === null || buttonElement === null) return;

        buttonElement.onclick = (event) => {

            event.preventDefault();
            if (inputElement.getAttribute("type") === "text") {
                inputElement.setAttribute("type", "password");
                inputElement.classList.add("fa-eye-slash");
                inputElement.classList.remove("fa-eye-slash");
                return;
            }

            inputElement.setAttribute("type", "text");
            inputElement.classList.add("fa-eye-slash");
            inputElement.classList.remove("fa-eye-slash");
        }

    }
}

passwordInput("showHidePassword", "showHidePasswordR");
