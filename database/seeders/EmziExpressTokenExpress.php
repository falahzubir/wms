<?php

namespace Database\Seeders;

use App\Models\AccessToken;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmziExpressTokenExpress extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emziExpress = AccessToken::where('type', 'emzi-express')->first();

        if (!$emziExpress) {
            $emziExpress = AccessToken::create([
                'type' => 'emzi-express',
                'client_id' => 'd566bd05-0924-438d-b64b-33e5f576bcf3',
                'company_id' => 3, #qiti
                'name' => 'Emzi Express Authentification',
                'token' => 'eyJ0eXA',
                'additional_data' => ([
                    'email' => 'emziqiti@gmail.com',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
