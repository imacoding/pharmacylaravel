<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemList extends Model
{
    use HasFactory;
    protected $table = 'cart';
    public $timestamps = false;

    public function medicine_details()
    {
        return $this->hasOne(\App\Models\Medicine::class, 'id', 'medicine')->select('item_code', 'item_name')->first();
    }
}
