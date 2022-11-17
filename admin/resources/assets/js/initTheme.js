const body = document.body;

if (localStorage.getItem('cmwDarkMode') === 'theme-dark') {
    body.classList.add("theme-dark");
} else {
    body.classList.add("theme-light");
}
