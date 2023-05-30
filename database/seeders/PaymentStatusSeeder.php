<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payment_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $paymentStatuses = [
            [
                'name' => 'PENDING',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'SUCCESS',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'FAILURE',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ];

        DB::table('payment_status')->insert($paymentStatuses);
    }
}
