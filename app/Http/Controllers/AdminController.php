<?php

namespace App\Http\Controllers;

if (!defined('CURRENCY_BEFORE')) define('CURRENCY_BEFORE', 'BEFORE');
if (!defined('CURRENCY_AFTER')) define('CURRENCY_AFTER', 'AFTER');
if (!defined('CACHE_PARAM_MEDICINE')) define('CACHE_PARAM_MEDICINE', 'medicines');
if (!defined('CACHE_PARAM_MEDICINE_A')) define('CACHE_PARAM_MEDICINE_A' , 'medicines_a');
if (!defined('INVOICE_PREFIX')) define('INVOICE_PREFIX' , 'INV_');

use App\Models\TopBrands;
use Illuminate\Http\Request;

use App\Imports\MedicineImport;

use App\Models\MedicalProfessional;
use App\Models\PrescriptionStatus;
use App\Models\NewMedicineEmail;
use App\Models\ShippingStatus;
use App\Models\InvoiceStatus;
use App\Models\Prescription;
use App\Models\NewMedicine;
use App\Models\UserStatus;
use App\Models\PayStatus;
use App\Models\UserType;
use App\Models\Medicine;
use App\Models\ItemList;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Admin;
use App\Models\User;

use App\Mail\resetAdminPassword;

use Validator;
use Response;
use Redirect;
use Session;
use Storage;
use Illuminate\Support\Facades\Cache;

use Image;
use Maatwebsite\Excel\Facades\Excel;

use Hash;
use View;
use Auth;
use Mail;
use Log;
use DB;

class AdminController extends Controller
{

	 public function index(){
        return view('admin.signin');
    }
    	public function logout() {
        Auth::logout();
        return redirect('/admin-login');
    }

    
   	public function anyLogin (Request $request) {
		// Validation Rules
		$validatedData = \Validator::make($request->all(), [
            'log_email'=> 'required',
            'password' => 'required',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status'=>false,'message' => $validatedData->messages(), 'error' => 0]);
        }
		
		// Success
		$email = $request->log_email ? $request->log_email : '';
		$password = $request->password ? $request->password : '';
		// Check if credientials work
		if (Auth::attempt (array('email' => $email , 'password' => $password , 'user_type_id' => UserType::where('user_type', 'ADMIN')->pluck('id')->first() , 'user_status' => UserStatus::where('name', 'ACTIVE')->pluck('id')->first()) , true)) return redirect('/admin/dashboard');
		else {                                                                                                                    // FAILURE
			Session::flash ('flash_message' , '<strong>Sorry !</strong> Invalid Login Credentials');
			Session::flash ('flash_type' , 'alert-danger');

			return Redirect::to ('/admin-login');
		}
		

	}

		public function getDashboard () {
		return View::make ("admin.dashboard");
	}

	public function getTodayPresDash () {
		// Get Pending Prescription List
		$pres = Prescription::select ('prescription.status' , 'prescription.created_at' , 'email' , 'path' , 'prescription.id as pres_id')
			->join ('users' , 'users.id' , '=' , 'prescription.user_id')
			->where ('prescription.status' , '=' , PrescriptionStatus::UNVERIFIED ())
			->where ('prescription.is_delete' , '=' , 0)
			->orderBy ('prescription.id' , 'DESC')
			->where ('path' , '>=' , strtotime ('today midnight'));                                                     // Path is saved as time stamp.
		$i = 0;
		$notif = '<div class="panel-heading b-b">

		         <strong>You have ' . $pres->count () . ' new Prescription(s)</strong>
		         </div>';
		foreach ($pres->get () as $press) {
			if ($press->status == 'active' || $press->status == 'shipped' || $press->status == 'paid') {
				$status = 'label label-info';
				$url_status = 0;
			} else {
				$status = "";
				$url_status = 1;
			}
			$date = "";
			if ($press->path != "") {
				$date = date ("h:i A" , strtotime ($press->created_at));
			} else
				if ($press->status == 'shipped')
					$status = 'label bg-success';
				else
					$status = 'label bg-danger';
			$notif .= "
			  <a href='" . url () . "/admin/pres-edit/$press->pres_id/$url_status'>
			  <div class='panel-heading b-b'>
				 <span class='media-body block m-b-none' >" .
				$press->email . "</br>"
				. "<small class='text-muted'>" . $date . "</small>
				 </span>
				 <span class='" . $status . "'>" . ucfirst ($press->status) . "</span>
			   </div>
			 </a>";
		}
		return Response::json (['notif' => $notif , 'todaysCount' => $pres->count ()]);
	}

	public function anyAdminPaySuccess (Request $request) {

		$invoice = $request->pres_id;
		$invoice = Invoice::find ($invoice);
		$invoice->status_id = InvoiceStatus::PAID ();
		$invoice->payment_status = PayStatus::SUCCESS ();
		$invoice->updated_at = date ('Y-m-d H:i:s');
		$invoice->updated_by = Auth::user ()->id;
		$invoice->save ();
		// User
		$user_detail = $invoice->getUser;
		$type = $user_detail->user_type_id;
		// Send Paid Mail
		if ($type == UserType::CUSTOMER ()) {
			$user = Customer::select ('mail' , 'name')->find ($user_detail->user_id);
			$user_email = $user->mail;
			$user_name = $user->name;
		} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
			$user = MedicalProfessional::select ('prof_mail as mail' , 'prof_name as name')->find ($user_detail->user_id);
			$user_email = $user->mail;
			$user_name = $user->name;
		}
		Mail::send ('emails.paid' , array('name' => $user_name) , function ($message) use ($user_email) {
			$message->to ($user_email)->subject ('Your payment received at ' . Setting::param ('site' , 'app_name')['value']);
		});

		return Redirect::to ('/admin/load-active-prescription');
	}


