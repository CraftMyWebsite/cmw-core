$(document).ready(function () {
    $('#summernote').summernote({
        height: 200,
        width: 200000,
        codeviewIframeFilter: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'strikethrough', 'clear']],
            ['font', ['superscript', 'subscript']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['division', ['hr']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['misc', ['undo', 'redo']]
        ],
    });
});