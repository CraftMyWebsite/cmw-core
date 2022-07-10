$('#title').on('keyup', function () {
    let val = $(this).val();
    val = val.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    val = val.replace(/[^\w\s]/gi, '');
    val = val.replace(/ /g, "-");
    $('#slug').val(val);
});