<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DuplicateSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::withoutEvents(function () {
            //setting category
            $dup = Setting::create([
                'key' => 'Possible Duplicate',
                'value' => 'none',
            ]);
            //setting lists bulk
            $dup->children()->createMany([
                [
                    'key' => 'detection_time',
                    'value' => '604800', // 7 days
                ], [
                    'key' => 'detect_by_phone',
                    'value' => '1',
                ], [
                    'key' => 'detect_by_address',
                    'value' => '1',
                ], [
                    'key' => 'detect_by_address_percentage',
                    'value' => '80',
                ], [
                    'key' => 'detect_operation_type',
                    'value' => 'AND',
                ]
            ]);

        });
    }
}
