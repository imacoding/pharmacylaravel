<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
if (!defined('CACHE_PARAM_MEDICINE')) define('CACHE_PARAM_MEDICINE', 'medicines');
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Auth;

class Medicine extends Model
{
    use HasFactory;
     protected $table = 'medicine';
    public $timestamps = false;
    
    protected $fillable = ['item_code', 'item_name', 'batch_no', 'quantity', 'cost_price', 'purchase_price', 'rack_number','selling_price', 'expiry', 'tax', 'composition', 'discount', 'manufacturer', 'marketed_by', 'group', 'created_at', 'created_by', 'product_image'];

    /**
     * Get all Medicines
     */
    public static function medicines ($key = '')
    {
        $medicines = Cache::get (CACHE_PARAM_MEDICINE , null);
        if (is_null ($medicines)) {
            $medicine_list = self::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->get ()->toArray ();
            $medicines = [];
            foreach ($medicine_list as $list) {
                $medicines[$list['id']] = $list;
            }
            Cache::put (CACHE_PARAM_MEDICINE , $medicines , 1440);
        }

        return empty($key) ? $medicines : $medicines[$key];
    }
}
