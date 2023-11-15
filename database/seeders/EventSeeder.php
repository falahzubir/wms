<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventTable = new \App\Models\OrderEvent();

        $eventsApiData = \App\Http\Traits\ApiTrait::getSalesEvent();

        foreach($eventsApiData as $val)
        {
            $eventTable->upsert([
                'event_id' => $val['event_id'],
                'event_name' => $val['event_name'],
                'company_id' => $val['company']
            ],['event_id'],['event_name','company_id']);
        }
    }
}
