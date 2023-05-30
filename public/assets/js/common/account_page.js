var url = $('#siteurl').val();
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var code = $('#phone_code').val();
    
    //country code seetings
    var input = document.querySelector("#phone");
        window.intlTelInput(input, { // init plugin
            geoIpLookup: function(success, failure) {
                $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    success(countryCode);
                });
            },
            // utilsScript: url + '/resources/js/country_code/js/utils.js' // just for formatting/placeholders etc
        });
        initial_country = code ? $('li.iti__country[data-dial-code="' + code + '"]').attr('data-country-code') : 'In';

        var iti = window.intlTelInput(input, {
            initialCountry: initial_country,
            preferredCountries: ['In']

        });
    $('#phone').val($('#phone').val());
    ($('.show.dial-code').length > 0) ? $('.show.dial-code').html('+' +  $('#phone_code').val()): $("<div class='show dial-code'>+" +  $('#phone_code').val() + '</div>').insertAfter($('.iti__selected-flag div.iti__flag'));
    
    input.addEventListener('countrychange', function(e, countryData) {
        ($('.show.dial-code').length > 0) ? $('.show.dial-code').html('+' + iti.getSelectedCountryData().dialCode): $("<div class='show dial-code'>+" + iti.getSelectedCountryData().dialCode + '</div>').insertAfter($('.iti__selected-flag div.iti__flag'));
        $('#phone_code').attr('value', iti.getSelectedCountryData().dialCode);
    });

});

$('#profile-update').validate({
    errorPlacement: function(error, element) {
        if (element.attr("name") == "phone") {
            error.insertAfter("#phone-group");
            $('#phone-group').addClass('error');
        } else {
            error.insertAfter(element);
        }

    },
    rules: {
        user_name: {
            required: true,
            name_validation :true,
            minlength: 3
        },
        pincode: {
            required: true,
            number: true
        },
        phone: {
            required: true,
            number: true,
            minlength: 10,
            maxlength: 13
        },
        address: { required: true },
    },
    success: function(label, element) {
        label.parents('div.error').removeClass('error');
        label.siblings('div.error').removeClass('error');
        label.remove();
    },
    ignore: ' ',
    submitHandler: function(form) {

        var form_data = new FormData($('#profile-update')[0]),
            ids = '';

        $.ajax({
            type: "POST",
            url: url + "/user/update-details-user/1",
            dataType: 'JSON',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#profile-update button[type="submit"]').attr('disabled', true);
                $('#user_loader').show();
                $('.common-error').remove();
                
                $('div.error').removeClass('error');                
            },
            statusCode: {
                400: function(data) {
                    $('<div class="alert alert-success">' + data.responseJSON.msg + '</div>').insertBefore('#profile-update');
                    $('#register').attr("disabled", true);
                    enableField();
                },
                409: function(errors) {
                    if ('data' in errors.responseJSON) {
                        $('<div class="common-error error">' + errors.responseJSON.msg + '</div>').insertAfter($('#' + errors.responseJSON.data));
                    } else {
                        $.each(errors.responseJSON.error, function(key, value) {
                            ids = '#' + key;
                            if (key == 'phone' || key == 'password') {
                                ids = '#' + key + '-group';
                            }
                            $('<div class="common-error error">' + value + '</div>').insertAfter($('#profile-update ' + ids));
                        });
                    }
                    enableField();
                }
            },
            success: function(result) {
                if (result.status) $('.alert').addClass('alert-success').html(result.msg).show();
                else $('<div class="alert alert-danger">' + result.msg + '</div>').insertBefore('#profile-update');

                // if (result.status) $('<div class="alert alert-success">' + result.msg + '</div>').insertBefore('#profile-update');
                // else $('<div class="alert alert-danger">' + result.msg + '</div>').insertBefore('#profile-update');
                enableField();
            },
        });

    }
});
function enableField() {
    $('#user_loader').hide();
    setTimeout(function(){
        $('.alert').hide();
        $('.common-error').remove();
        $('div.error').removeClass('error');
        $('#profile-update button[type="submit"]').removeAttr('disabled');
    }, 1000);
}

$('#upload-pic').click(function() {
    $('#profile_pic').click();
});


function readURL(input) {
    var ext = $('#profile_pic').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        alert("Only formats are allowed : " + ['gif', 'png', 'jpg', 'jpeg'].join(', '));
    } else {
        var form_data = new FormData();
        var file_data = $('#profile_pic').prop('files')[0];
        form_data.append('file', file_data);
        $.ajax({
            url: url + "/user/update-profile-pic",
            type: 'POST',
            dataType: 'JSON',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#user_loader').show();
            },
            success: function(result) {
                $('#user_loader').hide();
                if (result.status) {
                    $('div.profile-pic img').attr('src', result.image.thumb);
                    $('li.submenu img').attr('src', result.image.thumb);
                } else {
                    $(".alert").removeClass('alert-success').addClass('alert-danger').html(result.message).show();
                }

            }
        });
    }
}

function NumberOnly(event) {
    var key = window.event ? event.keyCode : event.which;

    return ((key > 47 && key < 58))
}
jQuery.validator.addMethod("name_validation", function(value, element) 
{
return this.optional(element) ||  /^([a-zA-Z]{3,25})+( [a-zA-Z]{3,25})*$/i.test(value);
}, "Enter a valid name."); 