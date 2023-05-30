<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrescriptionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('prescription_status')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $prescriptionStatuses = [
            [
                'name' => 'UNVERIFIED',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'VERIFIED',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'name' => 'REJECTED',
                'created_at' => now(),
                'created_by' => 1,
            ],
        ];

        DB::table('prescription_status')->insert($prescriptionStatuses);
    }
}
