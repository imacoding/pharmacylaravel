<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
     protected $table = 'customer';
    public $timestamps = false;

    /**
        * Customer Related User
        * @return mixed
    */
    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'user_id', 'id')->where('user_type_id', '=', \App\Models\UserType::CUSTOMER())->first();
    }
}
