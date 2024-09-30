<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update records in the states table where country_code is 'MY' to 1
        DB::table('states')
            ->where('country_code', 'MY')
            ->update(['country_code' => 1]);

        // Update records in the states table where country_code is 'ID' to 2
        DB::table('states')
            ->where('country_code', 'ID')
            ->update(['country_code' => 2]);

        // Update records in the states table where country_code is 'SG' to 3
        DB::table('states')
            ->where('country_code', 'SG')
            ->update(['country_code' => 3]);
    }
}
