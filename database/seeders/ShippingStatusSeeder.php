<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ShippingStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('shipping_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $shippingStatuses = [
            [
                'name' => 'NOT SHIPPED',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'SHIPPED',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'RETURNED',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'RECEIVED',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ];

        DB::table('shipping_status')->insert($shippingStatuses);
    }
}
