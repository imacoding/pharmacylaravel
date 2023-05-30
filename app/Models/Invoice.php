<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    Public $timestamps = false;

    /**
        * Prescriptions
    */
    Public function prescription()
    {
        return $this->hasOne(\App\Models\Prescription::class, 'id', 'pres_id')->first();
    }

    /**
        * Verified Prescriptions
    */
    Public function verifiedPrescription()
    {
        return $this->hasOne(\App\Models\Prescription::class, 'id', 'pres_id')->where('status', '=', PrescriptionStatus::VERIFIED())->first();

    }

    /**
        * Unverified Prescription
        * @return mixed
    */
    Public function unverifiedPrescription()
    {
        return $this->hasOne(\App\Models\Prescription::class, 'id', 'pres_id')->where('status', '=', PrescriptionStatus::UNVERIFIED())->first();
    }

    /**
        * Get Cart List
    */
    Public function cartList()
    {
        return $this->hasMany('\App\Models\ItemList', 'invoice_id', 'id')->where('is_removed','=',0)->get();
    }

    /**
        * Get User
        * @return mixed
    */
    Public function getUser()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}
