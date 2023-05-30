$(document).ready(function () {
	var url = $('#siteurl').val();
    $('#admin-login').validate({
        errorPlacement: function($error, $element) {
          $error.appendTo($element.closest("div"));
    	},
      rules: {
        log_email: {
          required: true,
        },
        password: {
          required: true,
        },
        
      },
      ignore: ' ',     
    });

    $('#reset-password').validate({
      errorPlacement: function($error, $element) {
        $error.appendTo($element.closest("div"));
    },
    rules: {
      email: {
        required: true,
        // email: true
      },      
    },
    ignore: ' ',
    submitHandler: function(form) {
          var form_data = new FormData($('#reset-password')[0]);
          var for_field = '',
          msg = '';
          $.ajax({
            type: "POST",
            url: url + '/admin/reset-password',
            dataType: 'JSON',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
              $(".error").html('');
            },
            success: function(result) {
              if(result.status){
                // 
              } else {
                if (result.message.email) { for_field = 'email';
                  msg = result.message.email[0];
                }
                $('<label for="' + for_field + '" class="error"><span>*</span>' + msg + '</label>').insertAfter('#reset-password input[name="' + for_field + '"]');
                $('#reset-password input[name="email"]').addClass('error');
              }
            }
          });
      }
  });
});

