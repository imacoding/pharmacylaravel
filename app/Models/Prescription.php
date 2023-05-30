<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
     protected $table = 'prescription';
    public $timestamps = false;

    /**
     * Get Invoice
     *
     * @return mixed
     */
    public function getInvoice ()
    {
        return $this->hasOne (\App\Models\Invoice::class , 'pres_id' , 'id');
    }

    /**
     * Get User
     *
     * @return mixed
     */
    public function getUser ()
    {
        return $this->hasOne (\App\Models\User::class , 'id' , 'user_id');
    }

    /**
     * Get Cart Items
     */
    public function getCart ()
    {
        return $this->hasManyThrough (\App\Models\ItemList::class , \App\Models\Invoice::class , 'pres_id' , 'invoice_id');

    }
}
