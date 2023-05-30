var url = $('#siteurl').val(),
    login_form = false,
    reset_count = 0;
$(document).ready(function () {   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });    
    var msg = $('#msg').val();

    if(performance.navigation.type == 2){
        if(window.location.href.indexOf('/?msg=') !== -1) {
            var site = window.location.origin + window.location.pathname;
            window.location.replace(site);
        }
    } else {
        if (msg == "success") {
            $('[data-target="#login-modal"]')[0].click();
            $('<div class="alert alert-success">Your account is activated successfully. You can login now.</div>').insertBefore('#login-form').delay(5000).fadeOut("slow");
            
        }else if(msg == "failed"){
            $('[data-target="#otp-modal"]').click();
            $('<div class="alert alert-danger">Something went wrong. Please try again later.</div>').insertBefore('#login-form').delay(5000).fadeOut("slow");
        }
    }    

    var token = $('input[name="security_code"]').val();
    /*reset password modal*/
    if(token) $('#new-pass-modal').show();

    $('.login_mail').keyup(function(e){
        if($(this).val() == "") $('#login_name_error').show();
        else $('#login_name_error').hide();
    });
    $('.cart_file_input').change(function(e){
        $file_name = $(this).val();
        $('.file-upload p').html($file_name);
    })
    /* menu toggle */
    $('.menu-toggle').click(function(e){
        var i;
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        if($('.sidebar-nav li').eq(1).hasClass('fadeInUp-1')){
            for(i=0;i<6;i++){
                 $('.sidebar-nav li').eq(i).removeClass('fadeInUp-'+i);
            }
        } else{
             for(i=0;i<6;i++){
                $('.sidebar-nav li').eq(i).addClass('fadeInUp-'+i);
            }
        }
    });

    $('#new-pass-modal .close').click(function(){
        $('#new-pass-modal').hide();
    });
    $('input').on('focus', function(){
        $('.common-error.error').remove();
        $('button[type="submit"], button[disabled="disabled"]').removeAttr('disabled');
        $('input[type="number"]').removeClass('error');
    });
    $('select').on('change', function () {
        $(this).parents('div.error').find('label.error').remove();
        $(this).parents('div.error').removeClass('error');
        
    });

    $("#user_type").change(function () {
		var user_type = $("#user_type").val();
		if (user_type == 3) {
			$("#user_type_error").css({"display": "block", "color": "green"});
			$("#user_type_error").html('You chosen as CUSTOMER type');
		}
		else {
			$("#user_type_error").css({"display": "block", "color": "green"});
			$("#user_type_error").html('You chosen as MEDICAL PRACTITIONER type');
		}
	});    

    

    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");
    
        dropZoneElement.addEventListener("click", (e) => {
        inputElement.click();
        });
    
        inputElement.addEventListener("change", (e) => {
        if (inputElement.files.length) {
            updateThumbnail(dropZoneElement, inputElement.files[0]);
        }
        });
    
        dropZoneElement.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZoneElement.classList.add("drop-zone--over");
        });
    
        ["dragleave", "dragend"].forEach((type) => {
        dropZoneElement.addEventListener(type, (e) => {
            dropZoneElement.classList.remove("drop-zone--over");
        });
        });
    
        dropZoneElement.addEventListener("drop", (e) => {
        e.preventDefault();
    
        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
        }
    
        dropZoneElement.classList.remove("drop-zone--over");
        });
    });
    
    /**
     * Updates the thumbnail on a drop zone element.
     *
     * @param {HTMLElement} dropZoneElement
     * @param {File} file
     */
    function updateThumbnail(dropZoneElement, file) {
        let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");
        // First time - remove the prompt
        if (dropZoneElement.querySelector(".drop-zone__prompt")) {
        dropZoneElement.querySelector(".drop-zone__prompt").remove();
        }
    
        // First time - there is no thumbnail element, so lets create it
        if (!thumbnailElement) {
        thumbnailElement = document.createElement("div");
        thumbnailElement.classList.add("drop-zone__thumb");
        dropZoneElement.appendChild(thumbnailElement);
        }
    
        thumbnailElement.dataset.label = file.name;
    
        // Show thumbnail for image files
        if (file.type.startsWith("image/")) {
            const reader = new FileReader();
        
            reader.readAsDataURL(file);
            reader.onload = () => {
                thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
                uploadPrescription(reader.result);
            };
        } else {
            thumbnailElement.style.backgroundImage = null;
        }
    }

    if ($('#phone').length) {
        //country code seetings
        var input = document.querySelector("#phone"),
            iti = window.intlTelInput(input, { // init plugin
                initialCountry: "In",
                preferredCountries: ['in'],
                geoIpLookup: function(success, failure) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        success(countryCode);
                    });
                },
                utilsScript: url + '/resources/js/country_code/js/utils.js' // just for formatting/placeholders etc
            });
        // iti.setCountry("in");
        $('#phone_code').attr('value','91');
        $("<div class='show dial-code'>+91</div>").insertAfter($('.iti__selected-flag div.iti__flag'));
        // listen to the telephone input for changes
        input.addEventListener('countrychange', function(e) {
            ($('.show.dial-code').length > 0) ? $('.show.dial-code').html('+' + iti.getSelectedCountryData().dialCode) : $("<div class='show dial-code'>+"+iti.getSelectedCountryData().dialCode + '</div>').insertAfter($('.iti__selected-flag div.iti__flag'));
            $('#phone_code').attr('value', iti.getSelectedCountryData().dialCode);
        });
    }

    //index page
    var item_code = '';

    $("#inlineFormInputGroupUsername2").autocomplete({
            create: function (e) {
                $(this).closest('.ui-helper-hidden-accessible').remove();
            },
            search: function(event, ui) {
                $('.med_search_loader').css('display','block' );
            },
            open: function(event, ui) {
                $('.med_search_loader').css('display','none' );
            },
            source:function(request, response)
                {
                    if(request.term !== '') {
                        $.ajax(
                        {
                            url: url + '/medicine/load-medicine-web/1',
                            dataType: 'json',
                            data: { term: request.term },
                            beforeSend: function() {
                                $('span[role="status"]').html('');
                                $('input.error, span.error').removeClass('error');                                
                            },
                            success: function(data, textStatus, jqXHR)
                            {
                                response(data);
                            },
                            statusCode:
                            {
                                409: function ()
                                {
                                    $('form.form-inline button[type="button"]').attr('disable',true);
                                    $('.ui-helper-hidden-accessible').html('');
                                }
                            }
                        });
                    } else {
                        $('form.form-inline button[type="button"]').attr('disable',true);
                        $('.ui-helper-hidden-accessible').html('');
                        $('ul.ui-autocomplete').attr('style',"display: none;");
                    }
                },
            // source: url + '/medicine/load-medicine-web/1',
            minLength: 0,
            delay: 0 ,
            select: function (event, ui) {
                item_code = ui.item.item_code;
                current_item_code=ui.item.item_code;
                $('#inlineFormInputGroupUsername2').attr('data-item' , ui.item.item_code);
                $('.ui-helper-hidden-accessible').html('');
            },
            response: function( event, ui ) {
                $('.med_search_loader').css('display','none' );
            }
        });     
    
});
$(document).on('hidden.bs.modal', function () {
    if($('.modal.show').length > 0) $('body').addClass('modal-open');
});

