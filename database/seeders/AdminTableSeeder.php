<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('admin')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = [
            [
                'name'           => 'admin',
                'email'          => 'admin@healwire.com',
                'admin_type'     => 1,
                'created_by'     => 1,
                'updated_by'     => 1,
                'created_at'     => now()
            ]
        ];

        foreach($users as $user){
            Admin::create($user);
        }
    }
}
