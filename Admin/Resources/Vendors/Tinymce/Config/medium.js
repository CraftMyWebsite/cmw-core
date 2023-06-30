tinymce.init({
  selector: '.tinymce',
  skin: 'theme-dark',
  content_css: 'theme-dark',
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
    'bullist numlist | '+
    'table | '+
    'link media image insertdatetime |' +
    'emoticons charmap |' +
    'preview fullscreen help',
  menubar:false,
  images_file_types: 'jpg,svg,webp',
  file_picker_types: 'file image media',
  statusbar: false,
});