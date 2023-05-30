<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-lg-4 my-auto order-2 order-lg-1 text-sm-right text-lg-left">
                <ul class="footer-nav-list mb-0 ft15">
                    <li>
                        <a href="{{url('/privacy-policy')}}">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="{{url('/terms-conditions')}}">Terms & Conditions</a>
                    </li>
                    <li>
                        <a href="{{url('/help-desk')}}">Help Desk</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="user_loader" class="user_loader" style="display: none;">
        <div class="loader-overlay">
            <div class="loading-gif"></div>
        </div>
    </div>
</footer>
@include('users.layouts.js_footer')