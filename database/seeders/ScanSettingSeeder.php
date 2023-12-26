<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScanSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::withoutEvents(function () {
            $parent = Setting::create([
                'key' => 'Scan Multiple Parcel Setting',
                'value' => 'none',
                'type' => SETTING_TYPE_SCAN,
            ]);
            Setting::create([
                'key' => 'scan_multiple',
                'value' => '0',
                'parent_id' => $parent->id,
                'type' => SETTING_TYPE_SCAN,
                'data_type' => SETTING_DATA_TYPE_BOOLEAN,
                'description' => 'If the scan multiple parcel setting is set to Yes, order status updated to ready to ship when all parcel are scanned. If the scan multiple parcel setting is set to No, order status updated to ready to ship when one parcel is scanned.',
            ]);
        });
    }

    public function down()
    {
        Setting::withoutEvents(function () {
            //delete setting bypass soft delete
            Setting::where('key', 'scan_multiple')->forceDelete();
            Setting::where('key', 'Scan Multiple Parcel Setting')->forceDelete();
        });
    }
}
