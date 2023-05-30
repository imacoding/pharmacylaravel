<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
define('CACHE_PARAM_USER_STATUS', 'user_status');
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UserStatus extends Model
{
    use HasFactory;
    
    
     protected $table = 'user_status';
        public $timestamps = false;

        /**
         * Get USer Status
         */
        public static function status($key = '')
        {
            $user_status = Cache::get(CACHE_PARAM_USER_STATUS, null);
            if (is_null($user_status)) {
                $user_status = self::all();
                $user_status = [];
                foreach ($user_status as $status) {
                    $user_status[strtoupper($status->name)] = $status->id;
                }
                Cache::put(CACHE_PARAM_USER_STATUS, $user_status, 43200);
            }

            return empty($key) ? $user_status : $user_status[$key];
        }



        /**
         * Active User Status
         */
        public static function ACTIVE()
        {
            return self::status('ACTIVE');
        }

        /**
         * Inactive User Status
         */
        public static function INACTIVE()
        {
            return self::status('INACTIVE');
        }

        /**
         * Pending User Status
         */
        public static function PENDING()
        {
            return self::status('PENDING');
        }
}
