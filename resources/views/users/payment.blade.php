<?php
$payment_mode = \App\Models\Setting::where('group','payment')->pluck('value','key')->toArray();

// $payment_mode['type'];
$payment = \App\Models\PaymentGatewaySetting::where('gateway_id',$payment_mode['mode'])->pluck('value','key')->toArray();

$PAYU_BASE_URL = $payment['payumoney_live_url'];
$MERCHANT_KEY = $payment['merchant_key'];
$SALT = $payment['merchant_hash'];

if($payment_mode['type'] !== 'LIVE') $PAYU_BASE_URL = $payment['payumoney_sandbox_url'];

$action = '';

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

@extends('users.layouts.user_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')

  <div class="payment-outer-div">
    <div class="contact-container" style="min-height: 760px">
      <div class="prescription-inner container">
        <h2>Payment Details</h2>
        <br/>
        <form action="<?php echo $action; ?>" method="post" name="payuForm">
          {{ method_field('POST') }}

          {{Form::token()}}
          <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
          <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
          <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
          <table class="table">
            <tr>
              <td>Amount: </td>
              <td>
                  <input type="text" name="amount1" value="<?php echo (empty($posted['amount'])) ? '' : \App\Models\Setting::currencyFormat($posted['amount']) ?>" class="form-control" readonly />
                  <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" class="form-control"/>
              </td>
              <td>First Name: </td>
              <td>
                <input type="text" name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" class="form-control"/>
              </td>
            </tr>
            <tr>
              <td>Email: </td>
              <td>
                <input type="text" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" class="form-control"/>
              </td>
              <td>Phone: </td>
              <td>
                <input type="text" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" class="form-control"/>
                <input type="hidden" name="invoice" value="<?php echo (empty($posted['invoice'])) ? '' : $posted['invoice'];?>">
                <input type="hidden" name="id" value="<?php echo (empty($posted['id'])) ? '' : $posted['id']; ?>">
              </td>
            </tr>
            <tr>
              <td>Product Info: </td>
              <td colspan="3">
                <textarea readonly="readonly" name="productinfo1" class="form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
                <textarea style="display: none" name="productinfo" class="form-control"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
              </td>
              <input name="surl" value="{{ url('/') }}/medicine/pay-success/<?php echo (empty($posted['id'])) ? '' : $posted['id'] ?>&_token=<?php echo csrf_token(); ?>"  type="hidden"/>
              <input name="furl" value="{{ url('/') }}/medicine/pay-fail/<?php echo (empty($posted['id'])) ? '' : $posted['id'] ?>&_token=<?php echo csrf_token(); ?>"  type="hidden"/>
              <input type="hidden" name="service_provider" value="payu_paisa"  />
            </tr>
            <tr>
              <?php if(!$hash) { ?>
                <td colspan="4">
                  <input type="submit" value="Pay Now" class="btn-success" style="height: 40px; width: 100px"/>
                </td>
              <?php } ?>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
  @endsection
  @push('js')
  <script>
    var hash = '<?php echo $hash ?>';
    $(document).ready(function() {
      submitPayuForm();
    });
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      $('#user_loader').fadeIn();
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  @endpush