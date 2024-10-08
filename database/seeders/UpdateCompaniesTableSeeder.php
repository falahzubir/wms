<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update records in the states table where country_code is 'MY' to 1
        DB::table('companies')
            ->where('country', 'MY')
            ->update(['country' => 1]);

        // Update records in the states table where country_code is 'ID' to 2
        DB::table('companies')
            ->where('country', 'ID')
            ->update(['country' => 2]);

        // Update records in the states table where country_code is 'SG' to 3
        DB::table('companies')
            ->where('country', 'SG')
            ->update(['country' => 3]);
    }
}
