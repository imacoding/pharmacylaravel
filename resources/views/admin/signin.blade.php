<!DOCTYPE html>
<html lang="en" class="app">
  <head>
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    @if(Schema::hasTable('settings') && \App\Models\Setting::where('is_active', 1)->count())
      <meta charset="utf-8" />
      <title>{{ \App\Models\Setting::param('site','app_name')['value'] }}</title>
      <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
      <link href="{{ url('assets/adminFiles/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ url('assets/adminFiles/css/animate.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ url('assets/adminFiles/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ url('assets/adminFiles/css/icon.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ url('assets/adminFiles/css/font.css') }}" rel="stylesheet" type="text/css">
      <link href="{{ url('assets/adminFiles/css/app.css') }}" rel="stylesheet" type="text/css"> 
    </head>
    <body class="">
      <?php if(Auth::check() && Auth::user()->user_type_id == \App\Models\UserType::ADMIN()){ ?>
        <script>
          window.location.href = "/admin/dashboard";
        </script>
      <?php }?>
      <div id="content" class="m-t-lg wrapper-md animated fadeInUp" style="height:700px">
        <div class="container aside-xl" >
          <section class="m-b-lg">
            <header class="wrapper text-center">
              <strong>Admin Login</strong>
            </header>
            <div style="">
              @if ( Session::has('flash_message') )
                <div class="alert {{ Session::get('flash_type') }}">
                  <h5 style="text-align: center">{{ Session::get('flash_message') }}</h5>
                </div>
              @endif
              <form action="{{ url('/admin/login') }}" method="POST" id="admin-login" class="form-horizontal admin-login">
                @csrf
                <div class="list-group">
                  <div class="list-group-item form-group">
                    <input class="form-control no-border log_email" type="text" name="log_email" id="log_email" placeholder="Username">
                  </div>
                  <div class="list-group-item form-group">
                    <input type="password" placeholder="Password" id="password" class="form-control no-border password" name="password">
                  </div>
                </div>
                <button type="submit" class="btn btn-lg btn-primary btn-block" style="color:#7C829D">Sign in</button>
                <div class="text-center m-t m-b"><a href="admin/reset"><small>Forgot password?</small></a></div>
                <div class="line line-dashed"></div>
              </form>
            </div>
          </section>
        </div>
      </div>
      <input type="hidden" id='siteurl' value="{{url('')}}">
      <!-- footer -->
      <footer id="footer">
        <div class="text-center padder">
          <p>
            <small>{{ \App\Models\

Setting::param('site','app_name')['value'] }}, An Online Medical Store<br>&copy; <?php echo date("Y"); ?></small>
          </p>
        </div>
      </footer>
      <!-- / footer -->
      <script src="{{ url('assets/adminFiles/js/jquery.min.js') }}"></script>
      <!-- Bootstrap -->
      <script src="{{ url('assets/adminFiles/js/bootstrap.js') }}"></script>
      <!-- App -->
      <script src="{{ url('assets/adminFiles/js/app.js') }}"></script>
      <script type="text/javascript" src="{{ url('assets/js/jquery.validate.min.js') }}"></script>
      <script src="{{ url('assets/adminFiles/js/slimscroll/jquery.slimscroll.min.js') }}"></script>
      <script src="{{ url('assets/adminFiles/js/app.plugin.js') }}"></script>
      <script src="{{ url('assets/adminFiles/js/admin_common.js') }}"></script>
    </body>
  @else
    <div class="alert alert-danger admin-signin-error">No migrations found. Please setup your system for further process.</div>
  @endif
</html>