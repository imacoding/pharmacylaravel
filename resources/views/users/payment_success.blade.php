@extends('users.layouts.index_layout')
@section('headerClass','')
@section('content')
@push('css')
<link rel="stylesheet" href="{{ url('assets/css/jquery-ui.css') }}">
@endpush
@push('js')
<script src="{{ url('assets/js/jquery-1.10.2.js') }}"></script>
<script src="{{ url('assets/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ url('assets/js/jquery.validate.min.js') }}"></script>
@endpush
<div class="payment-success-page">
  <div class="contact-container" style="min-height: 760px">
    <div class="prescription-inner container">
      <div class="contact-container" style="min-height: 790px">
        <div class="contact-container">
          <div class="prescription-inner container alert-success" style="min-height: 680px;text-align:center;line-height:60px;">
            <h3 style="text-align: center;color: #67A568;"><strong>Success !</strong> Your payment has been processed.</h3>
            <p style="line-height: 25px"> Thank you for shopping with us. We will ship your package once in a short while, you can track the status of the shippment in My Shipping.</p>
            <a href="{{url('/')}}" class="btn btn-warning" style="background-color: #6d7289">Back To Home</a>
          </div>
          <!-- prescription-cont -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection