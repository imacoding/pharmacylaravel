<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-lg-4 my-auto order-2 order-lg-1 text-sm-right text-lg-left">
                <ul class="footer-nav-list mb-0 ft15">
                    <li class="{{ Request::is('privacy-policy') ? 'active' : '' }}">
                        <a href="{{url('/privacy-policy')}}">Privacy Policy</a>
                    </li>
                    <li class="{{ Request::is('terms-conditions') ? 'active' : '' }}">
                        <a href="{{url('/terms-conditions')}}">Terms & Conditions</a>
                    </li>
                    <li class="{{ Request::is('help-desk') ? 'active' : '' }}">
                        <a href="{{url('/help-desk')}}">Help Desk</a>
                    </li>
                </ul>
            </div>
            <div class="col-sm-4 text-sm-center my-auto order-1 order-lg-2">
                <a href="{{ url('/') }}"><img class="footer-img" src="{{ Storage::disk('SYSTEM_IMAGE_URL')->url(logo()) }}" alt=""></a>
            </div>
            <div class="col-lg-4 my-auto text-center text-lg-right order-3">
                <p class="ft15 mb-0 footer-copy-rights">Designed by : <a target="_blank" href="https://webandcrafts.com/">webandcrafts</a></p>
            </div>
        </div>
    </div>
    <div id="user_loader" class="user_loader" style="display: none;">
        <div class="loader-overlay">
            <div class="loading-gif"></div>
        </div>
    </div>
</footer>
