<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name' => 'EMZI HOLDING SDN. BHD.',
            'code' => 'EH',
            'address' => 'SP PLAZA',
            'phone' => '0123456789',
            'city' => 'Sungai Petani',
            'state' => 'Kedah',
            'country' => 'MY',
            'postcode' => '08000',
            'contact_person' => 'Mustari',
            'address2' => 'Taman Sri Tanjung',
        ]);

        Company::create([
            'name' => 'EMZI DIGITAL SDN. BHD.',
            'code' => 'ED',
            'address' => 'SP PLAZA',
            'phone' => '0123456789',
            'city' => 'Sungai Petani',
            'state' => 'Kedah',
            'country' => 'MY',
            'postcode' => '08000',
            'contact_person' => 'Mustari',
            'address2' => 'Taman Sri Tanjung',
        ]);
    }
}
