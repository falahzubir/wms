<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exchange_rates')->insert([
            [
                'id' => 1,
                'country_id' => 2,
                'currency' => 'SGD',
                'rate' => 3.1,
                'start_date' => '2024-06-01 00:00:00',
                'end_date' => '2024-06-30 00:00:00',
                'created_at' => '2024-06-01 09:35:13',
                'updated_at' => '2024-06-01 09:35:13',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'country_id' => 2,
                'currency' => 'SGD',
                'rate' => 3.38,
                'start_date' => '2024-08-01 00:00:00',
                'end_date' => '2024-08-31 00:00:00',
                'created_at' => '2024-07-01 10:01:54',
                'updated_at' => '2024-07-01 10:01:54',
                'deleted_at' => null,
            ],
        ]);
    }
}
