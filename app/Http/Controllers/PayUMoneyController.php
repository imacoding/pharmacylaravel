<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\UserType;
use App\Models\PayStatus;
use App\Models\InvoiceStatus;
use App\Models\MedicalProfessional;

use Response;
use Redirect;
use Session;
use View;
use Mail;
use Auth;
class PayUMoneyController extends Controller
{
    public function anyMakePayment (Request $request, $invoice = NULL) {
		// If User Authenticated
		if (!Auth::check () && !(isset($request->isMobileNOs))) return Redirect::to ('/');
		
		if(!(isset($request->isMobileNOs)) ){

			$isMobile = isset($request->isMobile) ? $request->isMobile : '';
			$invoice = $isMobile ? $request->invoice : $invoice;
			// Get Invoice
			$invoiceDetails = Invoice::find ($invoice);
			// If Invoice Is Not Present
			if (is_null ($invoice) && !$isMobile) return Redirect::to ('/paid-prescription');

			$user = Auth::user ();

			$email = $user->email;
			$type = $user->user_type_id;

			if ($type == UserType::CUSTOMER ()) {
				$user_info = Customer::find ($user->user_id);
				$phone = $user_info->phone;
				$name = $user_info->name;
				$address = $user_info->address;
			} elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) {
				$user_info = MedicalProfessional::find ($user->user_id);
				$phone = $user_info->prof_phone;			
				$name = $user_info->prof_name;
				$address = $user_info->prof_address;
			}

			$data = array();
			$item_name = "";
			$i = 0;
			foreach ($invoiceDetails->cartList () as $cart) {
				$item_name .= Medicine::medicines ($cart->medicine)['item_name'];
				$item_name .= " ,";

			}
			$item_name = rtrim($item_name,', ');
			$total = $invoiceDetails->total;
			$data['amount'] = $total;
			$data['email'] = $email;
			$data['phone'] = $phone;
			$data['firstname'] = $name;
			$data['address'] = $address;
			$data['invoice'] = $invoiceDetails->invoice;
			$data['id'] = $invoice;
			$data['productinfo'] = $item_name;
			if ($isMobile) return View::make ('users.mobile_payment' , array('posted' => $data));
			else return View::make ('users.payment' , array('posted' => $data));
		} else {
			$invoiceDetails = Invoice::where ('id', $request->id)->where('invoice', $request->invoice)->first();

			$user = User::find($invoiceDetails['user_id']);

			$email = $user->email;

			$data = array();
			$item_name = "";
			foreach ($invoiceDetails->cartList () as $cart) {
				$item_name .= Medicine::medicines ($cart->medicine)['item_name'];
				$item_name .= " ,";

			}
			$item_name = rtrim($item_name,', ');
			$total = $invoiceDetails['total'];
			$data['amount'] = $total;
			$data['email'] = $email;
			$data['phone'] = $request->phone;
			$data['firstname'] = $request->firstname;
			$data['address'] = $request->address;
			$data['invoice'] = $invoiceDetails['invoice'];
			$data['id'] = $request->invoice;
			$data['productinfo'] = $item_name;

			return View::make ('users.mobile_payment', array('posted' => $data));
		}
	}

    public function anyPaySuccess (Request $request, $invoice) {
		if(count($request->all()) < 1) return Redirect::to ('/');
		else {
			if($request->status == 'success') {
				$transaction_id = $request->payuMoneyId ? $request->payuMoneyId : '';             // Save Return Transaction Id of Payment Gateway
				// Update Invoice
				$invoice = Invoice::find ($invoice);
				$invoice->status_id = InvoiceStatus::PAID ();
				$invoice->payment_status = PayStatus::SUCCESS ();
				$invoice->transaction_id = $transaction_id;
				$invoice->updated_at = date ('Y-m-d H:i:s');
				$invoice->updated_by = Auth::check() ? Auth::user ()->id : $invoice['user_id'];
				$invoice->save ();
				// User
				$user_detail = $invoice->getUser;
				$type = $user_detail->user_type_id;
				// Send Paid Mail
				if ($type == UserType::CUSTOMER ()) $user = Customer::select ('mail' , 'name')->find ($user_detail->user_id);
				elseif ($type == UserType::MEDICAL_PROFESSIONAL ()) $user = MedicalProfessional::select ('prof_mail as mail' , 'prof_name as name')->find ($user_detail->user_id);
			
				$user_email = $user->mail;
				$user_name = $user->name;
				Mail::send ('emails.paid' , array('name' => $user_name) , function ($message) use ($user_email) {
					$message->to ($user_email)->subject ('Your payment received at ' . Setting::param ('site' , 'app_name')['value']);
				});

				if(Auth::check()) return View::make ('users.payment_success');
			} else {
				if(Auth::check()) return View::make ('users.payment_failed');
			}
		}
	}
    public function anyPayFail (Request $request) {
		if(count($request->all()) < 1) return Redirect::to ('/');
		else {
			if(Auth::check()) return View::make ('users.payment_failed');
		}
	}
}
