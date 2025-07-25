<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QiscusController extends Controller
{
    public function getParcelStatus($trackingNumber)
    {
        $shipping = Shipping::where('tracking_number', $trackingNumber)->first();

        if (!$shipping) {
            return response()->json(['message' => 'No shipping found for this tracking number'], 404);
        }

        $latestOrder = Order::where('id', $shipping->order_id)->first();

        if (!$latestOrder) {
            return response()->json(['message' => 'No orders found for this tracking number'], 404);
        }

        // Send a GET request to the external API
        $response = Http::get("https://phantom.groobok.com/api/tracking/{$trackingNumber}");

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Failed to retrieve parcel status',
                'status_code' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }
    
        $data = $response->json();
    
        // Find latest event
        $events = collect($data['item_events'] ?? []);
        $latestEvent = $events->sortByDesc('datetime')->first();
    
        // Find delivered time
        $deliveredEvent = $events->firstWhere('description', 'Successfully delivered');

        // Estimated arrival time
        $estimatedArrivalTime = null;

        if ($latestEvent) {
            $eventTime = \Carbon\Carbon::parse($latestEvent['datetime']);

            switch (strtolower($latestEvent['description'])) {
                case 'shipment data received':
                    $estimatedArrivalTime = $eventTime->copy()->addDays(4);
                    break;
                case 'shipment picked up':
                case 'processed at facility':
                case 'departed from facility':
                    $estimatedArrivalTime = $eventTime->copy()->addDays(3);
                    break;
                case 'out for delivery':
                    $estimatedArrivalTime = $eventTime->copy()->addDay(); // Assume next day
                    break;
                case 'successfully delivered':
                    $estimatedArrivalTime = $eventTime; // Actual delivery time
                    break;
                default:
                    $estimatedArrivalTime = $eventTime->copy()->addDays(4); // fallback
                    break;
            }
        }
    
        return response()->json([
            'order_number' => order_num_format($latestOrder) ?? null,
            'delivery_status' => $latestEvent['description'] ?? 'No status available',
            'estimated_arrival_time' => $estimatedArrivalTime ? $estimatedArrivalTime->format('Y-m-d H:i:s') : null,
            'delivered_time' => $deliveredEvent['datetime'] ?? null,
        ]);
    }

}
