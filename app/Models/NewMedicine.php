<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewMedicine extends Model
{
    use HasFactory;
    protected $table = 'medicine_request';
    public $timestamps = false;
}
