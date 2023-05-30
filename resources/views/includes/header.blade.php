<!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Healwire</title>
    @include('users.layouts.css_header')

  </head>
  <body>  
  <header class="main-header logout-header">
        <input type="hidden" value="{{ url('') }}" id="siteurl" />
        <div class="container h-100 d-flx flx-vcenter">
            <div class="logo">
                <a href="{{ url('/') }}">
                    @if (!empty(logo()))
                        <img src="{{ Storage::disk('SYSTEM_IMAGE_URL')->url(logo()) }}" alt="Healwire">
                    @endif
                </a>
            </div>
            <div class="header-right ml-auto d-flx flx-vcenter">
                <nav class="main-nav">
                    <ul>
                        <li><a class="{{ Request::is('about-us') ? 'active' : '' }} fw600" id="about_head" href="{{ url('/about-us') }}">About us</a></li>
                        <li><a class="{{ Request::is('contact-us') ? 'active' : '' }} fw600" id="contact_head" href="{{ url('/contact-us') }}">Contact us</a></li>
                        @if(Schema::hasTable('users'))
                            <li><a class="nav-btn fw600" id="nav-btn" data-toggle="modal" data-target="#login-modal">Login/Sign Up</a></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        <div class="mob-btn">
			<span></span>
			<span></span>
			<span></span>
	    </div>
	    <div class="overlay"></div>	
    </header>