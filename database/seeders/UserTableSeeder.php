<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = [
            [
                'email'         => 'admin',
                'password'      => Hash::make('admin'),
                'user_type_id'  => 1,
                'security_code' => 8718,
                'user_status'   => 2,
                'created_by'    => 1,
                'updated_by'    => 1,
                'user_id'       => 1,
                'created_at'    => now(),
            ],
          
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
