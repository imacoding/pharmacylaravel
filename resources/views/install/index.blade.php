@include('install.header')
<div class="dashboard-page system-setup-page">
    <div class="container">
        <div class="setup-div-outer">

            <div class="setup-single-div br10 mb20 typ2">
                {{--Collapase One starts--}}
                <div class="single-div-innr typ2">
                    <div class="accordion accordion-paymnt-mode" id="accordion-database-mode">
                        <div class="card" id="card1">
                            <div class="card-head" id="headingOne">
                                <ul class="acc-ul" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <li>Database Information</li>
                                    <?php
                                    $db = Config::get('database.connections.'.Config::get('database.default'))
                                    ?>
                                    <input type="hidden" id="db_name" value="<?php echo $db['database'] ; ?>"/>

                                    <?php
                                    $table = 0;
                                    if (Schema::hasTable('settings') || (Schema::hasTable('settings') && \App\Models\Setting::where('is_active', 1)->count())) {
                                        $table = 1;
                                    }
                                    ?>
                                    <input type="hidden" id="table_exists" value="<?php echo $table ; ?>"/>
                                </ul>
                                <div class="completed-tick-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26.098" height="26.098" viewBox="0 0 26.098 26.098">
                                        <defs>
                                            <style>
                                                .a {
                                                    fill: #73d581;
                                                }
                                            </style>
                                        </defs>
                                        <path class="a" d="M3.375,16.424A13.049,13.049,0,1,0,16.424,3.375,13.047,13.047,0,0,0,3.375,16.424ZM19.07,17.906l-7.651-7.842a1.211,1.211,0,0,1,1.713-1.713l8.5,8.758a1.209,1.209,0,0,1,.038,1.669l-4.081,4.1a1.209,1.209,0,1,1-1.713-1.706Z" transform="translate(29.473 -3.375) rotate(90)"></path>
                                    </svg>
                                </div>
                            </div>

                            <div id="collapseOne" class="collapse {{(Schema::hasTable('settings') && \App\Models\Setting::where('is_active', 1)->count()) ? 'collapsed' : ''}}" aria-labelledby="headingOne" data-parent="#accordion-database-mode">
                                <div class="card-body">
                                    <div class="web-details-div">
                                        <p>Please use the below buttons once your database and database user credentials are added on the .env file</p>
                                        <div class="alert" role="alert" id="db_alert"></div>
                                        <p class="common-success">{{$migrated = (Schema::hasTable('settings') && \App\Models\Setting::where('is_active', 1)->count()) ? 'No need to run this step. Migrations are already exists.' : ''}} </p>
                                        <div class="double-btn-outer">
                                            <div class="double-btn-wrap">
                                                <button id="run_migration" class="update-app-btn" disabled="{{$migrated ? 'disabled' : ''}}">Run Migration</button>
                                                <button id="run_seeder" class="update-app-btn" disabled="{{$migrated ? 'disabled' : ''}}">Run Seeders</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {{--Collapase One ends--}}

                {{--Collapase Two starts--}}

                <?php
                $app_name = $logo = $info_mail = $phone = $website = $address = $currency = $curr_pos = $email = $mail_password = $mail_address = $mail_name = $port = $host = $driver = $discount = $payment_mode = $transaction_type = '';
                $site_Details = $mail_details = $payment_details = '';
                if (Schema::hasTable('settings')) {
                    $settings = \App\Models\Setting::select('group','value','key')->get()->toArray();
                    if(count($settings) != 0)
                        {
                            $data = [];
                            foreach($settings as $key => $value) {
                                $data[$value['group']][$value['key']] = $value['value'];
                            }
                            if(isset($data['site'])) {
                                $app_name = $data['site']['app_name'];
                                $logo = $data['site']['logo'];
                                $info_mail = $data['site']['mail'];
                                $phone = $data['site']['phone'];
                                $website = $data['site']['website'];
                                $address = $data['site']['address'];
                                $currency = $data['site']['currency'];
                                $curr_pos = $data['site']['curr_position'];
                                $discount = $data['site']['discount'];
                                if($app_name && $logo && $info_mail && $phone && $website && $address && $currency && $curr_pos && $discount) $site_Details = 'completed';
                            }
                            if(isset($data['mail'])) {
                                $email = $data['mail']['username'];
                                $mail_password = $data['mail']['password'];
                                $mail_address = $data['mail']['address'];
                                $mail_name = $data['mail']['name'];
                                $port = $data['mail']['port'];
                                $host = $data['mail']['host'];
                                $driver = $data['mail']['driver'];
                                if($email && $mail_password && $mail_address && $mail_name && $port && $host && $driver) $mail_details = 'completed';
                            }
                            if(isset($data['payment'])) {
                                $payment_mode = $data['payment']['mode'];
                                $transaction_type = $data['payment']['type'];
                                $payment_details =  $payment_mode ? 'completed' : '';
                            }
                        }
                }
                $admin_added = '';
                if (Schema::hasTable('admin')) {
                    if(\App\Models\Admin::count() > 0) $admin_added = true;
                }
                ?>

                <div class="single-div-innr typ2">
                    <div class="accordion" id="accordion-prescription">
                        <div class="card {{$site_Details}}" id="card2">
                            <div class="card-head" id="headingTwo">
                                <ul class="acc-ul" data-toggle="collapse" data-target="{{($table) ? '#collapseTwo' : ''}}" aria-expanded="true" aria-controls="collapseTwo">
                                    <li>Website Details</li>
                                </ul>
                                <div class="completed-tick-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26.098" height="26.098" viewBox="0 0 26.098 26.098">
                                        <defs>
                                            <style>
                                                .a {
                                                    fill: #73d581;
                                                }
                                            </style>
                                        </defs>
                                        <path class="a" d="M3.375,16.424A13.049,13.049,0,1,0,16.424,3.375,13.047,13.047,0,0,0,3.375,16.424ZM19.07,17.906l-7.651-7.842a1.211,1.211,0,0,1,1.713-1.713l8.5,8.758a1.209,1.209,0,0,1,.038,1.669l-4.081,4.1a1.209,1.209,0,1,1-1.713-1.706Z" transform="translate(29.473 -3.375) rotate(90)"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="collapseTwo" class="collapse {{$site_Details ? 'collapsed' : ''}}" aria-labelledby="headingTwo" data-parent="#accordion-prescription">
                                <div class="card-body">
                                    <div class="web-details-div">
                                        <div class="form-head">
                                            <h5 class="mb-0">Customize your App</h5>
                                            <div class="logo-preview-wrp">
                                                <a href="javascript:void(0)" class="{{empty($logo) ? 'd-none' : ''}}" data-toggle="modal" data-target="#preview-modal">Logo preview</a>
                                            </div>
                                        </div>
                                        <form id="create-setting-form" enctype="multipart/form-data" class="website-details-form">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token (); ?>">
                                            <div class="row">
                                                <div class="col">
                                                    <label>App Name</label>
                                                    <input type="text" id="app_name" name="name" value="{{$app_name ?? ''}}" class="form-control" placeholder="App name">
                                                </div>
                                                <div class="col type2 fileupload">
                                                    <label>Select Logo (Size 240px X 60px)</label>
                                                    <div class="wrap">
                                                        <div class="file">
                                                            <div class="file__input" id="file__input">
                                                                <label class="file__input--label" for="customFile" data-text-btn="Choose File"></label>
                                                                <input class="file__input--file" id="customFile" type="file" name="logo" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row typ-4">
                                                <div class="col">
                                                    <label>Website URL</label>
                                                    <input type="text" class="form-control" placeholder="Website url" id="website" name="website" value="{{$website ?? ''}}" required="required">
                                                </div>
                                                <div class="col">
                                                    <label>Address</label>
                                                    <input type="text" class="form-control" placeholder="Company location " id="location" name="location" value="{{$address ?? ''}}" required="required">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>Info Mail : (Customer request will be sent to this mail)</label>
                                                    <input type="text" class="form-control" placeholder="Info Email " id="email" name="email" value="{{$info_mail ?? ''}}" required="required">
                                                </div>
                                                <div class="col">
                                                    <label>Phone Number</label>
                                                    <input type="text" class="form-control" maxlength="13" minlength="10" onkeypress="return NumberOnly(event)" placeholder="Phone Number" id="phone_no" name="phone" value="{{$phone ?? ''}}" required="required">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>Currency</label>
                                                    <input type="text" class="form-control" placeholder="Currency to be displayed with amount" id="currency" name="currency" value="{{$currency ?? ''}}" required="required">
                                                </div>
                                                <div class="col">
                                                    <label>Currency Position (before /after the Amount)</label>
                                                    <?php if(isset($curr_pos)) { ?>
                                                    <div class="select-wrp">
                                                        <select class="form-select" name="curreny_position">
                                                            <option value="BEFORE"
                                                            <?php if ($curr_pos == CURRENCY_BEFORE) echo "selected"; ?>> Before
                                                            </option>
                                                            <option value="AFTER" <?php if ($curr_pos == CURRENCY_AFTER) echo "selected"; ?>>
                                                                After
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <?php
                                                        }
                                                        ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>Time Zone</label>
                                                    <div class="select-wrp">
                                                        <select class="form-select" id="timezone" name="timezone">
                                                            <option value="UTC">UTC (default)</option>
                                                            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                                                            <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
                                                            <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
                                                            <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
                                                            <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
                                                            <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                                                            <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
                                                            <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
                                                            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                                                            <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
                                                            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                                            <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
                                                            <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
                                                            <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                                            <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
                                                            <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
                                                            <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                                                            <option value="America/Havana">(GMT-05:00) Cuba</option>
                                                            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                                                            <option value="America/Caracas">(GMT-04:30) Caracas</option>
                                                            <option value="America/Santiago">(GMT-04:00) Santiago</option>
                                                            <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                                                            <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
                                                            <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
                                                            <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
                                                            <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
                                                            <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                                                            <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
                                                            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                                                            <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
                                                            <option value="America/Godthab">(GMT-03:00) Greenland</option>
                                                            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                                                            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                                                            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                                                            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                                                            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                                                            <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
                                                            <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
                                                            <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
                                                            <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
                                                            <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
                                                            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna
                                                            </option>
                                                            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague
                                                            </option>
                                                            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                                                            <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                                                            <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
                                                            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                                                            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                                                            <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
                                                            <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
                                                            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                                                            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                                                            <option value="Asia/Damascus">(GMT+02:00) Syria</option>
                                                            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                                                            <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
                                                            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                                                            <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                                                            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                                                            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                                                            <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
                                                            <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                                                            <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                                                            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                                                            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                                                            <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
                                                            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                                                            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                                                            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                                                            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                                                            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                                                            <option value="Australia/Perth">(GMT+08:00) Perth</option>
                                                            <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
                                                            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                                                            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                                                            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                                                            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                                                            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                                                            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                                                            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                                                            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                                                            <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
                                                            <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
                                                            <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
                                                            <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
                                                            <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
                                                            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                                                            <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                                            <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
                                                            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                                                            <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col discount-sec">
                                                    <label>Default Discount Amount</label>
                                                    <input type="text" class="form-control" placeholder="Discount Amount" id="discount" name="discount" value="{{$discount ?? ''}}">
                                                    <span class="disclamer">*This discount will be applied for each item</span>
                                                </div>
                                            </div>
                                            <div class="double-btn-outer">
                                                <div class="double-btn-wrap">
                                                    <button class="update-app-btn" id="update_site">Update</button>
                                                    <button class="continue-btn {{$site_Details ? '' : 'd-none'}}" id="continue_to_mail">Continue</button>
                                                </div>
                                            </div>

                                            <div class="alert" role="alert" id="app_alert"></div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {{--Collapase Two ends--}}
                <div class="single-div-innr typ2">
                    <div class="accordion" id="accordion-email-client">
                        <div class="card {{$mail_details}}" id="card3">
                            <div class="card-head" id="headingThree">
                                <ul class="acc-ul" data-toggle="collapse" data-target="{{($table && $site_Details) ? '#collapseThree' : ''}}" aria-expanded="true" aria-controls="collapseThree">
                                    <li>Email Client</li>
                                </ul>
                                <div class="completed-tick-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26.098" height="26.098" viewBox="0 0 26.098 26.098">
                                        <defs>
                                            <style>
                                                .a {
                                                    fill: #73d581;
                                                }
                                            </style>
                                        </defs>
                                        <path class="a" d="M3.375,16.424A13.049,13.049,0,1,0,16.424,3.375,13.047,13.047,0,0,0,3.375,16.424ZM19.07,17.906l-7.651-7.842a1.211,1.211,0,0,1,1.713-1.713l8.5,8.758a1.209,1.209,0,0,1,.038,1.669l-4.081,4.1a1.209,1.209,0,1,1-1.713-1.706Z" transform="translate(29.473 -3.375) rotate(90)"></path>
                                    </svg>
                                </div>
                            </div>

                            <div id="collapseThree" class="collapse {{$mail_details ? 'collapsed' : ''}}" aria-labelledby="headingThree" data-parent="#accordion-email-client">
                                <div class="card-body">
                                    <div class="web-details-div">
                                        <div class="form-head">
                                            <h5>Configure your email configuration, This is mandatory for the system to work</h5>
                                            <div class="logo-preview-wrp">
                                                <a href="javascript:void(0)" class="{{empty($mail_details) ? 'd-none' : ''}}" data-toggle="modal" data-target="#test-mail">Test Mail</a>
                                            </div>
                                        </div>
                                        <form class="website-details-form" id="frmMailSettings">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token (); ?>">

                                            <div class="row">
                                                <div class="col">
                                                    <label>Mail driver</label>
                                                    <input type="text" class="form-control" id="driver" name="driver" placeholder="Eg ., smtp,sendmail,mandrill" value="{{$driver ?? ''}}">
                                                </div>
                                                <div class="col">
                                                    <label>Mail Id(Mail will be sent from this account)</label>
                                                    <input type="text" class="form-control" id="mail_id" name="mail_id" placeholder="Mail User Name" value="{{$email ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>From Address</label>
                                                    <input type="text" class="form-control" id="from_address" name="from_address" placeholder="From address" value="{{$mail_address ?? ''}}">
                                                </div>
                                                <div class="col">
                                                    <label>Mail Port</label>
                                                    <input type="text" class="form-control" id="port" name="port" placeholder="587" value="{{$port ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>Mail Host</label>
                                                    <input type="text" class="form-control" id="host" name="host" placeholder="smtp.gmail.com" value="{{$host ?? ''}}">
                                                </div>
                                                <div class="col">
                                                    <label>Password</label>
                                                    <input type="password" class="form-control" id="mail_password" name="mail_password" placeholder="Password" value="{{$mail_password ?? ''}}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <label>From Name</label>
                                                    <input type="text" class="form-control" id="from_name" name="from_name" placeholder="From Name" value="{{$mail_name ?? ''}}">
                                                </div>
                                            </div>

                                            <div class="double-btn-outer">
                                                <div class="double-btn-wrap">
                                                    <button class="update-app-btn" id="update_email">Update</button>
                                                    <button class="continue-btn {{$mail_details ? '' : 'd-none'}}" id="continue_to_payment">Continue</button>
                                                </div>
                                            </div>

                                            <div class="alert" role="alert" id="email_alert"></div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if (Schema::hasTable('payment_gateways')) {
                    $payment_gateways = \App\Models\PaymentGateway::all ();
                    $gateway_setting_list = "";
                }
                ?>
                <div class="single-div-innr typ2">
                    <div class="accordion accordion-paymnt-mode" id="accordion-paymnt-mode">
                        <div class="card {{$payment_details}}" id="card4">
                            <div class="card-head" id="headingFour">
                                <ul class="acc-ul" data-toggle="collapse" data-target="{{($table && $mail_details && $site_Details) ? '#collapseFour' : ''}}" aria-expanded="true" aria-controls="collapseFour">
                                    <li>Payment Mode</li>
                                </ul>
                                <div class="completed-tick-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26.098" height="26.098" viewBox="0 0 26.098 26.098">
                                        <defs>
                                            <style>
                                                .a {
                                                    fill: #73d581;
                                                }
                                            </style>
                                        </defs>
                                        <path class="a" d="M3.375,16.424A13.049,13.049,0,1,0,16.424,3.375,13.047,13.047,0,0,0,3.375,16.424ZM19.07,17.906l-7.651-7.842a1.211,1.211,0,0,1,1.713-1.713l8.5,8.758a1.209,1.209,0,0,1,.038,1.669l-4.081,4.1a1.209,1.209,0,1,1-1.713-1.706Z" transform="translate(29.473 -3.375) rotate(90)"></path>
                                    </svg>
                                </div>
                            </div>

                            <div id="collapseFour" class="collapse {{$payment_details ? 'collapsed' : ''}}" aria-labelledby="headingFour" data-parent="#accordion-paymnt-mode">
                                <div class="card-body">
                                    <div class="web-details-div">
                                        <div class="form-head">
                                            <h5>Configure your payment configuration . This is mandatory for the system to make payment .</h5>

                                        </div>
                                        <form class="website-details-form" id="frmPaymentSettings">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token (); ?>">
                                            <div class="row">
                                                <div class="payment-gateway">
                                                    <p>Select Payment Gateways:</p>
                                                    <?php
                                                    if(isset($payment_gateways))
                                                        {
                                                    ?>
                                                    <div class="custom02 payment-type-outr">
                                                        <?php
                                                        $gateway_divs = "";
                                                        foreach ($payment_gateways as $gateway) { ?>
                                                            <div class="input-radio-outer">
                                                            <label>
                                                            <input type="radio" class="radio-control form-control pay_mode" id="radio03-02" name="pay_mode" value="<?php echo $gateway->id ?>" data-id="payment<?php echo $gateway->id; ?>" <?php if ($payment_mode == $gateway->id) { ?> checked <?php } ?> />
                                                            <img class="ml-2" src="{{url('public/assets/images').'/'.$gateway->image}}" alt="<?php echo ucfirst ($gateway->name); ?>"/>
                                                            </label>
                                                            </div>
                                                            <?php
                                                        
                                                            $gateway_setting = \App\Models\PaymentGatewaySetting::where('gateway_id',$gateway->id)->get();

                                                                $gateway_setting_list = '<div class="payment_class" id="payment' . $gateway->id . '" style="display:' . (($payment_mode == $gateway->id) ? 'block' : 'none') . '">';
                                                            foreach($gateway_setting as $setting){

                                                                $gateway_setting_list .= '<div class="row">';
                                                                $gateway_setting_list .='<div class="col">';
                                                                $gateway_setting_list .='<label>' . ucfirst ($gateway->name) . ' ' . $setting->description . ':</label>';

                                                                switch ($setting->type) {
                                                                    case 'TEXT':
                                                                    case 'EMAIL':
                                                                        $gateway_setting_list .= '<input class="form-control" type="' . $setting->type . '"  name="' . $setting->key . '" placeholder="' . ucfirst ($gateway->name) . ' ' . $setting->description . '" value="' . $setting->value . '" />';
                                                                        break;
                                                                    case 'SELECT':
                                                                        $data_set = unserialize ($setting->dataset);
                                                                        $gateway_setting_list .= '<div class="select-wrp">';
                                                                        $gateway_setting_list .= '<select class="form-select" id="currency" name="' . $setting->key . '">';
                                                                        foreach ($data_set as $key => $value) {
                                                                            $gateway_setting_list .= '<option value="' . $key . '" ' . ($setting->value == $key ? 'selected highlighted' : '') . '>"' . $value . '"(' . $key . ')</option>';
                                                                        }
                                                                        $gateway_setting_list .= '</select>';
                                                                        $gateway_setting_list .='</div>';
                                                                        break;
                                                                }

                                                                $gateway_setting_list .='</div>';
                                                                $gateway_setting_list .='</div>';
                                                            }
                                                            $gateway_setting_list .= '</div>';
                                                            $gateway_divs .= $gateway_setting_list;


                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                        }

                                                        ?>
                                                </div>
                                            </div>
                                            <?php
                                            if(isset($transaction_type))
                                            {
                                            ?>
                                            <div class="row">
                                               <div class="col">
                                                    <label>Transaction Mode</label>
                                                    <div class="select-wrp">
                                                        <select class="form-select" id="transaction_type" name="transaction_type">
                                                            <option
                                                                    value="TEST" <?php if ($transaction_type == 'TEST') echo "selected"; ?>>
                                                                TESTING
                                                            </option>
                                                            <option
                                                                    value="LIVE" <?php if ($transaction_type == 'LIVE') echo "selected"; ?>>
                                                                LIVE
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            }

                                            ?>
                                            {!! $gateway_divs ?? '' !!}
                                            <div class="double-btn-outer">
                                                <div class="double-btn-wrap">
                                                    <button class="update-app-btn" id="payment_update">Update</button>
                                                    <button class="continue-btn {{$payment_details ? '' : 'd-none'}}" id="continue_to_admin">Continue</button>
                                                </div>
                                            </div>

                                            <div class="alert" role="alert" id="payment_alert"></div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-div-innr typ2">
                    <div class="accordion accordion-paymnt-mode" id="accordion-admin-mode">
                        <div class="card {{$admin_added ? 'completed' : '' }}" id="card5">
                            <div class="card-head" id="headingFive">
                                <ul class="acc-ul" data-toggle="collapse" data-target="{{($table && $mail_details && $payment_details && $site_Details) ? '#collapseFive' : ''}}" aria-expanded="true" aria-controls="collapseFive">
                                    <li>Admin Credentials</li>
                                </ul>
                                <div class="completed-tick-mark">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26.098" height="26.098" viewBox="0 0 26.098 26.098">
                                        <defs>
                                            <style>
                                                .a {
                                                    fill: #73d581;
                                                }
                                            </style>
                                        </defs>
                                        <path class="a" d="M3.375,16.424A13.049,13.049,0,1,0,16.424,3.375,13.047,13.047,0,0,0,3.375,16.424ZM19.07,17.906l-7.651-7.842a1.211,1.211,0,0,1,1.713-1.713l8.5,8.758a1.209,1.209,0,0,1,.038,1.669l-4.081,4.1a1.209,1.209,0,1,1-1.713-1.706Z" transform="translate(29.473 -3.375) rotate(90)"></path>
                                    </svg>
                                </div>
                            </div>

                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion-admin-mode">
                                <div class="card-body">
                                    <div class="web-details-div">
                                        <div class="form-head">
                                            <h5>Create the site admin here</h5>
                                            <div class="logo-preview-wrp">
                                                <a href="{{url('admin-login')}}" class="{{$admin_added ? '' : 'd-none'}}" target="_blank">Goto Admin panel</a>
                                            </div>
                                        </div>
                                        <form class="website-details-form" id="create-admin-form">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token (); ?>">

                                            <div class="row">
                                                <div class="col">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control required-input" placeholder="Admin's Email" id="admin_email" name="email" value="" required="required">
                                                </div>
                                                <div class="col">
                                                    <label>User Name</label>
                                                    <input type="text" class="form-control required-input" placeholder="User name" id="admin_name" name="name" value="" required="required">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <label>Password</label>
                                                    <input type="password" class="form-control required-input" placeholder="password" id="admin_password" name="password" required="required">
                                                </div>
                                                <div class="col">
                                                    <label>Re-enter Password</label>
                                                    <input type="password" class="form-control required-input" placeholder="re enter password" id="re_password" name="re-password" required="required">
                                                </div>
                                            </div>

                                            <div class="double-btn-outer">
                                                <div class="double-btn-wrap">
                                                    <button class="update-app-btn" id="create_user">Done</button>
                                                </div>
                                            </div>

                                            <div class="alert" role="alert" id="admin_alert"></div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<!-- preview modal  -->
<div class="modal fade preview-modal" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php
                $exists = (!empty($logo) && Storage::disk('SYSTEM_IMAGE_URL')->exists($logo)) ? true : false;
            ?>
                <img class="{{$exists ? '' : 'd-none'}}" src= "<?php echo SYSTEM_IMAGE_URL . $logo; ?>" alt="preview-image">
                <p class="common-error {{$exists ? 'd-none' : ''}}">Sorry image doesn't exists in your system. Please upload a new one.</p>
            
            </div>

        </div>
    </div>
</div>
<div class="modal fade login-signin-popup" id="test-mail" tabindex="-1" role="dialog" aria-labelledby="test-mail" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
             <form action="{{ url('') }}" method="POST" class="form-horizontal" role="form" id="test-mail-form">
    @csrf
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="text" name="email" id="email" placeholder="Enter max 3 mails (example: ex@gm.com, ex1@gm.in)" class="form-control login_mail" aria-describedby="emailHelp">
    </div>
    <button type="submit" class="btn btn-primary w-100">Send Mail</button>
</form>

            </div>

        </div>
    </div>
</div>
@push('js')
<script src="{{url('assets/js/install/system-setup.js')}}" type="text/javascript"></script>
@endpush
<!-- preview modal end  -->
@include('install.footer')
</body>

</html>

