<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
if (!defined('CACHE_PARAM_USER_TYPE')) define('CACHE_PARAM_USER_TYPE', 'user_type');
use Illuminate\Support\Facades\Cache;

class UserType extends Model
{
    use HasFactory;
    protected $table = 'user_type';
        public $timestamps = false;

        /**
         * Get USer Status
         */
        public static function users($key = '')
        {
            $user_types = Cache::get(CACHE_PARAM_USER_TYPE, null);
            if (is_null($user_types)) {
                $types = self::all();
                $user_types = [];
                foreach ($types as $type) {
                    $user_types[strtoupper($type->user_type)] = $type->id;
                }
                Cache::put(CACHE_PARAM_USER_TYPE, $user_types, 43200);
            }
            return ((count($user_types) < 1) || empty($key)) ? $user_types : $user_types[$key];
        }

        /**
         * Admin User
         */
        public static function ADMIN()
        {
            return self::users('ADMIN');
        }

        /**
         * Medical Professional
         */
        public static function MEDICAL_PROFESSIONAL()
        {
            return self::users('MEDICAL_PROFESSIONAL');
        }

        /**
         * Customer
         */
        public static function CUSTOMER()
        {
            return self::users('CUSTOMER');
        }
}
