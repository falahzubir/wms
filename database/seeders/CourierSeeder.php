<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Product::factory()->count(10)->create();
        $csvData = fopen(base_path('database/imports/cmn_courier.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                Courier::create([
                    'id' => $data['0'],
                    'name' => $data['1'],
                    'code' => $data['2'],
                    'url' => $data['3'],
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
