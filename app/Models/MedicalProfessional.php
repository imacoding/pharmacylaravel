<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalProfessional extends Model
{
    use HasFactory;
    protected $table = 'ed_professional';
    public $timestamps = false;

    /**
    * Professional Related User
    * @return mixed
    */
    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'user_id', 'id')->where('user_type_id', '=', \App\Models\UserType::MEDICAL_PROFESSIONAL())->first();
    }

    public function getName()
    {
        return "{$this->prof_name}";
    }
}
