<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Carbon\Carbon;

class WebhookController extends Controller
{
    /**
     * Get failed order on webhook
     */

     public function fail_insert($date)
     {
        $from_time = "$date 00:00:00";
        $to_time = "$date 23:59:59";

        $hooks = WebhookCall::select('payload', 'exception')
            ->where('created_at', '>', $from_time)
            ->where('created_at', '<', $to_time)
            ->whereNot('payload', 'like', '%"company":"QA"%')
            ->whereNotNull('exception')
            ->get();


        $payload = $hooks->pluck('payload');
        $exception = $hooks->pluck('exception');

        $count = 0;
        foreach($hooks as $hook){
            $result[$count]['exception'] = $hook->exception['message'];
            $result[$count]['payload'] = $hook->payload;
            $count++;
        }

        return response()->json($result);

     }
}
