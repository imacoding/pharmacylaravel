@extends('users.layouts.index_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<main class="helpdesk-page">
    <section class="home-banner-sec helpdesk-banner" style="background-image:url( {{ url('assets/images/helpdesk-banner.png') }})">
        <article class="container">
            <div class="home-bnr-content">
                <h1 class="fw600 ft45">What Can We Help You With ?</h1>
                {{--<form class="form-inline">--}}
                    {{--<div class="input-group mb-2 w-100">--}}
                        {{--<input type="text" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Search medicine here">--}}
                        {{--<div class="input-group-append">--}}
                            {{--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalsearch">Search</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</form>--}}
                {{--<p class="mt-4"><span>Popular searches :</span>how to buy medicine , How do i lorem Medicine ?</p>--}}
            </div>
        </article>
    </section>
    <section class="helpdesk-details-outer">
        <div class="container">
            <div class="helpdesk-innr text-center">
                <h2 class="fw600">Help Desk</h2>
                <p>Our help desk is active 24*7 to help you with any possible queries. Get in touch with us through phone, email or in person.</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="help-single">
                        <figure>
                            <img src="{{url ('assets/images/telephone.png') }}" alt="telephone-icon">
                        </figure>
                        <h6 class="fw600">Give Us a Ring</h6>
                        <p>Always there to answer your queries and calls. We’re just a call away.
                        {{-- <a href="tel:+{{\App\Models\Setting::param ('site' , 'phone')['value']}}">+{{\App\Models\Setting::param ('site' , 'phone')['value']}}</a> --}}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="help-single">
                        <figure>
                            <img src="{{url ('assets/images/envelope.png') }}" alt="telephone-icon">
                        </figure>
                        <h6 class="fw600">Send Us an Email</h6>
                        <p>We are prompt and will reply to your emails right away. Just send us an email.
                        {{--<a href="mailto:{{\App\Models\Setting::param ('site' , 'mail')['value']}}">{{\App\Models\Setting::param ('site' , 'mail')['value']}}</a> --}}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="help-single">
                        <figure>
                            <img src="{{url ('assets/images/walkin.png') }}" alt="telephone-icon">
                        </figure>
                        <h6 class="fw600">Walk-in Anytime</h6>
                        <p>We’re up for in-person assistance any time you need it. Walk-in and we’ll talk it through.
                        {{--{{\App\Models\Setting::param ('site' , 'address')['value']}} --}}

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- modal content  -->
    <div class="modal fade modalsearch" id="modalsearch" tabindex="-1" role="dialog" aria-labelledby="modalsearchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title fw700" id="modalsearchLabel">How do i pay by cash on delivery (cod) method?</h5>
                    <p>Contrary to popular belief, lorem ipsum is not simply random text. it has roots in a piece of classical latin literature from 45 bc, making it over 2000 years old. richard mcclintock, a latin professor at hampden-sydney college in virginia, looked up one of the more obscure latin words, consectetur, from a lorem ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. lorem ipsum comes from sections 1.10.32 and 1.10.33 of "de finibus bonorum et malorum. the extremes of good and evil) by cicero, written in 45 bc. </p>
                    <p>this book is a treatise on the theory of ethics, very popular during the renaissance. the first line of lorem ipsum, "lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.the standard chunk of lorem ipsum used since the 1500s is reproduced below for those interested. sections 1.10.32 and 1.10.33 from "de finibus bonorum et malorum" by cicero are also reproduced in their exact original form, accompanied by english versions from the 1914 translation by h. rackham.</p>
                    <ul class="custom-list">
                        <li>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below</li>
                        <li>The standard chunk of Lorem  the 1500s is reproduced below</li>
                        <li>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!-- modal content end  -->
</main>
@include('users.layouts.index_modals')
</body>

@endsection
@push('js')

<script src="{{url('assets/js/common/homepage.js')}}" type="text/javascript"></script>
@endpush