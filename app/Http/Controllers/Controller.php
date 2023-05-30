<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Config;
use Session;
use Schema;
use Log;
use Redirect;
use App\Models\Setting;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function isCsrfAccepted ()
    {
        $session_token = Session::token ();
        $input_token = request('_token');
        if ($session_token != $input_token)
            return false;
        else
            return true;
    }

}
