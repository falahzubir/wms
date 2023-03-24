<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvData = fopen(base_path('database/imports/payment_type.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                PaymentType::create([
                    'id' => $data['0'],
                    'payment_type_name' => $data['1'],
                    'auto_approve' => $data['2'],
                    'payment_type_status' => $data['3'],
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
