document.addEventListener("DOMContentLoaded", function () {
    // Recherchez la classe "theme-dark" ou "dark" sur la balise <body> ou un autre élément pertinent.
    const bodyElement = document.body;
    const hasDarkTheme = bodyElement.classList.contains("theme-dark") || bodyElement.classList.contains("dark");

    // Définissez le skin en fonction de hasDarkTheme
    const tinymceSkin = hasDarkTheme ? 'theme-dark' : 'theme-light';


    tinymce.init({
  selector: '.tinymce',
  skin: tinymceSkin,
  content_css: tinymceSkin,
  promotion: false,
  toolbar_sticky: true,
  toolbar_mode: 'sliding',
  plugins: ['emoticons', 'image', 'autoresize', 'wordcount', 'advlist', 'lists', 'charmap', 'codesample', 'code', 'directionality', 'fullscreen', 'link', 'insertdatetime', 'media', 'pagebreak', 'nonbreaking', 'preview', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars'],
  toolbar:
    'undo redo | ' +
    'formatpainter casechange blocks fontsizeselect | ' +
    'alignleft aligncenter alignright alignjustify | ' +
    'bold italic strikethrough | ' +
    'forecolor backcolor removeformat |' +
    'bullist numlist outdent indent | '+
    'table | '+
    'visualchars visualblocks ltr rtl | '+
    'searchreplace nonbreaking pagebreak|' +
    'link media image insertdatetime |' +
    'emoticons charmap |' +
    'wordcount codesample code |' +
    'preview fullscreen help',
  menubar:false,
  min_height: 350,
  images_file_types: 'jpg,svg,webp',
  file_picker_types: 'file image media',
  statusbar: false,
  relative_urls : false,
});
});