$('.upload-prescription-btn').click(function() {
    check_session();
});
$('#login-form #password').keypress(function(e) {
    
    if(e.which == 13) {
        e.preventDefault();
        $('#login-form button[type="submit"]').trigger('click');

    }
});
$('#login-form #email').keypress(function(e){
    if(e.which == 13) {
        e.preventDefault();
        $('#login-form #password').focus();

    }
})

$('#user-register').validate({
    errorPlacement: function(error, element) {
        if (element.attr("name") == "phone") {
            error.insertAfter("#phone-group");
            $('#phone-group').addClass('error');
        } else if (element.attr("name") == "accept_terms") error.insertAfter('#accept_term');
        else if (element.attr("name") == "user_type") error.appendTo($(element).parents('div.form-group').addClass('error'));
        else {
            error.insertAfter(element);
            if (element.attr("name") == "password") $('#pswrd-grps').addClass('error');
        }
            
    },
    rules: {
        user_name: {
            required: true,
            name_validation :true,
            minlength: 3
        },
        phone: {
            required: true,
            number: true,
            minlength: 6,
            maxlength: 13
        },
        password: { required: true, minlength: 8},
        user_type: { required: true },
        email: { required: true, email: true},
        address: { required: true},
        accept_terms: { required: true}
    },
    success: function(label,element) {
        label.parents('div.error').removeClass('error');
        label.siblings('div.error').removeClass('error');
        label.remove();
    },
    ignore: ' ',
    submitHandler: function(form) {

        var form_data = new FormData($('#user-register')[0]),
            ids = '';

        $.ajax({
            type: "POST",
            url: url + "/user/create-user/1",
            dataType: 'JSON',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#user_loader').show();
                $('#signup-modal div.alert, #signup-modal .common-error, #signup-modal span.error').remove();
                $('div.error').removeClass('error');
                $('#user-register button[type="submit"], button[data-dismiss="modal"]').attr('disabled', true);
                $('body').addClass('modal-open on-process');
            },
            statusCode:{
                400:function(data){
                    $('#user_loader').hide();
                    $('<div class="alert alert-danger">' + data.responseJSON.msg + '</div>').insertBefore('#user-register');
                    $('#register').attr("disabled", true);
                },
                409:function(errors){
                    $('#user_loader').hide();
                    if ('data' in errors.responseJSON){
                        $('<div class="common-error error">' + errors.responseJSON.msg + '</div>').insertAfter($('#' + errors.responseJSON.data));
                    } else {
                        $.each(errors.responseJSON.error, function(key, value) {
                            ids = '#' + key;
                            if (key == 'phone' || key == 'password') {
                                ids = '#'+ key + '-group';
                            }
                            $('<div class="common-error error">' + value + '</div>').insertAfter($('#user-register ' + ids));
                        });
                    }
                }
            },
            success: function(result) {
                $('#user_loader').hide();
                $('body').removeClass('on-process');
                $('#user-register button[type="submit"], button[data-dismiss="modal"]').removeAttr('disabled');
                if (result.status == 'SUCCESS') {
                    
                    $('<div class="alert alert-success">' + result.msg + '</div>').insertBefore('#user-register');
                    setTimeout(function(){
                        $('#hidden_user_id').attr('value',$('#user-register #email').val());
                        $('#user-register')[0].reset();
                        $('#user_type').prop('selectedIndex', 0).selectric('refresh');
                        $('#signup-modal .alert').remove();
                        $('#signup-modal button.close').trigger('click');
                        $('#toggle-modal').trigger('click');
                    }, 1000);
                } else  $('<div class="alert alert-danger">' + result.msg + '</div>').insertBefore('#user-register');
            }
        });
    }
});