	public function getDashDetail () {

		// Get Details Count
		$medicine = DB::table ('medicines')->select (DB::raw ('COUNT(*) as count'))->where ('is_delete' , '=' , 0);
		$active_user = DB::table ('users')->select (DB::raw ('COUNT(*) as count'))->where ('user_status' , '<>' , UserStatus::INACTIVE ());
		$counts = DB::table ('prescriptions')->select (DB::raw ('COUNT(*) as count'))->where ('status' , '=' , PrescriptionStatus::VERIFIED ())->unionAll ($medicine)->unionAll ($active_user)->get ();
		$rev = $mr = $disc_total = 0.0;
		$sales_details = Invoice::select (DB::raw ("count(*) as total_sales,SUM(total) as total_revenue,(SELECT count(*) as monthly_sales FROM invoice where created_at BETWEEN '" . date ('Y-m-01 00:00:00') . "' AND NOW() AND status_id = " . InvoiceStatus::PAID () . ") as monthly_count,(SELECT SUM(total) as monthly_revenue FROM invoice where created_at BETWEEN '" . date ('Y-m-01 00:00:00') . "' AND NOW() AND status_id = " . InvoiceStatus::PAID () . ") as monthly_revenue"))
			->where ('status_id' , '=' , InvoiceStatus::PAID ())->first ();

		return Response::json (['pres' => $counts[0]->count ,
			'med' => $counts[1]->count ,
			'user' => $counts[2]->count ,
			'rev' => is_null ($sales_details->total_revenue) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($sales_details->total_revenue) ,
			'mp' => is_null ($sales_details->monthly_count) ? 0 : $sales_details->monthly_count ,
			'mr' => is_null ($sales_details->monthly_revenue) ? Setting::currencyFormat (0.00) : Setting::currencyFormat ($sales_details->monthly_revenue)

		]);
	}

		public function getDashOrd () {
		$shipping = DB::table ('invoices')->select (DB::raw ('count(*) as shipped'))->where ('shipping_status' , '=' , ShippingStatus::SHIPPED ());
		$paid = DB::table ('invoices')->select (DB::raw ('count(*) as paid'))->where ('status_id' , '=' , InvoiceStatus::PAID ())->whereIn ('shipping_status' , [0 , ShippingStatus::NOTSHIPPED ()]);
		$customer = DB::table ('customers')->select (DB::raw ('count(*) as customer'))->where ('is_delete' , '=' , 0);
		$counts = DB::table ('medical_professional')->select (DB::raw ('count(*) as count'))->where ('prof_is_delete' , '=' , 0)->unionAll ($shipping)->unionAll ($customer)->unionAll ($paid)->get ();
		dd($counts);

		return Response::json (['prof' => $counts[0]->count , 'shipped' => $counts[1]->count , 'cust' => $counts[2]->count , 'tobe' => $counts[3]->count]);
	}

		public function getLoadCustomers () {
		return View::make ('admin.customerlist');

	}
	public function systemSetup() {
		return redirect('/system-setup');
	}


	public function getLoadCustomersList (Request $request) {
		$totalData = Customer::select('customer.*')->where ('is_delete' , '!=' , 1)->join ('users' , 'users.user_id' , '=' , DB::raw ('customer.id AND user_type_id=' . UserType::CUSTOMER ()))->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, 'customer.' .  $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $customers = Customer::select('customer.*', 'users.user_status as userStatus')->where ('is_delete' , '!=' , 1)->leftjoin ('users' , 'users.user_id' , '=' , DB::raw ('customer.id AND user_type_id=' . UserType::CUSTOMER ()));

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $customers = $customers->where('customer.name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $customers->count();

		$orders = empty($ordersBy) ? 'customer.mail ASC' : implode(', ',array_filter($ordersBy));

        $customers = $customers
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $customers
        );

        return $json_data;
	}

	public function getCustomerDelete (Request $request) {
		$customer = Customer::find ($request->customer_id);
		$user = $customer->user ();
		// Customer Save
		$customer->is_delete = 1;
		$customer->save ();
		// User Save
		$user->user_status = UserStatus::INACTIVE ();
		$user->save ();

		return Redirect::to ('admin/load-customers');
	}

	public function getUserChangeStatus (Request $request) {
		$user = User::where ('user_id' , '=' , $request->customer_id)->where ('user_type_id' , '=' , UserType::CUSTOMER ());
		$user->update (array('user_status' => UserStatus::ACTIVE ()));

		return Redirect::to ('admin/load-customers');
	}

	public function getLoadMedicalprof () {
		return View::make ('admin.mproflist');

	}


	public function getLoadMedicalprofList (Request $request) {
		$totalData = MedicalProfessional::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, 'ed_professional.' .  $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $mProfs = MedicalProfessional::select('ed_professional.*', 'users.user_status as userStatus')->where ('prof_is_delete' , '!=' , 1)->join ('users' , 'users.user_id' , '=' , DB::raw ('ed_professional.id AND user_type_id=' . UserType::MEDICAL_PROFESSIONAL()));

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $mProfs = $mProfs->where('ed_professional.prof_name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $mProfs->count();

		$orders = empty($ordersBy) ? 'ed_professional.prof_mail ASC' : implode(', ',array_filter($ordersBy));

        $mProfs = $mProfs
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $mProfs
        );

        return $json_data;

	}

	public function getMedicalprofDelete(Request $request){
		$medicalProfessional = MedicalProfessional::find ($request->mprof_id);
		$user = $medicalProfessional->user ();
		// Professional Save
		$medicalProfessional->prof_is_delete = 1;
		$medicalProfessional->save ();
		// User Save
		$user->user_status = UserStatus::INACTIVE ();
		$user->save ();

		return Redirect::to ('admin/load-medicalprof');
	}

