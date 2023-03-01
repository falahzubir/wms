<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OtherCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //add others on courier table
        Courier::create([
            'id' => 99,
            'name' => 'Others',
            'code' => 'others',
            'status' => true,
        ]);
    }
}
