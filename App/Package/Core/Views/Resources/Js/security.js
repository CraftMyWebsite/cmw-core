const generateCaptchaInputs = () => {
    let select = document.getElementById("captcha");
    let methodName = select.value

    cleanContentWrapper();

    if (select.value === 'none') {
        return
    }

   const fn = new Function(`return ${methodName}()`);
    fn()
}

generateCaptchaInputs()


//Clear old type
function cleanContentWrapper(parent = null) {

    if (parent === null) {
        parent = document.getElementById("security-content-wrapper");
    }

    parent.innerHTML = "";
}
