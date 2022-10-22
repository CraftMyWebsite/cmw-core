const generateCaptchaInputs = () => {
  let select = document.getElementById("captcha");

  switch (select.value){
      case 'captcha-none':
          cleanContentWrapper();
          break;
      case 'captcha-hcaptcha':
          cleanContentWrapper();
          generateHcaptchaInputs();
          break;
      case 'captcha-recaptcha':
          cleanContentWrapper();
          generateRecaptchaInputs();
          break;


  }
}

generateCaptchaInputs()


//Clear old type
function cleanContentWrapper(parent = null) {

    if (parent === null) {
        parent = document.getElementById("security-content-wrapper");
    }

    parent.innerHTML = "";
}
