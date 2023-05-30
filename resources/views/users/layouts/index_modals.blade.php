@if(Schema::hasTable('users'))
    <!-- end modal login -->
    <div class="modal fade login-signin-popup" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="icon-close"></span>
            </button>
        </div>
        <div class="modal-body">
                <div class="modal-title-wrap">
                    <h3 class="ft32 fw700">Hey, please login!</h3>
                    <p class="ft18 color-dark-gray pab-20">Sign in by entering the information below</p>
                </div>
                @if (\Session::has('success'))
                    <div class="alert alert-success">{!! \Session::get('success') !!}</div>
                @endif
                @if (\Session::has('failed'))
                    <div class="alert alert-danger">{!! \Session::get('failed') !!}</div>
                @endif
                <p class="alert login_msg" style="display: none; text-align: center"></p>
                  <form action="" method="post" class="form-horizontal addPersonal form-group-sm" role="form" id="login-form">
                    @csrf
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" placeholder="Enter mail address" class="form-control login_mail" aria-describedby="emailHelp">
                        </div>
                        <div class="form-group mb-0" id="pswrd-grp">
                            <label for="exampleInputPassword1">Password</label>
                            <div class="input-group pswd-shw-hide-wrap">
                                <input class="form-control paswd" type="password" id="password" placeholder="Enter password" name="password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-link password-show-hide p-hide"></button>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 text-right pab-20">
                            <button class="btn btn-link dark-link forgot" data-toggle="modal" data-dismiss="modal" data-target="#forgot-password-modal">Forgot password ?</button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Log in</button>
                    </form>
                <p class="ft15 text-center mb-0 pat-15">Don’t have an account ?<button class="btn btn-link text-secondary" data-dismiss="modal" data-toggle="modal" data-target="#signup-modal"><u>Sign up</u></button></p>
        </div>
        </div>
    </div>
    </div>
    <!-- Forgot password modal -->
    <div class="modal fade login-signin-popup forgot-password-modal" id="forgot-password-modal" tabindex="-1" role="dialog" aria-labelledby="forgot-password-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-title-wrap">
                        <h3 class="ft32 fw700">Forget Password</h3>
                        <p class="ft18 color-dark-gray pab-20">Enter your phone number or recovery email</p>
                    </div>
                                <form action="" id="forgot-password" class="form-horizontal" method="POST">
    @csrf
    <div class="form-group mar-b-30">
        <input type="text" class="form-control" name="email" id="email" placeholder="Enter mobile number or email address">
    </div>
    <button type="submit" id="frgt_pswd" class="btn btn-primary w-100">Next</button>
</form>
                </div>
            </div>
        </div>
    </div>
    <!-- OTP verification modal -->
<div class="modal fade login-signin-popup" id="otp-modal" tabindex="-1" role="dialog" aria-labelledby="otp-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-title-wrap">
                    <h3 class="ft32 fw700">Activate your account</h3>
                    <p class="ft18 color-dark-gray">We have sent a security code to your registered email address</p>
                </div>
                <div id="otp-verification" class="form-horizontal otp-verification">
                    <div class="form-group otp-field d-flx">
                        <input id="login_otp_1" type="number" maxlength="1" onkeyup="onKeyUpEvent(1, event)" onfocus="onFocusEvent(1)">
                        <input id="login_otp_2" type="number" maxlength="1" onkeyup="onKeyUpEvent(2, event)" onfocus="onFocusEvent(2)">
                        <input id="login_otp_3" type="number" maxlength="1" onkeyup="onKeyUpEvent(3, event)" onfocus="onFocusEvent(3)">
                        <input id="login_otp_4" type="number" maxlength="1" onkeyup="onKeyUpEvent(4, event)" onfocus="onFocusEvent(4)">
                    </div>
                    <div class="form-group d-flx flx-hcenter">
                        <label class="d-flx flx-vcenter">Didn't receive any code?</label>
                        <button class="btn btn-link resend">Resend</button>
                    </div>
                    <input type="text" class="d-none" id="hidden_user_id">
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" data-toggle="modal" data-target="#new-pass-modal" id="new-pass-trigger">

