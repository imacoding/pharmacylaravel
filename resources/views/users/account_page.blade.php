@extends('users.layouts.user_layout')

@section('headerClass','')

@push('css')
@endpush

@section('content')
<div class="dashboard">
    <div class="container">
        <?php
            $user = userDetails('account_page');
        ?>
        <div class="dashboard-main-wrap">
            <!-- dashboard left side start -->
            @include('users.layouts.dashboard-side-bar')
            <!-- dashboard left section end-->

        <!-- dashboard right section -->
        <div class="right_side_wrppr">
            <div class="right-wrapper-innr">
                <div class="dashboard-header">
                    <p>Hi {{ $user['name'] }} ! Welcome back to healwire</p>
                    <h1>Personal Details</h1>
                </div>
                <div class="dashboard-contents">
                    <div class="personal-details-form">
                        <div class="alert" style="display:none"></div>
                        <form method="POST" action="" id="profile-update" class="form-horizontal">
                            @csrf
                            <div class="form-row">
                                <div class="col-6">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="user_name" id="user_name" value="{{ $user['name'] }}">
                                </div>
                                <div class="col-6">
                                    <label>Mobile Number</label>
                                    <div class="input-group mobile-wrap" id='phone-group'>
                                        <input type="tel" id='phone' name="phone" class="form-control" placeholder="Enter mobile number" maxlength="13" minlength="10" onkeypress="return NumberOnly(event)" value="{{ $user['phone'] }}">
                                        <input type="hidden" id='phone_code' name="phone_code" value="{{ $user['phn_code'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-6">
                                    <label>Pincode</label>
                                    <input type="text" class="form-control" placeholder="Pincode"  name="pincode" id="pincode"  value="{{ empty($user['pincode']) ? '' : $user['pincode'] }}">
                                </div>
                                <div class="col-6">
                                    <label for="personal-details">Address</label>
                                    <textarea class="form-control" name="address" id="address"  rows="3">{{ $user['address'] }}</textarea>
                                </div>
                            </div>
                            <div class="button-wrap">
                                <button type="submit" class="btn btn-primary w-100">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- dashboard right section end-->
    </div>
</div>

</div>

@endsection

@push('js')
<script src="{{url('assets/js/common/account_page.js')}}" type="text/javascript"></script>

@endpush