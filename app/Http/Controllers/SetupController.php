<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\UserType;
use App\Models\AdminType;
use App\Models\UserStatus;
use App\Models\PaymentGateway;
use App\Models\PaymentGatewaySetting;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Helper\Helper;
use Exception;
use Validator;
use Session;
use Storage;
use Config;
use Image;
use File;
use Mail;

if (!defined('CURRENCY_BEFORE')) define('CURRENCY_BEFORE', 'BEFORE');
if (!defined('CURRENCY_AFTER')) define('CURRENCY_AFTER', 'AFTER');
if (!defined('CACHE_PARAM_SETTINGS')) define('CACHE_PARAM_SETTINGS', 'settings');

date_default_timezone_set ("Asia/Calcutta");
define('SYSTEM_SETTINGS_IMAGE', public_path() . 'assets/images/setting/');
define('SYSTEM_IMAGE_URL', URL::to('/') . 'assets/images/setting/');

class SetupController extends Controller
{
    public function index() {
        return view('install.index');
    }

    public function runMigration(Request $request)
    {

        try {
            define('STDIN', fopen("php://stdin", "r"));
            Artisan::call('migrate:fresh', ['--quiet' => true, '--force' => true]);
            Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);

            return response()->json(['status' => "SUCCESS", 'msg' => 'Database Migrated Successfully'], 201);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode(),'msg'=>'Failure, Please try again']);
        }
    }

    public function runSeeder(Request $request)
    {

        try {
            define('STDIN', fopen("php://stdin", "r"));
            Artisan::call('db:seed', ['--quiet' => true, '--force' => true]);

            return response()->json(['status' => "SUCCESS", 'msg' => 'Database Seeded Successfully'], 201);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode() ,'msg'=>'Failure, Please try again']);
        }
    }
    

    public function addBasicSettings(Request $request)
    {
        try {
            $validatedData = \Validator::make($request->all(), [

                "file"  => "dimensions:max_width=240,max_height=60",

            ]);
            if ($validatedData->fails()){
                return response()->json(['status' => 'FAILURE', 'code' => 401, 'msg' => 'Invalid logo size']);
            }

            $app_name =  $request->get('name', '');
            $email =  $request->get('email', '');
            $location =  $request->get('location', '');
            $website =  $request->get('website', '');
            $phone =  $request->get('phone', '');
            $timezone =  $request->get('timezone', 'UTC');
            $currency =  $request->get('currency', '');
            $curreny_position =  $request->get('curreny_position', 'BEFORE');
            $discount =  $request->get('discount', '0');

                $image = "";
            if ($request->file('file')) {
                $file = $request->file('file', '');
                $extension = strtolower($file->getClientOriginalExtension());
                $existing_logo = Setting::where('group','site')->where('key', 'logo')->pluck('value')->first();
                if (in_array($extension, ['png', 'jpg','jpeg'])) {
                    $image = 'logo.' . $extension;

                    $store_original = $request->file('file')->storeAs('/public/images/setting', $image);
                    $avatar = $request->file('file');
					Image::make($avatar)->resize(25, 25)->save( public_path('/assets/images/'. $image ) );
                  
                } else {
                    throw new Exception('Invalid File Uploaded ! Please upload either png or jpg file', 400);
                }
            }
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'mail'], 'value' => $email];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'address'], 'value' => $location];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'app_name'], 'value' => $app_name];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'website'], 'value' => $website];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'phone'], 'value' => $phone];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'timezone'], 'value' => $timezone];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'currency'], 'value' => $currency];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'curr_position'], 'value' => $curreny_position];
            $conditions[] = ['column' => ['group' => 'site', 'key' => 'discount'], 'value' => $discount];
            if ($image) $conditions[] = ['column' => ['group' => 'site', 'key' => 'logo'], 'value' => $image];
    
            foreach ($conditions as $condition) {
                $this->updateSetting($condition);
            }
            $settings = Setting::select('group','value','key')->get()->toArray();

            $data = [];
            foreach($settings as $key => $value) {
                $data[$value['group']][$value['key']] = $value['value'];
            }
            $email = $mail_password = $mail_address = $mail_name = $port = $host = $driver = '';
            if(isset($data['mail'])) {
                $email = $data['mail']['username'];
                $mail_password = $data['mail']['password'];
                $mail_address = $data['mail']['address'];
                $mail_name = $data['mail']['name'];
                $port = $data['mail']['port'];
                $host = $data['mail']['host'];
                $driver = $data['mail']['driver'];
            }
            $img = ($data['site']['logo']) ? (Storage::disk('SYSTEM_IMAGE_URL')->has($data['site']['logo']) ? Storage::disk('SYSTEM_IMAGE_URL')->url($data['site']['logo']) : '') : '';

            return response()->json(['status' => 'SUCCESS', 'code' => 200,'msg' =>'Basic details inserted successfully', 'data' => ['email' => $email, 'mail_password' => $mail_password, 'mail_address' => $mail_address, 'mail_name' => $mail_name, 'port' => $port, 'host' => $host, 'driver' => $driver, 'logo' => $img]]);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
    }


        public function addMailSettings(Request $request)
    {
        try {

            $email = $request->get('mail_id', '');
            $mail_password = $request->get('mail_password', '');
            $mail_address = $request->get('from_address', '');
            $mail_name = $request->get('from_name', '');
            $port = $request->get('port', '');
            $host = $request->get('host', '');
            $driver = $request->get('driver', '');
            if (!empty($email))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'username'], 'value' => $email];
            if (!empty($mail_password))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'password'], 'value' => $mail_password];
            if (!empty($mail_address))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'address'], 'value' => $mail_address];
            if (!empty($mail_name))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'name'], 'value' => $mail_name];
            if (!empty($port))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'port'], 'value' => $port];
            if (!empty($host))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'host'], 'value' => $host];
            if (!empty($driver))
                $conditions[] = ['column' => ['group' => 'mail', 'key' => 'driver'], 'value' => $driver];
            if (!empty($conditions)) {
                foreach ($conditions as $condition) {
                    $this->updateSetting($condition);
                }
            }

            return response()->json(['status' => 'SUCCESS', 'code' => 200, 'data' => "Your Preferences are updated",'msg'=>'Mail driver settings saved successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
    }

     public function addPaymentSettings(Request $request)
    {
        try {
            $pay_mode = $request->get('payment', '');
            $transaction_mode = $request->get('transaction', 'TEST');
            $params = $request->get("params", []);
            
            if (!empty($pay_mode)) {
                $conditions = [
                    ['column' => ['group' => 'payment', 'key' => 'mode'], 'value' => $pay_mode], 
                    ['column' => ['group' => 'payment', 'key' => 'type'], 'value' => $transaction_mode]
                ];
            
                if (!empty($conditions)) {
                    foreach ($conditions as $condition) {
                        $this->updateSetting($condition);
                    }
                }
                // Update Param Settings
                foreach ($params as $key => $param) {
                    if(!empty($param)) {
                        $payment_setting = PaymentGatewaySetting::where('gateway_id', '=', $pay_mode)->where('key', '=', $key)->first();
                        $payment_setting->value = $param;
                        $payment_setting->save();
                    }
                }
                
                return response()->json(['status' => 'SUCCESS', 'code' => 200, 'data' => "Your Preferences are updated",'msg'=>'Payment details saved successfully!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
    }


    
    public function addAdminUser(Request $request)
    {
        try {

            $name = $request->get('name', '');
            $email = $request->get('email', '');
            $password = $request->get('password', '');

            if(Admin::where('name', $name)->where('email', '!=', $email)->count()) return response()->json(['status' => false, 'code' => 400, 'msg'=>'Name is already exists. Name should be unique for each admin.']);

            $admin = Admin::where('email', '=', $email)->first();

            $user = '';
            if ($admin) {
                $user = $admin->user_details();
            } else {
                $admin = new Admin;
                $admin->name = $name;
                $admin->email = $email;
                $admin->admin_type = 1;
                $admin->created_by = 1;
                $admin->created_at = date('Y-m-d H:i:s');
            }
            $status = $admin->save();

            if ($status && !($user)) {
                $user = new User();
                $user->email = $name;
                $user->password = Hash::make($password);
                $user->user_type_id = UserType::where('user_type', 'ADMIN')->pluck('id')->first();
                $user->user_status = UserStatus::where('name', 'ACTIVE')->pluck('id')->first();
                $user->user_id = $admin->id;
                $user->created_by = 1;
                $user->created_at = date('Y-m-d H:i:s');
            } else {
                $user->password = Hash::make($password);
            }
            $user_status = $user->save();

            if($status && $user_status) return response()->json(['status' => 'SUCCESS', 'code' => 200, 'data' => "User has been created" ,'msg'=>'Admin user saved successfully. Use your name and password for login.']);
            else return response()->json(['status' => false, 'code' => 400, 'msg'=>'Something went wrong. Please try again later.']);

        } catch (Exception $e) {
            return response()->json(['status' => 'FAILURE', 'code' => $e->getCode(), 'msg' => $e->getMessage()]);

        }
    }


    protected function updateSetting($parameters)
    {
        $setting = Setting::where('is_active', '=', 1);
        
        foreach ($parameters['column'] as $column => $value) {
            $setting->where($column, '=', $value);
        }

        $setting_first = $setting->first();
        if($setting_first) {
            $setting_first->value = $parameters['value'];
            $setting_first->save();
        } else {
            $setting = new Setting;
            $setting->group = $parameters['column']['group'];
			$setting->key = $parameters['column']['key'];
			$setting->value = $parameters['value'];
            $setting->save ();
        }

    }
    public function sendTestMails(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'email.*' => 'required|email',
            ],[   
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid e-mail address.',
            ]);

            if ($validator->fails()) throw new Exception($validator->errors()->first() , 200);

            $emails = explode(',', str_replace(", ", ",", $request->email));

            if(count($emails) > 3) return response()->json(['status' => false , 'msg' => 'You can send maximum 3 mails at a time.']);
            

            foreach($emails as $key => $email){
                Mail::send([], array('key' => 'value'), function ($message) use ($email){
                    $message->to($email)->subject('Test Mail');
                });
            }
            
            return response()->json(['status' => true , 'msg' => 'Send send successfully, please check inbox of ' . $request->email]);

        } catch (Exception $e) {
            return response()->json(['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
        }
	}

    



}