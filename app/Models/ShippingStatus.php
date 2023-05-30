<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
if (!defined('CACHE_PARAM_SHIPPING_STATUS')) define('CACHE_PARAM_SHIPPING_STATUS', 'shipping_status');
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;



class ShippingStatus extends Model
{
    use HasFactory;
    protected $table = 'shipping_status';
    public $timestamps = false;

    /**
        * Get USer Status
    */
    public static function status($key = '')
    {
        $shipping_status = Cache::get(CACHE_PARAM_SHIPPING_STATUS, null);
        if (is_null($shipping_status)) {
            $shipping_status = self::all();
            $shipping_status = [];
            foreach ($shipping_status as $status) {
                $shipping_status[strtoupper($status->name)] = $status->id;
            }
            Cache::put(CACHE_PARAM_SHIPPING_STATUS, $shipping_status, 43200);
        }

        return empty($key) ? $shipping_status : $shipping_status[$key];
    }

    /**
         * Not Shipped
    */
    public static function NOTSHIPPED()
    {
        return self::status('NOT SHIPPED');
    }

    /**
         * shipped
    */
    public static function SHIPPED()
    {
        return self::status('SHIPPED');
    }

    /**
         * Returned
    */
    public static function RETURNED()
    {
        return self::status('RETURNED');
    }

    /**
         * Received
    */
    public static function RECEIVED()
    {
        return self::status('RECEIVED');
    }

    /**
         * Get  Status Name
         * @param $status_id
         * @return string
    */
    public static function statusName($status_id)
    {
        $i = 0;
        switch ($status_id) {
            case (self::RECEIVED()):
                return "Received";
                break;
            case (self::RETURNED()):
                return "Returned";
                break;
            case (self::SHIPPED()):
                return "Shipped";
                break;
            case (self::NOTSHIPPED()):
                return "Not Shipped";
                break;
            default:
                return "Not Shipped";
                break;
        }
    }
}
