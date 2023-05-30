<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\ItemList;
use App\Models\Medicine;
use App\Models\UserType;
use App\Models\PayStatus;
use App\Models\NewMedicine;
use App\Models\SessionData;
use App\Models\Prescription;
use App\Models\InvoiceStatus;
use App\Models\PaymentGateway;
use App\Models\ShippingStatus;
use App\Models\NewMedicineEmail;
use App\Models\PrescriptionStatus;
use App\Models\MedicalProfessional;

use Validator;
use Redirect;
use Exception;
use Response;
use DateTime;
use Session;
use Storage;
use Schema;
use Image;
use Illuminate\Support\Facades\Cache;

use Mail;
use View;
use Auth;
use File;
use DB;
if (!defined('CACHE_PARAM_MEDICINE')) define('CACHE_PARAM_MEDICINE', 'medicines');
if (!defined('CURRENCY_BEFORE')) define('CURRENCY_BEFORE', 'BEFORE');
if (!defined('CURRENCY_AFTER')) define('CURRENCY_AFTER', 'AFTER');
class MedicineController extends Controller
{
	//Load Medicine List
    public function anyLoadMedicineWeb (Request $request, $isWeb = NULL) {   
		try {   
			header ("Access-Control-Allow-Origin: *");
			$validator = Validator::make($request->all(), [
				'term' => 'required'
			]);

			if ($validator->fails()) throw new Exception($validator->errors()->first() , 409);

			$key = $request->get('term');

			$medicines = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->get ()->toArray ();

			if (!empty($key)) {
				$medicines = array_filter ($medicines , function ($medicine) use ($key) {
					$medTemp = stringClean ($medicine['item_name']);
					$keyTemp = stringClean ($key);
					if ((strpos (strtolower ($medicine['item_name']) , strtolower ($key)) === 0
							|| strpos (strtolower ($medTemp) , strtolower ($key)) === 0
							|| strpos (strtolower ($medTemp) , strtolower ($keyTemp)) === 0
						) && $medicine['is_delete'] == 0
					)
						return true;
					else
						return false;
				});
			}
			if ($isWeb) {
				$json = [];
				foreach ($medicines as $data) {
					$json[] = array(
						'value' => $data['item_name'] ,
						'label' => $data['item_name'] ,
						'item_code' => $data['item_code'] ,
					);
				}
				return Response::json ($json);

			} else {
				$medicines = array_slice ($medicines , 0 , 4);

				if (empty($medicines))
					return Response::make (['status' => false , 'msg' => 'No Medicines Found'] , 404);
				//	$result = array(array('result' => array('status' => 'success' , 'msg' => $medicines)));
				$result = ['status' => true , 'msg' => 'Search Results' , 'data' => ['medicines' => $medicines, 'currency' => Setting::param ('site' , 'currency')['value'] , 'curr_position' => Setting::param ('site' , 'curr_position')['value']]];

				return Response::json ($result);
			}
		} catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
	//Get Medicine Details Search option
	public function getMedicineDetail ($searched_medicine) {
		$med_info = Medicine::select ('*')
			->where ('item_code' , '=' , $searched_medicine)
			->get ();
		if (count ($med_info) > 0) return View::make ('users.medicine_details' , array('med_info' => $med_info));
		else return Redirect::back ()->withErrors (['Sorry no more search results available']);
	}
	//Load Alternate Medicines
	public function anyLoadSubMedicine (Request $request) {
		try {
			$validator = Validator::make($request->all(), [
				'n' => 'required',
				'id' => 'required'
			]);

			if ($validator->fails()) throw new Exception($validator->errors()->first() , 409);

			$med_name = $request->get('n') ? $request->get('n') : '';
			$med_id = intval ($request->get('id') ? $request->get('id') : 0);

			if (empty($med_id)) throw new Exception('Id not passed !' , 400);

			$medicines = Medicine::medicines ();
			$key = Medicine::medicines ($med_id);
			if (!empty($key['composition'])) {
				if ($key['composition'] != "Not available") {
					$medicines = array_filter ($medicines , function ($medicine) use ($key , $med_name) {
						if ((strcmp ($medicine['composition'] , $key['composition']) == 0) && ($medicine['item_name'] != $med_name) && $medicine['is_delete'] == 0) return true;
						else return false;
					});

					if (empty($medicines)) return Response::json (['status' => true , 'msg' => 'No alternate medicines available!' , 'data' => ['price' => 0 , 'medicines' => []]]);

					$medicines = array_slice ($medicines , 0 , 5);

					foreach ($medicines as &$value) {
						$value['selling_price'] = $value['mrp'];
						$value['mrp'] = Setting::currencyFormat ($value['mrp']);
						$value['product_image'] = ($value['product_image'] && Storage::disk('MEDICINE_PIC')->exists('/thumbnails/' . $value['product_image'])) ? Storage::disk('MEDICINE_PIC')->url('/thumbnails/' . $value['product_image']) : Storage::disk('MEDICINE_PIC')->url('no-image-available.png');
					}
					$result = ['status' => true , 'msg' => 'Alternatives Found !' , 'data' => ['price' => $key['mrp'] , 'medicines' => $medicines]];

				} else $result = ['status' => true , 'msg' => 'No alternate medicines available!' , 'data' => ['price' => 0 , 'medicines' => $medicines]];
			} else $result = ['status' => true , 'msg' => 'No alternate medicines available!' , 'data' => ['price' => 0 , 'medicines' => $medicines]];

			return Response::json ($result);


		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
	//make user orders, stores or updates each orders to sessions table
	public function anyAddCart (Request $request, $isWeb = NULL) {
		if ($isWeb && Session::token() != $request->cookie('XSRF-TOKEN')) return Response::json([ 'status' => false, 'msg' => 'Cannot able to process now. Try again later!'] , 400);

		$medicine = (Session::get ('medicine') == "") ? $request->medicine : Session::get ('medicine');
		$med_quantity = (Session::get ('med_quantity') == "") ? $request->med_quantity : Session::get ('med_quantity');
		$med_mrp = (Session::get ('med_mrp') == "") ? $request->selling_price : Session::get ('med_mrp');
		$item_code = (Session::get ('item_code') == "") ? $request->item_code : Session::get ('item_code');
		$item_id = (Session::get ('item_id') == "") ? $request->id : Session::get ('item_id');
		
		$session_id = (Session::get ('session_id') == "") ? $request->session_id : Session::get ('session_id');

		$pres_required = (Session::get ('pres_required') == "") ? $request->pres_required : Session::get ('pres_required');
		$med_quantity = ($med_quantity < 1) ? 1 : $med_quantity;
		if (!$session_id) {
			Session::put ('medicine' , $medicine);
			Session::put ('med_quantity' , $med_quantity);
			Session::put ('med_mrp' , $med_mrp);
			Session::put ('item_code' , $item_code);
			Session::put ('item_id' , $item_id);
			Session::put ('pres_required' , $pres_required);
		}
		$email = "";
		if (Auth::check ()) {
			if($session_id) { //updating cart count
				$medicine_exist = SessionData::find($session_id);
				$increment = $medicine_exist->update (['medicine_count' => $med_quantity, 'updated_at' => date ('Y-m-d H:i:s')]);
				if(!$isWeb || $request->ajax()) return Response::json([ 'status' => true, 'msg' => 'Updated']);
				else return Redirect::to ('/my-cart');
			} else {
				$email = Session::has('user_id') ? Session::get ('user_id') : Auth::user()->email;
				$medicine_exist = DB::table ('sessions')->select ('id','medicine_name')->where ('user_id' , '=' , $email)->where('medicine_id',$item_id)->where ('medicine_name' , '=' , $medicine)->first ();

				if ($medicine_exist) {
					$medicine_exist = SessionData::find($medicine_exist->id);
					$increment = $medicine_exist->update (['medicine_count' => DB::raw('medicine_count + '.$med_quantity), 'updated_at' => date ('Y-m-d H:i:s')]);
					if ($increment) {
						Session::forget ('medicine');
						Session::forget ('med_quantity');
						Session::forget ('med_mrp');
						Session::forget ('item_code');
						Session::forget ('item_id');

						Session::forget ('pres_required');
						if(!$isWeb || $request->ajax()) return Response::json([ 'status' => true, 'msg' => 'Updated']);
						else return Redirect::to ('/my-cart');
					}

				} else {
					$insert = DB::table ('sessions')->insert (array('medicine_id' => $item_id , 'medicine_name' => $medicine , 'medicine_count' => $med_quantity , 'user_id' => $email , 'unit_price' => $med_mrp , 'item_code' => $item_code , 'is_pres_required' => $pres_required, 'created_at' => date ('Y-m-d H:i:s')));
					if ($insert) {
						Session::forget ('medicine');
						Session::forget ('med_quantity');
						Session::forget ('med_mrp');
						Session::forget ('item_code');
						Session::forget ('item_id');

						Session::forget ('pres_required');
						if (!$isWeb || $request->ajax()) return Response::json([ 'status' => true, 'msg' => 'Inserted']);
						else return Redirect::to ('/my-cart');
					}
				}
			}
		} else return Response::json([ 'status' => true, 'msg' => 'Not authorised!', 'data' => ['logged' => 0]] , 200);

	}
	public function removeCartItem (Request $request, $isWeb = NULL) {
		if ($isWeb && Session::token() != $request->cookie('XSRF-TOKEN')) return Response::json([ 'status' => false, 'msg' => 'Cannot able to process now. Try again later!'] , 400);

		if (Auth::check ()) {
			$validator = Validator::make($request->all(), [
				'session_id' => 'required',
			]);
			if ($validator->fails()) return Response::json([ 'status' => false, 'error' => $validator->errors()->first()] , 409);

			$session_id = $request->session_id;
			$medicine_deleted = false;
			if(count(SessionData::whereIn('id', explode(',',$session_id))->where('user_id', Auth::user()->email)->get())) {				
				$medicine_deleted = SessionData::whereIn('id', explode(',',$session_id))->where('user_id', Auth::user()->email)->delete();

				if($medicine_deleted) return Response::json([ 'status' => true, 'msg' => 'Removed!']);
				else return Response::json([ 'status' => false, 'msg' => 'Something went wrong, try again later!']);

			} else return Response::json(['status' => false, 'msg' => 'Item not available!'], 409);		

		} else return Response::json([ 'status' => true, 'msg' => 'Not authorised!', 'data' => ['logged' => 0]] , 200);
	}
	//Load Medicine List
	public function anyLoadMedicine (Request $request) {
		try {
			header ("Access-Control-Allow-Origin: *");

			$validator = Validator::make($request->all(), [
				'medicine' => 'required'
			]);

			if ($validator->fails()) return Response::json (['status' => false, 'msg' => $validator->errors()->first()] , 409);

			$medicineName = $request->medicine;

			$medicine = Medicine::where ('item_name' , 'LIKE' , $medicineName . '%')->take (4)->get ();
			$i = 0;
			if ($medicine->count () > 0) {
				foreach ($medicine as $med) {
					$medicineNameArray[$i] = array("id" => $i + 1 , "name" => $med->item_name , 'mrp' => substr ($med->mrp , 0 , 4) , 'exp' => $med->expdt , 'item_code' => $med->item_code);
					$i++;
				}
				$result = ['status' => true , 'msg' => $medicineNameArray];
			} else $result = ['status' => false, 'msg' => 'No Medicines Found'];

			return Response::json ($result);
		} catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}

	}
	// store prescription
	public function anyStorePrescription (Request $request, $isWeb = NULL) {
		if ($isWeb) {
			try {
				if (Session::token() != $request->cookie('XSRF-TOKEN')) return Response::make (['status' => true , 'msg' => 'Invalid File Request!'] , 400);
				
				if (Auth::check ()) {
					$email = Auth::user()->email;
					$user_type = Auth::user ()->user_type_id;

					$is_pres_required = isset($request->is_pres_required) ? $request->is_pres_required : 0;
					$file_name = $store_original = "";

					if ($request->file('files')) {
						$validator = Validator::make($request->all(), [
							'files' => 'mimes:jpeg,jpg,png|max:8192',
						],
						[   
							'files.mimes'      => 'Please upload prescription in jpeg, jpg or png format.',
							'files.max' => 'The prescription size may not be greater than 8MB.',
						]);

						if ($validator->fails()) throw new Exception($validator->errors()->first() , 200);

						$extn = $request->file('files')->extension();
						$file_name = Auth::user ()->id . '_' . time () . '.' . $extn;
						$path = '/public/prescription/'. $email . '/';

						$store_original = $request->file('files')->storeAs($path, $file_name);
						if (!$store_original) throw new Exception('File Not saved !' , 200);
						else { //thumbnail creation
							$path = $path . 'thumbnails/';
							if(!Storage::exists($path)) Storage::makeDirectory($path);
							$avatar = $request->file('files');
							Image::make($avatar)->resize(60, 60)->save( public_path('/storage/prescription/' . $email . '/thumbnails/' . $file_name) );						
						}
					} else if ($is_pres_required == 1) return Response::make (['status' => false , 'msg' => 'You are mandated to upload prescription to place the order.'] , 400);
					
					$pres_required = false;
					$current_medicines = SessionData::select ('medicine_id' , 'medicine_count')->where ('user_id' , '=' , $email)->get ();
					if ($store_original || count ($current_medicines) > 0){ // Save Prescription
						$prescription = new Prescription;
						$user_id = Auth::user ()->id;
						$file_name ?  $prescription->path = $file_name : '';
						$prescription->created_at = date ('Y-m-d H:i:s');
						$prescription->user_id = $user_id;
						$prescription->created_by = $user_id;
						$prescription->save ();

						$pres_id = $prescription->id;
						$invoice = new Invoice;
						$invoice->pres_id = $pres_id;
						$invoice->user_id = $user_id;
						$invoice->created_at = date ('Y-m-d h:i:s');
						$invoice->updated_at = date ('Y-m-d h:i:s');
						$invoice->created_by = $user_id;
						$invoice->updated_by = $user_id;
						$invoice->save ();
						$invoice_id = $invoice->id;

					
						if (count ($current_medicines) > 0) {
							
							foreach ($current_medicines as $medicine) {
								$medicine_details = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->where('id', $medicine['medicine_id'])->first()->toArray();
								if($medicine_details['is_pres_required'] && !$file_name) $pres_required = true;
								else {
									$total_discount = $medicine_details['discount'] * $medicine['medicine_count'];
									$total_price = ($medicine_details['mrp'] * $medicine['medicine_count']) - $total_discount;
									$itemList = new ItemList;
									$itemList->invoice_id = $invoice_id;
									$itemList->medicine = $medicine['medicine_id'];
									$itemList->quantity = $medicine['medicine_count'];
									$itemList->unit_price = $medicine_details['mrp'];
									$itemList->discount_percentage = $medicine_details['discount'];
									$itemList->discount = $total_discount;
									$itemList->total_price = $total_price;
									$itemList->created_at = date ('Y-m-d H:i:s');
									$itemList->updated_at = date ('Y-m-d H:i:s');
									$itemList->updated_by = $user_id;
									$itemList->created_by = $user_id;
									$itemList->save ();
									SessionData::where ('user_id' , '=' , $email)->where('medicine_id', $medicine['medicine_id'])->delete ();	
								}
							}
							
						}
					
						$data['email'] = $email;
						$name = "";
						
						if ($user_type == UserType::CUSTOMER ()) $name = Auth::user ()->customer->name;
						elseif ($user_type == UserType::MEDICAL_PROFESSIONAL ()) $name = Auth::user ()->professional->prof_name;

						Mail::send ('emails.prescription_upload' , array('name' => $name) , function ($message) use ($data) {
							$message->to ($data['email'])->subject ('New order has been submitted to ' . Setting::param ('site' , 'app_name')['value']);
						});

						Mail::send ('emails.admin_prescription_upload' , array('name' => $name) , function ($message) use ($data) {
							$message->to (Setting::param ('site' , 'mail')['value'])->subject ('New prescription uploaded to ' . Setting::param ('site' , 'app_name')['value']);
						});

						if($pres_required) return Response::json (['status' => false , 'msg' => 'You are mandated to upload prescription to place the order.']);
						else return Response::json (['status' => true , 'msg' => 'Your order has been requested successfully!']);
					}
				} else return Response::json (['status' => false , 'msg' => 'Sorry, Please login first!'] , 400);
			} catch (Exception $e) {
				return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
			}
		} else {
			try {
				if (!Auth::check ()) return Response::json (['status' => false, 'msg' => 'You are not authorized to do this action.'] , 401);
				
				$email = Auth::user ()->email;
				$user_type = Auth::user ()->user_type_id;
				$prescription = $request->file('prescription') ? $request->file('prescription') : '';
				$is_pres_required = isset($request->is_pres_required) ? $request->is_pres_required : 0;

				if ($is_pres_required && empty($prescription))  return Response::json (['status' => false, 'msg' => 'Prescription is required for this order.'] , 412);

				$file_name = $store_original = "";

				if (!empty($prescription)) {
					$validator = Validator::make($request->all(), [
						'prescription' => 'mimes:jpeg,jpg,png|max:8192',
					],
					[   
						'prescription.mimes'      => 'Please upload prescription in jpeg, jpg or png format.',
						'prescription.max' => 'The prescription size may not be greater than 8MB.',
					]);

					if ($validator->fails()) return Response::json (['status' => false, 'msg' => $validator->errors()->first()] , 400);

					$extn = $request->file('prescription')->extension();
					$file_name = Auth::user ()->id . '_' . time () . '.' . $extn;
					$path = '/public/prescription/'. $email . '/';

					$store_original = $request->file('prescription')->storeAs($path, $file_name);

					if (!$store_original) return Response::json (['status' => false , 'msg' => 'File Not saved !'], 403);
					else { //thumbnail creation
						$path = $path . 'thumbnails/';
						if(!Storage::exists($path)) Storage::makeDirectory($path);
						$avatar = $request->file('prescription');
						Image::make($avatar)->resize(60, 60)->save( public_path('/storage/prescription/' . $email . '/thumbnails/' . $file_name) );						
					}

				}

				$pres_required = false;
				$current_medicines = SessionData::select ('medicine_id' , 'medicine_count')->where ('user_id' , '=' , $email)->get ();
				if ($store_original || count ($current_medicines) > 0){ // Save Prescription
					$prescription = new Prescription;
					$user_id = Auth::user ()->id;
					$file_name ?  $prescription->path = $file_name : '';
					$prescription->created_at = date ('Y-m-d H:i:s');
					$prescription->user_id = $user_id;
					$prescription->created_by = $user_id;
					$prescription->save ();

					$pres_id = $prescription->id;
					$invoice = new Invoice;
					$invoice->pres_id = $pres_id;
					$invoice->user_id = $user_id;
					$invoice->created_at = date ('Y-m-d h:i:s');
					$invoice->updated_at = date ('Y-m-d h:i:s');
					$invoice->created_by = $user_id;
					$invoice->updated_by = $user_id;
					$invoice->save ();
					$invoice_id = $invoice->id;

					
					if (count ($current_medicines) > 0) {
							
						foreach ($current_medicines as $medicine) {
							$medicine_details = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->where('id', $medicine['medicine_id'])->first()->toArray();
							if($medicine_details['is_pres_required'] && !$file_name) $pres_required = true;
							else {
								$total_discount = $medicine_details['discount'] * $medicine['medicine_count'];
								$total_price = ($medicine_details['mrp'] * $medicine['medicine_count']) - $total_discount;
								$itemList = new ItemList;
								$itemList->invoice_id = $invoice_id;
								$itemList->medicine = $medicine['medicine_id'];
								$itemList->quantity = $medicine['medicine_count'];
								$itemList->unit_price = $medicine_details['mrp'];
								$itemList->discount_percentage = $medicine_details['discount'];
								$itemList->discount = $total_discount;
								$itemList->total_price = $total_price;
								$itemList->created_at = date ('Y-m-d H:i:s');
								$itemList->updated_at = date ('Y-m-d H:i:s');
								$itemList->updated_by = $user_id;
								$itemList->created_by = $user_id;
								$itemList->save ();
								SessionData::where ('user_id' , '=' , $email)->where('medicine_id', $medicine['medicine_id'])->delete ();	
							}
						}
							
					}
					$data['email'] = $email;
					$name = '';
					if ($user_type == UserType::CUSTOMER ()) $name = Auth::user ()->customer->name;
					elseif ($user_type == UserType::MEDICAL_PROFESSIONAL ()) $name = Auth::user ()->professional->prof_name;
					$name = ($name) ? $name : $email;

					$data['email'] = $email;

					Mail::send ('emails.prescription_upload' , array('name' => $name) , function ($message) use ($data) {
						$message->to ($data['email'])->subject ('New order has been submitted to ' . Setting::param ('site' , 'app_name')['value']);
					});

					Mail::send ('emails.admin_prescription_upload' , array('name' => $name) , function ($message) use ($data) {
						$message->to (Setting::param ('site' , 'mail')['value'])->subject ('New prescription uploaded to ' . Setting::param ('site' , 'app_name')['value']);
					});

					if($pres_required) return Response::json (['status' => false , 'msg' => 'You are mandated to upload prescription to place the order.']);
					else return Response::json (['status' => true , 'msg' => 'Your order has been requested successfully!']);
				}
			}
			catch (Exception $e) {
				return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
			}

		}
	}
	// STORED PRECRIPTION for API
	public function anyGetPrescriptionsDetails (Request $request, $is_category = NULL) {
		try {

			if (!Auth::check ()) return Response::json (['status' => false, 'msg' => 'You are not authorized to access this content.'] , 401);

			$email = Auth::user ()->email;

			if (is_null ($email)) return Response::json (['status' => false, 'msg' => 'Email is not available.'] , 400);
			
			$user_id = Auth::user ()->id;

			if (empty($user_id) || is_null ($user_id)) return Response::json (['status' => false, 'msg' => 'User not available.'] , 404);

			$prescriptions = Prescription::select ('i.*', 'prescription.status' , 'prescription.path' , 'prescription.id as pres_id' , 'prescription.created_at as date_added')->where ('prescription.user_id' , '=' , $user_id)->where ('is_delete' , '=' , 0)
			->join ('invoice as i' , 'i.pres_id' , '=' , 'prescription.id')
			// ->join ('invoices as i' , 'i.pres_id' , '=' , DB::raw ("prescription.id AND i.payment_status IN (" . PayStatus::PENDING () . ",0) "))
			->orderBy('prescription.created_at', 'desc');

			if (empty($prescriptions)) return Response::json (['status' => false, 'msg' => 'Prescriptions not available.'] , 404);

			$prescription = [];
			switch ($is_category) {
				case (PrescriptionStatus::VERIFIED ()):
					$prescriptions = $prescriptions->where ('status' , '=' , PrescriptionStatus::VERIFIED ());
					break;
				case(PrescriptionStatus::UNVERIFIED ()):
					$prescriptions = $prescriptions->where ('status' , '=' , PrescriptionStatus::UNVERIFIED ());
					break;
				default:
					break;
			}
			$results = $prescriptions->get ();

			foreach ($results as $result) {
				$items = [];
				$medicines = Medicine::medicines ();
				if (!is_null ($result->id) || !empty($result->id)) {
					$carts = ItemList::where ('invoice_id' , '=' , $result->id)->get ();
					foreach ($carts as $cart) {
						$items[] = ['id' => $cart->id ,
							'item_id' => $cart->medicine ,
							'item_code' => $medicines[$cart->medicine]['item_code'] ,
							'item_name' => $medicines[$cart->medicine]['item_name'] ,
							'unit_price' => $cart->unit_price ,
							'discount_percent' => $cart->discount_percentage ,
							'discount' => $cart->discount ,
							'quantity' => $cart->quantity ,
							'total_price' => $cart->total_price
						];
					}
				}
				$presc = (($result->path) && Storage::disk('PRESCRIPTION')->exists($email . '/thumbnails/' . $result->path)) ? Storage::disk('PRESCRIPTION')->url($email . '/thumbnails/' . $result->path) : Storage::disk('PRESCRIPTION')->url('no_pres_square.png');
				$img = (($result->path) && Storage::disk('PRESCRIPTION')->exists($email . '/' . $result->path)) ? Storage::disk('PRESCRIPTION')->url($email . '/' . $result->path) : '';
				$details = [
					'id' => (is_null ($result->id)) ? 0 : $result->id ,
					'invoice' => (is_null ($result->invoice)) ? '' : $result->invoice ,
					'sub_total' => (is_null ($result->sub_total)) ? 0 : $result->sub_total ,
					'discount' => (is_null ($result->discount)) ? 0 : $result->discount ,
					'tax' => (is_null ($result->tax)) ? 0 : $result->tax ,
					'shipping' => (is_null ($result->shipping)) ? 0 : $result->shipping ,
					'total' => (is_null ($result->total)) ? 0 : $result->total ,
					'created_on' => (is_null ($result->date_added)) ? 0 : $result->date_added ,
					'cart' => $items ,
					'shipping_status' => (is_null ($result->shipping_status)) ? '' : ShippingStatus::statusName ($result->shipping_status),
					'pres_status' =>PrescriptionStatus::statusName ($result->status) ,
					'invoice_status' => is_null ($result->status_id) ? '' : InvoiceStatus::statusName ($result->status_id),
					'img' => $img,
					'path' => $presc
				];
				$prescription[] = $details;
			}
			$payment_mode = Setting::select ('value')->where ('group' , '=' , 'payment')->where ('key' , '=' , 'mode')->first ();
			$payment_mode = $payment_mode->value;
			$link_url = "";
			if ($payment_mode == PaymentGateway::PAYU_INDIA ()) $link_url = 'medicine/make-payment';
			else if ($payment_mode == PaymentGateway::PAYPAL ()) $link_url = 'medicine/make-paypal-payment';

			return Response::json (['status' => true , 'msg' => 'Prescriptions Obtained' , 'data' => ['prescriptions' => $prescription , 'payment_url' => $link_url , 'currency' => Setting::param ('site' , 'currency')['value'] , 'curr_position' => Setting::param ('site' , 'curr_position')['value']]]);
		}
		catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}
	// STORED PRECRIPTION
	public function postGetPresImg (Request $request) {
		try {
			$validator = Validator::make($request->all(), [
				'pres_id' => 'required',
			],[
				'pres_id.required' => 'The prescription id is required.'
			]);

			if ($validator->fails()) return Response::json (['status' => false, 'msg' => $validator->errors()->first()] , 409);

			$pres_id = $request->pres_id;
			$u = Prescription::where('id',$pres_id)->first(); //User::join ('prescription' , 'prescription.user_id' , '=' , 'users.id')->where ('id' , '=' , $pres_id)->first ();
			
			if(Storage::disk('PRESCRIPTION')->exists($u->path)) {
				$result = ['result' => array('status' => true , 'msg' => "", 'data' => Storage::disk('PRESCRIPTION')->url($u->path))];
			} else
				$result = ['result' => array('status' => false , 'msg' => "No prescription uploaded.")];

			return Response::json ($result);
		} catch (Exception $e) {
			return Response::make (['status' => false , 'msg' => $e->getMessage()] , $e->getCode());
		}	
	}
	
	//cart page load
	public function getMyCart () {
		return Auth::check() ? View::make ('users.mycart') :  Redirect::to('/');
	}
	//cart API load
	public function getMyCartItems ($isWeb = NULL) {
		if(Auth::check()) {
			$email = Session::has ('user_id') ? Session::get ('user_id') : Auth::user() -> email;
			$orders = SessionData::where ('user_id' , '=' , $email)->get ()->toArray();
			$current_orders = [];
			if($isWeb) {
				$total = $sub_total = 0;
				foreach ($orders as $result) {
					$items = [];
					$medicine = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->where('id', $result['medicine_id'])->first ();
					if ($medicine) {
						$total = (number_format($medicine['mrp'],2) * $result['medicine_count']) - (number_format($medicine['discount'],2) * $result['medicine_count']);			
						$items = [
							'session_id' => $result['id'],
							'mrp' => $medicine['mrp'] ? number_format($medicine['mrp'],2) : 0 ,
							'discount' => $medicine['discount'] ? number_format($medicine['discount'],2) : 0,
							'product_image' => ($medicine['product_image'] && Storage::disk('MEDICINE_THUMB')->exists($medicine['product_image'])) ? Storage::disk('MEDICINE_THUMB')->url($medicine['product_image']) : Storage::disk('MEDICINE_PIC')->url('no-image-available.png'),
							'med_count' => $result['medicine_count'],
							'is_pres_required' => $result['is_pres_required'],
							'unit_price' => Setting::currencyFormat (number_format($medicine['mrp'],2)),
							'total' => Setting::currencyFormat ($total)
						];
					}

					$current_orders[] = $items;
					$sub_total += $total;
					
				}
				return Response::json (['status' => true, 'msg' => '', 'data' => ['current_orders' => $current_orders, 'sub_total' => Setting::currencyFormat ($sub_total)]]);
			} else {
				foreach ($orders as $result) {
					$items = [];
					$medicine = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->where('id', $result['medicine_id'])->first ();

					if ($medicine) {
						$items = [
							'product_image' => ($medicine['product_image'] && Storage::disk('MEDICINE_THUMB')->exists($medicine['product_image'])) ? Storage::disk('MEDICINE_THUMB')->url($medicine['product_image']) : Storage::disk('MEDICINE_PIC')->url('no-image-available.png')
						];
					}
					$current_orders[] = array_merge($result, $items);
				}
			}
			return Response::json (['status' => true, 'msg' => '', 'data' => $current_orders]);
		} else return Response::json (['status' => false, 'msg' => 'Not Authorised!']);
	}	
	public function getPrescriptionView () {
		if (!Auth::check ()) return Redirect::to ('/');
		$prescription = [];
		return View::make ('users.prescriptions', compact('prescription'));
	}
	public function anyDownloading (Request $request) {
		$email = Auth::user()->email;
		$file_name = $request->fname;
		if ($file_name == 'no_pres_square.pn') $pathToFile = Storage::disk('PRESCRIPTION')->get($file_name);
		else $pathToFile = Storage::disk('PRESCRIPTION')->url($email . '/' . $file_name) ;

		return response()->download(
			($file_name == 'no_pres_square.pn') ? public_path('/storage/prescription/' . $file_name) : public_path('/storage/prescription/' . $email . '/' . $file_name), 
			$file_name, 
			['Content-Type' => 'application/jpeg', 'Content-Disposition' => 'attachment']);

	}
	public function getAwaitingResponse() {
		if (!Auth::check ()) return Redirect::to ('/');

		$email = Auth::user() ->email;
		$invoices = Invoice::where ('user_id' , '=' , Auth::user ()->id)->where ('status_id' , '=' , InvoiceStatus::PAID ())->whereIn ('shipping_status' , array(0 , ShippingStatus::NOTSHIPPED ()))->get ();
		//dd($invoices);

		return View::make ('users.awaiting_shipment', compact('invoices', 'email'));
	}
	public function getShippedOrder() {
		if(!Auth::check()) return Redirect::to ('/');
		$email = Auth::user() -> email;
		$user_id = Auth::user ()->id;
		$invoices = Invoice::where ('user_id' , '=' , $user_id)->where ('shipping_status' , '=' , ShippingStatus::SHIPPED ())->get (); 
		return View::make ('users.shipped_order', compact('invoices', 'email'));
	}
	public function getMyPrescription (Request $request) { // jquery appending
		
		if (!Auth::check ()) return Redirect::to ('/');
		$is_category = $request->is_category;
		$email = Auth::user() -> email;
		$user_id = Auth::user ()->id;
		$invoices = Invoice::where ('user_id' , '=' , $user_id)->get ();
		$prescriptions = Prescription::select ('i.*', 'prescription.status' , 'prescription.path' , 'prescription.id as pres_id' , 'prescription.created_at as date_added')->where ('prescription.user_id' , '=' , $user_id)
			->join ('invoice as i' , 'i.pres_id' , '=' , DB::raw ("prescription.id AND i.payment_status IN (" . PayStatus::PENDING () . ",0) "))
		->orderBy('prescription.created_at', 'desc');

		$prescription = [];
		switch ($is_category) {
			case (PrescriptionStatus::VERIFIED ()):
				$prescriptions = $prescriptions->where ('prescription.status' , '=' , PrescriptionStatus::VERIFIED ())->where ('prescription.is_delete', 0);
				break;
			case(PrescriptionStatus::UNVERIFIED ()):
				$prescriptions = $prescriptions->where ('prescription.status' , '=' , PrescriptionStatus::UNVERIFIED ())->where ('prescription.is_delete', 0);
				break;
			case(PrescriptionStatus::REJECTED ()):
				$prescriptions = $prescriptions->where ('prescription.status' , '=' , PrescriptionStatus::REJECTED ());
				break;
			// default:
			// 	$prescriptions = $prescriptions->where ('prescription.status' , '=' , PrescriptionStatus::VERIFIED ());
			// break;
		}
		$results = $prescriptions->get ();
		if($results) {
			$payment_mode = Setting::select ('value')->where ('group' , 'payment')->where ('key' , 'mode')->first ();
			$payment_mode = $payment_mode->value;
			$payment = '';
			if($payment_mode==PaymentGateway::PAYU_INDIA()) $payment = 'payu';
			else if($payment_mode==PaymentGateway::PAYPAL()) $payment = 'paypal';

			foreach ($results as $result) {
				$items = [];
				$medicines = Medicine::medicines ();
				if (!is_null ($result->id) || !empty($result->id)) {
					$carts = ItemList::where ('invoice_id' , '=' , $result->id)->get ();

					foreach ($carts as $cart) {
						$items[] = [
							'id' => $cart->id ,
							'item_id' => $cart->medicine ,
							'item_code' => $medicines[$cart->medicine]['item_code'] ,
							'item_name' => $medicines[$cart->medicine]['item_name'] ,
							'unit_price' => number_format ($cart->unit_price , 2) ,
							'discount_percent' => number_format ($cart->discount_percentage , 2) ,
							'discount' => number_format ($cart->discount , 2) ,
							'quantity' => $cart->quantity ,
							'total_price' => number_format ($cart->total_price , 2),
							'sub_total' => number_format ($cart->quantity * $cart->unit_price , 2)
						];
					}
				}

				$presc = (($result->path) && Storage::disk('PRESCRIPTION')->exists($email . '/thumbnails/' . $result->path)) ? Storage::disk('PRESCRIPTION')->url($email . '/thumbnails/' . $result->path) : Storage::disk('PRESCRIPTION')->url('no_pres_square.png');
				$img = (($result->path) && Storage::disk('PRESCRIPTION')->exists($email . '/' . $result->path)) ? url('/') . '/medicine/downloading?fname='. $result->path : '';
				
				$details = [
					'id' => (is_null ($result->id)) ? 0 : $result->id ,
					'invoice' => (is_null ($result->invoice)) ? '' : $result->invoice ,
					'sub_total' => (is_null ($result->sub_total)) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($result->sub_total) ,
					'discount' => (is_null ($result->discount)) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($result->discount) ,
					'tax' => (is_null ($result->tax)) ? 0 : $result->tax ,
					'shipping' => (is_null ($result->shipping)) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($result->shipping) ,
					'total' => (is_null ($result->total)) ? 0 : $result->total ,
					'created_on' => (is_null ($result->date_added)) ? '' : date('d-m-Y' , strtotime($result->date_added)) ,
					'cart' => $items ,
					'shipping_status' => ShippingStatus::statusName($result->shipping_status) ,
					'pres_status' => PrescriptionStatus::statusName($result->status) ,
					'invoice_status' => InvoiceStatus::statusName($result->status_id) ,
					'img' => $img,
					'path' => $presc,
					'net_payable' => (is_null ($result->total)) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($result->total),
					'invoice_id' => (($result->id) && PrescriptionStatus::statusName($result->status) == 'Verified') ? $result->id : '',
					'payment_mode' => $payment
				];
				$prescription[] = $details;
			}
		}
	//	dd($prescription);
		
		return Response::json(['status' => true, 'msg' => '', 'data' => ['prescription' => $prescription, 'email' => $email, 'payment_mode' => $payment_mode]]);
	}
	public function anyAddMedicine (Request $request) {
		if(Schema::hasTable('medicine_request')) {
			$name = $request->name;
			$oldMed = NewMedicine::where ('name' , '=' , $name)->first();
			$who = '';
			$email = isset($request->email) ? $request->email : (Auth::check() ? Auth::user()->email : NULL);
			$user_id = Auth::check() ? Auth::user()->id : NULL;
			if ($oldMed) {
				$oldMed = $oldMed->toArray();
				$newCount = array('count' => NewMedicineEmail::where('request_id', $oldMed['id'])->count() + 1 , 'updated_at' => date ('Y-m-d H:i:s'));
				$affectedRows = NewMedicine::where ('name' , '=' , $name)->update ($newCount);
				$who = new NewMedicineEmail;
				$who->email = $email;
				$who->user_id = $user_id;
				$who->request_id = $oldMed['id'];
				$who->created_at = date ('Y-m-d H:i:s');
				$who->save ();
			} else {
				$newMed = new NewMedicine;
				$newMed->name = $name;
				$newMed->count = 1;
				$newMed->created_at = date ('Y-m-d H:i:s');
				$newMed->save ();
				$who = new NewMedicineEmail;
				$who->email = $email;
				$who->user_id = $user_id;
				$who->request_id = $newMed->id;
				$who->created_at = date ('Y-m-d H:i:s');
				$who->save ();
			}
			$result = $who ? ['status' => true, 'msg' => 'This medicine is not available for now. Please check availability later.'] : ['status' => true, 'msg' => 'Something went wrong. Please try again later.'];
		} else $result = ['status' => false, 'msg' => 'No migration exists please setup your system.'];
		return Response::json ($result);
	}
}