$('#forgot-password').validate({
    rules: {
        email: { required: true, email: true},
    },
    ignore: ' ',
    submitHandler: function(form) {
        var form_data = new FormData($('#forgot-password')[0]);
        $.ajax({
            type: "POST",
            url: url + "/user/reset-password",
            dataType: 'JSON',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#user_loader').show();
                $('.common-error, .common-success, #forgot-password-modal div.alert').remove();
                $('div.error').removeClass('error');
                $('#forgot-password button[type="submit"], button[data-dismiss="modal"]').attr('disabled', true);
                $('body').addClass('modal-open on-process');
            },
            statusCode:{
                404:function(error){   
                    $('#user_loader').hide();                 
                    $('<span class="common-error error">' + error.responseJSON.msg + '</span>').insertAfter('#forgot-password #email');
                },
                400:function(error){
                    $('#user_loader').hide();                    
                    $('<span class="common-error error">' + error.responseJSON.msg + '</span>').insertAfter('#forgot-password #email');
                },
                409: function (error) {
                    $('#user_loader').hide();
                    if ('data' in errors.responseJSON){
                        $('<span class="common-error error">' + errors.responseJSON.msg + '</span>').insertAfter($('#' + errors.responseJSON.data));
                    } else {
                        $.each(errors.responseJSON.error, function(key, value) {
                            ids = '#' + key;
                            if (key == 'phone' || key == 'password') {
                                ids = '#'+ key + '-group';
                            }
                            $('<span class="common-error error">' + value + '</span>').insertAfter($('#user-register ' + ids));
                        });
                    }
                },
            },
            success: function(result) {
                $('#user_loader').hide();
                $('body').removeClass('on-process');
                $('#forgot-password button[type="submit"], button[data-dismiss="modal"]').removeAttr('disabled');
                
                if (result.status) {
                    $('<div class="alert alert-success">' + result.msg + '</div>').insertAfter('#forgot-password');
                    setTimeout(function(){                        
                        $('#forgot-password')[0].reset();
                    }, 1000);
                } else $('<div class="alert-error alert">' + result.msg + '</div>').insertAfter('#forgot-password');

                setTimeout(function(){
                    $('#forgot-password-modal div.alert').remove();
                }, 1000);
            }
        });

    }
});

