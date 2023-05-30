@extends('users.layouts.user_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')

<div class="dashboard">
    <div class="container">
        <div class="dashboard-main-wrap">
            <!-- dashboard left side start -->
            @include('users.layouts.dashboard-side-bar')
            <!-- dashboard left section end-->

            <!-- dashboard right section -->

            <div class="right_side_wrppr">
                <div class="right-wrapper-innr">
                    <div class="dashboard-header typ2">
                        <a href="" class="back-bttn">Back</a>
                        <h1>Shipped Orders</h1>
                    </div>
                    <div class="dashboard-contents">
                        <div class="mycart-contents">
                            <div class="prescription-table-outer">
                                <div class="prescription-items-innr">
<div class="prescription-table shipment-table">
<ul class="item-info-bar">
    <li>Invoice</li>
    <li>Prescription</li>
    <li>Date</li>
    <li>Status</li>
</ul>
<!-- repeat "prescription-items" for each row of cart items -->
@if(!empty(count($invoices)))
    <div class="prescription-items shipment-table">
    
        @php
            $i=0;                                                     
            $f = new \NumberFormatter( locale_get_default(), \NumberFormatter::SPELLOUT );
        @endphp
        <div class="accordion shipment-accordion" id="accordion-prescription">
            @foreach($invoices as $invoice)
                @php 
                    $prescription = $invoice->prescription();
                    $cart_list = $invoice->cartList();
                    $count = ucwords($f->format($i));
                @endphp
                <div class="card">
                    <div class="card-head" id="heading{{$count}}">
                        <ul data-toggle="collapse" class="collapsed" data-target="#collapse{{$count}}" aria-expanded="true" aria-controls="collapse{{$count}}">
                            <li>
                                <p>Invoice</p>
                                {{ $invoice->invoice  }}
                            </li>
                            <li>
                                <div class="item-details-outer">
                                    <div class="item-details-inner">
                                <figure>
                    @php
                        $pre_img = $prescription->path;
                        if($pre_img)
    $pre_img = Storage::disk('PRESCRIPTION')->exists($email . '/thumbnails/' . $pre_img) ? Storage::disk('PRESCRIPTION')->url($email . '/thumbnails/' . $pre_img) : Storage::disk('PRESCRIPTION')->url('no_pres_square.png')
        @endphp
    <img src="{{ $pre_img }}" alt="tablet-bottle">
</figure>

</div>
</div>
</li>
<li>
<p>Date</p>
{{ date('d-m-Y', strtotime($prescription->created_at))}}
</li>
<li>
<p>Status</p>
{{ \App\Models\InvoiceStatus::statusName($invoice->status_id) }}
</li>
</ul>

</div>
@if(!empty($cart_list))
<div id="collapse{{$count}}" class="collapse" aria-labelledby="heading{{$count}}" data-parent="#accordion-prescription">
<div class="card-body">
<div class="prescription-details-table">
<div class="prescription-tbl-head">
<p>Medicine</p>
<p>Quantity</p>
<p>Unit Price</p>                                                                                
<p>Sub Total</p>
<p>Unit Disc</p>
<p>Discount</p>
<p>Total Price</p>
</div>
<div class="table-contents-wrap">
@foreach($cart_list as $cart)  
    <div class="table-contents">                                                                               
        <div class="medicine-name">
            <p>{{ \App\Models\Medicine::medicines($cart->medicine)['item_name'] }}</p>
</div>                                                                                
<div class="medicine-qty">
    <p>{{ $cart->quantity}}</p>
</div>
<div class="unitprice">
    <p>{{ number_format($cart->unit_price,2)}}</p>
</div>
<div class="medicine-s-total">
    <p>{{ number_format($cart->unit_price * $cart->quantity,2)}}</p>
</div>
<div class="med-unit-discnt">
    <p>{{ number_format($cart->discount_percentage,2)}}</p>
</div>
<div class="med-discount">
    <p>{{ number_format($cart->discount,2)}}</p>
</div>
<div class="med-total">
    <p>{{ \App\Models\Setting::currencyFormat($cart->total_price)}}</p>
</div>
</div>
@endforeach
</div>
</div>
</div>                                                                    
<div class="medicine-total-outer">
<p><span>Sub Total :</span><span>{{ \App\Models\Setting::currencyFormat($invoice->sub_total)}}</span></p>
<p><span>Shipping Cost :</span><span>{{ \App\Models\Setting::currencyFormat($invoice->shipping)}}</span></p>
<p><span>Discount :</span><span>{{ \App\Models\Setting::currencyFormat($invoice->discount)}}</span></p>
<p><span>Net Payable :</span><span>{{ \App\Models\Setting::currencyFormat($invoice->total)}}</span></p>
</div>

</div>
@endif
</div>
@php $i++; @endphp
@endforeach
</div>                                        

                </div>
            @else
            <div class="prescription-items shipment-table">
                <div class="empty-section pb40">
                    <p>No Order Availables Presently.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
</div>
</div>
<!-- dashboard right section end-->
</div>
</div>
</div>

</body>

@endsection

@push('js')
@endpush