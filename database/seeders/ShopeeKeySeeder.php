<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopeeKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update the existing row
        DB::table('access_tokens')
            ->where('type', 'shopee')
            ->where('company_id', 3)
            ->update([
                'client_id' => '2004184',
                'client_secret' => '6e75676c4b776841546a6f7a7962784859564d47576656476365654b4f5a4264',
                'name' => 'Shopee Access Token & Partner Key for EMZI',
                'updated_at' => now(),
            ]);

        // Insert the new row
        DB::table('access_tokens')->updateOrInsert(
            ['type' => 'shopee', 'company_id' => 6],
            [
                'client_id' => '2009885',
                'client_secret' => '496f4a6679464e786d70584266444d75585241687a774f6a6446454e7376774b',
                'name' => 'Shopee Access Token & Partner Key for SPV',
                'token' => '786d4f7767736b5673726449616b5554',
                'additional_data' => json_encode([
                    'shop_id' => 1381617825,
                    'refresh_token' => ''
                ]),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