<!-- Create password modal -->
<div class="modal fade login-signin-popup" id="new-pass-modal" tabindex="-1" role="dialog" aria-labelledby="new-pass-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-title-wrap">
                    <h3 class="ft32 fw700">Create new password</h3>
                    <p class="ft18 color-dark-gray pab-20">Your new password must be different from previously used passwords</p>
                </div>
                @php
                    $reset_email = $security_code = '';
                    if (isset($data) && $data) {
                        $reset_email = $data['email'];
                        $security_code = $data['security_code'];
                    }
                @endphp
                <form action="" id="new-password" class="form-horizontal">
                    <div class="form-group">
                        <label for="new_password">New password</label>
                        <div class="input-group pswd-shw-hide-wrap">
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-link password-show-hide p-hide"></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-5">
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter password">
                    </div>
                    <input type="hidden" name="security_code" value="{{ $security_code }}">
                    <input type="hidden" name="email" value="{{ $reset_email }}">
                    <button type="submit" data-toggle="modal" data-target="#login-error-modal" class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="d-none" id="toggle-modal" data-toggle="modal" data-target="#otp-modal"></button>

<!-- Signup modal -->
<div class="modal fade login-signin-popup signup-modal" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="signup-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-title-wrap">
                    <h3 class="ft32 fw700">Welcome to Healwire</h3>
                    <p class="ft18 color-dark-gray pab-20">Register in Healwire by entering the information below</p>
                </div>
                <form action="" id="user-register" class="form-horizontal" signupform>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_name">Your Name</label>
                                <input type="text" class="form-control" name="user_name" id="user_name" aria-describedby="name" placeholder="Enter your Name">
                                <p style="display: none;" id="user_name_error"></p>
                            </div>
                            <div class="form-group">
                                <label for="phone">Mobile Number</label>
                                <div class="input-group mobile-wrap">
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter mobile number" maxlength="13" minlength="10" onkeypress="return NumberOnly(event)">
                                    <input type="hidden" id="phone_code" name="phone_code">
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="password">Password</label>
                                <div class="input-group pswd-shw-hide-wrap" id="pswrd-grps">
                                    <input type="password" class="form-control" name="password" id="password" minlength="8" placeholder="Enter password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-link password-show-hide p-hide"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label for="user_type">User type</label>
                                <select class="form-control form-control-lg ht-56" id="user_type" name="user_type">
                                    <option value="">Select</option>
                                    @php
                                        $user_type = \App\Models\UserType::users();
                                        foreach ($user_type as $key => $type) {
                                            if ($type != \App\Models\UserType::ADMIN()) {
                                                echo "<option value='$type'>$key</option>";
                                            }
                                        }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" name="email" onblur="CheckUsername(this.value);" id="email" aria-describedby="emailHelp" placeholder="Enter email address">
                            </div>
                            <div class="form-group mb-2">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Enter address"></textarea>
                            </div>
                            <div class="form-check" id="accept_term">
                                <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms">
                                <label class="form-check-label" for="accept_terms">I have read and agree to the <a target="_blank" href="{{ url('/terms-conditions') }}">Terms and Conditions</a></label>
                            </div>
                            <button type="submit" id="register" class="btn btn-primary w-100">Sign Up</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<input type="hidden" data-toggle="modal" data-target="#order-placed-popup" id="triggerMe">

<!-- Place order modal content -->
<div class="modal order-placed-popup fade" id="order-placed-popup" tabindex="-1" role="dialog" aria-labelledby="order-placed-Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="tickmark-wrp">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                        <circle class="path circle" fill="none" stroke="#73D581" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"></circle>
                        <polyline class="path check" fill="none" stroke="#73D581" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "></polyline>
                    </svg>
                    <p class="success">Success!</p>
                </div>
                <p>Your order has been requested successfully.<br>Please track the status in my prescriptions.</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">Ok</button>
            </div>
        </div>
    </div>
</div>


@endif

