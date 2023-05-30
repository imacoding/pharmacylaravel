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
<div class="payment-success-page payment-failure-page">
  <div class="contact-container" style="min-height: 760px">
    <div class="prescription-inner container">
      <div class="contact-container" style="min-height: 790px">
        <div class="contact-container">
          <div class="prescription-inner container alert-danger" style="min-height: 680px;text-align:center;line-height:60px;">
            <h2><strong>Failure !</strong> Your payment was not processed.</h2>
            <p> Due to some technical issues, your payment was not processed, please contact us incase of any queries.</p>
            <a href="{{url('/')}}" class="btn btn-warning" style="background-color: #6d7289">Back To Home</a>
            </div>
          <!-- prescription-cont -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection