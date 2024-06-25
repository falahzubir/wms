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
        if(app()->environment() != 'production'){

            $emziExpressQiti = AccessToken::where('type', 'emzi-express')->where('company_id', 3)->first();
            
            if (!$emziExpressQiti) {
                $emziExpressQiti = AccessToken::create([
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

        $emziExpressEH = AccessToken::where('type', 'emzi-express')->where('company_id', 1)->first();

        if (!$emziExpressEH) {
            $emziExpressEH = AccessToken::create([
                'type' => 'emzi-express',
                'client_id' => 'd566bd05-0924-438d-b64b-33e5f576bcf3',
                'company_id' => 1, #eh
                'name' => 'Emzi Express Authentification',
                'token' => 'eyJ0eXA',
                'additional_data' => ([
                    'email' => 'emziEH@gmail.com',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $emziExpressED = AccessToken::where('type', 'emzi-express')->where('company_id', 2)->first();

        if (!$emziExpressED) {
            $emziExpressED = AccessToken::create([
                'type' => 'emzi-express',
                'client_id' => 'd566bd05-0924-438d-b64b-33e5f576bcf3',
                'company_id' => 2, #ed
                'name' => 'Emzi Express Authentification',
                'token' => 'eyJ0eXA',
                'additional_data' => ([
                    'email' => 'emziED@gmail.com',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $emziExpressEI = AccessToken::where('type', 'emzi-express')->where('company_id', 4)->first();

        if (!$emziExpressEI) {
            $emziExpressEI = AccessToken::create([
                'type' => 'emzi-express',
                'client_id' => 'd566bd05-0924-438d-b64b-33e5f576bcf3',
                'company_id' => 4, #EI
                'name' => 'Emzi Express Authentification',
                'token' => 'eyJ0eXA',
                'additional_data' => ([
                    'email' => 'emziEI@gmail.com',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
