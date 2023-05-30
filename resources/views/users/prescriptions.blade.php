@extends('users.layouts.user_layout')
@section('headerClass','')

@push('css')
@endpush
@section('content')
<div class="dashboard">
    <div class="container">
        <div class="dashboard-main-wrap">
            <!-- dashboard left side start -->
            @include('users.layouts.dashboard-side-bar')
            <!-- dashboard left section end-->

            <!-- dashboard right section -->

            <div class="right_side_wrppr">
                <div class="right-wrapper-innr">
                    <div class="dashboard-header typ2">
                        <a href="" class="back-bttn">Back</a>
                        <h1>Upload Prescriptions</h1>
                    </div>
                    <div class="dashboard-contents">
                        <div class="mycart-contents">
                            <div class="upload-top-section">
                                <div class="dropzone-outer">
                                    <div class="drop-zone">
                                        <div class="drop-zone__prompt ft14">
                                            <figure>
                                                <img class="mb-2" src="{{ url('assets/images/upload-placeholder2.svg') }}" alt="">
                                            </figure>
                                            <p>
                                                You can use either JPEG, JPG or PNG images.<br>
                                                We will identify the medicines and process your order at the earliest.</p>
                                            <div class="btn-wrp">
                                                <a href="javascript:void(0)" class="browse-btn">Browse files</a>
                                            </div>
                                        </div>
                                        <input type="file" name="myFile" accept='image/*' class="drop-zone__input" multiple>
                                    </div>
                                </div>
                                <div class="upload-file-sec">
                                    <h5 class="d-none">Uploaded files</h5>
                                </div>
                            </div>

                            <div class="prescription-table-outer">
                                <div class="head-select-wrp">
                                    <h2>My Prescriptions</h2>
                                    <div class="select-wrp">
                                        <select class="form-select" onchange="getData();">
                                            <option value="" selected>All</option>
                                            <?php
                                            $status = \App\Models\PrescriptionStatus::status();
                                            $unverified = \App\Models\PrescriptionStatus::UNVERIFIED();

                                            foreach ($status as $key => $type) {
                                                $key = ucwords(strtolower($key));
                                                $selected = ($type == $unverified) ? 'selected highlighted' : '';
                                                echo "<option value='$type' >$key</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="prescription-items-innr">
                                    <div class="prescription-table">
                                        <ul class="item-info-bar">
                                            <li>Date</li>
                                            <li>Status</li>
                                            <li>Prescription</li>
                                        </ul>

                                        <div class="prescription-items">

                                        </div>
                                    </div>
                                    <div class="loader-overlay typ2">
                                        <div class="loading-gif"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- dashboard right section end-->
            </div>
        </div>
    </div>
    <input type='hidden' data-toggle="modal" data-target="#order-placed-popup" id="triggerMe" />
    <!-- place order modal content  -->
    <div class="modal order-placed-popup fade" id="order-placed-popup" tabindex="-1" role="dialog" aria-labelledby="order-placed-Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="tickmark-wrp">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                            <circle class="path circle" fill="none" stroke="#73D581" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                            <polyline class="path check" fill="none" stroke="#73D581" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                        </svg>
                        <p class="success">Success!</p>
                    </div>
                    <p>Your order has been requested successfully. <br>Please track the status in my prescriptions.</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        Ok
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- place order modal content  end-->
    </body>

    @endsection

    @push('js')
    <script src="{{url('assets/js/common/prescription_page.js')}}" type="text/javascript"></script>
    @endpush