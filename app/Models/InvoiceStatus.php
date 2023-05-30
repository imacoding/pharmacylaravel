<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
if (!defined('CACHE_PARAM_INVOICE_STATUS')) define('CACHE_PARAM_INVOICE_STATUS', 'invoice_status');

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class InvoiceStatus extends Model
{
    use HasFactory;
    protected $table = 'invoice_status';
    public $timestamps = false;


    /**
        *Get User Status
    */
    public static function status($key = '')
    {
        $invoice_status = Cache::get(CACHE_PARAM_INVOICE_STATUS, null);
        if (is_null($invoice_status)) {
            $invoice_status = self::all();
            $invoice_status = [];
            foreach ($invoice_status as $status) {
                $invoice_status[strtoupper($status->name)] = $status->id;
            }
            Cache::put(CACHE_PARAM_INVOICE_STATUS, $invoice_status, 43200);
        }

         return empty($key) ? $invoice_status : $invoice_status[$key];
    }

    /**
        *PENDING
    */
    public static function PENDING()
    {
        return self::status('PENDING');
    }

    /**
        *PAID
    */
    public static function PAID()
    {
        return self::status('PAID');
    }

    /**
        *UNPAID
    */
    public static function UNPAID()
    {
        return self::status('UNPAID');
    }

    /**
        *CANCELLED
    */
    public static function CANCELLED()
    {
        return self::status('CANCELLED');
    }

    /**
        *Get  Status Name
        *@param $status_id
        *@return string
    */
    public static function statusName($status_id)
    {
        $i= 0;
        switch ($status_id) {
            case (self::PENDING()):
                return "Pending";
                break;
            case (self::PAID()):
                return "Paid";
                break;
            case (self::UNPAID()):
                return "Unpaid";
                break;
            case (self::CANCELLED()):
                return "Cancelled";
                break;
            default:
                return "Invoice Not Created";
                break;
        }
    }
}
