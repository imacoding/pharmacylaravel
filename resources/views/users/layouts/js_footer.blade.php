
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ url('assets/js/jquery-3.5.1.min.js') }}"></script>

<!-- <script  src="{{ url('/assets/js/vendors/jquery-3.5.1.slim.min.js') }}" crossorigin="anonymous"></script> -->
<script src="{{ url('assets/js/vendors/popper.min.js') }}" crossorigin="anonymous"></script>
<script  src="{{ url('assets/js/vendors/bootstrap.min.js') }}" crossorigin="anonymous"></script>
<!-- Lazyload -->
<script src="{{ url('assets/js/vendors/lazyload-all.js') }}" async=""></script>

<script src="{{ url('assets/js/jquery.validate.js') }}"></script>


<!-- begin::Custom Js files include -->
@stack('js')
<!-- END: Custom Js files include -->
<script src="{{ url('assets/js/jquery-latest.min.js') }}"></script>
<script src="{{ url('assets/js/jquery-3.2.1.min.js') }}"></script>
<script src="{{ url('assets/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/js/jquery-migrate-3.0.0.js') }}"></script>
<script src="{{ url('assets/js/country_code/js/utils.js') }}"></script>
<script src="{{ url('assets/js/country_code/js/intlTelInput.min.js') }}"></script>
<script src="{{ url('assets/js/country_code/js/intlTelInput-jquery.min.js') }}"></script>




<script type="text/javascript" src="{{ url('/assets/js/vendors/selectric.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/assets/js/main.js') }}"></script>
<script>
    function loadJS(u) {
    var r = document.getElementsByTagName("script")[0],
        s = document.createElement("script");
        s.src = u;
        r.parentNode.insertBefore(s, r);
    }
</script>