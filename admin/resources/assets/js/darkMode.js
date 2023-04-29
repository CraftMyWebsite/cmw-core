
const THEME_KEY = "cmwDarkMode"
const THEME_REGEX = /\btheme-[a-z0-9]+\b/g
const toggler = document.getElementById("toggle-dark")

function toggleDarkTheme() {
    setTheme(
        document.body.classList.contains("Theme-dark")
            ? "Theme-light"
            : "Theme-dark"
    )
}

/**
 * @param {"Theme-dark"|"Theme-light"} theme
 * @param {boolean} dontPersist
 */
function setTheme(theme, dontPersist = false) {
    document.body.className = document.body.className.replace(THEME_REGEX, "")
    document.body.classList.add(theme)
    if (toggler) toggler.checked = theme === "Theme-dark"

    if (!dontPersist) {
        localStorage.setItem(THEME_KEY, theme)
    }
}

if (toggler) {
    toggler.addEventListener("input", (e) => {
        setTheme(e.target.checked ? "Theme-dark" : "Theme-light")
    })
}

/**
 * Init Theme from setTheme()
 */
function initTheme() {
    //If the user manually set a Theme, we'll load that
    const storedTheme = localStorage.getItem(THEME_KEY)
    if (storedTheme) {
        return setTheme(storedTheme)
    }

    //Detect if the user set his preferred color scheme to dark
    if (!window.matchMedia) {
        return
    }

    //Media query to detect dark preference
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)")

    //Register change listener
    mediaQuery.addEventListener("change", (e) =>
        setTheme(e.matches ? "Theme-dark" : "Theme-light", true)
    )

    return setTheme(mediaQuery.matches ? "Theme-dark" : "Theme-light", true)
}

if (document.readyState === 'loading') {
    // Document not yet loaded, so wait for it.
    window.addEventListener('DOMContentLoaded', initTheme);
} else {
    // Document is ready (interactive or complete), so call init immediately.
    initTheme();
}