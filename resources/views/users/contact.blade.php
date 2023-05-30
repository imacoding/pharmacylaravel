@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<main class="contact-page">
    <section class="inner-banner about-banner contact-banner">
        <div class="container">
            <div class="inner-bnr-content">
                <h1 class="fw700 ft45">Contact us</h1>
                <div class="breadcrumps banner-crumps">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Contact Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-details">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-form-section">
                        <h2 class="fw700">Get in touch with us</h2>
                        <p>Please feel free to reach out to us. We will be more than happy to help.</p>
                        <form class="form-horizontal contact-form" id="contact_form">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <label for="inputname">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <label for="inputemail">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email ID" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Message</label>
                                <textarea class="form-control"  rows="3" name="msg" id="msg" placeholder="Enter your message here..."></textarea>
                            </div>
                            @if(Schema::hasTable('settings') && isset(\App\Models\Setting::param ('site' , 'phone')['value']))
                                <button type="submit" class="send-bttn" id="send_contact">Send</button>
                            @endif
                            <div class="alert" role="alert" id="contact_alert"></div>
                        </form>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="map-section">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15703.65267537973!2d76.353127!3d10.26858!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xe5b5a4092bbae8dd!2sWeb%20and%20Crafts!5e0!3m2!1sen!2sin!4v1622700690352!5m2!1sen!2sin" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    @if(Schema::hasTable('settings') && isset(\App\Models\Setting::param ('site' , 'phone')['value']))
                        <div class="contact-info-section">
                            <div class="info-single">
                                <figure>
                                    <img src="{{url ('assets/images/call-icon.svg') }}" alt="call-icon">
                                </figure>
                                <a href="tel:+{{\App\Models\Setting::param ('site' , 'phone')['value']}}">+{{\App\Models\Setting::param ('site' , 'phone')['value']}}</a>
                            </div>
                            <div class="info-single">
                                <figure>
                                    <img src="{{url ('assets/images/message-box.svg') }}" alt="message-box">
                                </figure>

                                <a href="mailto:{{\App\Models\Setting::param ('site' , 'mail')['value']}}">{{\App\Models\Setting::param ('site' , 'mail')['value']}}</a>
                            </div>
                            <div class="info-single">
                                <figure>
                                    <img src="{{url ('assets/images/locator.svg') }}" alt="locator icon">
                                </figure>
                                <a href="https://goo.gl/maps/5dfPz1Va8LqSkJK27" target="_blank">{{\App\Models\Setting::param ('site' , 'address')['value']}}</a>
                            </div>
                        </div>
                    @endif
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
<script src="{{url('assets/js/common/contact.js')}}" type="text/javascript"></script>
@endpush