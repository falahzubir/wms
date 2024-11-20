<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiktokAccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Update existing user data
        DB::table('access_tokens')
            ->where('additional_data->shop_id', 7495003145797929663) // Match condition to find the existing record
            ->update([
                'type' => 'tiktok',   // Fields to update
                'client_id' => '68r1ubo74bujf',
                'client_secret' => 'f0e77dbfcdce30efe2b144dc0ebc99c1b35499e5',
                'company_id' => 1,
                'prefix' => null,
                'suffix' => null,
                'name' => 'TikTok Access Token EMZI Shop 1',
                'token' => 'ROW_k-xElgAAAAC9m7k-frjchRInjXvg0cjBDqSQVqzwYgBdfAdtMvj2eK5Jwn0Wd2_x4AskEHOJjH9UOclzWkoPCf1cf1hIcpZlA4m005BJHorEKbHsaXOiOhCX_4_SSSEGbDKG-R3pjw3OEMV1vZlzxU5zhRI7rabo4qKPkzguo4o3Nofry0turg',
                'additional_data' => '{"shop_id":7495003145797929663}',
                'abilities' => null,
                'last_used_at' => null,
                'expires_at' => '2024-11-17 15:42:16',
                'created_at' => '2024-07-01 10:47:26',
                'updated_at' => now(),
            ]);

        DB::table('access_tokens')
            ->where('additional_data->shop_id', 7494538943783406026) // Match condition to find the existing record
            ->update([
                'type' => 'tiktok',   // Fields to update
                'client_id' => '68r1ubo74bujf',
                'client_secret' => 'f0e77dbfcdce30efe2b144dc0ebc99c1b35499e5', 
                'company_id' => 1,
                'prefix' => null,
                'suffix' => null,
                'name' => 'TikTok Access Token EMZI Shop 2',
                'token' => 'ROW_k-xElgAAAAC9m7k-frjchRInjXvg0cjBDqSQVqzwYgBdfAdtMvj2eK5Jwn0Wd2_x4AskEHOJjH9UOclzWkoPCf1cf1hIcpZlA4m005BJHorEKbHsaXOiOhCX_4_SSSEGbDKG-R3pjw3OEMV1vZlzxU5zhRI7rabo4qKPkzguo4o3Nofry0turg',
                'additional_data' => '{"shop_id":7494538943783406026}',
                'abilities' => null,
                'last_used_at' => null,
                'expires_at' => '2024-06-17 12:26:10',
                'created_at' => '2024-07-01 10:47:26',
                'updated_at' => now(),
            ]);

        // // add FOR SPV KEY
        DB::table('access_tokens')->updateOrInsert(
            // Match condition (what identifies the record)
            [
                'type' => 'tiktok',
                'client_id' => '6eb4t0995ff17',
                'client_secret' => '3ce258556e4fe260732b403ce2dac762db9e7d7e',
            ],
            // Data to update or insert
            [
                'company_id' => 6, //THIS IS SPV FROM LIVE DB
                'prefix' => null,
                'suffix' => null,
                'name' => 'TikTok Access Token SPV Shop 1',
                'token' => 'secret',
                'additional_data' => '{"shop_id":7495975592359331879}',
                'abilities' => null,
                'last_used_at' => null,
                'expires_at' => null,
                'created_at' => now(), // Only used during insertion
                'updated_at' => now(), // Updated in both cases
            ]
        );
    }
}
