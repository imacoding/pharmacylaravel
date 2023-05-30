<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

if (!defined('CACHE_PARAM_PAYMENT_STATUS')) define('CACHE_PARAM_PAYMENT_STATUS', 'payment_status');

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class PayStatus extends Model
{
    use HasFactory;
    protected $table = 'pay_status';
        public $timestamps = false;

        /**
         * Get USer Status
         */
        public static function status($key = '')
        {
            $payment_status = Cache::get(CACHE_PARAM_PAYMENT_STATUS, null);
            if (is_null($payment_status)) {
                $payment_status = self::all();
                $payment_status = [];
                foreach ($payment_status as $status) {
                    $payment_status[strtoupper($status->name)] = $status->id;
                }
                Cache::put(CACHE_PARAM_PAYMENT_STATUS, $payment_status, 43200);
            }

            return empty($key) ? $payment_status : $payment_status[$key];
        }

        /**
         * Active User Status
         */
        public static function PENDING()
        {
            return self::status('PENDING');
        }

        /**
         * Inactive User Status
         */
        public static function SUCCESS()
        {
            return self::status('SUCCESS');
        }

        /**
         * Pending User Status
         */
        public static function FAILURE()
        {
            return self::status('FAILURE');
        }
}