	public function getMedicalprofChangeStatus(Request $request){
		$user = User::where ('user_id' , '=' , $request->mprof_id)->where ('user_type_id' , '=' , UserType::MEDICAL_PROFESSIONAL ());
		$user->update (array('user_status' => UserStatus::ACTIVE ()));

		return Redirect::to ('admin/load-medicalprof');
	}
    

    
	/**
	 * Load Medicine List
	 *
	 * @return mixed
	 */
	public function getLoadMedicines () {
		if(Auth::check())
			return View::make ('admin.medicinelist');
		return Redirect::to ('/admin-login');

	}

	public function getLoadMedicinesList(Request $request) {
		$totalData = Medicine::select('id' , 'item_name as name' , 'batch_no' , 'manufacturer as mfg' , 'group' , 'expiry as exp' , 'item_code' , 'selling_price as mrp' , 'composition' , 'is_pres_required')->where ('is_delete' , '=' , 0)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, 'medicines.' .  $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
	}
        $medicines = Medicine::select('id' , 'item_name as name' , 'batch_no' , 'manufacturer as mfg' , 'group' , 'expiry as exp' , 'item_code' , 'selling_price as mrp' , 'composition' , 'is_pres_required')->where ('is_delete' , '=' , 0);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $medicines = $medicines->where('medicines.item_name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $medicines->count();

		$orders = empty($ordersBy) ? 'item_name ASC' : implode(', ',array_filter($ordersBy));

        $medicines = $medicines
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $medicines
        );

