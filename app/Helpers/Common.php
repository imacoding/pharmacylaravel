<?php

use App\Models\User;
use App\Models\UserType;
use App\Models\Medicine;
use App\Models\Setting;
use Soumen\Agent\Facades\Agent;
use Illuminate\Support\Facades\Auth;

function stringClean ($string)
{
    return preg_replace ('/[-" "`*().]/' , '' , $string);
}
function str_before($str, $needle)
{
    $pos = strpos($str, $needle);

    return ($pos !== false) ? substr($str, 0, $pos) : $str;
}

function userDetails($page)
{
    if(Auth::check()) {
        $user = User::select('*')->where('id', Auth::user ()->id)->where('user_type_id' , Auth::user ()->user_type_id);
        $user_data = [];
        $name = $phone = '';
        
        if ( UserType::MEDICAL_PROFESSIONAL () == Auth::user ()->user_type_id) {
            $user_data = $user->with('professional')->first();
            $user = $user_data['professional'];
            $name = !empty($user->prof_name) ? ucwords($user->prof_name) : '';
            $pincode = $user->prof_pincode;
            $phone = $user->prof_phone;
            $address = $user->prof_address;
        } else if (UserType::CUSTOMER () == Auth::user ()->user_type_id){
            $user_data = $user->with('customer')->first();
            $user = $user_data['customer'];
            $name = !empty($user->name) ? ucwords($user->name) : '';        
            $pincode = $user->pincode;
            $phone = $user->phone;
            $address = $user->address;
        }

        $phn_country =  $phone ? $phone : '+' . $user_data->country_code . ' ' . $user_data->phone;
        $email = $user_data->email;
        $profile_pic  = $user_data->profile_pic;
        $prof_pic_thumb = '';
        if ($profile_pic) {
            $prof_pic = Storage::disk('PROFILE_PIC')->exists($profile_pic) ? Storage::disk('PROFILE_PIC')->url($profile_pic) : Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');
            $prof_pic_thumb = Storage::disk('PROFILE_THUMB')->exists($profile_pic) ? Storage::disk('PROFILE_THUMB')->url($profile_pic) : Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');
        } else $prof_pic =  $prof_pic_thumb = Storage::disk('SYSTEM_IMAGE_URL')->url('no-profile.png');
        
        $data = [
            'id' => $user_data->id,
            'user_type' => $user_data->user_type_id,
            'name' => $name, 
            'email' => $email,
            'profile_pic_original' => $prof_pic,
            'profile_pic' => $prof_pic_thumb
        ];

        if ($page == 'account_page') $data = array_merge($data, ['dial_code' => $user_data->country_code, 'pincode' => $pincode,'phone' => (int)$user_data->phone, 'phn_code' => $user_data->country_code, 'address' => $address ]);
        else if ('dash-side-header') $data = array_merge($data, ['phone' => $phn_country]);

        
        return $data;
    }
}

function logo() {
    $logo = '';
    if (Schema::hasTable('settings') && Setting::where('group','site')->where('key', 'logo')->where('is_active', 1)->count()) $logo = Setting::where('group','site')->where('key', 'logo')->where('is_active', 1)->pluck('value')->first();
    
    if($logo && Storage::disk('SYSTEM_IMAGE_URL')->exists($logo)) $logo = $logo;
    else $logo = '';
    
    return $logo;
}

function logo_icon() {
    $logo = '';
    if (Schema::hasTable('settings') && Setting::where('group','site')->where('key', 'logo')->where('is_active', 1)->count()) $logo = 'assets/images/' . Setting::where('group','site')->where('key', 'logo')->where('is_active', 1)->pluck('value')->first();
    return $logo;
}
function contact_mail() {
    $mail = '';
    if (Schema::hasTable('settings')) $mail = Setting::where('group','site')->where('key', 'mail')->where('is_active', 1)->pluck('value')->first();
    return $mail;
}

function medicine($id) {
    $medicine_list = Medicine::select ('id' , 'item_code' , 'item_name' , 'item_name as value' , 'item_name as label' , 'item_code' , 'selling_price as mrp' , 'composition' , 'discount' , 'discount_type' , 'tax' , 'tax_type' , 'manufacturer' , 'group' , 'is_delete' , 'is_pres_required', 'product_image')->where('id', $id)->first ();

    return $medicine_list ? $medicine_list : [];
}

function settingsValue ($group, $key) {
    $value = '';
    if (Schema::hasTable('settings')) $value = Setting::where('group' , $group)->where('key', $key)->where('is_active', 1)->pluck('value')->first();
    return $value;
}