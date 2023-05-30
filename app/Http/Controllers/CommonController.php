<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewaySetting;
use Illuminate\Http\Request;

use App\Models\Setting;

use Exception;
use Response;
use Session;
use Illuminate\Support\Facades\Cache;

use View;
use Auth;
use Mail;
class CommonController extends Controller
{
    public function contactUs(){

        return view('users.contact');
    }
    /**
	 * Contact Email
	 *
	 * @return int
	 */
	public function sendContactUs (Request $request)
	{
        $client_name = $request->get('name');
		$client_mail = $request->get('email');
		$client_msg = $request->get('msg');
		$mail_id = Setting::param ('site' , 'mail')['value'];

		Mail::send ('emails.customer_query' , array('client_name' => $client_name , 'client_mail' => $client_mail , 'client_msg' => $client_msg) , function ($message) use ($mail_id) {
			$message->to ($mail_id)->subject ('Customer Query');
		});

		if (count (Mail::failures ()) > 0) {

			return response()->json(['status' => 'FAILURE', 'code' =>401, 'msg' => 'Failure! Please try again']);
		} else {
			return response()->json(['status' => 'SUCCESS', 'code' => 200, 'msg' => "Thanks you! Our team will reach you soon"]);
		}

	}

	public function aboutUs()
	{
		return view('users.about_us');
	}

	public function helpDesk()
	{
		return view('users.help_desk');
	}

	public function privacyPolicy()
	{
		return view('users.privacy_policy');
	}

	public function termsConditions()
	{
		return view('users.terms_conditions');
	}

}
