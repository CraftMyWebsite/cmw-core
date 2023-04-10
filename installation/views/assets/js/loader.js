/* document.onreadystatechange = function () {
    if (document.readyState !== "complete") {
        document.querySelector("body").style.visibility = "hidden";
        document.querySelector("#loader").style.visibility = "visible";
    } else {
        document.querySelector("#loader").style.display = "none";
        document.querySelector("body").style.visibility = "visible";
    }
};*/


const launchLoader = () => {
    console.log('click')
    let loader = document.getElementById('loader')
    let body = document.getElementById('body')

    loader.classList.remove('hidden')
    body.classList.add("hidden")
}

const btn = document.getElementById('formBtn')

btn.addEventListener('click', launchLoader)

const customLaunchLoader = () => {
    console.log('click')
    let loader = document.getElementById('loader')
    let body = document.getElementById('body')

    loader.classList.remove('hidden')
    body.classList.add("hidden")
}