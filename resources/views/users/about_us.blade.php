@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<main class="about-page">
    <section class="inner-banner about-banner">
        <div class="container">
            <div class="inner-bnr-content">
                <h1 class="fw700 ft45">About Healwire</h1>
                <div class="breadcrumps banner-crumps">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>About Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="about-details">
        <div class="container">
            <div class="about-detail-innr">
                <div class="row">
                    <div class="col-md-6">
                        <figure>
                            <img src="{{url ('assets/images/about-details.png') }}" alt="about-us image">
                        </figure>
                    </div>
                    <div class="col-md-6">
                        <div class="details-sec">
                            <div class="inner-head-wrp">
                                <h6 class="txt-green fw400 ft18">About us</h6>
                                <h2 class="ft45 fw700">Buy Medicines Online Now, Easier than Ever!</h2>
                            </div>
                            <p>Healwire is committed to delivering good health and high-quality medicines to our customers online. Upload your prescription or choose your medicines and add them to the cart and place your order. We bring the medicines to your doorstep so that you no more have to head out of your homes and endure the long waits at the local medical stores.
                            </p>
                            <p> All the medicines we deliver comes with a long shelf life and an invoice bill will be provided with the delivery. Order your medicines online at discounted rates from Healwire and enjoy our super fast delivery.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="howit-works">
        <div class="container">
            <div class="howit-works-innr1">
                <div class="details-sec">
                    <div class="inner-head-wrp text-center">
                        <h6 class="fw700 ft45">How it <span class="txt-green">Works</span></h6>
                        <p class="pt-0">We made it simple and trouble free for you to connect with us for making your purchase of medicine online.</p>
                    </div>
                </div>
                <div class="howit-works-innr2">
                    <article class="container our-features-main">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 our-feature-item-wrap">
                                <span class="our-feature-item howit-wrks-item">
                                    <img src="{{url ('assets/images/howit-works1.svg') }}" alt="how it works icon">
                                    <p class="ft16">Initially upload us your prescription of the required medicines. </p>
                                </span>
                            </div>
                            <div class="col-lg-3 col-sm-6 our-feature-item-wrap">
                                <span class="our-feature-item howit-wrks-item">
                                    <img src="{{url ('assets/images/howit-works2.svg') }}" alt="how it works icon">
                                    <p class="ft16">Else select online from the list of medicines and add it to your cart. </p>
                                </span>
                            </div>
                            <div class="col-lg-3 col-sm-6 our-feature-item-wrap">
                                <span class="our-feature-item howit-wrks-item">
                                    <img src="{{url ('assets/images/howit-works3.svg') }}" alt="how it works icon">
                                    <p class="ft16">Our pharmacists will identify, examine and update your prescription in the very next moment.</p>
                                </span>
                            </div>
                            <div class="col-lg-3 col-sm-6 our-feature-item-wrap">
                                <span class="our-feature-item howit-wrks-item">
                                    <img src="{{url ('assets/images/howit-works4.svg') }}" alt="how it works icon">
                                    <p class="ft16">Verify and make the payment, you will be notified regarding status of the shipment.</p>
                                </span>
                            </div>

                        </div>
                    </article>
                </div>
            </div>

        </div>
    </section>
    <section class="social-responsibilty">
        <div class="container">
            <div class="social-reponse-innr">
                <div class="row column-sm-reverse">
                    <div class="col-md-6">
                        <div class="details-sec">
                            <div class="inner-head-wrp">
                                <h6 class="txt-green fw400 ft18">Social responsibility</h6>
                                <h2 class="ft45 fw700">Reliable & Swift Online Medicine Store</h2>
                            </div>
                            <p>We believe in giving it back to society. By facilitating super fast doorstep delivery and effortless purchase, we are offering hassle-free online medicine purchase and delivery. By adapting to the prevailing situations, Healwire is following all the safety protocols and regularly conducts basic health checkups and standard sanitisation for all the team members.</p>
                            <p>Our humble initiative to make necessary medicines accessible for all through our reliable and trusted platform is an effort from our part to make lives simpler for our fellow people. We offer uninterrupted 24*7 support services for our customers and are always just a call away.</p>
                            <!-- <ul class="custom-list">
                                <li>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below</li>
                                <li>Ipsum used since the 1500s is reproduced below for those intereste</li>
                                <li>Provider.We have started this medicine provider</li>
                            </ul> -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <figure>
                            <img src="{{url ('assets/images/social-resp-img.png') }}" alt="grand-parents-img">
                        </figure>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@include('users.layouts.index_modals')
</body>

@endsection
@push('js')

<script src="{{url('assets/js/common/homepage.js')}}" type="text/javascript"></script>
@endpush