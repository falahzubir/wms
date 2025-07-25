<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Product::factory()->count(10)->create();
        $csvData = fopen(base_path('database/imports/product_main.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                Product::create([
                    'id' => $data['0'],
                    'name' => $data['1'],
                    'description' => $data['2'],
                    'code' => $data['3'],
                    'price' => $data['4'],
                    'weight' => $data['5'],
                    'is_foc' => $data['6'],
                    'main_product_id' => $data['7'],
                    'is_active' => $data['8'],
                    'sensitive' => $data['9'],
                    'updated_at' => $data['11'],
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);

    }
}
