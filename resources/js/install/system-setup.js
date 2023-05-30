/**
 * Created by midhun on 6/1/2021.
 */
 var url = $('#siteurl').val();
 $(document).ready(function () {
 
     var db = $('#db_name').val();
     if (db) {
         $('.collapse').removeClass('show');
         $('#collapseOne').addClass('show');
     }
 
     var table_exists = $('#table_exists').val();
     if (table_exists ==1) {
         $('.collapse').removeClass('show');
         // $('#collapseTwo').addClass('show');
         $('div.collapse:not(.collapsed):first').addClass('show');
         $('#card1').addClass('completed');
     }
     else
     {
         $('.collapse').removeClass('show');
         $('#collapseOne').addClass('show');
     }
 
     $('#run_migration').click(function (e) {
         e.preventDefault();
 
         $('#run_migration').attr('disabled', true);
 
         if($('#table_exists').val() == 1) {
             $('#db_alert').html("Couldnot process the request. Migrations already exists.").removeClass('alert-success').addClass('alert-danger').show();
             setTimeout(function(){ $('#db_alert').hide(); }, 3000);
         } else {
             //Loader shows
             $('#user_loader').fadeIn();
             $.ajax({
                 url: 'run-migration',
                 type: 'GET',
                 dataType: 'json',
                 statusCode: {
                     500: function (data) {
                         $('#run_migration').removeAttr('disabled');
                         //Loader hides
                         $('#user_loader').fadeOut();
                         $('#db_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                         setTimeout(function(){ $('#db_alert').hide(); }, 3000);
                     }
                 },
                 success: function (data) {
                     $('#user_loader').fadeOut();
                     //Loader hides
                     $('#table_exists').val('1');
                     
                     $('#db_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                     setTimeout(function(){ $('#db_alert').hide(); }, 3000);
                 }
             })
         }
     });
 
 
     $('#run_seeder').click(function (e) {
         e.preventDefault();
 
         $(this).attr('disabled', true);
 
         if($('#table_exists').val() == 1) {
              //Loader shows
              $('#user_loader').fadeIn();
              $.ajax({
                  url: 'run-seeder',
                  type: 'GET',
                  dataType: 'json',
                  statusCode: {
                      500: function (data) {
                         $('#user_loader').fadeOut();
                          $(this).removeAttr('disabled');
                          $('#db_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                          setTimeout(function(){ $('#db_alert').hide(); }, 3000);
                      }
                  },
                  success: function (data) {
                     $('#user_loader').fadeOut();
                     $('#db_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                     setTimeout(function(){
                         $('#db_alert').hide();
  
                         $('.collapse').removeClass('show');
                         $('#headingTwo ul').attr('data-target', '#collapseTwo');
 
                         $('#accordion-database-mode div.card').addClass('completed');
 
                         $('#collapseTwo').addClass('show');
                          
                     }, 3000);
                  }
              });            
         } else {
             $('#run_migration').removeAttr('disabled');
             $(this).removeAttr('disabled');
             $('#db_alert').html('Please run migrations first.').removeClass('alert-success').addClass('alert-danger').show();
             setTimeout(function(){ $('#db_alert').hide(); }, 3000);
         }
     });
 
     $('#update_site').click(function (e) {
         e.preventDefault();
 
         var fd = new FormData();
         var file_data = $('#customFile').prop('files')[0]; // for multiple files
         fd.append("file", file_data);
         var other_data = $('#create-setting-form').serializeArray();
         $.each(other_data, function (key, input) {
             fd.append(input.name, input.value);
         });
         $.ajax({
             url: "add-basic-settings",
             data: fd,
             type: 'POST',
             contentType: false,
             cache: false,
             processData: false,
             beforeSend: function (e) {
                 //Loader shows
                 $('#user_loader').fadeIn();
             },
             success: function (data) {
                 var values = data.data;
                 $('#user_loader').fadeOut();
                 if (data.code == 401 || data.code == 400 || data.code == 500) {
                     //Loader hides                    
                     $('#app_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                     setTimeout(function(){ $('#app_alert').hide(); }, 3000);
                     return false;
                 }
                 $('#mail_id').val(values.email.value);
                 $('#mail_password').val(values.mail_password.value);
                 $('#from_address').val(values.mail_address.value);
                 $('#from_name').val(values.mail_name.value);
                 $('#port').val(values.port.value);
                 $('#host').val(values.host.value);
                 $('#dirver').val(values.driver.value);

                 if(values.logo) {
                    $('#preview-modal img').attr('src' , values.logo).removeClass('d-none');
                    $('#preview-modal p.common-error').addClass('d-none');
                    $('[data-target="#preview-modal"]').removeClass('d-none');
                 } else {
                    $('#preview-modal img').addClass('d-none');
                    $('#preview-modal p.common-error').removeClass('d-none');
                    $('[data-target="#preview-modal"]').addClass('d-none');
                 }
                 //Loader hides
                 $('#app_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                 $('#continue_to_mail').removeClass('d-none');
                 setTimeout(function(){
                     $('#app_alert').hide();
                     $('.collapse').removeClass('show');
 
                     $('#accordion-prescription div.card').addClass('completed');
 
                     $('#headingThree ul').attr('data-target', '#collapseThree');
 
                     $('#collapseThree').addClass('show');
                     
                 }, 3000);
 
 
             }
         })
 
     });
 
     $('#continue_to_mail').click(function (e) {
         e.preventDefault();
         $('.collapse').removeClass('show');
         $('#collapseThree').addClass('show');
     });
 
     $('#update_email').click(function (e) {
         e.preventDefault();
 
 
         var is_empty = false;
         $('#frmMailSettings input').each(function (e) {
             if ($(this).val() == "")
                 is_empty = true;
         });
 
         if (is_empty) {
             alert('Enter all fields as this is mandatory for site to work');
             return false;
         }
         $.ajax({
             url: url + "/add-mail-settings",
             data: $('#frmMailSettings').serialize(),
             type: 'POST',
             datatype: 'JSON',
             beforeSend: function (e) {
                 //Loader shows
                 $('#user_loader').fadeIn();
                 $('[data-target="#test-mail"]').addClass('d-none');
             },
             success: function (data) {
                 var values = data.data;
                 if (data.code == 401 || data.code == 400 || data.code == 500) {
                     //Loader hides
                     $('#user_loader').fadeOut();
                     $('#email_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                     setTimeout(function(){ $('#email_alert').hide(); }, 3000);
                     return false;
                 }
                 //Loader hides
                 $('#user_loader').fadeOut();
                 $('#email_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                 setTimeout(function(){
                     $('#email_alert').hide();
                    //  $('.collapse').removeClass('show');
                     $('#accordion-email-client div.card').addClass('completed');
                     $('#headingFour ul').attr('data-target', '#collapseFour');
                     $('[data-target="#test-mail"]').removeClass('d-none');
                     $('[data-target="#test-mail"]')[0].click();
                    //  $('#collapseFour').addClass('show');
 
                 }, 3000);
 
             }
         });
     });
 
     $('#continue_to_payment').click(function (e) {
         e.preventDefault();
         $('.collapse').removeClass('show');
         $('#collapseFour').addClass('show');
     });
 
     $('.pay_mode').click(function (e) {
         var elementId = $(this).data('id');
         $('.payment_class').hide();
         $('#' + elementId).show();
 
     });
 
 
     $('#payment_update').click(function (e) {
         e.preventDefault();
 
         var transaction = $('#transaction_type').val();
         var is_empty = false,
         field_empty = false;
         $('#payment_update .error').removeClass('error');
         if ($('.pay_mode:checked').val() == '') {
             is_empty = true,
             field_empty = true;
         } else {
             var elementId = $('.pay_mode:checked').val();
             var data = {};
             // Check if all inputs are filled ;
             $("#payment" + elementId + " input,#payment" + elementId + " select").each(function (e) {
                 if($(this).val()) data[$(this).attr('name')] = $(this).val();
                 else {
                     if(!($(this).hasClass('selectric-input'))){
                         field_empty = true;
                         $(this).addClass('error');
                     }
                 }
             });
         }
         if(!is_empty && !field_empty) {
             $.ajax({
                 url: url + "/add-payment-settings",
                 data: {
                     payment: elementId,
                     transaction: transaction,
                     params: data,
                     _token: $("#frmPaymentSettings input[name='_token']").val()
                 },
                 type: 'POST',
                 dataType: 'JSON',
                 beforeSend: function (e) {
                     //Loader shows
                     $('#user_loader').fadeIn();
 
                 },
                 success: function (data) {
                     var values = data.data;
                     if (data.code == 401 || data.code == 400 || data.code == 500) {
                         //Loader hides
                         $('#user_loader').fadeOut();
                         $('#payment_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                         setTimeout(function(){ $('#payment_alert').hide(); }, 3000);
                         return false;
                     }
                     //Loader hides
                     $('#user_loader').fadeOut();
                     $('#payment_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                     setTimeout(function(){
                         $('#payment_alert').hide();
                         $('.collapse').removeClass('show');
 
                         $('#accordion-paymnt-mode div.card').addClass('completed');
 
                         $('#headingFive ul').attr('data-target', '#collapseFive');
 
                         $('#collapseFive').addClass('show');
                     }, 3000);
                 }
             });
         }  else  {
             $('#payment_alert').html('All fields are mandatory.').removeClass('alert-success').addClass('alert-danger').show();
             setTimeout(function(){ $('#payment_alert').hide(); }, 3000);
             return false;
         }
     });
 
     $('#continue_to_admin').click(function (e) {
         e.preventDefault();
         $('.collapse').removeClass('show');
         $('#collapseFive').addClass('show');
     });
 
 
     $('#create_user').click(function (e) {
         e.preventDefault();
         var is_empty = false;
         $('#create-admin-form .required-input').each(function (e) {
             if ($(this).val() == "") {
                 is_empty = true;
             }
         });
 
         if (is_empty) {
             $('#admin_alert').html('All fields are mandatory.').removeClass('alert-success').addClass('alert-danger').show();
             setTimeout(function(){ $('#admin_alert').hide(); }, 3000);
             return false;
         }
 
         if ($('#admin_password').val() !== $('#re_password').val()) {
             $('#admin_alert').html('password does not match !!').removeClass('alert-success').addClass('alert-danger').show();
             setTimeout(function(){ $('#admin_alert').hide(); }, 3000);
             return false;
         }
         $.ajax({
             url: "add-admin-user",
             data: $('#create-admin-form').serialize(),
             type: 'POST',
             beforeSend: function (e) {
                 //Loader shows
                 $('#user_loader').fadeIn();
 
             },
             success: function (data) {
                 var values = data.data;
                 if (data.code == 401 || data.code == 400 || data.code == 500) {
                     //Loader hides
                     $('#user_loader').fadeOut();
                     $('#admin_alert').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
                     setTimeout(function(){ $('#admin_alert').hide(); }, 3000);
                     return false;
                 }
                 $('#accordion-admin-mode div.card').addClass('completed');
                 $('#accordion-admin-mode .logo-preview-wrp a').removeClass('d-none');
                 //Loader hides
                 $('#user_loader').fadeOut();
                 $('#admin_alert').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();
                 setTimeout(function(){
                     $('#admin_alert').hide();
                 }, 3000);
 
             }
         })
 
     });
 
 
 
 
 });
 
 $('#test-mail-form').validate({
     rules: {
         email: {
             required: true,
             validateEmail:true,
             stringLength: true
         }
     },
     ignore: ' ',
     submitHandler: function(form) {
         event.preventDefault;
         var form_data = new FormData($('#test-mail-form')[0]);
         $.ajax({
             type: "POST",
             url: "test-mails",
             dataType: 'JSON',
             data: form_data,
             cache: false,
             contentType: false,
             processData: false,
             beforeSend: function() {
                 $('#user_loader').show();
                 $('body').addClass('modal-open on-process');
                 $('#test-mail .alert').remove();
             },
             statusCode: {
                500: function (error) {
                    $('#user_loader').hide();
                    if(error.responseJSON.exception == 'InvalidArgumentException') $('<div class="alert alert-danger">Couldnot send the mail. Please check your mail configurations.</div>').insertBefore($('#test-mail-form'));
                    else $('<div class="alert alert-danger">' + error.responseJSON.message + '</div>').insertBefore($('#test-mail-form'));
                    
                    setTimeout(function(){
                        $('#test-mail .alert').remove();
                    }, 3000);
                }
            },
             success: function(result) {
                 $('#user_loader').hide();
                 $('body').removeClass('on-process');
                 console.log(result);
                 if(result.status) $('<div class="alert alert-success">' + result.msg + '</div>').insertBefore($('#test-mail-form'));
                 else $('<div class="alert alert-danger">' + result.msg + '</div>').insertBefore($('#test-mail-form'));
 
                 setTimeout(function(){
                     $('#test-mail .alert').remove();
                 }, 3000);
             }
         });
     }
 });
 
 jQuery.validator.addMethod("validateEmail", function(value, element) 
 {
     var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
     var result = value.replace(/\s/g, "").split(/,|;/);
     for(var i = 0;i < result.length;i++) {
         if(!regex.test(result[i])) return false;
     }   
         
     return true;
 
 }, 'Enter valid emails.');
 jQuery.validator.addMethod("stringLength", function(value, element) 
 {
     var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
     var result = value.replace(/\s/g, "").split(/,|;/);
         if(result.length > 3) { return false; }
 
         return true;
 }, 'You can send maximum 3 mails at a time.');
 
 function NumberOnly(event){
 
     var key = window.event ? event.keyCode : event.which;
 
     return ((key > 47 && key < 58))
 }
 