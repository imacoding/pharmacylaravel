<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TopBrands;

class TopBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TopBrands::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'title' => 'Test Brand 1',
                'content' => 'Test Content 1',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 2',
                'content' => 'Test Content 2',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 3',
                'content' => 'Test Content 3',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 4',
                'content' => 'Test Content 4',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 5',
                'content' => 'Test Content 5',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 6',
                'content' => 'Test Content 6',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 7',
                'content' => 'Test Content 7',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 8',
                'content' => 'Test Content 8',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 9',
                'content' => 'Test Content 9',
                'brand_image' => '',
                'created_at' => now(),
            ],
            [
                'title' => 'Test Brand 10',
                'content' => 'Test Content 10',
                'brand_image' => '',
                'created_at' => now(),
            ],
        ];

        TopBrands::insert($data);
    }
}