$('#login-form').validate({
    errorPlacement: function(error, element) {
        error.insertAfter(element);
        if (element.attr("name") == "password") $('#pswrd-grp').addClass('error');
    },
    rules: {
        email: { required: true, email: true},
        password: { required: true}
    },
    success: function(label,element) {
        label.parents('div.error').removeClass('error');
        label.siblings('div.error').removeClass('error');
        label.remove();
        $('#msg').val('');
    },
    ignore: ' ',
    submitHandler: function(form) {
        if(!login_form) {
            $('#login_form button[type="submit"]').attr('disabled', true);
            login_form = $.ajax({
                type: "POST",
                url: url + "/user/user-login/1",
                data: $('#login-form').serialize(),
                datatype: 'json',
                statusCode:{
                    403:function(data){
                        $('#user_loader').hide();
                        $(".login_msg").addClass('alert-danger').html('Please Login from Admin URL').css({"display":"block"}).delay(5000).fadeOut("slow");
                    }
                },
                beforeSend: function(){
                    $('#user_loader').show();
                    $('#login-modal .alert').hide();
                    $('.common-error.error').remove();
                    $('div.error').removeClass('error');
                    $('#hidden_user_id').attr('value','');
                    $('#login-form button[type="submit"], button[data-dismiss="modal"]').attr('disabled', true);
                    $('body').addClass('modal-open on-process');
                },
                success: function (data) {
                    $('#user_loader').hide();
                    var status=data.result.status;
                    var page=data.result.page;
                    login_form = false;
                    $('body').removeClass('on-process');
                    $('#login-form button[type="submit"], button[data-dismiss="modal"]').removeAttr('disabled');
                    if(status=='pending')
                    {
                        $('#login-modal button.close').trigger('click');
                        $('#hidden_user_id').attr('value',$('#login-form #email').val());
                        $('<input type="hidden" data-login="pending"/>').insertBefore('#otp-verification button.resend');
                        $('#toggle-modal').trigger('click');

                    }
                    if(status=='failure') $(".login_msg").addClass('alert-danger').html('Invalid username or password').css({"display":"block"}).delay(5000).fadeOut("slow");
                    if(status !== 'pending' || status !== 'failure')  {
                        setTimeout(function(){                        
                            $('#forgot-password')[0].reset();
                        }, 1000);
                    }
                    
                    if(status=='success')
                    {
                        if(page=='no') location.href= url + "/account-page";
                        else window.location.replace( url + '/medicine/add-cart/1');
                    }

                    if(status == 'delete'){
                        $(".login_msg").addClass('alert-danger').html('You have been deleted by admin ! Contact support team.').css({"display":"block"}).delay(5000).fadeOut("slow");
                    }
                    
                }
            });
        }
    }
});