        return $json_data;
	}

	/**
	 * Add Medicine Screen
	 *
	 * @return mixed
	 */
	public function getAddMed () {
		return View::make ("admin.addmedicine");
	}

	/**
	 * Add New Medicines
	 */
	public function postNewMed (Request $request) {

		if (!$this->isCsrfAccepted ())
			return Redirect::to ('admin/add-med')->with ('message' , 'Bad Request');

		$input = $request->all ();
		$store_original = $fileName = NULL;
		if($request->file('file')) {
			$fileName = preg_replace('/[^A-Za-z0-9\-]/', '-', $input['item_name']) . '_' . time() . '.' . $request->file('file')->extension();

			$store_original = $request->file('file')->storeAs('/public/medicine', $fileName);
					
			if (!$store_original) throw new Exception('File Not saved !' , 403);
			else { //thumbnail creation
				$path = '/public/medicine/thumbnails';
				if(!Storage::exists($path)) Storage::makeDirectory($path);

				$avatar = $request->file('file');
				Image::make($avatar)->resize(60, 60)->save( public_path('/storage/medicine/thumbnails/' . $fileName) );
			}
		}
		if ($input['id'] == "") {
			$medicine = new Medicine;
			$medicine->item_code = $input['item_code'];
			$medicine->item_name = $input['item_name'];
			$medicine->batch_no = $input['batch_no'];
			$medicine->quantity = $input['quantity'];
			$medicine->cost_price = $input['cost_price'];
			$medicine->purchase_price = $input['purchase_price'];
			$medicine->rack_number = $input['rack_number'];
			$medicine->selling_price = $input['selling_price'];
			$medicine->expiry = date ('Y-m-d' , strtotime ($input['expiry']));
			$medicine->tax = $input['tax'];
			$medicine->composition = $input['composition'];
			$medicine->discount = $input['discount'];
			$medicine->manufacturer = $input['manufacturer'];
			$medicine->group = $input['group'];
			$medicine->product_image = $fileName;

			$medicine->is_pres_required = $input['is_prescription'];
			$medicine->save ();

		} else {
			if ($store_original) $uploaded_image = Medicine::where ('id' , '=' , $input['id'])->pluck('product_image')->first();
			$edit = array('item_code' => $input['item_code'] ,
				'item_name' => $input['item_name'] ,
				'batch_no' => $input['batch_no'] ,
				'quantity' => $input['quantity'] ,
				'cost_price' => $input['cost_price'] ,
				'purchase_price' => $input['purchase_price'] ,
				'rack_number' => $input['rack_number'] ,
				'selling_price' => $input['selling_price'] ,
				'expiry' => date ('Y-m-d' , strtotime ($input['expiry'])) ,
				'tax' => $input['tax'] ,
				'discount' => $input['discount'] ,
				'manufacturer' => $input['manufacturer'] ,
				'group' => $input['group'] ,
				'composition' => $input['composition'] ,
				'is_pres_required' => $input['is_prescription'],
				'product_image' => $fileName
			);
			$affectedRows = Medicine::where ('id' , '=' , $input['id'])->update ($edit);

			if ($affectedRows) { //fetching user profile_pic if it not empty then will delete it
				if (Storage::disk('MEDICINE_PIC')->has($uploaded_image)) { //checking image is existing or not
					Storage::disk('MEDICINE_PIC')->delete($uploaded_image); //deleting original pic
					Storage::disk('MEDICINE_THUMB')->delete($uploaded_image); //deleting thumbnail
				}
			}
		}
		// Clear Cache for the medicines...
		Cache::forget(CACHE_PARAM_MEDICINE);

		return Redirect::to ('admin/load-medicines');

	}
	
	/**
	 * Edit Medicine Screen
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getMedicineEdit (Request $request) {
		$medicine = Medicine::where ('id' , '=' , $request->id)->get ()->first ();

		return View::make ("admin.addmedicine")->with ('details' , $medicine);
	}

	/**
	 * Delete Medicine List
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getMedicineDelete (Request $request) {
		$medicine = Medicine::find ($request->id);
		$medicine->is_delete = 1;
		$medicine->save ();
		Cache::forget (CACHE_PARAM_MEDICINE);

		return Redirect::to ('admin/load-medicines');
	}

	/**
	 * Update Medicine Prescription List
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getMedicinePrescription (Request $request) {
		$medicine = Medicine::find ($request->id);
		$medicine->is_pres_required = ($medicine->is_pres_required == 1) ? 0 : 1;
		$medicine->save ();
		Cache::forget (CACHE_PARAM_MEDICINE);

		return Redirect::to ('admin/load-medicines');
	}

		public function medcineBulkUpload (Request $request) {
		try {
			if (!$this->isCsrfAccepted ()) {
				return response()->json('Please Login for upload', 403);//return Response::json (['status' => 'FAILURE' , 'msg' => 'Please Login for upload'] , 403);
			} else if (!$request->file('file'))
				return Response::json (['status' => 'FAILURE' , 'msg' => 'Please Upload a file'] , 400);//throw new Exception('Please Upload a file' , 400);
			else {
				Excel::import(new MedicineImport, $request->file('file'));
				return Response::json ('success' , 200);
			}
		} catch (Exception $e) {
			return Response::make (['status' => 'FAILURE' , 'msg' => $e->getMessage()] , $e->getCode());
		}
	}

		public function getLoadTopBrands () {
		if(Auth::check())
			return View::make ('admin.top_brands_list');
		return Redirect::to ('/admin-login');

	}

	public function getLoadTopBrandsList(Request $request) {

		$totalData = TopBrands::select('id' , 'title' , 'content' , 'brand_image' , 'is_delete')->where ('is_delete' , '=' , 0)->count();

		$totalFiltered = $totalData;

		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, 'top_brands.' .  $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}
		$topbrands = TopBrands::select('id' , 'title' , 'content' , 'brand_image' , 'is_delete')->where ('is_delete' , '=' , 0);

		if (!empty($request->input('search.value'))) {
			$search = $request->input('search.value');

			$topbrands = $topbrands->where('top_brands.title', 'LIKE', "%{$search}%");
		}

		$totalFiltered = $topbrands->count();

		$orders = empty($ordersBy) ? 'id ASC' : implode(', ',array_filter($ordersBy));

		$medicines = $topbrands
			->offset($start)
			->limit($limit)
			->orderByRaw($orders)
			->get();

		$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $medicines
		);

		return $json_data;
	}

	public function getAddBrand () {
		return View::make ("admin.addbrands");
	}

	public function postNewBrand (Request $request) {

		if (!$this->isCsrfAccepted ())
			return Redirect::to ('admin/add-brand')->with ('message' , 'Bad Request');

		$validatedData = \Validator::make($request->all(), [

			"file"  => "dimensions:max_width=656,max_height=656",

		]);
		if ($validatedData->fails()) return redirect()->back()->with('success', 'Invalid image size');


		$input = $request->all ();
		$store_original = $fileName = NULL;
		if($request->file('file')) {
			$fileName = preg_replace('/[^A-Za-z0-9\-]/', '-', $input['title']) . '_' . time() . '.' . $request->file('file')->extension();

			$store_original = $request->file('file')->storeAs('/public/brand', $fileName);

			if (!$store_original) return redirect()->back()->with('success', 'File Not saved !');
			else { //thumbnail creation
				$path = '/public/brand/thumbnails';
				if(!Storage::exists($path)) Storage::makeDirectory($path);

				$avatar = $request->file('file');
				Image::make($avatar)->resize(60, 60)->save( public_path('/storage/brand/thumbnails/' . $fileName) );
			}
		}
		if ($input['id'] == "") {
			$medicine = new TopBrands();
			$medicine->title = $input['title'];
			$medicine->content = $input['content'];
			$medicine->brand_image = $fileName;
			$medicine->is_delete = $input['is_delete'];
			$medicine->save ();

		} else {
			$uploaded_image = '';
			if ($store_original) $uploaded_image = TopBrands::where ('id' , '=' , $input['id'])->pluck('brand_image')->first();

			$data = [
				'title' => $input['title'] ,
				'content' => $input['content'] ,
				'is_delete' => $input['is_delete']
			];
			if (!empty($fileName)) $data['brand_image'] = $fileName;

			$affectedRows = TopBrands::where ('id' , '=' , $input['id'])->update ($data);

			if ($affectedRows && $uploaded_image) { //fetching user profile_pic if it not empty then will delete it
				if (Storage::disk('BRAND_PIC')->has($uploaded_image)) { //checking image is existing or not
					Storage::disk('BRAND_PIC')->delete($uploaded_image); //deleting original pic
					Storage::disk('BRAND_THUMB')->delete($uploaded_image); //deleting thumbnail
				}
			}
		}
		return Redirect::to ('admin/list-top-brands');

	}

	public function getTopBrandsEdit (Request $request) {
		$medicine = TopBrands::where ('id' , '=' , $request->id)->get ()->first ();

		return View::make ("admin.addbrands")->with ('details' , $medicine);
	}

	/**
	 * Delete Medicine List
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getTopBrandsDelete (Request $request) {
		$medicine = TopBrands::find ($request->id);
		$medicine->is_delete = 1;
		$medicine->save ();
		Cache::forget (CACHE_PARAM_MEDICINE);

		return Redirect::to ('admin/list-top-brands');
	}

   	public function getLoadNewMedicines () {
		return View::make ('admin.new_medicine_list');
	}

	public function getLoadNewMedicinesList (Request $request) {
		// $medicine_list = NewMedicine::where ('is_delete' , '=' , 0)->orderBy ('id' , 'DESC')->paginate (30);

		$totalData = NewMedicine::where ('is_delete' , '=' , 0)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $medicine_list = NewMedicine::where ('is_delete' , '=' , 0);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $medicine_list = $medicine_list->where('name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $medicine_list->count();

		$orders = empty($ordersBy) ? 'id ASC' : implode(', ',array_filter($ordersBy));

        $medicine_list = $medicine_list
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $medicine_list
        );

        return $json_data;
	}

	/**
	 * Get the email list who requested a particular medicine
	 *
	 * @return mixed
	 */
	public function getNewMedicineEmail (Request $request) {
		$med = $request->med;
		$list = NewMedicineEmail::where ('request_id' , '=' , $med)->where('email', '!=', 'Not Available')->groupBy('user_id')->get();
		return Response::json ($list);
	}

	/**
	 * Delete a requested medicine from back end
	 *
	 * @param $mid
	 *
	 * @return mixed
	 */
	public function getDeleteNewMedicine (Request $request) {
		$medicine = NewMedicine::find ($request->newMedID);
		$medicine->is_delete = 1;
		$medicine->save ();

		return Redirect::to ('admin/load-new-medicines');
	}

	public function anyLoadPendingPrescription () {
		return View::make ('admin.pending_prescription_list');
	}

	public function anyLoadPendingPrescriptionList (Request $request) {

		$totalData = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.created_at as date_created')->where ('prescription.is_delete' , ' = ' , 0)->where ('prescription.status' , '=' , PrescriptionStatus::UNVERIFIED ())
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $mProfs = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.created_at as date_created')->where ('prescription.is_delete' , ' = ' , 0)->where ('prescription.status' , '=' , PrescriptionStatus::UNVERIFIED ())
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $mProfs = $mProfs->where('users.email', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $mProfs->count();

		$orders = empty($ordersBy) ? 'prescription.created_at DESC' : implode(', ',array_filter($ordersBy));
        $mProfs = $mProfs
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $mProfs
        );

        return $json_data;
	}

	/**
	 * Prescription Edit
	 *
	 * @param $pres_id
	 * @param $status
	 *
	 * @return mixed
	 */
	public function getPresEdit (Request $request) {
		$pres_id = $request->pres_id;
		$status = $request->status;
		$pres = Prescription::find ($pres_id);
		$invoice = $pres->getInvoice;
		if (!is_null ($invoice) && ($invoice->status_id == InvoiceStatus::PAID () && $status == PrescriptionStatus::UNVERIFIED ())) {
			return Redirect::to ('admin/pres-edit/' . $pres_id . '/0');
		}
		$shipping = '';
		$medicines = Medicine::medicines ();
		$setting = Setting::param ('site' , 'discount')['value'];
		$discounts = floatval (Setting::param ('site' , 'discount')['value']);
		$items = [];
		$invoice_id = '';
		
		if ($invoice) {
			$invoice_id = $invoice->id;
			$items_list = ItemList::where ('invoice_id' , '=' , $invoice_id)->where ('is_removed' , '=' , 0)->get ();
			$items = [];
			foreach ($items_list as $item) {
				$items[] = [
					'cart_id' => $item->id ,
					'item_id' => $item->medicine ,
					'item_name' => $item->medicine_details ()->item_name ,
					'unit_price' => $item->unit_price ,
					'discount' => $item->discount ,
					'unit_disc' => $item->discount_percentage ,
					'total_price' => $item->total_price ,
					'quantity' => $item->quantity ,
					'item_code' => $item->medicine_details ()->item_code
				];
			}
			$shipping = $invoice->shipping;
		}

		return View::make ("admin.presedit" , array("shipping" => $shipping , "pres_id" => $pres_id , "email" => $pres->getUser->email , "path" => $pres->path , 'items' => $items , 'invoice_id' => $invoice_id , 'discount' => $discounts , 'status' => $status));
	}

		public function anyLoadAllPrescription () {
		return View::make ('admin.all_prescription_list');
	}
	
	public function anyLoadAllPrescriptionList(Request $request) {

		$totalData = $totalFiltered = Prescription::count();

		$pendingList = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.created_at as date_created' , 'invoice.invoice' , 'invoice.shipping_status', 'invoice.payment_status')
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')
		->where ('prescription.user_id' , '<>' , "")
		->where ('prescription.is_delete' , '=' , 0);

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		// foreach($request->order as $key => $order){
		// 	if($order['column'] > 0) array_push($ordersBy, 'ed_professional.' .  $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		// }

		if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $pendingList = $pendingList->where('users.email', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $pendingList->count();

		$orders = empty($ordersBy) ? 'prescription.id DESC' : implode(', ',array_filter($ordersBy));

        $pendingList = $pendingList
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $pendingList
        );

        return $json_data;
	}

