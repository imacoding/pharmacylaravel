@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')

<main class="home-page">
    <section class="home-banner-sec" style="background-image:url( {{ url('assets/images/home-banner-img.jpg') }})">
        <article class="container">
            <div class="home-bnr-content">
                <h1 class="fw600 ft45">Buy medicines online. It’s easy as its name</h1>
                <p class="ft17 line17-28">Buying medicines was never this easy. Stay at the comfort of your home and get medicines delivered
                    to your door-step. And it’s double the gain when our discounted rates offer you the benefit of extra savings !</p>

                <form class="form-inline">
                   @csrf

                    <div class="input-group mb-2 w-100">
                        <input type="text" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Search medicine here">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" onclick="goto_detail_page();">Search</button>
                        </div>
                    </div>
                </form>
                <div class="upload-prescription-sec">
                    <!-- <input type="file" name="" id="btn-upload-prescription" class="d-none"> -->
                    <button class="btn btn-outline-light upload-prescription-btn"><span class="icon-upload"></span> Upload Prescription</button>
                    <button class="d-none" style="display:none;" id="upload-prescription-btn" data-toggle="modal" data-target="#upload-prescription-modal"></button>
                </div>
            </div>
        </article>
    </section>

    <section class="our-features-sec">
        <article class="container our-features-main">
            <div class="row our-feature-slider">
                <!--  -->
                <div class="">
                    <div class="our-feature-item-wrap">
                        <span class="our-feature-item">
                            <span class="icon-free-shipping our-feature-item-icon"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>
                            <h4 class="ft22 fw600">Free Shipping</h4>
                            <p class="ft18">Free shipping world wide</p>
                        </span>
                    </div>
                </div>

                <!--  -->
                <div>
                    <div class="our-feature-item-wrap">
                        <span class="our-feature-item">
                            <span class="icon-support-24-7 our-feature-item-icon"><span class="path1"></span><span class="path2"></span></span>
                            <h4 class="ft22 fw600">Support 24/7</h4>
                            <p class="ft18">Contact us 24 hours a day</p>
                        </span>
                    </div>
                </div>

                <!--  -->
                <div>
                    <div class="our-feature-item-wrap">
                        <span class="our-feature-item">
                            <span class="icon-secure-payments our-feature-item-icon"><span class="path1"></span><span class="path5"></span></span>
                            <h4 class="ft22 fw600">Secure Payments</h4>
                            <p class="ft18">100% payment protection</p>
                        </span>
                    </div>
                </div>
                <!--  -->
                <div>
                    <div class="our-feature-item-wrap">
                        <span class="our-feature-item">
                            <span class="icon-easy-return our-feature-item-icon"><span class="path1"></span><span class="path2"></span></span>
                            <h4 class="ft22 fw600">Easy Return</h4>
                            <p class="ft18">Simple returns policy</p>
                        </span>
                    </div>
                </div>
                <!--  -->
            </div>
        </article>
    </section>

    @if(count($top_brands))
    <section class="top-brands-sec pb60 pt50">
        <article class="container">
            <div class="row pb50">
                <div class="col-8 my-auto">
                    <h2 class="ft45 fw700 mb-0">Top Brands</h2>
                </div>
                {{--<div class="col-4 my-auto text-right">--}}
                {{--<a class="ft21 fw300 dark-link" href="#">View All</a>--}}
                {{--</div>--}}
            </div>


            <div class="top-brand-products w-100">
                <div class="row top-brand-products-slider">
                    <!--  -->
                    @foreach($top_brands as $brands)
                    <div class="col-lg-3 col-sm-6 top-brand-product-item-wrap">
                        <a href="#" class="top-brand-product-item">
                            <div class="top-brand-product-img">
                                <img class="img-fluid" src="{{ url('/storage/brand').'/'.$brands->brand_image}}" alt="{{$brands->title}}">
                            </div>
                            <div class="top-brand-product-content">
                                <h5 class="ft22 ft600">{{$brands->title}}</h5>
                                <span class="ft18 text-primary">{{$brands->content}}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    <!--  -->

                </div>
            </div>
        </article>
    </section>
    @endif
    <section class="our-key-features-sec py60">
        <article class="container">
            <h2 class="ft45 fw700 mb-0 pb50">Our Key Features</h2>

            <div class="our-key-features w-100">
                <div class="row">
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-system our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Interactive Dashboard</h5>
                                <p class="ft16 line16-28">User can easily navigate through and choose the desired medicines effortlessly.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-email our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Swift Prescription Uploads</h5>
                                <p class="ft16 line16-28">Upload your prescriptions real quick and we’ll take care of your order.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-process our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Multiple Payment Methods</h5>
                                <p class="ft16 line16-28">There are multiple payment methods available for you in Healwire including COD.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-medicine our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Alternative Medicine Suggestions</h5>
                                <p class="ft16 line16-28">We’ll give alternative medicine suggestions if the exact one you are looking for isn’t available.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-admin-theme our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Simple Design</h5>
                                <p class="ft16 line16-28">The simple and agile design will ensure that you have a very good experience during your shopping.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                    <div class="col-lg-4 col-sm-6 our-key-features-wrap">
                        <span class="our-key-features-item">
                            <span class="icon-click-option our-key-feature-icon"></span>
                            <div class="key-feature-details">
                                <h5 class="ft24 ft600">Easy Installation</h5>
                                <p class="ft16 line16-28">An effortless installation that will give absolutely no trouble at all. As quick and easy as possible.</p>
                            </div>
                        </span>
                    </div>
                    <!--  -->
                </div>
            </div>
        </article>
    </section>
    @if(env('ANDROID_URL') || env('IOS_URL'))
    <section class="try-with-mobile-app-sec pb60">
        <article class="container pb60  ">
            <div class="try-mobile-app-wrap">
                <div class="row">
                    <div class="col-lg-6 mt-auto text-center order-lg-1">
                        <img class="img-fluid try-app-img" src="{{ url('assets/images/try-our-mobile-app.png') }}" alt="">
                    </div>
                    <div class="col-lg-6 my-auto order-lg-2">
                        <div class="try-mobile-app-content">
                            <h3 class="ft34 line34-50 fw700">Now you can try<br> with our Mobile App</h3>
                            <p class="ft18 line18-32">Healwire Mobile app is at your fingertips to facilitate smarter buying of medicines. The App is available both for iOS and Android platforms.</p>
                            <div class="w-100 try-app-btns">
                                @if(env('ANDROID_URL'))
                                    <a href="<?php echo env('ANDROID_URL'); ?>" target="_blank" class="d-inline-block try-mob-app-btn">
                                        <img src="{{ Storage::disk('COMMON_IMAGES')->url('google-play.png') }}" alt="">
                                    </a>
                                @endif
                                @if(env('IOS_URL'))
                                <a href="<?php echo env('IOS_URL'); ?>" target="_blank" class="d-inline-block try-mob-app-btn">
                                    <img src="{{ Storage::disk('COMMON_IMAGES')->url('app-store.png') }}" alt="">
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </article>
    </section>
    @endif
</main>

<input type="hidden" name="msg" id="msg" value="<?php if (isset($_GET['msg'])) echo $_GET['msg']; ?>">
<!-- begin Modal prescription -->
<div class="modal fade" id="upload-prescription-modal" tabindex="-1" role="dialog" aria-labelledby="upload-prescription-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close"></span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="ft30 fw600">Upload Prescription</h3>
                <p class="ft13 color-gray">JPG , PNG Or PDF Smaller Than 10 Mb</p>
                <div class="drop-zone">

                    <span class="drop-zone__prompt ft14">
                        <img class="mb-2" src="{{ url('assets/images/upload-placeholder-img.svg') }}" alt=""><br>
                        Drag and Drop your Prescription here<br>
                        or<br>
                        <span class="browse-files-label ft17">Browse files</span>
                    </span>
                    <input type="file" name="myFile" class="drop-zone__input">
                </div>
            </div>
        </div>
    </div>
</div>
@include('users.layouts.index_modals')
</body>


@endsection
@push('js')

<script src="{{url('assets/js/common/homepage.js')}}" type="text/javascript"></script>
@endpush