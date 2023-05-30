<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $array_values = array(
            array('group' => 'site', 'key' => 'app_name', 'value' => 'Healwire', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'logo', 'value' => 'logo.svg', 'type' => 'IMAGE'),
            array('group' => 'site', 'key' => 'mail', 'value' => 'midhunraj@webandcrafts.com', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'website', 'value' => 'http://localhost/heal_wire_api', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'address', 'value' => 'Healwire India', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'timezone', 'value' => 'UTC', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'phone', 'value' => '999-99-99999', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'discount', 'value' => '10', 'type' => 'FLOAT'),
            array('group' => 'site', 'key' => 'currency', 'value' => '$', 'type' => 'TEXT'),
            array('group' => 'site', 'key' => 'curr_position', 'value' => 'BEFORE', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'username', 'value' => 'store@webandcrafts.com', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'password', 'value' => 'Ekst56h7', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'address', 'value' => 'store@webandcrafts.com', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'name', 'value' => 'Healwire', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'port', 'value' => '587', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'host', 'value' => 'smtp.gmail.com', 'type' => 'TEXT'),
            array('group' => 'mail', 'key' => 'driver', 'value' => 'smtp', 'type' => 'TEXT'),
            array('group' => 'payment', 'key' => 'mode', 'value' => '1', 'type' => 'TEXT'),
            array('group' => 'payment', 'key' => 'type', 'value' => 'TEST', 'type' => 'TEXT')
        );

        // Find and Update
        foreach ($array_values as $value) {
            $count = DB::table('settings')
                ->where('group', $value['group'])
                ->where('key', $value['key'])
                ->count();

            if ($count === 0) {
                DB::table('settings')->insert([
                    'group' => $value['group'],
                    'key' => $value['key'],
                    'value' => $value['value'],
                    'type' => $value['type'],
                    'created_at' => now()
                ]);
            }
        }
    }
}
