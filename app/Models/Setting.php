<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
if (!defined('CACHE_PARAM_USER_TYPE')) define('CACHE_PARAM_USER_TYPE', 'user_type');
if (!defined('CACHE_PARAM_SETTINGS')) define('CACHE_PARAM_SETTINGS', 'settings');
if (!defined('CURRENCY_BEFORE')) define('CURRENCY_BEFORE', 'BEFORE');
if (!defined('CURRENCY_AFTER')) define('CURRENCY_AFTER', 'AFTER');

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    public $timestamps = false;
     /**
         * Return a setting value
         * @param $group
         * @param $key
         * @return mixed
         */
      static public function param($group, $key)
        {
            $cache_key = implode('|', [$group, $key]);
             Log::info($cache_key);
            
            $settings = self::settings();
           // dd($settings);
          
            if ($settings) return $settings[$cache_key];
            else return [];
        }

        /**
         * Load all Settings
         * @return array
         */
        static public function settings()
        {
            $settings = Cache::get('CACHE_PARAM_SETTINGS', null);
            if (is_null($settings)) {
                $settings = [];
                $parameters = self::all();
                foreach ($parameters as $param) {

                    $key = implode('|', [$param->group, $param->key]);
                    $settings[$key] = ['value' => $param->value, 'type' => $param->type];
                }
                Cache::put('CACHE_PARAM_SETTINGS', $settings, 1440);
            }

            return $settings;
        }

        /**
         * Create Currency Format
         */
        public static function currencyFormat($amount){

            $currency_position = self::param('site', 'curr_position')['value'];

            $currency = self::param('site', 'currency')['value'];
            switch ($currency_position) {
                case CURRENCY_BEFORE:
                    return implode(' ', [$currency, number_format($amount, 2)]);
                    break;
                case CURRENCY_AFTER:
                    return implode(' ', [number_format($amount, 2), $currency]);
                    break;
            }
        }
}
