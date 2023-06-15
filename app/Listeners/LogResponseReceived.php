<?php

namespace App\Listeners;

use App\Models\ThirdPartyRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogResponseReceived
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        ThirdPartyRequest::create([
            "parameters" => json_encode($event->request->data()),
            "response" => json_encode($event->response->json()),
            "status_code" => $event->response->status(),
            "url" => $event->request->url(),
            "method" => $event->request->method(),
            "requested_at" => $event->request->requested_at,
        ]);
    }
}
