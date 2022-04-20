//Show / Hide input type password
$(document).ready(function() {
    $("#showHidePassword a").on('click', function(event) {
        event.preventDefault();
        if($('#showHidePassword input').attr("type") == "text"){
            $('#showHidePassword input').attr('type', 'password');
            $('#showHidePassword i').addClass( "fa-eye-slash" );
            $('#showHidePassword i').removeClass( "fa-eye" );
        }else if($('#showHidePassword input').attr("type") == "password"){
            $('#showHidePassword input').attr('type', 'text');
            $('#showHidePassword i').removeClass( "fa-eye-slash" );
            $('#showHidePassword i').addClass( "fa-eye" );
        }
    });
});

//If we have 2 password, this is for the second (verify)
$(document).ready(function() {
    $("#showHidePasswordR a").on('click', function(event) {
        event.preventDefault();
        if($('#showHidePasswordR input').attr("type") == "text"){
            $('#showHidePasswordR input').attr('type', 'password');
            $('#showHidePasswordR i').addClass( "fa-eye-slash" );
            $('#showHidePasswordR i').removeClass( "fa-eye" );
        }else if($('#showHidePasswordR input').attr("type") == "password"){
            $('#showHidePasswordR input').attr('type', 'text');
            $('#showHidePasswordR i').removeClass( "fa-eye-slash" );
            $('#showHidePasswordR i').addClass( "fa-eye" );
        }
    });
});