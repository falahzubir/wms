<?php

namespace Database\Seeders;

use App\Models\AccessToken;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TikTokAccessToken extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shop_id_one = 7495003145797929663;
        $accessToken_one = AccessToken::where('type', 'tiktok')->where('additional_data->shop_id',$shop_id_one)->first();
        if (!$accessToken_one) {
            $accessToken_one = AccessToken::create([
                'type' => 'tiktok',
                'token' => 'ROW_kdHWWwAAAAC9m7k-frjchRInjXvg0cjBaFmcy81dz3975NBkl7jlwzZrH1GLFPh78Z_mI6ayeuH1rNiKgpMh66jQ528EjtQWLsYJCwPbOfohDbQj--6jsNTGw-N6v4kxFbTRq163xqNl6pEa18A-BoKWfM0GYz2AcSmkDv0jEiaRhR9ObY2NCA',
                'additional_data' => ([
                    'shop_id' => $shop_id_one
                ]),
                'company_id' => 1,
                'name' => 'Tiktok Access Token Shop 1',
                'expires_at' => '2023-10-17 10:58:56',
            ]);
        }
        else{
            echo "Tiktok Access Token Shop 1 already exist\n";
        }

        $shop_id_two = 7494538943783406026;
        $accessToken_two = AccessToken::where('type', 'tiktok')->where('additional_data->shop_id',$shop_id_two)->first();
        if(!$accessToken_two) {
            $accessToken_two = AccessToken::create([
                'type' => 'tiktok',
                'token' => 'ROW_cnhOnAAAAAC9m7k-frjchRInjXvg0cjBs_0bjF8MNvHEq8cFqn2DMVK1LFNR1B_1QECz6kYJA3DUmEL25uxF8kwfB5YGgJRisVnpyZ2uSEYbMJgmMvBTqSEaeUv3ObuJJe4-BTjsBha8ZLI4XnGjcx4frhRRLWcQ-dp6q_FjIkrF5FWAvVBGWg',
                'additional_data' => ([
                    'shop_id' => $shop_id_two
                ]),
                'company_id' => 1,
                'name' => 'Tiktok Access Token Shop 2',
                'expires_at' => '2023-10-17 10:58:56',
            ]);
        }
        else{
            echo "Tiktok Access Token Shop 2 already exist\n";
        }

    }
}
