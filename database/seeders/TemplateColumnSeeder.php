<?php

namespace Database\Seeders;

use App\Models\ColumnMain;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TemplateColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ColumnMain::updateOrCreate([
            'column_name' => 'shipping_date'
        ],[
            'column_name' => 'shipping_date',
            'column_display_name' => 'Shipping Date',
            'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
            'updated_at' => now()->timezone('Asia/Kuala_Lumpur')
        ]);

        ColumnMain::updateOrCreate([
            'column_name' => 'scan_date'
        ],[
            'column_name' => 'scan_date',
            'column_display_name' => 'Scan Date',
            'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
            'updated_at' => now()->timezone('Asia/Kuala_Lumpur')
        ]);

        ColumnMain::updateOrCreate([
            'column_name' => 'delivered_date'
        ],[
            'column_name' => 'delivered_date',
            'column_display_name' => 'Delivered Date',
            'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
            'updated_at' => now()->timezone('Asia/Kuala_Lumpur')
        ]);

        ColumnMain::updateOrCreate([
            'column_name' => 'date_insert'
        ],[
            'column_name' => 'date_insert',
            'column_display_name' => 'Date Insert',
            'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
            'updated_at' => now()->timezone('Asia/Kuala_Lumpur')
        ]);

        ColumnMain::updateOrCreate([
            'column_name' => 'pic_scan'
        ],[
            'column_name' => 'pic_scan',
            'column_display_name' => 'PIC Scan',
            'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
            'updated_at' => now()->timezone('Asia/Kuala_Lumpur')
        ]);
    }
}
