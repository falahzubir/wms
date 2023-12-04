<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosMalaysiaSettingSeeder extends Seeder
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
            $pos = Setting::create([
                'key' => 'Pos Malaysia Setting',
                'value' => 'none',
            ]);
            //setting lists bulk
            $pos->children()->createMany([
                [
                    'key' => 'genconnote_prefix_paid',
                    'value' => 'ER',
                ], [
                    'key' => 'genconnote_prefix_cod',
                    'value' => 'EC',
                ], [
                    'key' => 'genconnote_application_code',
                    'value' => 'StagingPos',
                ], [
                    'key' => 'genconnote_secret_id',
                    'value' => 'StagingPos@1234',
                ], [
                    'key' => 'genconnote_username',
                    'value' => 'StagingPos',
                ], [
                    'key' => 'gen3pl_account_no',
                    'value' => '8800586268',
                ], [
                    'key' => 'gen3pl_secret_id',
                    'value' => '1234',
                ], [
                    'key' => 'gen3pl_username',
                    'value' => 'EmziHoldings',
                ], [
                    'key' => 'genpreacceptedsingle_subscription_code',
                    'value' => 'ECON001',
                ], [
                    'key' => 'genpreacceptedsingle_require_to_pickup',
                    'value' => 'true',
                ], [
                    'key' => 'genpreacceptedsingle_require_web_hook',
                    'value' => 'true',
                ], [
                    'key' => 'genpreacceptedsingle_account_no',
                    'value' => '8800586268',
                ], [
                    'key' => 'genpreacceptedsingle_caller_name',
                    'value' => 'EMZI FULFILMENT',
                ], [
                    'key' => 'genpreacceptedsingle_caller_phone',
                    'value' => '01912345678',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_location_id',
                    'value' => '104242',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_location_name',
                    'value' => 'EMZI Holding SDN. BHD.',
                ] , [
                    'key' => 'genpreacceptedsingle_contact_person',
                    'value' => '.',
                ], [
                    'key' => 'genpreacceptedsingle_phone_no',
                    'value' => '601312345678',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_address',
                    'value' => 'EMZI FULFILMENT, Kompleks SP Plaza, Jalan Ibrahim, Sungai Petani, 08000 Sungai Petani, Kedah',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_district',
                    'value' => 'Sungai Petani',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_province',
                    'value' => 'Kedah',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_country',
                    'value' => 'MY',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_location',
                    'value' => '',
                ], [
                    'key' => 'genpreacceptedsingle_pickup_email',
                    'value' => 'pickup@abc.com',
                ], [
                    'key' => 'genpreacceptedsingle_post_code',
                    'value' => '08000',
                ] , [
                    'key' => 'genpreacceptedsingle_item_type',
                    'value' => '2',
                ] , [
                    'key' => 'genpreacceptedsingle_close_at',
                    'value' => '06:00 PM',
                ]
            ]);

        });
    }
}
