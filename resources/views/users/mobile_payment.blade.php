<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" id="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>{{ \App\Models\Setting::param('site','app_name')['value'] }}</title>

    <link rel="stylesheet" href="{{ url('assets/css/custom.css') }}">


    <link rel="shortcut icon" href="{{ url('/') }}<?= logo_icon() ?>" type="image/x-icon">
    <link rel="icon" href="{{ url('/') }}<?= logo_icon() ?>" type="image/x-icon">

    <link rel="stylesheet" href="{{ url('assets/css/jquery-ui.css') }}">
    <script src="{{ url('assets/js/jquery-1.10.2.js') }}"></script>
    <script src="{{ url('assets/js/jquery-ui.js') }}"></script>


    <script type="text/javascript" src="{{ url('assets/js/jquery.validate.min.js') }}"></script>
  </head>
  <?php

    $payment_mode = \App\Models\Setting::where('group','payment')->pluck('value','key')->toArray();

    $payment = \App\Models\PaymentGatewaySetting::where('gateway_id',$payment_mode['mode'])->pluck('value','key')->toArray();

    $PAYU_BASE_URL = $payment['payumoney_live_url'];
    $MERCHANT_KEY = $payment['merchant_key'];
    $SALT = $payment['merchant_hash'];

    if($payment_mode['type'] !== 'LIVE') $PAYU_BASE_URL = $payment['payumoney_sandbox_url'];

    $action = url('/') . '/medicine/make-payment/' . ($posted['id'] ? $posted['id'] : '');

    if(!empty($_POST)) {
      $posted = array();
      foreach($_POST as $key => $value) {    
        $posted[$key] = $value; 
      
      }
    }

    $formError = 0;

    if(empty($posted['txnid'])) {
      // Generate random transaction id
      $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    } else {
      $txnid = $posted['txnid'];
    }
    $hash = '';
    // Hash Sequence
    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    if(empty($posted['hash']) && sizeof($posted) > 0) {

      if( empty($posted['key'])
              || empty($posted['txnid'])
              || empty($posted['amount'])
              || empty($posted['firstname'])
              || empty($posted['email'])
              || empty($posted['phone'])
              || empty($posted['productinfo'])
              || empty($posted['surl'])
              || empty($posted['furl'])
          || empty($posted['service_provider'])
      ) {
        $formError = 1;
      } else {
        //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
      $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';	
      foreach($hashVarsSeq as $hash_var) {
          $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
          $hash_string .= '|';
        }

        $hash_string .= $SALT;


        $hash = strtolower(hash('sha512', $hash_string));
        $action = $PAYU_BASE_URL . '/_payment';
      }
    } elseif(!empty($posted['hash'])) {
      $hash = $posted['hash'];
      $action = $PAYU_BASE_URL . '/_payment';
    }


  ?>
  <body>
    <div id="wrapper">
      <div id="page-content-wrapper">
        <div class="container">
          <header>
            <div style="height:50px;background:url('{{ Storage::disk('SYSTEM_IMAGE_URL')->url(logo()) }}');
                      background-position: center; background-size: contain; background-repeat: no-repeat;">

            </div>

          </header>
        </div>
        <div class="loader-overlay">
          <div class="loading-gif"></div>
        </div>
        <div class="contact-container cust-container">
          <div class=" container">
            <div class="paypal-form-outer">
              <form action="<?php echo $action; ?>" method="post" name="payuForm">
              
                <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
                <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
                <h2>Payment Details</h2>
                <div class="form-group">
                  <label>Amount: </label>
                  <input type="text" name="amount1" value="<?php echo (empty($posted['amount'])) ? '' : \App\Models\Setting::currencyFormat($posted['amount']) ?>" class="form-control" readonly />
                  <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" class="form-control"/>
                </div>
                <div class="form-group">
                  <label>First Name: </label>
                  <input type="text" name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" class="form-control"/>
                </div>
                <div class="form-group">
                  <label>Email: </label>
                  <input type="text" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" class="form-control"/>
                </div>
                <div class="form-group">
                  <label>Phone: </label>
                  <input type="text" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" class="form-control"/>
                  <input type="hidden" name="invoice" value="<?php echo (empty($posted['invoice'])) ? '' : $posted['invoice'];?>">
                  <input type="hidden" name="id" value="<?php echo (empty($posted['id'])) ? '' : $posted['id']; ?>">
                </div>
                <div class="form-group">
                  <label>Product Info: </label>
                  <textarea readonly="readonly" name="productinfo1" class="form-control pt-2"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
                  <textarea style="display: none" name="productinfo" class="form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
                  <input name="surl" value="{{ url('/') }}/medicine/pay-success/<?php echo (empty($posted['id'])) ? '' : $posted['id'] ?>"  type="hidden"/>
                  <input name="furl" value="{{ url('/') }}/medicine/pay-fail/<?php echo (empty($posted['id'])) ? '' : $posted['id'] ?>"  type="hidden"/>
                  <input type="hidden" name="service_provider" value="payu_paisa"  />
                  <input type="hidden" name="isMobileNOs" value="1"  />
                </div>
                <div class="form-group custom-fromdiv">
                    <?php if(!$hash) { ?>
                    <input type="submit" value="Pay Now" class="btn-success" style="height: 40px; width: 100px"/>
                    <?php } ?>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="container">
          <footer class="paypal-cust-footer">
            <div class="col-md-12" style="text-align: center;">
              <p>Copyrighted @ {{strtolower(\App\Models\Setting::param('site','app_name')['value']) }}.Inc <?php echo date('Y') ?>.</p>

            </div>
            <div class="clear"></div>

            <div id="user_loader" class="user_loader" style="display: none;">
              <div class="loader-overlay">
                <div class="loading-gif"></div>
              </div>
            </div>
          </footer>
        </div>
      </div>
    </div>
    
    <script>
      var hash = '<?php echo $hash ?>';
      $(document).ready(function() {
        submitPayuForm();
      });
      function submitPayuForm() {
        if(hash == '') {
          return;
        } else {
          event.preventDefault();
          $('#user_loader').fadeIn();
          var payuForm = document.forms.payuForm;
          payuForm.submit();
        }
      }
    </script>
    <script>
      function loader_show() {
    	$('#user_loader').show();
        //submitPayuForm();
      }
    </script>
  </body>
</html>