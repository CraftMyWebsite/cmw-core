document.addEventListener("DOMContentLoaded", function () {
    let theme = localStorage.getItem("color-theme");
    if (theme === "dark") {
        theme = "theme-dark";
    } else {
        theme = "theme-light";
    }

    function initTinyMCE(skin) {
        document.querySelectorAll('.tinymce').forEach(function(textarea) {
            const minHeight = textarea.getAttribute('data-tiny-height') || 350;

            tinymce.init({
                selector: `.tinymce`,
                skin: skin,
                content_css: skin,
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
                    'bullist numlist | ' +
                    'table | ' +
                    'link media image insertdatetime |' +
                    'emoticons charmap |' +
                    'preview fullscreen help',
                menubar: false,
                min_height: parseInt(minHeight, 10),
                images_file_types: 'jpg,svg,webp',
                file_picker_types: 'file image media',
                statusbar: false,
                relative_urls: false,
            });
        });
    }

    initTinyMCE(theme);

    document.getElementById('theme-toggle').addEventListener('click', function () {
        let newTheme = localStorage.getItem("color-theme");
        if (newTheme === "dark") {
            newTheme = "theme-dark";
        } else {
            newTheme = "theme-light";
        }
        tinymce.remove('.tinymce');
        initTinyMCE(newTheme);
    });
});
