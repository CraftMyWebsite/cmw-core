const changeLang = async (lang) => {
    await fetch(`installer/lang/${lang}`)
    location.reload()
}