$('#otp-verification').validate({
    rules: {
        login_otp_1: {
            required: true,
        },
        login_otp_2: {
            required: true,
        },
        login_otp_3: {
            required: true,
        },
        login_otp_4  : {
            required: true,
        },
    },
    ignore: ' ',
    submitHandler: function(form) {
        var security_code = $('#otp-verification #login_otp_1').val()+ $('#otp-verification #login_otp_2').val() + $('#otp-verification #login_otp_3').val() + $('#otp-verification #login_otp_4').val();
        $.ajax({
            type: "POST",
            url: url + '/user/activate-account',
            data: 'security_code=' + security_code + '&email=' + $('#hidden_user_id').val(),
            datatype: 'json',
            beforeSend: function(){
                $('#user_loader').show();
                $('#otp-modal div.alert, #otp-modal .common-error.error').remove();
                $('div.error').removeClass('error');
                $('#otp-verification button[type="submit"], button[data-dismiss="modal"], #otp-verification .resend').attr('disabled', true);
                $('body').addClass('modal-open on-process');
            },
            statusCode:{
                409:function(error){
                    $('#user_loader').hide();
                    $('#otp-verification input[type="number"]').addClass('error');
                    $('<span class="common-error error" style="padding-left: 10%;"> ' + error.responseJSON.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
                },
                400 : function (error) {
                    $('#user_loader').hide();
                    $('#otp-verification input[type="number"]').addClass('error');
                    $('<span class="common-error error" style="padding-left: 10%;"> ' + error.responseJSON.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
                },
                500: function (error) {
                    $('#user_loader').hide();
                    $('#otp-verification input[type="number"]').addClass('error');
                    $('<span class="common-error error" style="padding-left: 10%;"> ' + error.responseJSON.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
                },
                404: function (error) {
                    $('#user_loader').hide();
                    $('#otp-verification input[type="number"]').addClass('error');
                    $('<span class="common-error error" style="padding-left: 10%;"> ' + error.responseJSON.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
                }
            },
            success: function (result) {
                $('body').removeClass('on-process');
                $('#user_loader').hide();
                $('button[data-dismiss="modal"]').removeAttr('disabled', true);
                if (result.status) {
                    
                    if ($('input[type="hidden"][data-login="pending"').length > 0) msg = '';
                    else msg = ' You can login now.';

                    $('<div class="alert alert-success">' + result.msg + msg +'</div>').insertAfter('#otp-verification');
                    if(msg !== '') {
                        setTimeout(function(){
                            $('#otp-modal button.close').trigger('click');
                            $('[data-target="#login-modal"]')[0].click();
                        }, 2000);
                    } else {
                        $('#user_loader').show();
                        setTimeout(function(){
                            $('#login-form').submit();
                            location.href= url + "/account-page";
                            $('#user_loader').hide();

                        }, 3000);
                    }
                    setTimeout(function(){                        
                        $('#otp-verification')[0].reset();
                        $('#otp-modal .alert').remove();
                    }, 3000);
                } else {
                    $('#otp-verification button[type="submit"], #otp-verification .resend').removeAttr('disabled', true);
                    $('#otp-verification input[type="number"]').addClass('error');
                    $('<span class="common-error error" style="padding-left: 10%;"> ' + result.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
                }
            }
        });
    }
});

$('#otp-verification .resend').click(function(e){
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: url + '/user/resend-activation-code/1',
        data: 'email=' + $('#hidden_user_id').val(),
        datatype: 'json',
        beforeSend: function(){
            $('#user_loader').show();
            $('#otp-modal .common-error.error, #otp-modal .common-success, #otp-modal .alert').remove();
            $('div.error').removeClass('error');
            $('body').addClass('modal-open on-process');
            $('#otp-verification .resend, #otp-verification button[type="submit"], button[data-dismiss="modal"]').attr('disabled', true);
        },
        statusCode:{
            409:function(error){
                $('#user_loader').hide();
                $('<span class="common-error error"> ' + error.responseJSON.msg + '</span>').insertAfter($('#otp-verification div.otp-field'));
            },
            400: function (error) {
                $('#user_loader').hide();
                $('<div class="alert alert-danger">' + error.responseJSON.msg + '</div>').insertAfter('#otp-verification');
            },
            500: function (error) {
                $('#user_loader').hide();
                $('<div class="alert alert-danger">' + error.responseJSON.msg + '</div>').insertAfter('#otp-verification');
            }
        },
        success: function (result) {
            $('#user_loader').hide();
            $('body').removeClass('on-process');
            $('#otp-verification button[type="submit"], #otp-verification .resend, button[data-dismiss="modal"]').removeAttr('disabled');
            if (result.status == 'SUCCESS') $('<div class="alert alert-success">' + result.msg + '</div>').insertAfter('#otp-verification');
            else $('<div class="alert alert-danger">' + result.msg + '</div>').insertAfter('#otp-verification');
        }
    });
});

$('#new-password').validate({
    rules: {
        new_password : {
            minlength : 8
        },
        confirm_password : {
            minlength : 8,
            equalTo : "#new_password"
        }
    },
    ignore: ' ',
    submitHandler: function(form) {

        $.ajax({
            type:"POST",
            url: url + '/user/reset-password',
            data: $('#new-password').serialize(),
            dataType:'JSON',
            beforeSend: function(){
                $('#user_loader').show();
                $('#new-pass-modal .alert, .common-error.error').remove();
                $('div.error').removeClass('error');
                $('#new-password button[type="submit"], button[data-dismiss="modal"]').attr('disabled', true);
                $('body').addClass('modal-open on-process');
            },
            statusCode:{
                404:function(error){
                    $('#user_loader').hide();
                    $('<span class="common-error error"> ' +  + '</span>').insertAfter($('#new-password'));
                },
                409:function(errors){
                    $('#user_loader').hide();
                    if ('data' in errors.responseJSON){
                        $('<div class="common-error error">' + errors.responseJSON.msg + '</div>').insertAfter($('#' + errors.responseJSON.data));
                    } else {
                        $.each(errors.responseJSON.error, function(key, value) {
                            ids = '#' + key;
                            $('<div class="common-error error">' + value + '</div>').insertAfter($('#new-password ' + ids));
                        });
                    }
                }
            },
            success:function(result){
                $('#user_loader').hide();
                $('body').removeClass('on-process');
                $('button[data-dismiss="modal"]').removeAttr('disabled', true);
                if (result.status) {
                    $('<div class="alert alert-success">' + result.msg + '. You can Login with new password.</div>').insertBefore($('#new-password'));
                    setTimeout(function(){
                        $('#new-pass-modal button.close').trigger('click');
                        $('[data-target="#login-modal"]')[0].click();
                        $('#new-password')[0].reset();
                        $('#new-pass-modal .alert').remove();
                    }, 4000);
                } else {
                    $('#new-password button[type="submit"]').attr('disabled', true);
                    $('<div class="alert alert-error">' + result.msg + '</div>').insertBefore($('#new-password'));
                }
            }

        });    
    }
})

function check_session() {
    event.preventDefault();
    $.ajax({
        url: url + '/user/check-session/1',
        type: 'GET',
        datatype: 'JSON',
        statusCode: {
                404: function(error) {
                    $('.login_msg').addClass('alert-warning').html('You need to login for uploading prescription. Please Login.').show();
                    $('#loginModal').click();
                },
                401: function(error) {
                    $('.login_msg').addClass('alert-warning').html('You need to login for uploading prescription. Please Login.').show();
                    $('#loginModal').click();
                }
        },
        success: function (data) {
            if(data==1) $('#upload-prescription-btn').click();
            else $('[data-target="#login-modal"]')[0].click();
        }
    });
}
/* OTP field focusing code - start */
function getCodeBoxElement(index) {    
    return document.getElementById('login_otp_' + index);
}
function onKeyUpEvent(index, event) {
    if($('#login_otp_' + index).val().length > 1 ) {
        $('#login_otp_' + index).val($('#login_otp_' + index).val().substr( 0, 1 ));
    }
    const eventCode = event.which || event.keyCode;
    
    if (getCodeBoxElement(index).value.length === 1) {
      if (index !== 4) {
        getCodeBoxElement(index+ 1).focus();
      } else {
        getCodeBoxElement(index).blur();
        // Submit code
        $("#otp-verification button[type='submit']").focus();
      }
    }
    if (eventCode === 37 && index !== 1) {
      getCodeBoxElement(index - 1).focus();
    }
}
function onFocusEvent(index) {
    
    for (item = 1; item < index; item++) {
      const currentElement = getCodeBoxElement(item);
      if (!currentElement.value) {
          currentElement.focus();
          break;
      }
    }
}
/* OTP field focusing code - end */

function CheckUsername(u_name)
{
    $.ajax({
        type: "GET",
        url: url + '/user/check-user-name',
        data: "u_name="+u_name,
        datatype: 'json',
        beforeSend: function() {
            $(".user-name.error").remove();
        },
        statusCode:{
            409:function(error){
                $('<span class="user-name error">' + error.responseJSON.msg + '</span>').insertAfter('#user-register #email');
                $('#register').attr("disabled", true);
            }
        },
        success: function (data) {
            if(data.status){
                $(".user-name.error").remove();
                $('#register').attr("disabled", false);
            } else {
                $('<span class="user-name error">' + data.msg + '</span>').insertAfter('#user-register #email');
                $('#register').attr("disabled", true);
            }
        }
    });
}

function NumberOnly(event){
    var key = window.event ? event.keyCode : event.which;

    return ((key > 47 && key < 58))
}

function goto_detail_page() {
    var name=$("#inlineFormInputGroupUsername2").val(),
        current_item_code = $('#inlineFormInputGroupUsername2').attr('data-item');
        $('input.error, span.error').removeClass('error');
        if($("#inlineFormInputGroupUsername2").val()){            
            if(!current_item_code) {
                $.ajax({
                    type: "POST",
                    dataType: 'JSON',
                    url: url + '/medicine/request-medicine',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: name
                    },
                    beforeSend: function() {
                        $('span[role="status"]').removeClass('common-success').removeClass('common-error').html('');
                    },
                    success: function (data) {
                        if(data.status) $('span[role="status"]').addClass('common-success').html(data.msg);
                        else $('span[role="status"]').addClass('common-error').html(data.msg);
                        setTimeout(function(){ $('span[role="status"]').removeClass('common-success').removeClass('common-error').html(''); }, 3000);
                    }
                });
            }
            else window.location="medicine-detail/"+current_item_code;
        } else {
            $('#inlineFormInputGroupUsername2').addClass('error');
            $('span[role="status"]').addClass('error').html('This field is required.');
        }

}

function uploadPrescription(img) {
    var form_data = new FormData();
        if(img) {
            img = b64toBlob(img);
            form_data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            form_data.append('files', img)
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url + "/upload-prescription/1",
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(this).attr('disabled', true);
                    $('.loader-overlay.typ2').show();
                    $('span.error').remove();
                    $('span.success').remove();
                },
                success: function (result) {
                    $('.loader-overlay.typ2').hide();
                    $(this).attr('disabled', false);
                    if(result.status) {       
                        $("#triggerMe").trigger("click");
                        ($('.upload-item-outer').length === 1) ? $('div.upload-file-sec h5').addClass('d-none') : '';
                        // getData();
                    } else $('<span class="common-error error"> ' + result.msg + '</span>').insertAfter('.modal-body p');
                }
            });
        }
}

function b64toBlob(dataImage, sliceSize) {
	var block = dataImage.split(";");
	// Get the content type of the image
	var contentType = block[0].split(":")[1]; // In this case "image/gif"
	// get the real base64 content of the file
	var b64Data = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."
	contentType = contentType || '';
	sliceSize = sliceSize || 512;
	var byteCharacters = atob(b64Data);
	var byteArrays = [];
	for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
		var slice = byteCharacters.slice(offset, offset + sliceSize);
		var byteNumbers = new Array(slice.length);
		for (var i = 0; i < slice.length; i++) {
			byteNumbers[i] = slice.charCodeAt(i);
		}
		var byteArray = new Uint8Array(byteNumbers);
		byteArrays.push(byteArray);
	}
	var blob = new Blob(byteArrays, {
		type: contentType
	});
	return blob;
}

jQuery.validator.addMethod("name_validation", function(value, element) 
{
return this.optional(element) ||  /^([a-zA-Z]{3,25})+( [a-zA-Z]{3,25})*$/i.test(value);
}, "Enter a valid name.");
