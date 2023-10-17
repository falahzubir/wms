<?php

namespace Database\Seeders;

use App\Models\AccessToken;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShopeeAccessToken extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accessToken = AccessToken::where('type', 'shopee')->first();
        $refresh_access_token = '78456c4e617845476b4d7455456a6b53';
        $shop_id = 753309133;
        if (!$accessToken) {
            $accessToken = AccessToken::create([
                'type' => 'shopee',
                'token' => '71486b7247585050466343537a4f4d51',
                'additional_data' => ([
                    'refresh_token' => $refresh_access_token,
                    'shop_id' => $shop_id]),
                'company_id' => 3,
                'name' => 'Shopee Access Token',
                'expires_at' => '2023-10-17 10:58:56',
            ]);
        }
    }
    
}
