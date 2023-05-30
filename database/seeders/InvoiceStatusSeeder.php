<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('invoice_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('invoice_status')->insert([
            [
                'name' => 'PENDING',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'name' => 'PAID',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'name' => 'UNPAID',
                'created_at' => now(),
                'created_by' => 1
            ],
            [
                'name' => 'CANCELLED',
                'created_at' => now(),
                'created_by' => 1
            ]
        ]);
    }
}
