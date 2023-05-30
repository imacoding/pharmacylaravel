<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Models\User;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\UserType;
use App\Models\PayStatus;
use App\Models\InvoiceStatus;
use App\Models\PaymentGateway;
use App\Models\MedicalProfessional;
use App\Models\PaymentGatewaySetting;

use Response;
use Redirect;
use Session;
use Artisan;
use View;
use Mail;
use Auth;
class PayPalController extends Controller
{
	/* paypal start */
    public function anyMakePaypalPayment (Request $request, $invoice = 0) {
		// If User Authenticated
		if (!Auth::check ())
			return Redirect::to ('/');
		$isMobile = isset($request->isMobile) ? $request->isMobile : '';
		$invoice = $isMobile ? $request->invoice : $invoice;
		// Get Invoice
		$invoiceDetails = Invoice::find ($invoice);
		// If Invoice Is Not Present
		if (is_null ($invoice)) return Redirect::to ('/paid-prescription');

		$data = array();		
		$user = Auth::user ();
		$email = $user->email;
		$type = $user->user_type_id;
		if ($type == UserType::CUSTOMER ()) {
			$user_info = Customer::find ($user->user_id);
			$phone = $user_info->phone;
			$fname = $user_info->name;
			$address = $user_info->address;
		} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
			$user_info = MedicalProfessional::find ($user->user_id);
			$phone = $user_info->prof_phone;
			$fname = $user_info->prof_name;
			$address = $user_info->prof_address;
		}
		$data = array();
		$item_name = "";
		$i = 0;
		foreach ($invoiceDetails->cartList () as $cart) {
			$item_name .= Medicine::medicines ($cart->medicine)['item_name'];
			$item_name .= ", ";

		}
		$item_name = rtrim($item_name,', ');
		$total = $invoiceDetails->total;
		$data['amount'] = $total;
		$data['email'] = $email;
		$data['phone'] = $phone;
		$data['firstname'] = $fname;
		$data['address'] = $address;
		$data['invoice'] = $invoiceDetails->invoice;
		$data['id'] = $invoice;
		$data['productinfo'] = $item_name;
        $posted = $data;
		$user = [
			'name' => Auth::user()->email,
			'key_pas' => Auth::user()->password
		];		
		
		if ($isMobile) return View::make ('users.mobile_paypal_payment' , compact('posted', 'user'));
		else return View::make ('users.paypal_payment' , compact('posted'));

	}

    public function handlePayment(Request $request) {
        $product = [];
		$invoice = $request->invoice;
		$invoiceDetails = Invoice::where ('invoice', $invoice)->first();
		// If Invoice Is Not Present
		if (is_null ($invoice)) return Redirect::to ('/paid-prescription');

		$data = array();		
		
		$data = array();
		$item_name = "";
		$i = 0;
		foreach ($invoiceDetails->cartList () as $cart) {
			$item_name .= Medicine::medicines ($cart->medicine)['item_name'];
			$item_name .= ", ";

		}
		$item_name = rtrim($item_name,', ');
		$total = $invoiceDetails->total;
		$data['amount'] = $total;
		$data['email'] = $request->email;
		$data['phone'] = $request->phone;
		$data['firstname'] = $request->first_name;
		$data['address'] = $request->address;
		$data['invoice'] = $request->invoice;
		$data['id'] = $invoice;
		$data['productinfo'] = $item_name;
        $posted = $data;
		$user = [
			'name' => Auth::user()->email,
			'key_pas' => Auth::user()->password
		];
		return View::make ('users.mobile_paypal_payment' , compact('posted'));
    }
   
    public function paymentCancel(Request $request) {
		if(Auth::check()) return View::make ('users.payment_failed'); //return view for Web
    }
  
    public function paymentSuccess(Request $request) {
		session_start ();
		session_destroy ();
		$invoice = $request->pay_id;
		$transaction_id = $request->transaction_id;
		if ($transaction_id != abs (crc32 ($invoice))) {
			session_start ();
			session_destroy ();

			if(Auth::check()) return View::make ('users.payment_failed');
		}
		$invoice = Invoice::where ('invoice' , '=' , $invoice)->first ();
		$invoice->status_id = InvoiceStatus::PAID ();
		$invoice->payment_status = PayStatus::SUCCESS ();
		$invoice->transaction_id = $transaction_id;
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
			$user_name = $user->first_name;
		} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
			$user = MedicalProfessional::select ('prof_mail as mail' , 'prof_first_name as first_name')->find ($user_detail->user_id);
			$user_email = $user->mail;
			$user_name = $user->first_name;
		}
		Mail::send ('emails.paid' , array('name' => $user_name) , function ($message) use ($user_email) {
			$message->to ($user_email)->subject ('Your payment received at ' . Setting::param ('site' , 'app_name')['value']);
		});

		if(Auth::check()) return View::make ('users.payment_success');

    }
	/* paypal end */
}
