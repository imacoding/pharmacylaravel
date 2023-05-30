@extends('users.layouts.index_layout')
@section('headerClass','')
@section('content')
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

<div class="payment-outer-div">
  <div class="contact-container" style="min-height: 760px">
    <div class="prescription-inner container">
      <h4>Payment Details</h4>
      <form action="" method="post" name="paypalForm">
      <!-- <form action="{{ url('medicine/handlePayment') }}" method="post" name="paypalForm"> -->
      @csrf
        <div class="form-group">
          <label>Amount:</label>
          <input type="text" name="amount1" value="<?php echo (empty($posted['amount'])) ? '' : \App\Models\Setting::currencyFormat($posted['amount']) ?>" class="ro-005 ro-451 form-control" readonly />
        </div>
        <div class="form-group">
          <label>First Name:</label>
          <input type="text" name="first_name" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" class="form-control" />
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="text" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" class="form-control" />
        </div>
        <div class="form-group">
          <label>Phone:</label>
          <input type="text" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" class="form-control" />
        </div>
        <div class="form-group">
          <label>Product Info:</label>
          <textarea readonly name="productinfo1" class="ro-005 ro-451 form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
        </div>
        <div class="form-group custom-fromdiv typ2">
          <input type="submit" value="Pay Now" name="paypal" class="btn-success " style="height: 40px; width: 100px" />
        </div>
        <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" class="form-control ro-005 ro-451" />
        <input type="hidden" name="invoice" value="<?php echo (empty($posted['invoice'])) ? '' : $posted['invoice'] ?>" class="form-control ro-005 ro-451" />
        <textarea readonly name="productinfo" class="ro-005 ro-451 d-none form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
      </form>
    </div>
  </div>
</div>
@endsection
