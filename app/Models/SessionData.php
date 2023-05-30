<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionData extends Model
{
    use HasFactory;
    protected $table = 'sessions';

    protected $fillable = [ 'medicine_id', 'medicine_name', 'medicine_count', 'user_id', 'status', 'unit_price', 'item_code', 'is_pres_required', 'created_at', 'updated_at' ];
}
