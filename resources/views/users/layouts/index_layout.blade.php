   @if (Auth::check() && Auth::user()->user_type_id !== \App\Models\UserType::ADMIN())
        @include('includes.dashboard-header')    
    @else
    
        @include('includes.header')
    @endif
    <!-- BEGIN: Content Dynamically -->
    @yield('content')
    <!-- END: Content -->

    <!-- begin::Footer -->
    @include('includes.footer')
    <!-- END: Footer -->

<!-- begin::Default Js files include -->
@include('users.layouts.js_footer')
<!-- END: Default Js files include -->



</html>