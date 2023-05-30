                <?php
                    $user_data = userDetails('dash-side-header');
                ?>
                <div class="left_side_wrppr-outr">
                    <div class="left_side_wrppr">
                        <div class="profile-wrp">
                            <div class="profile-pic-wrp">
                                <div class="profile-pic">
                                    <img src="{{ $user_data['profile_pic'] }}">
                                </div>
                                @if (Request::is('account-page'))
                                    <div class="prof-edit">
                                        <input type="file" id="profile_pic" style="display:none;" accept=".jpeg,.jpg,.png,.gif" onchange="readURL(this);">
                                        <a href="javascript:void(0)" id="upload-pic" alt="edit-profile"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 70 70">
                                                <defs>
                                                    <style>
                                                        .a7 {
                                                            opacity: 0.08;
                                                        }

                                                        .b7 {
                                                            fill: #fff;
                                                        }
                                                    </style>
                                                </defs>
                                                <g transform="translate(-480 -225)">
                                                    <circle class="a7" cx="20" cy="20" r="20" transform="translate(495 240)" />
                                                    <circle class="b7" cx="20" cy="20" r="20" transform="translate(495 240)" />
                                                    <g transform="translate(12031.445 -1145.83)">
                                                        <path d="M7.585,13.7a3.431,3.431,0,1,0,3.431-3.431A3.431,3.431,0,0,0,7.585,13.7ZM18.4,7.9H14.71c-.264-1.056-.528-2.111-1.583-2.111H8.9C7.849,5.784,7.585,6.84,7.321,7.9H3.626A1.059,1.059,0,0,0,2.571,8.951v9.5a1.059,1.059,0,0,0,1.056,1.056H18.4a1.059,1.059,0,0,0,1.056-1.056v-9.5A1.059,1.059,0,0,0,18.4,7.9Zm-7.389,10.49A4.684,4.684,0,1,1,15.7,13.7,4.684,4.684,0,0,1,11.015,18.385ZM18.4,11.062H16.293V10.006H18.4Z" transform="translate(-11527.016 1393.046)" />
                                                    </g>
                                                </g>
                                            </svg>                                    
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="pro_contnt">
                                <div class="pro_name">
                                    <p>{{$user_data['name']}}</p>
                                </div>
                                <div class="pro_mail">
                                    <p>{{ $user_data['email']}}</p>
                                </div>
                                <div class="pro_mob">
                                    <p>{{ $user_data['phone'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard_tab-outr">
                            <div class="dashboard_tab">
                                <ul>
                                    <li class="{{ Request::is('account-page') ? 'active' : '' }}">
                                        <a href="{{ url('/account-page') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a,
                                                            .c,
                                                            .d {
                                                                fill: none;
                                                            }

                                                            .b {
                                                                clip-path: url(#a);
                                                            }

                                                            .c {
                                                                stroke: #000;
                                                                stroke-width: 1.5px;
                                                            }

                                                            .e,
                                                            .f {
                                                                stroke: none;
                                                            }

                                                            .f {
                                                                fill: #000;
                                                            }
                                                        </style>
                                                        <clipPath id="a">
                                                            <rect class="a" width="23" height="21" transform="translate(0.207)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b" transform="translate(-0.207)">
                                                        <g transform="translate(2.117 0.934)">
                                                            <g class="c">
                                                                <circle class="e" cx="9.566" cy="9.566" r="9.566" />
                                                                <circle class="a" cx="9.566" cy="9.566" r="8.816" />
                                                            </g>
                                                            <g class="c" transform="translate(5.5 4)">
                                                                <circle class="e" cx="4.066" cy="4.066" r="4.066" />
                                                                <circle class="a" cx="4.066" cy="4.066" r="3.316" />
                                                            </g>
                                                            <g class="d" transform="translate(2.733 11)">
                                                                <path class="e" d="M0,5.261a7.068,7.068,0,0,1,13.667,0A9.538,9.538,0,0,1,6.833,8.132,9.538,9.538,0,0,1,0,5.261Z" />
                                                                <path class="f" d="M 6.833251953125 6.632399559020996 C 8.715198516845703 6.632399559020996 10.49384498596191 5.994756698608398 11.92873001098633 4.820428371429443 C 11.0521993637085 2.829092502593994 9.064831733703613 1.499999761581421 6.833251953125 1.499999761581421 C 4.601672172546387 1.499999761581421 2.61430287361145 2.829096794128418 1.737772226333618 4.820439338684082 C 3.172651052474976 5.994765281677246 4.951296806335449 6.632399559020996 6.833251953125 6.632399559020996 M 6.833251953125 8.132399559020996 C 4.156611919403076 8.132399559020996 1.736331939697266 7.032609939575195 1.95312509276846e-06 5.260689735412598 C 0.7979719638824463 2.232949733734131 3.554931879043579 -2.20489496882692e-07 6.833251953125 -2.20489496882692e-07 C 10.111572265625 -2.20489496882692e-07 12.86853218078613 2.232939720153809 13.66650199890137 5.260679721832275 C 11.93017196655273 7.032599925994873 9.509891510009766 8.132399559020996 6.833251953125 8.132399559020996 Z" />
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            My profile
                                        </a>
                                    </li>
                                    <li class="{{ Request::is('my-cart') ? 'active' : '' }}">
                                        <a href="{{ url('/my-cart') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a1 {
                                                                fill: #e8e8e8;
                                                            }

                                                            .b1 {
                                                                clip-path: url(#a1);
                                                            }

                                                            .c1 {
                                                                fill: none;
                                                                stroke: #37393e;
                                                                stroke-linecap: round;
                                                                stroke-linejoin: round;
                                                                stroke-width: 1.5px;
                                                            }
                                                        </style>
                                                        <clipPath id="a1">
                                                            <rect class="a1" width="23" height="21" transform="translate(343 477)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b1" transform="translate(-343 -477)">
                                                        <g transform="translate(343.786 477.5)">
                                                            <path class="c1" d="M13.675,30.838A.838.838,0,1,1,12.838,30,.838.838,0,0,1,13.675,30.838Z" transform="translate(-4.637 -12.585)" />
                                                            <path class="c1" d="M30.175,30.838A.838.838,0,1,1,29.338,30,.838.838,0,0,1,30.175,30.838Z" transform="translate(-11.923 -12.585)" />
                                                            <path class="c1" d="M1.5,1.5H4.85L7.1,12.716A1.675,1.675,0,0,0,8.77,14.064h8.142a1.675,1.675,0,0,0,1.675-1.349l1.34-7.028H5.688" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            My cart
                                        </a>

                                    </li>
                                    <li class="{{ Request::is('prescriptions') ? 'active' : '' }}">
                                        <a href="{{ url('/prescriptions') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a2 {
                                                                fill: #e8e8e8;
                                                            }

                                                            .b2 {
                                                                clip-path: url(#a2);
                                                            }

                                                            .c2,
                                                            .d2,
                                                            .f2 {
                                                                fill: none;
                                                            }

                                                            .c2,
                                                            .d2 {
                                                                stroke: #37393e;
                                                                stroke-width: 1.5px;
                                                            }

                                                            .d2 {
                                                                stroke-linecap: round;
                                                            }

                                                            .e2 {
                                                                stroke: none;
                                                            }
                                                        </style>
                                                        <clipPath id="a2">
                                                            <rect class="a2" width="23" height="21" transform="translate(343 523)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b2" transform="translate(-343 -523)">
                                                        <g transform="translate(346.91 522.642)">
                                                            <g class="c2" transform="translate(-0.41 1.358)">
                                                                <rect class="e2" width="16" height="19" rx="2" />
                                                                <rect class="f2" x="0.75" y="0.75" width="14.5" height="17.5" rx="1.25" />
                                                            </g>
                                                            <path class="d2" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -734.15)" />
                                                            <path class="d2" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -731.149)" />
                                                            <path class="d2" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -728.147)" />
                                                            <path class="d2" d="M2137.417,740.368h5.714" transform="translate(-2134.358 -725.146)" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            Prescription
                                        </a>

                                    </li>
                                    <li class="{{ Request::is('awaiting-shipment') ? 'active' : '' }}">
                                        <a href="{{ url('/awaiting-shipment') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a3 {
                                                                fill: #e8e8e8;
                                                            }

                                                            .b3 {
                                                                clip-path: url(#a3);
                                                            }

                                                            .c3 {
                                                                fill: none;
                                                                stroke: #37393e;
                                                                stroke-linecap: round;
                                                                stroke-linejoin: round;
                                                                stroke-width: 1.5px;
                                                            }
                                                        </style>
                                                        <clipPath id="a3">
                                                            <rect class="a3" width="23" height="21" transform="translate(367 629)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b3" transform="translate(-367 -629)">
                                                        <g transform="translate(361.906 628)">
                                                            <path class="c3" d="M29,5.8V2h2.532V5.8" transform="translate(-13.67)" />
                                                            <path class="c3" d="M19,18.6v-4a.6.6,0,0,1,.6-.6h7.654a.6.6,0,0,1,.6.6v4" transform="translate(-6.835 -8.202)" />
                                                            <path class="c3" d="M11,23.164,17.963,21.9l6.963,1.265M16.064,20m-4.071,2.985a34.536,34.536,0,0,0,2.17,8.75m9.766-8.75a34.535,34.535,0,0,1-2.17,8.75m-3.8-9.836v9.493" transform="translate(-1.367 -12.302)" />
                                                            <path class="c3" d="M9,55.994a1.9,1.9,0,0,0,3.8.026,1.9,1.9,0,0,0,3.8-.026m.005,0a1.9,1.9,0,0,0,3.8.026,1.9,1.9,0,0,0,3.8-.026" transform="translate(0 -36.905)" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            Awaiting shipment
                                        </a>
                                    </li>
                                    <li class="{{ Request::is('shipped-order') ? 'active' : '' }}">
                                        <a href="{{ url('/shipped-order') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a4 {
                                                                fill: #e8e8e8;
                                                            }

                                                            .b4 {
                                                                clip-path: url(#a4);
                                                            }

                                                            .c4,
                                                            .d4,
                                                            .f4 {
                                                                fill: none;
                                                            }

                                                            .c4,
                                                            .d4 {
                                                                stroke: #37393e;
                                                                stroke-width: 1.5px;
                                                            }

                                                            .d4 {
                                                                stroke-linecap: round;
                                                            }

                                                            .e4 {
                                                                stroke: none;
                                                            }
                                                        </style>
                                                        <clipPath id="a4">
                                                            <rect class="a4" width="23" height="21" transform="translate(341 618)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b4" transform="translate(-341 -618)">
                                                        <g transform="translate(344.91 618.642)">
                                                            <g class="c4" transform="translate(-0.41 1.358)">
                                                                <rect class="e4" width="16" height="19" rx="2" />
                                                                <rect class="f4" x="0.75" y="0.75" width="14.5" height="17.5" rx="1.25" />
                                                            </g>
                                                            <g transform="translate(4.796 0.785)">
                                                                <path class="d4" d="M2138.725,734v2.387" transform="translate(-2138.725 -734)" />
                                                                <path class="d4" d="M2138.725,734v2.387" transform="translate(-2135.55 -734)" />
                                                                <path class="d4" d="M2138.725,734v2.387" transform="translate(-2132.375 -734)" />
                                                            </g>
                                                            <path class="d4" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -734.15)" />
                                                            <path class="d4" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -731.149)" />
                                                            <path class="d4" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -728.147)" />
                                                            <path class="d4" d="M2137.417,740.368h9.063" transform="translate(-2134.358 -725.146)" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            Shipped order
                                        </a>
                                    </li>
                                    <li class="{{ Request::is('logout') ? 'active' : '' }}">
                                        <a href="{{ route('user.logout') }}">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="23" height="21" viewBox="0 0 23 21">
                                                    <defs>
                                                        <style>
                                                            .a5 {
                                                                fill: #e8e8e8;
                                                            }

                                                            .b5 {
                                                                clip-path: url(#a5);
                                                            }

                                                            .c5 {
                                                                fill: none;
                                                                stroke: #37393e;
                                                                stroke-linecap: round;
                                                                stroke-linejoin: round;
                                                                stroke-width: 1.5px;
                                                            }
                                                        </style>
                                                        <clipPath id="a5">
                                                            <rect class="a5" width="23" height="21" transform="translate(345 728)" />
                                                        </clipPath>
                                                    </defs>
                                                    <g class="b5" transform="translate(-345 -728)">
                                                        <g transform="translate(349 730.616)">
                                                            <path class="c5" d="M4.5,20.267H8a1.752,1.752,0,0,0,1.752-1.752V6.252A1.752,1.752,0,0,0,8,4.5H4.5" transform="translate(6.011 -4.5)" />
                                                            <path class="c5" d="M28.38,19.259,24,14.88l4.38-4.38" transform="translate(-24 -6.996)" />
                                                            <path class="c5" d="M13.5,18H24.011" transform="translate(-13.5 -10.116)" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                            Log out
                                        </a>
                                    </li>
                                </ul>
                                <div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>