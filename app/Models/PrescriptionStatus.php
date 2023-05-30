<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
if (!defined('CACHE_PARAM_PRESCRIPTION_STATUS')) define('CACHE_PARAM_PRESCRIPTION_STATUS', 'prescription_status');

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class PrescriptionStatus extends Model
{
    use HasFactory;
     protected $table = 'prescription_status';
    Public $timestamps = false;

    /**
        * Get USer Status
    */
    Public static function status($key = '')
    {
        $prescription_status = Cache::get(CACHE_PARAM_PRESCRIPTION_STATUS, null);
        if (is_null($prescription_status)) {
            $prescription_status = self::all();
            $prescription_status = [];
            foreach ($prescription_status as $status) {
                $prescription_status[strtoupper($status->name)] = $status->id;
            }
            Cache::put(CACHE_PARAM_PRESCRIPTION_STATUS, $prescription_status, 43200);
        }

        return empty($key) ? $prescription_status : $prescription_status[$key];
    }

    /**
        * Active User Status
    */
    Public static function UNVERIFIED()
    {
        return self::status('UNVERIFIED');
    }

    /**
        * Inactive User Status
    */
    Public static function VERIFIED()
    {
        return self::status('VERIFIED');
    }

    /**
        * Pending User Status
    */
    Public static function REJECTED()
    {
        return self::status('REJECTED');
    }

    /**
        * Get  Status Name
        * @param $status_id
        * @return string
    */
    Public static function statusName($status_id)
    {
        $i = 0;
        switch ($status_id) {
            case (self::UNVERIFIED()):
                return "Unverified";
                break;
            case (self::VERIFIED()):
                return "Verified";
                break;
            case (self::REJECTED()):
                return "Rejected";
                break;
        }
    }
}
