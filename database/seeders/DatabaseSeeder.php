<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         //\App\Models\User::factory(10)->create();
        $this->call(UserTypeTableSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(AdminTypeSeeder::class);
        $this->call(InvoiceStatusSeeder::class);
        $this->call(ShippingStatusSeeder::class);
        $this->call(PaymentStatusSeeder::class);
        $this->call(PrescriptionStatusSeeder::class);
        $this->call(UserStatusSeeder::class);
        $this->call(PaymentGatewaySeeder::class);
         $this->call(AdminTableSeeder::class);
        $this->call(UserTableSeeder::class);
         $this->call(TopBrandsSeeder::class);
        $this->command->info('Data table seeded!');
    }
}
