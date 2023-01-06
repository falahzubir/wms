<?php

namespace Database\Seeders;

use App\Models\Bucket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BucketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bucket::factory()->count(10)->create();
    }
}
