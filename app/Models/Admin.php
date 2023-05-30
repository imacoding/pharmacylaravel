<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admin';
    public $timestamps = false;
    /**
     * Return User Details;
     *
     * @return mixed
     */
    public function user_details ()
    {
        return $this->hasOne ('App\Models\User' , 'user_id' , 'id')->where ('user_type_id' , '=' , UserType::ADMIN ())->first ();
    }
}
