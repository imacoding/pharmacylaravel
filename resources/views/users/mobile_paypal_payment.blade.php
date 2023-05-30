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

<body>
<?php
session_start();
$_SESSION['amount']=$posted['amount'];
$_SESSION['first_name']=$posted['firstname'];
$_SESSION['item_name']=$posted['amount'];
$_SESSION['invoice']=$posted['invoice'];

if(isset($_POST['paypal']))
{
    $payment_mode = \App\Models\Setting::where('group','payment')->pluck('value','key')->toArray();
    $transaction_mode = $payment_mode['type'];
    $gateway_params = \App\Models\PaymentGatewaySetting::where('gateway_id',$payment_mode['mode'])->pluck('value','key')->toArray();;
    $settings = [];
    
    foreach($gateway_params as $key => $params){
        $settings[$key] = $params;
    }
    $paypal_location  = ($transaction_mode == 'LIVE') ? $settings['paypal_live_url'] : $settings['paypal_sandbox_url'];

    $query = array();
    $query['cmd'] = '_xclick';
    $query['business'] = $settings['business_email'];
    $query['first_name'] = $_SESSION['first_name'];
    $query['email'] = $_POST['email'];
    $query['item_name'] = $_SESSION['invoice'];
    $query['quantity'] = 1;
    $query['amount'] = $_SESSION['amount'];
    $query['currency_code'] = $settings['paypal_currency'];
    $transaction_id=abs(crc32($_SESSION['invoice']));
    $query['txn_id'] = $transaction_id;

    $query['cancel_return'] = url('/')."/medicine/paypal-fail?status=cancel";
    $query['return'] = url('/')."/medicine/paypal-success?status=success&pay_id=" . $_SESSION['invoice']."&transaction_id=".$transaction_id;
    // Prepare query string
    $query_string = http_build_query($query);
    header('Location: '.$paypal_location. $query_string);
    exit;
}
?>
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

            <form action="{{ url('medicine/handlePayment') }}" method="post" name="paypalForm">
              <h2>Payment Details</h2>
              <div class="form-group">
                <label>Amount: </label>
                <input type="text" name="amount1" value="<?php echo (empty($posted['amount'])) ? '' : \App\Models\Setting::currencyFormat($posted['amount']) ?>" class="ro-005 ro-451 form-control" readonly />
              </div>
              <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" class="form-control" />
              </div>
              <div class="form-group">
                <label>Email:</label>
                <input type="text" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" class="ro-005 ro-451 form-control" />
              </div>
              <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" class="ro-005 ro-451 form-control" />
              </div>
              <div class="form-group">
                <label>Product Info: </label>
                <textarea readonly name="productinfo1" class="ro-005 ro-451 form-control pt-2"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
              </div>
              <div class="form-group custom-fromdiv">
                <input type="submit" value="Pay Now" onclick="loader_show()" name="paypal" class="btn-success " style="height: 40px; width: 100px" />
              </div>
              <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" class="ro-005 ro-451 form-control" />
              <textarea style="display:none;" name="productinfo" class="ro-005 ro-451 form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
              <input type="hidden" name="invoice" value="<?php echo (empty($posted['invoice'])) ? '' : $posted['invoice'] ?>" class="ro-005 ro-451 form-control" />

            </form>
          </div>

        </div>
        <!-- prescription-cont -->
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
  function loader_show() {
    $('#user_loader').show();
  }
</script>
</body>

</html>