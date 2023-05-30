@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<div class="dashboard">
    <div class="container">
        <div class="breadcrumps banner-crumps">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                @if(count($med_info))
                    <li>{{ $med_info[0]->item_name }}</li>
                @endif
            </ul>
        </div>
        <div class="addto-cart-outer">
            <h2><span class="countof-product">{{ count($med_info)}} </span> {{ ngettext('Product', 'Products', count($med_info)) }} found</h2>
            <div class="addto-cart-innr">
                <div class="product-outer">
                    @foreach($med_info as $key => $med)
                        <div class="single-item-wrap">
                            <div class="product-wrap-left">
                                <figure>
                                    @if ($med->product_image && Storage::disk('public')->url('medicine/' . $med->product_image))
                                       <img src="{{ Storage::disk('public')->url('medicine/' . $med->product_image) }}" alt="add-to-cart-image">

                                    @else
                                        <img src="{{ Storage::disk('public')->url('medicine/no-image-available.png') }}" alt="">
                                    @endif
                                </figure>
                                <div class="product-details" data-med-code="{{ $med->item_code }}" data-med-id="{{ $med->id }}" data-pres="{{ $med->is_pres_required }}" data-sel-prz="{{ $med->selling_price }}">
                                    <h6>{{ $med->item_name }}</h6>
                                    @if($med->manufacturer && $med->manufacturer != "Not available")
                                        <p class="manufacturer">Mfr: {{ $med->manufacturer }}</p>
                                    @endif
                                    <p>Prescription - <?= ($med->is_pres_required == 1) ? "Mandatory" : "Optional" ;?> </p>
                                    <p class="pr-price">Best price* <span class="prod-price">{{ \App\Models\Setting::currencyFormat($med->selling_price) }}</span></p>
                                </div>
                            </div>

                            <div class="addcart-right">
                                <input type="text" class="prd-qty" value="10">
                                <div class="addcart-btn-wrp">
                                    <a href="javascript:void(0)" class="add-to-cart-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                            <defs>
                                                <style>
                                                    .a {
                                                        fill: #ffffff;
                                                    }

                                                    .b {
                                                        clip-path: url(#a);
                                                    }

                                                    .c {
                                                        fill: none;
                                                        stroke: #ffffff;
                                                        stroke-linecap: round;
                                                        stroke-linejoin: round;
                                                        stroke-width: 1.5px;
                                                    }
                                                </style>
                                                <clipPath id="a">
                                                    <rect class="a" width="23" height="21" transform="translate(343 477)" />
                                                </clipPath>
                                            </defs>
                                            <g class="b" transform="translate(-343 -477)">
                                                <g transform="translate(343.786 477.5)">
                                                    <path class="c" d="M13.675,30.838A.838.838,0,1,1,12.838,30,.838.838,0,0,1,13.675,30.838Z" transform="translate(-4.637 -12.585)" />
                                                    <path class="c" d="M30.175,30.838A.838.838,0,1,1,29.338,30,.838.838,0,0,1,30.175,30.838Z" transform="translate(-11.923 -12.585)" />
                                                    <path class="c" d="M1.5,1.5H4.85L7.1,12.716A1.675,1.675,0,0,0,8.77,14.064h8.142a1.675,1.675,0,0,0,1.675-1.349l1.34-7.028H5.688" />
                                                </g>
                                            </g>
                                        </svg>
                                        Add to Cart
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" id="hidden_medicine_id" value="{{{ $med_info[0]->id }}}">
                <input type="hidden" id="hidden_medicine" value="{{{ $med_info[0]->item_name }}}">
                <input type="hidden" id="hidden_selling_price" value="{{{ $med_info[0]->selling_price }}}">
                <input type="hidden" id="hidden_item_code" value="{{{ $med_info[0]->item_code }}}">
                <input type="hidden" id="hidden_item_pres_required" value="{{{ $med_info[0]->is_pres_required }}}">
                <div class="alternative-section">
                    <h2>Alternatives</h2>
                    <div class="alternatives-wrap">
                                              
                    </div>
                </div>
            </div>

        </div>
    </div>
    @if(!Auth::check())
        @include('users.layouts.index_modals')
    @endif
@push('js')
<script src="{{url('assets/js/common/medicine_details.js')}}" type="text/javascript"></script>
<script src="{{url('assets/js/common/homepage.js')}}" type="text/javascript"></script>
@endpush                                   
@endsection
