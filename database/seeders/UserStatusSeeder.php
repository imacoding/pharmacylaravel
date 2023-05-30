<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserStatus;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserStatus::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'name' => 'PENDING',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'INACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ];

        UserStatus::insert($data);
    }
}
