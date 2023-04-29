document.addEventListener("DOMContentLoaded", () => {
  console.log("initing", document.body.classList.contains("Theme-dark"))

  const themeOptions = document.body.classList.contains("Theme-dark")
    ? {
        skin: "oxide-dark",
        content_css: "dark",
      }
    : {
        skin: "oxide",
        content_css: "default",
      }

  tinymce.init({ selector: "#Default", ...themeOptions })
  tinymce.init({
    selector: "#dark",
    toolbar:
      "undo redo styleselect bold italic alignleft aligncenter alignright bullist numlist outdent indent code",
    plugins: "code",
    ...themeOptions,
  })
})
