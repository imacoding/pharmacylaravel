<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('user_type')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $data = [
            [
                'user_type'   => 'ADMIN',
                'created_at'  => now(),
            ],
            [
                'user_type'   => 'MEDICAL_PROFESSIONAL',
                'created_at'  => now(),
            ],
            [
                'user_type'   => 'CUSTOMER',
                'created_at'  => now(),
            ],
        ];

        DB::table('user_type')->insert($data);
    }
}
