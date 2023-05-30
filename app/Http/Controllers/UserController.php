<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\resetAdminPassword;
use App\Models\MedicalProfessional;
use App\Models\PrescriptionStatus;
use App\Models\ShippingStatus;
use App\Models\InvoiceStatus;
use App\Models\UserStatus;
use App\Models\PayStatus;
use App\Models\UserType;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;

use Redirect;

use Exception;

use Session;
use Storage;
use Image;
use Auth;
use DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;


class UserController extends Controller
{
   public function anyCheckUserName (Request $request) {
		try {
			$current_mail = $request->u_name;
			$regex = "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^";

			if (preg_match ($regex , $current_mail)) {
				$name = User::where ('email' , $current_mail)->pluck ('email');
				if (count ($name) > 0) {
					return Response::json (['status' => false , 'msg' => 'Email Already Exists !'] , 409);
				}
			} else {
				return Response::json (['status' => false , 'msg' => 'Email is not valid !'] , 400);
			}

			return Response::json (['status' => true , 'msg' => 'Email is valid'] , 200);
		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
	public function registerUser (Request $request, $isWeb = NULL) {
		try {
			if(Setting::where('group', 'mail')->count()) {
				$error_message = $isWeb ? 'This field is required.' : 'All fields are mandatory';
				$validator = Validator::make($request->all(), [
					'user_name' => 'required',
					'email' => 'required|email|unique:users',
					'password' => 'required|min:8',
					'phone_code' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:1',
					'address' => 'required',
					'user_type' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|max:1',
					'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6|max:13'
				],
				[
					'phone_code.required' => 'Dial code is required.',
					'*.required' => $error_message
				]);
				if ($validator->fails()) {
					$messages = $validator->errors()->messages();
					foreach ($messages as $key => $value) {					
						if (!str_contains($key, 'external_media') || !$flag) $results[$key] = $value[0];
						if (str_contains($key, 'external_media') && !$flag) $flag = true;
					}
					return $isWeb ? Response::json([ 'status' => false, 'error' => $results] , 409) : Response::json([ 'status' => false, 'msg' => $validator->errors()->first()] , 409);
				}
				$name = $request->user_name;
			//	dd($name);
				$email = $request->email;
				$phone = $request->phone;
				$phone_code = isset($request->phone_code) ? $request->phone_code : '';
				$address = isset($request->address) ? htmlentities($request->address) : ''; //not in app
				$pincode = isset($request->pincode) ? $request->pincode : ''; //not in app
				$password = $request->password;
				$user_type = isset($request->user_type) ? (int)  $request->user_type : 2;
				$is_Web = isset($request->isWeb) ? $request->isWeb : '';

				if (User::where ('email' , '=' , $email)->count () != 0) {
					$data = $isWeb ? ['status' => false, 'data' => 'email', 'msg' => 'Account already exists'] : ['status' => false, 'msg' => 'Account already exists'];
					return Response::json($data , 409);
				}
				//check if password == confirm password(not needed for mobile), then proceed the following
				$digits = 4;
			$randomValue = rand (pow (10 , $digits - 1) , pow (10 , $digits) - 1);
	       	switch ((int)$user_type) {
					case (UserType::MEDICAL_PROFESSIONAL ())://med
						
						$medProf = new MedicalProfessional;

						$medProf->prof_name = $name;
						$medProf->prof_address = $address;
						$medProf->prof_phone = '+' . $phone_code . ' ' . $phone;
						$medProf->prof_mail = $email;
						$medProf->prof_pincode = $pincode;						
						$medProf->created_at = date ('Y-m-d H:i:s');
						$medProf->updated_at = date ('Y-m-d H:i:s');

						$medProf->save ();

						$userId = $medProf->id;
						break;
					case (UserType::CUSTOMER ())://cust
						$name = $request->user_name;
						$customer = new Customer;
						
						$customer->name = $name;
						$customer->address = $address;
						$customer->phone = '+' . $phone_code . ' ' . $phone;
						$customer->mail = $email;					
						$customer->pincode = $pincode;					
						$customer->created_at = date ('Y-m-d H:i:s');
						$customer->updated_at = date ('Y-m-d H:i:s');

						$customer->save ();

						$userId = $customer->id;
						break;
				}//switch
				
					$user = new User;
				$user->email = $email;
				$user->password = Hash::make ($password);
				$user->phone = $phone;
				$user->user_type_id = $user_type;
				$user->user_id = $userId;
				$user->country_code = $phone_code;
				$user->security_code = $randomValue;
				$user->save ();
				$result = [];
				$path = public_path () . '/storage/prescription/' . $email;
				File::makeDirectory ($path , $mode = 0777 , true , true);
				try {
					
					Mail::send ('emails.register' , array('name' => $name , 'user_name' => $email , 'pwd' => $password , 'code' => $randomValue) , function ($message) use ($email) {
						$message->to ($email)->subject ('Activate Account');
					});
					if (!$is_Web) return Response::json ( ['status' => true , 'msg' => 'Acccount has been successfully created, Please check mail for the code', 'userEmail' => $email], 200);
					else return Response::json ( ['status' => 'SUCCESS' , 'msg' => 'Acccount has been successfully created, Please check mail for the code', 'userEmail' => $email]);

				}
				catch (Exception $e) {
					return Response::make (['status' => false , 'msg' => $e->getMessage()] , 500);
				}
			} else {
				return Response::json ( ['status' => false , 'msg' => "Couldn't process now. No migratins found, please setup your system. Please contact your system administrator."], 400);
			}
		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}
	public function loginUser (Request $request, $isWeb = NULL) {
		try {
			
			$isWeb = isset($request->isWeb) ? $request->isWeb : '';
			if ($isWeb) {
				if (!$this->isCsrfAccepted ()) {
					$result = ['result' => array('status' => 'failure')];
					return Response::json ($result);
				}
				$email = $request->email;
				$password = $request->password;

				
				$user = User::select ('user_status as status', 'user_type_id', 'password', 'id')->where ('email' , '=' , $email)->first ();
           
				if (!empty($user) && Hash::check($password, $user->password)) {
					if ($user->user_type_id == UserType::ADMIN()) return Response::make (['status' => 'FAILURE' , 'msg' =>'Please Login as Admin.'] , 403);

					else if ($user->status == UserStatus::PENDING ()) {
						$result = ['result' => array('status' => 'pending')];

						Session::put ('user_password' , $password);
					} elseif ($user->status == UserStatus::ACTIVE ()) {
						if (Auth::attempt (array('email' => $email , 'password' => $password))) {
							Session::put ('user_id' , $email);
							if (Session::get ('medicine') != "") $result = ['result' => array('status' => 'success' , 'page' => 'yes')];

							else $result = ['result' => array('status' => 'success' , 'page' => 'no')];

						} else $result = ['result' => array('status' => 'failure')];

					} else $result = ['result' => array('status' => 'delete')];

				} else $result = ['result' => ['status' => 'failure', 'msg' => 'Invalid Login Credientials!']];

			} else {
				$validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required'
                ]);
        
                if ($validator->fails()) {
                    return Response::json([ 'status' => false, 'msg' => $validator->errors()->first()] , 400);
                }
				$email = $request->email;
				$password = $request->password;

                try{
					if(!(User::where ('email' , '=' , $email)->exists ()))
						return Response::make (['status' => false , 'msg' => 'No user found!'] , 400);
                    else if (Auth::attempt (array('email' => $email , 'password' => $password))) {

						$user = User::where('users.id' , Auth::user()->id)->join ('user_status as us' , 'us.id' , '=' , 'user_status')->first();
						$token = User::find(Auth::user()->id)->createToken('Personal Access Token');
						$settings = Setting::where('is_active', 1)->where('group', 'site')->whereIn('key', ['currency', 'curr_position','mail'])->pluck('value','key')->toArray();
						$status = $user->name;
						Session::put ('user_id' , $email);
						$pres_status = PrescriptionStatus::status ();
						$invoice_status = InvoiceStatus::status ();
						$payment_status = PayStatus::status ();
						$shipping_status = ShippingStatus::status ();
						$result = ['status' => true , 'msg' => 'User Logged In' , 'data' => ['status' => $status , 'pres_status' => $pres_status ,'currency_format' => $settings['currency'], 'contact_mail' => $settings['mail'],
						'currency_position' => $settings['curr_position'], 'invoice_status' => $invoice_status , 'payment_status' => $payment_status , 'shipping_status' => $shipping_status,'access_token' => $token->accessToken]];
					} else return Response::make (['status' => false , 'msg' => 'Invalid Login Credientials!'] , 401);
                } catch (Exception $e) {
					return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
                }
			}
			return Response::json ($result, 200);
		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}

	public function resendCode (Request $request, $isWeb = NULL) {
		try {
			$validator = Validator::make($request->all(), [
				'email' => 'required'
			],
			[
				'email.required' => 'This field is required.'
			]);
			if ($validator->fails()) return Response::json([ 'status' => false, 'msg' => $validator->errors()->first()] , 409);

			$digits = 4;
			$randomValue = rand (pow (10 , $digits - 1) , pow (10 , $digits) - 1);
			$email = $request->email;

			$user = User::where ('email' , '=' , $email)->first ();
			$user->security_code = $randomValue;
			$user->update();
			
			if (is_null ($user)) return Response::json([ 'status' => false, 'msg' => 'No user found !'] , 404);
			if($user->user_status == UserStatus::ACTIVE()) return Response::json([ 'status' => false, 'msg' => 'Account is already activated. Please login!'] , 404);

			$user_type = $user->user_type_id;
			$customer = '';
			switch ((int)$user_type) {
				case (UserType::MEDICAL_PROFESSIONAL ())://med
					$customer = MedicalProfessional::select('prof_name as name')->where('id','=',$user->user_id)->first();
					break;
				case (UserType::CUSTOMER ())://cust
					$customer = Customer::select('name')->where('id','=',$user->user_id)->first();
					break;
			}
			$name = '';
			if($customer)
				$name = $customer->name;
			try {
				if (!$isWeb) {
					
					$true = Mail::send ('contact.display' , array('code' => $randomValue) , function ($message) use ($email) {
						$message->to ($email)->subject ('Activate Account');
					});
					return Response::json ( ['status' => true , 'msg' => 'Verification code was resent successfully.', 'data' => $email],200);
				} else {
					
					$true = Mail::send ('emails.register' , array('name' => $name, 'code' => $randomValue) , function ($message) use ($email) {
						$message->to ($email)->subject ('Activate Account');
					});
					return Response::json ( ['status' => 'SUCCESS' , 'msg' => 'Verification code was resent successfully.', 'userEmail' => $email]);
				}
			}
			catch (Exception $e) {
				return Response::json (['status' => false , 'msg' => $e->getMessage()] , 500);
			}
		} catch (Exception $e) {
			return Response::json (['status' => false , 'msg' => $e->getMessage()] , 500);
		}
	}
    public function activateAccount (Request $request) {
		try {
			$validator = Validator::make($request->all(), [
				'security_code' => 'required'
			],
			[
				'security_code.required' => 'Activation code is required'
			]);
			if ($validator->fails()) return Response::json([ 'status' => false, 'msg' => $validator->errors()->first()], 409);

		
			$email = $request->email;
			$user = User::where ('email' , '=' , $email)->first ();
			
			if (is_null ($user)) return Response::json([ 'status' => false, 'msg' => 'No user found !'], 404);
			else if ($user->user_status == UserStatus::ACTIVE()) return Response::json([ 'status' => false, 'msg' => 'Account already active. Please login !'], 404);
			
			$sec_code = $request->security_code;
			$securityCode = $user->security_code;

			if (strcmp ($securityCode , $sec_code) == 0) {
				$updatedValues = array('user_status' => UserStatus::ACTIVE ());
				$updated = User::where ('email' , '=' , $email)->update ($updatedValues);
				$pass = Session::get ('user_password');
				if($updated){
					$result = ['status' => true , 'msg' => 'Your account has been successfully activated !'];
					if($pass) {
						if(!(Auth::attempt (['email' => $email , 'password' => $pass]))) $result = ['status' => false , 'msg' => 'Invalid Login Credientials!'];
					}
				}
				Session::put ('user_id' , $email);

				return Response::json ($result);
			} else {
				return Response::json([ 'status' => false, 'msg' => 'Invalid activation code!'], 400);
			}			
		}
		catch (Exception $e) {
			return Response::json (['status' => false , 'msg' => $e->getMessage()], $e->getCode());
		}
	}


	public function postUpdateDetailsUser (Request $request, $isWeb = NULL) {
		try {
			$error_message = $isWeb ? 'This field is required!' : 'All fields are required!';
			$validator = Validator::make($request->all(), [
				'user_name' => 'required',
				'phone_code' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:1',
				'address' => 'required',
				'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:6|max:13',
				'pincode' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/'
			],
			[
				'phone_code.required' => 'Dial code is required.',
				'*.required' => $error_message
			]);
			if ($validator->fails()) {
				$messages = $validator->errors()->messages();
				foreach ($messages as $key => $value) {					
					if (!str_contains($key, 'external_media') || !$flag) $results[$key] = $value[0];
					if (str_contains($key, 'external_media') && !$flag) $flag = true;
				}
				return $isWeb ? Response::json([ 'status' => false, 'error' => $results] , 409) : Response::json([ 'status' => false, 'msg' => $validator->errors()->first()] , 409);
			}

			$email = Auth::user ()->email;
			$user_type = Auth::user ()->user_type_id;
			$name = isset($request->user_name) ? $request->user_name : '';
			$address = isset($request->address) ? htmlentities($request->address) : '';
			$pincode = isset($request->pincode) ? $request->pincode : '';
			$phone_code = isset($request->phone_code) ? $request->phone_code : '';
			$phone = (isset($request->phone) && isset($phone_code)) ? '+'. $phone_code . ' ' . $request->phone : '';

			switch ($user_type) {
				case UserType::MEDICAL_PROFESSIONAL ():
					$medicalProfDetails = array('prof_name' => $name ,
						'prof_address' => $address ,
						'prof_phone' => $phone ,
						'prof_pincode' => $pincode,
						'updated_at' => date ('Y-m-d H:i:s')
					);
					$affectedRows = MedicalProfessional::where ('prof_mail' , '=' , $email)->update ($medicalProfDetails);
					break;
				case UserType::CUSTOMER ():
					$customerDetails = array('name' => $name ,
						'address' => $address ,
						'phone' => $phone ,
						'pincode' => $pincode,
						'updated_at' => date ('Y-m-d H:i:s')
					);
					$affectedRows = Customer::where ('mail' , '=' , $email)->update ($customerDetails);
					break;
			}
			$user = User::where('id', Auth::user()->id)->update(['phone' => $request->phone, 'country_code' => $phone_code, 'updated_at' => date('Y-m-d H:i:s')]);

			if ($affectedRows) $result = ['status' => true , 'msg' => 'User profile updated !'];
			else return Response::json (['status' => false , 'msg' => 'Profile not updated ! due to some technical error'], 400);
			return Response::json ($result);
		}
		catch (Exception $e) {
			return Response::json (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
    

    public function storeUserProfilePic ( Request $request ) {
		try {
			if(Auth::check()) {
				$email = Auth::user()->email;
				$validator = Validator::make($request->all(), [
					'file' => 'mimes:jpeg,jpg,png|max:4096',
				], [   
					'*.mimes' => 'Please upload prescription in jpeg, jpg or png format.',
					'*.max' => 'The prescription size may not be greater than 4MB.',
				]);

				if ($validator->fails()) return Response::json (['status' => false , 'msg' => $validator->errors()->first()] , 400);

				$user_image = Auth::user() -> profile_pic;				
				
				$fileName = str_before($email, '@') . '_' . time() . '.' . $request->file('file')->extension();
				$store_original = $request->file('file')->storeAs('/public/profile_pic', $fileName);
				
				if (!$store_original) return Response::json (['status' => false , 'msg' => 'File Not saved !'] , 403);
				else { //thumbnail creation
					$path = '/public/profile_pic/thumbnails';
					if(!Storage::exists($path)) Storage::makeDirectory($path);

					$avatar = $request->file('file');
					Image::make($avatar)->resize(60, 60)->save( public_path('/storage/profile_pic/thumbnails/' . $fileName) );

					$user = User::where('id', Auth::user()->id)->update(['profile_pic' => $fileName, 'updated_at' => date('Y-m-d H:i:s')]);
					if ($user) {

						if ($user_image) { //deleting profile pic if already exists
							if (Storage::disk('PROFILE_PIC')->has($user_image)) { //checking image is existing or not
								Storage::disk('PROFILE_PIC')->delete($user_image); //deleting original pic
								Storage::disk('PROFILE_THUMB')->delete($user_image); //deleting thumbnail
							}
						}

						return Response::json (['status' => true , 'msg' => 'Profile updated successfully.', 'image' => ['thumb' => Storage::disk('PROFILE_PIC')->url('/thumbnails/' . $fileName), 'original' => Storage::disk('PROFILE_PIC')->url('/' . $fileName)]]);
					}
					else return Response::json (['status' => false , 'msg' => 'Profile doesnot updated, try again later.', 'image' => '']);


				}
			} else return Response::json (['status' => false , 'msg' => 'Session has expired. Please login! '] , 400);
		} catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}

    public function getAccountPage () {
		try {
			if(Auth::check())  return View::make ('users.account_page');
			else return Redirect::to ('/');
		} catch (Exception $e) {
			return Response::make (['status' => 'FAILURE' , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}
	// userType 
	public function getObtainUserType () {
		try {
			// Model To Obtain User Type
			$user_type = UserType::where ('user_type' , '!=' , 'admin')->get ();
			foreach ($user_type as $type) {
				$type_array[] = ['id' => $type->id , 'type' => $type->user_type];
			}
			$response_array = array('status' => true , 'msg' => 'User types Obtained !' , 'data' => $type_array);

			return Response::json ($response_array);

		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
	public function anyResetPassword (Request $request, $isWeb = NULL) {
		// try {
			$validator = Validator::make($request->all(), [
				'email' => 'required|email'
			],
			[
				'*.required' => 'This field is required.',
				'*.email'	 => 'Please enter a valid Email address.'
			]);

			if ($validator->fails()){
				if (!$isWeb) return Response::json (['status' => false , 'msg' => $validator->errors()->first()] , 409);

				$messages = $validator->errors()->messages();
				foreach ($messages as $key => $value) {					
					if (!str_contains($key, 'external_media') || !$flag) $results[$key] = $value[0];
					if (str_contains($key, 'external_media') && !$flag) $flag = true;
				}
				return Response::json([ 'status' => false, 'data' => $results] , 409);
			}
			$email = isset($request->email) ? $request->email : '';
			if(User::where ('email' , '=' , $email)->exists()) {
				if ($email && isset($request->security_code) && isset($request->status_code)) { //1st post for API
					$message = $isWeb ? 'This field is required.' : 'All fields are required.';
					$validator = Validator::make($request->all(), [
						'security_code' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:4'
					],
					[
						'*.required' => $message
					]);

					if ($validator->fails()){
						if (!$isWeb) return Response::json (['status' => false , 'msg' => $validator->errors()->first()] , 409);

						$messages = $validator->errors()->messages();
						foreach ($messages as $key => $value) {					
							if (!str_contains($key, 'external_media') || !$flag) $results[$key] = $value[0];
							if (str_contains($key, 'external_media') && !$flag) $flag = true;
						}
						return Response::json([ 'status' => false, 'data' => $results] , 409);
					}
					$security_code = $request->security_code;
					$user = User::where ('email' , '=' , $email)->where ('security_code' , '=' , $security_code)->first ();

					if (empty($user)) return Response::json (['status' => false , 'msg' => "Invalid validation code"] , 404);
					else return Response::json (['status' => true , 'msg' => "Verified."]);

				} else if ($email && isset($request->security_code)  && !isset($request->status_code)) {//2nd post for API
					$message = $isWeb ? 'This field is required.' : 'All fields are required.';
					$validator = Validator::make($request->all(), [
						'security_code' => 'required',
						'new_password' => 'required|required_with:confirm_password|same:confirm_password',
						'confirm_password' => 'required'
					],
					[
						'*.required' => $message
					]);
					if ($validator->fails()){
						if (!$isWeb) return Response::json (['status' => false , 'msg' => $validator->errors()->first()] , 409);

						$messages = $validator->errors()->messages();
						foreach ($messages as $key => $value) {
							if (!str_contains($key, 'external_media') || !$flag) $results[$key] = $value[0];
							if (str_contains($key, 'external_media') && !$flag) $flag = true;
						}
						return Response::json([ 'status' => false, 'data' => $results] , 409);
					}
					
					$security_code = $request->security_code;
					$password = $request->new_password;
					$confirm_password = $request->confirm_password;

					$user = User::where ('email' , '=' , $email)->where ('security_code' , '=' , $security_code)->first ();
					if (!is_null ($user)) {
						$user->password = Hash::make ($password);
						$user->user_status = UserStatus::ACTIVE ();
						$user->save ();
						$result = ['status' => true , 'msg' => 'Password Changed'];

					} else return Response::json (['status' => false , 'msg' => "Invalid validation code"] , 404);

				} else {
					if($email && (User::where ('email' , '=' , $email)->count() > 0)) {
						$token = Str::random(64);
						DB::table('password_resets')->where('email', $request->email)->delete();
						DB::table('password_resets')->insert(
							['email' => $request->email, 'token' => $token, 'created_at' => date('Y-m-d H:i:s')]
						);
						$digits = 4;
						$randomValue = rand (pow (10 , $digits - 1) , pow (10 , $digits) - 1);
						$updatedValues = array('security_code' => $randomValue);
						$user = User::where ('email' , '=' , $email)->update ($updatedValues);
						$result = ['status' => true , 'msg' => 'Account recovery email sent to ' . $request->email];

						Mail::to($email)->later(now()->addMinutes(10), new resetAdminPassword(['code' => $randomValue, 'token' => $token]));					
						
					} else return Response::json (['status' => false , 'msg' => "Account doesn't exist, Please try with registered Email."] , 404);
				}
			} else {
				return Response::json (['status' => false, 'msg' => "Account doesn't exist, Please try with registered Email."], 400);
			}

			return Response::json ($result);
		// } catch (Exception $e) {
		// 	return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		// }

	}

	public function logout(Request $request) {
		Auth::logout();
        return redirect('/');
    }
    public function anyUserDetails (Request $request) {

		try {
			if (!Auth::check ()) return Response::json (['status' => false , 'msg' => "You are not authorised"] , 401);
			
			$validator = Validator::make($request->all(), [
				'email' => 'required|email'
			]);
	
			if ($validator->fails()) return Response::json (['status' => false , 'msg' => $validator->errors()->first()] , 409);
			
			$email = $request->email;
			$result = '';
			$user = User::where ('email' , '=' , $email)->first ();
			if ($user != null) {
				$profile_pic  = $user->profile_pic;
				$prof_pic_thumb = '';
				if ($profile_pic)
				{
					$prof_pic = Storage::disk('PROFILE_PIC')->exists($profile_pic) ? Storage::disk('PROFILE_PIC')->url($profile_pic) : Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');
					$prof_pic_thumb = Storage::disk('PROFILE_THUMB')->exists($profile_pic) ? Storage::disk('PROFILE_THUMB')->url($profile_pic) : Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');
				} else $prof_pic =  $prof_pic_thumb = Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');

				if ($user->user_type_id == UserType::CUSTOMER ()) {
					$customer = Customer::where ('id' , '=' , $user->user_id)->first ();
					$Details = array('user_name' => $customer->name ,
						'address' => $customer->address ,
						'phone_number' => $customer->phone,
						'phone' => $user->phone ,
						'type_user' => UserType::CUSTOMER () ,
						'pincode' => $customer->pincode,
						'phone_code' => $user->country_code,
						'profile_pic' => $prof_pic,
						'profile_pic_thumb' => $prof_pic_thumb
					);
				} else if ($user->user_type_id == UserType::MEDICAL_PROFESSIONAL ()) {
					$professional = MedicalProfessional::where ('id' , '=' , $user->user_id)->first ();
					$Details = array('user_name' => $professional->prof_name ,
						'address' => $professional->prof_address ,
						'phone' => $user->phone ,
						'phone_number' => $professional->prof_phone,
						'type_user' => UserType::MEDICAL_PROFESSIONAL () ,
						'pincode' => $professional->prof_pincode,
						'phone_code' => $user->country_code,
						'profile_pic' => $prof_pic,
						'profile_pic_thumb' => $prof_pic_thumb
					);
				}
				$result = ['status' => true , 'msg' => 'User details obtained !' , 'data' => $Details];
			} else $result = ['status' => false , 'msg' => 'No User Details Found!'];
			return Response::json ($result);
		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}

		public function anyWebActivateAccount ($code) {
		$user = User::where ('security_code' , '=' , $code)->first ();
		$update = '';
		if (!empty ($user)) {
			$updatedValues = array('user_status' => UserStatus::ACTIVE ());
			$update = User::where ('security_code' , '=' , $code)->update ($updatedValues);

			return $update ? Redirect::to ('/?msg=success') : Redirect::to ('/?msg=failed');

		} else return Redirect::to ('/?msg=failed');
	}

	public function getCheckSession ()
	{
		if (Auth::check ()) {
			$login = 1;
		} else {
			$login = 0;
		}

		return Response::json ($login);
	}


}