public function anyLoadActivePrescription () {
		return View::make ('admin.active_prescription_list');
	}

	public function anyLoadActivePrescriptionList(Request $request) {

		$totalData = Prescription::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $prescriptions = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.invoice' , 'invoice.created_at as date_created')
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')
		->where ('prescription.user_id' , '<>' , "")
		->where ('prescription.status' , '=' , PrescriptionStatus::VERIFIED ())
		->where ('invoice.status_id' , '!=' , InvoiceStatus::PAID ())
		->where ('prescription.is_delete' , '=' , 0);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $prescriptions = $prescriptions->where('users.email', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $prescriptions->count();

		$orders = empty($ordersBy) ? 'prescription.id DESC' : implode(', ',array_filter($ordersBy));

        $prescriptions = $prescriptions
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $prescriptions
        );

        return $json_data;
	}

	public function anyLoadPaidPrescription () {
		return View::make ('admin.paid_prescription_list');
	}

	public function anyLoadPaidPrescriptionList (Request $request) {

		$totalData = Prescription::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}

        $prescriptions = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.invoice' , 'invoice.created_at as date_created' , 'invoice.shipping_status')
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')
		->where ('prescription.user_id' , '<>' , "")
		->where ('invoice.status_id' , '=' , InvoiceStatus::PAID ())
		->where ('prescription.is_delete' , '=' , 0);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $prescriptions = $prescriptions->where('users.email', 'LIKE', "%{$search}%");
        }

        $totalFiltered = $prescriptions->count();

		$orders = empty($ordersBy) ? 'prescription.created_at DESC' : implode(', ',array_filter($ordersBy));

        $prescriptions = $prescriptions
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($orders)
            ->get();

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $prescriptions
        );

        return $json_data;
	}

	public function anyLoadShippedPrescription () {
		return View::make ('admin.shipped_prescription_list');
	}

	public function anyLoadShippedPrescriptionList (Request $request) {
		$totalData = Prescription::count();

		$totalFiltered = $totalData;
	
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}
	
		$prescriptions = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.created_at as date_created' , 'invoice.invoice')
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')
		->where ('prescription.user_id' , '<>' , "")
		->where ('invoice.shipping_status' , '=' , ShippingStatus::SHIPPED ())
		->where ('prescription.is_delete' , '=' , 0);
	
		if (!empty($request->input('search.value'))) {
			$search = $request->input('search.value');
			$prescriptions = $prescriptions->where('users.email', 'LIKE', "%{$search}%");
		}
	
		$totalFiltered = $prescriptions->count();
	
		$orders = empty($ordersBy) ? 'prescription.created_at DESC' : implode(', ',array_filter($ordersBy));
	
		$prescriptions = $prescriptions
			->offset($start)
			->limit($limit)
			->orderByRaw($orders)
			->get();
	
		$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $prescriptions
		);
	
		return $json_data;
	}

	public function anyLoadDeletedPrescription () {
		return View::make ('admin.deleted_prescription_list');
	}

	public function anyLoadDeletedPrescriptionList (Request $request) {
		$totalData = Prescription::count();

		$totalFiltered = $totalData;
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $request->input('order.0.dir');
		$ordersBy = [];
		foreach($request->order as $key => $order){
			if($order['column'] > 0) array_push($ordersBy, $request->input('columns.'. $order['column']. '.name') . ' ' . strtoupper($order['dir']));
		}
		
		$prescriptions = Prescription::select ('prescription.created_at as created_date' , 'prescription.status' , 'users.email' , 'prescription.path' , 'prescription.id as pres_id' , 'invoice.status_id as in_status' , 'invoice.id' , 'invoice.created_at as date_created' , 'invoice.invoice')
		->leftjoin ('users' , 'users.id' , '=' , 'prescription.user_id')
		->leftjoin ('invoice' , 'invoice.pres_id' , '=' , 'prescription.id')
		->where ('prescription.user_id' , '<>' , "")
		->where ('prescription.is_delete' , '=' , 1);
		
		if (!empty($request->input('search.value'))) {
			$search = $request->input('search.value');
			$prescriptions = $prescriptions->where('users.email', 'LIKE', "%{$search}%");
		}
		
		$totalFiltered = $prescriptions->count();
		
		$orders = empty($ordersBy) ? 'prescription.created_at DESC' : implode(', ',array_filter($ordersBy));
		
		$prescriptions = $prescriptions
			->offset($start)
			->limit($limit)
			->orderByRaw($orders)
			->get();
		
		$json_data = array(
			"draw"            => intval($request->input('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $prescriptions
		);
		
		return $json_data;
	}

		public function getLoadInvoice ($id) {
		$invoice = Invoice::find ($id);
		$userType = intval ($invoice->getUser->user_type_id);
		if ($userType == UserType::MEDICAL_PROFESSIONAL ()) {
			$mprof = MedicalProfessional::select ('prof_name' , 'prof_address' , 'prof_phone' , 'prof_mail' , 'prof_pincode')->find ($invoice->getUser->user_id);
			$detail = array('name' => $mprof->prof_name , 'addr' => $mprof->prof_address , 'ph' => $mprof->prof_phone , 'mail' => $mprof->prof_mail , 'pin' => $mprof->prof_pincode);
		} else if ($userType == UserType::Customer ()) {
			$cust = Customer::select ('name' ,'address' , 'phone' , 'mail' , 'pincode')->find ($invoice->getUser->user_id);
			$detail = array('name' => $cust->name , 'addr' => $cust->address , 'ph' => $cust->phone , 'mail' => $cust->mail , 'pin' => $cust->pincode);
		}
		$status = InvoiceStatus::statusName ($invoice->status_id);

		return View::make ('admin.loadinv' , array("id" => $invoice->invoice , 'date' => $invoice->created_at , 'details' => $detail , 'orderDate' => $invoice->created_at , 'status' => InvoiceStatus::statusName ($invoice->status_id) , 'invID' => $id));
	}


	public function getLoadInvoiceItems ($id) {
		$tbody = "";
		$i = 1;
		$invoice = Invoice::find ($id);
		$items = $invoice->cartList ();
		$total = 0;
		if (empty($shipping))
			$shipping = 0;
		//$total += $shipping;
		$discount = 0;
		foreach ($items as $itemList) {
			$medicine = Medicine::medicines ($itemList->medicine);
			$tbody .= "<tr>
				    <td>" . $i++ . "</td>
				    <td>" . $medicine['item_name'] . "</td>
				    <td class='text-right'>" . $itemList->quantity . "</td>
				    <td class='text-right'>" . number_format ($itemList->unit_price , 2) . "</td>
				    <td class='text-right' >" . number_format ($itemList->quantity * $itemList->unit_price , 2) . "</td>
				    <td class='text-right'>" . number_format ($itemList->discount_percentage , 2) . "</td>
				    <td class='text-right'>" . number_format ($itemList->discount , 2) . "</td>
				    <td class='text-right'>" . number_format ($itemList->total_price , 2) . "</td>
		        	 </tr>";
		}
		$tbody .= "
		  	        <tr>
		            <td colspan='7' class='text-right no-border'><strong>Price Total</strong></td>
		            <td class='text-right'><strong>" . Setting::currencyFormat ($invoice->sub_total) . "</strong></td>
		          </tr>
		          <tr>
		            <td colspan='7' class='text-right no-border'><strong>Discount</strong></td>
		            <td class='text-right'>" . Setting::currencyFormat ($invoice->discount) . "</td>
		          </tr>
		          <tr>
		            <td colspan='7' class='text-right no-border'><strong>Shipping</strong></td>
		            <td class='text-right'>" . Setting::currencyFormat ($invoice->shipping) . "</td>
		          </tr>
		          <tr>
		            <td colspan='7' class='text-right no-border'><strong>Total</strong></td>
		            <td class='text-right'><strong>" . Setting::currencyFormat ($invoice->total) . "</,2)strong></td>
		          </tr>";

		return Response::json (['tbody' => $tbody]);

	}

		public function anyShipOrder (Request $request) {
		$pres_id = $request->pres_id;
		$prescription = Prescription::find ($pres_id);
		$invoice = $prescription->getInvoice;
		$userDetails = $prescription->getUser;
		// Save Invoice Details
		$invoice->shipping_status = ShippingStatus::SHIPPED ();
		$invoice->updated_at = date ('Y-m-d H:i:s');
		$invoice->updated_by = Auth::user ()->id;
		$invoice->save ();
		// Send Mail

		$type = $userDetails->user_type_id;
		if ($type == UserType::CUSTOMER ()) {
			$user = Customer::find ($userDetails->user_id);
			$user_email = $user->mail;
			$user_name = $user->name;
		} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
			$user = MedicalProfessional::find ($userDetails->user_id);
			$user_email = $user->prof_mail;
			$user_name = $user->prof_name;
		}
		if ($user_name) {
			Mail::send ('emails.shipped' , array('name' => $user_name) , function ($message) use ($user_email) {
				$message->to ($user_email)->subject ('Your item shipped from ' . Setting::param ('site' , 'app_name')['value']);
			});
		}

		return Redirect::to ('admin/load-paid-prescription');
	}


	public function postUpdateInvoice (Request $request) {
		
		if (!$this->isCsrfAccepted ())
			return Redirect::back ()->withErrors (['Invalid Request']);


		$got = $request->all ();
		$sub_total = $discount = $total_price = $overall_discount = $shipping = 0;
		$overall_discount = $request->get('overall_disc' , 0.00);
		$shipping = $request->get('shipping' , 0.00);
		$prescription = Prescription::find ($got['pres_id']);
		$type = $prescription->getUser->user_type_id;
		$user_id = $prescription->getUser->id;
		// Check if Cart Is Not Empty
		if (empty($got['item_code1'])) return Redirect::back ()->withErrors (['No items added to the cart']);
				
		if (!empty($got['invoice_id'])) {       // If Invoice Already Exists
			$i = 1;
			$items = ItemList::where ('invoice_id' , '=' , $got['invoice_id'])->get ();
			while ($i <= $got['itemS']) {
				$discount += $got['discount' . $i];
				$sub_total += $got['total_price' . $i];
				$alreadyIn = 0;
				foreach ($items as $item) {     // Update already Existings Cart
					if ($got['item_code' . $i] == $item->medicine) {
						$itemUpdate = ['quantity' => $got['qty' . $i] ,
							'unit_price' => $got['pricee' . $i] ,
							'total_price' => $got['total_price' . $i] ,
							'discount_percentage' => $got['unit_discount' . $i] ,
							'discount' => $got['discount' . $i] ,
							'updated_at' => date ('Y-m-d H:i:s') ,
							'updated_by' => Auth::user ()->id
						];
						ItemList::where ('invoice_id' , '=' , $got['invoice_id'])->where ('medicine' , '=' , $got['item_code' . $i])->update ($itemUpdate);
						$alreadyIn = 1;
						break;
					}
				}
				
				if ($alreadyIn == 0) {

					$newItem = new ItemList;					
					$newItem->invoice_id = $got['invoice_id'];
					$newItem->medicine = $got['item_code' . $i];
					$newItem->quantity = $got['qty' . $i];
					$newItem->unit_price = $got['pricee' . $i];
					$newItem->total_price = $got['total_price' . $i];
					$newItem->discount_percentage = (float) $got['unit_discount' . $i];
					$newItem->discount = $got['discount' . $i];
					$newItem->created_at = date ('Y-m-d H:i:s');
					$newItem->updated_at = date ('Y-m-d H:i:s');
					$newItem->created_by = Auth::user ()->id;					
					$newItem->save ();					
				}
				$i++;
			}
			// Calculate Total Price of Invoice
			$total_price = $sub_total + $shipping - $overall_discount;
			$invoice = Invoice::find ($got['invoice_id']);
			$invoice->invoice = INVOICE_PREFIX . (10000 + $got['invoice_id']);
			$invoice->updated_at = date ("Y-m-d H:i:s");
			$invoice->sub_total = $sub_total;
			$invoice->total = $total_price;
			$invoice->shipping = $shipping;
			$invoice->discount = $overall_discount;
			$invoice->updated_by = Auth::user ()->id;
			$invoice->updated_at = date ('Y-m-d H:i:s');
			$invoice->save ();
		} else {
			//while($i<=$got['itemS']){
			$i = 1;
			$invoice = new Invoice;
			$invoice->pres_id = $got['pres_id'];
			$invoice->user_id = $user_id;
			$invoice->created_at = date ("Y-m-d H:i:s");
			$invoice->created_by = Auth::user ()->id;
			$invoice->save ();
			while ($i <= $got['itemS']) {
				// Calculate Prices
				$sub_total += $got['sub_total' . $i];
				$discount += $got['discount' . $i];
				$total_price += $got['total_price' . $i];
				// Add Items
				$newItem = new ItemList;
				$newItem->invoice_id = $invoice->id;
				$newItem->medicine = $got['item_code' . $i];
				$newItem->quantity = $got['qty' . $i];
				$newItem->unit_price = $got['pricee' . $i];
				$newItem->total_price = $got['total_price' . $i];
				$newItem->discount_percentage = $got['unit_discount' . $i];
				$newItem->discount = $got['discount' . $i];
				$newItem->created_at = date ('Y-m-d H:i:s');
				$newItem->created_by = Auth::user ()->id;
				$newItem->save ();
				$i++;

			}
			$total_price = $sub_total + $shipping - $overall_discount;
			// Update Other Columns
			$invoice->invoice = INVOICE_PREFIX . (10000 + $invoice->id);
			$invoice->sub_total = $sub_total;
			$invoice->total = $total_price;
			$invoice->shipping = $shipping;
			$invoice->discount = $overall_discount;
			$invoice->save ();
		}
		// To Delete Items From Cart
		if (isset($got['todelete']) && !empty($got['todelete'])) {
			$todelete = explode ("," , $got['todelete']);
			foreach ($todelete as $item) {
				ItemList::where ('invoice_id' , '=' , $invoice->id)->where ('medicine' , '=' , $item)->update (['is_removed' => 1]);
			}
		}
		// Select User Types
		if ($type == UserType::CUSTOMER ()) {
			$user = Customer::find ($prescription->getUser->user_id);
			$user_email = $user->mail;
			$user_name = $user->name;
		} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
			$user = MedicalProfessional::find ($prescription->getUser->user_id);
			$user_email = $user->prof_mail;
			$user_name = $user->prof_name;
		}
		// Save Prescription Status
		$prescription->status = PrescriptionStatus::VERIFIED ();
		$prescription->updated_by = Auth::user ()->id;
		$prescription->updated_at = date ('Y-m-d H:i:s');
		$status = $prescription->save ();
		// Send Mail
		Mail::send ('emails.verify' , array('name' => $user_name) , function ($message) use ($user_email) {
			$message->to ($user_email)->subject ('Your prescription verified by ' . Setting::param ('site' , 'app_name')['value']);
		});

		return Redirect::to ("admin/load-all-prescription");
	}

	public function getReset () {
		return View::make ("admin.reset");
	}

	/**
	 * Password Reset form submit
	 *
	 * @return mixed
	 */
	public function postResetPassword (Request $request) {
		$validatedData = \Validator::make($request->all(), [
            'email'=> 'required|email',
        ]);

		if ($validatedData->fails()) {
            return response()->json(['status'=>false,'message' => $validatedData->messages(), 'error' => 0]);
        }

		$email = $request->email;
		
		if (Admin::where ('email' , '=' , $email)->count() == 0) {
			return Redirect::to ("/admin/reset")->withErrors ('You are not an Admin');
		} else {
			try{
				Mail::to($email)->send(new resetAdminPassword($email));
				return Redirect::to ("/admin/reset")->withSuccess ('Please Check your email');				
			} catch (Exception $e) {
				return Response::make (['status' => 'FAILURE' , 'msg' => $e->getMessage()] , $e->getCode());
			}
		}
	}

	public function getAdminResetPassword ($md) {
		return View::make ("admin.reset-reset-password" , array("md" => $md));
	}

	/**
	 * Change Admin Password
	 *
	 * @return mixed
	 */
	public function anyAdminChangePassword (Request $request) {
		$email = $request->email;
		$md = $request->mdofemail;
		if ($md != md5 ($email)) {
			return Redirect::to ("admin/admin-reset-password/" . $md)->with ('passwordError' , 'Token Mismatch');
		} else {
			$edit = array('password' => Hash::make ($request->password));
			if(User::where ('email' , '=' , $email)->exists()) {
				$result = User::where ('email' , '=' , $email)->update ($edit);
				if($result) return Redirect::to ("admin-login")->withSuccess ('Password changed successfully.');
				else return Redirect::to ("admin/admin-reset-password/" . $md)->with ('passwordError', '<b>Sorry</b>, request cannot process now, please try again later.');			
			} else return Redirect::to ("admin/admin-reset-password/" . $md)->with ('passwordError', 'Please enter a valid Email.');
		}
	}

 /*  public function getPresEdit (Request $request) {
		$pres_id = $request->pres_id;
		$status = $request->status;
		$pres = Prescription::find ($pres_id);
		$invoice = $pres->getInvoice;
		if (!is_null ($invoice) && ($invoice->status_id == InvoiceStatus::PAID () && $status == PrescriptionStatus::UNVERIFIED ())) {
			return Redirect::to ('admin/pres-edit/' . $pres_id . '/0');
		}
		$shipping = '';
		$medicines = Medicine::medicines ();
		$setting = Setting::param ('site' , 'discount')['value'];
		$discounts = floatval (Setting::param ('site' , 'discount')['value']);
		$items = [];
		$invoice_id = '';
		
		if ($invoice) {
			$invoice_id = $invoice->id;
			$items_list = ItemList::where ('invoice_id' , '=' , $invoice_id)->where ('is_removed' , '=' , 0)->get ();
			$items = [];
			foreach ($items_list as $item) {
				$items[] = [
					'cart_id' => $item->id ,
					'item_id' => $item->medicine ,
					'item_name' => $item->medicine_details ()->item_name ,
					'unit_price' => $item->unit_price ,
					'discount' => $item->discount ,
					'unit_disc' => $item->discount_percentage ,
					'total_price' => $item->total_price ,
					'quantity' => $item->quantity ,
					'item_code' => $item->medicine_details ()->item_code
				];
			}
			$shipping = $invoice->shipping;
		}

		return View::make ("admin.presedit" , array("shipping" => $shipping , "pres_id" => $pres_id , "email" => $pres->getUser->email , "path" => $pres->path , 'items' => $items , 'invoice_id' => $invoice_id , 'discount' => $discounts , 'status' => $status));
	}*/


}