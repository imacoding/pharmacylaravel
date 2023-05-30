/**
 * Created by midhun on 6/9/2021.
 */
var url = $('#siteurl').val();
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });    
});

$('#contact_form').validate({
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
    rules: {
        name: {
            required: true
        },
        email: {
            required: true,
            email: true
        },
        msg: {
            required: true
        },
    },
    ignore: ' ',
    submitHandler: function(form) {
        var form_data = new FormData($('#contact_form')[0]);
        $.ajax({
            url: url + "/submit-contact-us",
            data: $('#contact_form').serialize(),
            type: 'POST',
            datatype: 'JSON',
            beforeSend: function (e) {
                //Loader shows
                $('#user_loader').fadeIn();
                $('#contact_form button[type="submit"]').attr('disabled', true);

            },
            success: function (data) {

                if (data.code == 401 || data.code == 400 || data.code == 500) {
                    //Loader hides
                    $('#user_loader').fadeOut();
                    $('#contact_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                    setTimeout(function(){ $('#admin_alert').hide(); }, 3000);
                    return false;
                }
                //Loader hides
                $('#user_loader').fadeOut();
                $('#contact_form').trigger("reset");
                $('#contact_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                setTimeout(function(){
                    $('#contact_alert').hide();
                    $('#contact_form button[type="submit"]').removeAttr('disabled');


                }, 3000);

            }
        })
    }
});