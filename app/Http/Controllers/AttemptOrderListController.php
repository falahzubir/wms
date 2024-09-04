<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipping;
use App\Models\ShippingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttemptOrderListController extends Controller
{
    public function index(Request $request)
    {
        // Start with the ShippingEvent model
        $shippingEvents = ShippingEvent::with(['shipping.order.customer', 'shipping.order.courier'])
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('shipping_events')
                    ->groupBy('shipping_id');
            })
            ->whereHas('shipping.order', function ($query) use ($request) {
                // Apply search query if present
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('tracking_number', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('customer', function ($query) use ($searchTerm) {
                                $query->where('phone', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhereHas('courier', function ($query) use ($searchTerm) {
                                $query->where('name', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhere('sales_id', 'like', '%' . $searchTerm . '%');
                    });
                }

                // Apply date filters for the first log entry of each order if present
                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $dateFrom = \Carbon\Carbon::parse($request->date_from)->startOfDay();
                    $dateTo = \Carbon\Carbon::parse($request->date_to)->endOfDay();

                    $query->whereHas('logs', function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery->whereRaw('order_logs.id IN (SELECT MAX(id) FROM order_logs GROUP BY order_id)')
                            ->whereBetween('created_at', [
                                $dateFrom,
                                $dateTo
                            ]);
                    });
                }
            })
            // Always filter shipping events by related order logs where status = 1
            ->whereHas('shipping.order.logs', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('order_status_id', ORDER_STATUS_DELIVERED)
                        ->orWhere('order_status_id', ORDER_STATUS_SHIPPING);
                });
            })
            ->paginate(10);

        return view('attempt_order_list.index', [
            'title' => 'Attempt Order List',
            'shippingEvents' => $shippingEvents
        ]);
    }

    public function filter(Request $request)
    {
        // Redirect to index with query parameters
        return redirect()->route('attempt_order_list', $request->all());
    }

    public function downloadCSV(Request $request)
    {
        // Increase the execution time limit to handle large data processing
        set_time_limit(0); // 0 = unlimited

        $date = date('YmdHis');

        // Generate CSV file
        $response = new StreamedResponse(function () use ($request) {
            $handle = fopen('php://output', 'w');

            // Add headers
            fputcsv($handle, [
                'Business Unit',
                'Order ID',
                'Courier',
                'Purchase Type',
                'Tracking Number',
                'Shipping Date',
                'Postcode',
                'State',
                'Pickup Date',
                'Pickup Time',
                'Pickup Day',
                'Start Date',
                'First Attempt Date',
                'Failed 1st Attempt Date & Time',
                'First Attempt Status',
                '2nd Attempt Date',
                'Failed 2nd Attempt Date & Time',
                '2nd Attempt Status',
                '3rd Attempt Date',
                'Failed 3rd Attempt Date & Time',
                '3rd Attempt Status',
                'Delivery Date',
                'Number Attempt',
                'City',
            ]);

            // Fetch data in chunks and write each chunk to the CSV
            $this->getDataForCSV($request)->chunk(1000, function ($shippingEvents) use ($handle) {
                foreach ($shippingEvents as $event) {
                    $shipping = $event->shipping;
                    $order = $shipping->order;

                    // Retrieve the created_at date from order_logs where order_status_id is 5
                    $shippingDate = $order->logs->where('order_status_id', ORDER_STATUS_SHIPPING)->where('remarks', 'First Milestone from Phantom')->sortByDesc('id')->first();

                    // Retrieve the created_at date from order_logs where order_status_id is 6
                    $deliveryDate = $order->logs->where('order_status_id', ORDER_STATUS_DELIVERED)->sortByDesc('id')->first();

                    // Retrieve events with specific attempt statuses
                    $events = $shipping->events->whereIn('attempt_status', [77090, 'EM013', 'EM080'])->sortBy('created_at');

                    // Retrieve reasons with specific attempt statuses
                    $reasons = $shipping->events->whereIn('attempt_status', [
                        77098,
                        77101,
                        77102,
                        77171,
                        77191,
                        'EM014',
                        'EM093',
                        'EM094',
                        'EM095',
                        'EM115',
                    ])->sortBy('created_at');

                    // Initialize default values
                    $firstAttemptDate = '';
                    $firstAttemptDescription = '';
                    $firstAttemptDateAndTime = '';
                    $secondAttemptDate = '';
                    $secondAttemptDescription = '';
                    $secondAttemptDateAndTime = '';
                    $thirdAttemptDate = '';
                    $thirdAttemptDescription = '';
                    $thirdAttemptDateAndTime = '';

                    // Retrieve and process events and reasons
                    if ($events->count() > 0) {
                        $firstEvent = $events->first();
                        $firstReason = $reasons->first();

                        // Ensure $firstReason is not null before accessing properties
                        if (!is_null($firstReason)) {
                            $firstAttemptDate = \Carbon\Carbon::parse($firstEvent->attempt_time)->format('d/m/Y') ?? '';
                            $firstAttemptDescription = $firstReason->description ?? '';
                            $firstAttemptDateAndTime = \Carbon\Carbon::parse($firstReason->attempt_time)->format('d/m/Y h:i A') ?? '';
                        } else {
                            $firstAttemptDate = \Carbon\Carbon::parse($firstEvent->attempt_time)->format('d/m/Y') ?? '';
                            $firstAttemptDescription = $firstEvent->description ?? '';
                            $firstAttemptDateAndTime = \Carbon\Carbon::parse($firstEvent->attempt_time)->format('d/m/Y h:i A') ?? '';
                        }

                        if ($events->count() > 1) {
                            $secondEvent = $events->skip(1)->first();
                            $secondReason = $reasons->skip(1)->first();

                            // Ensure $secondReason is not null before accessing properties
                            if (!is_null($secondReason)) {
                                $secondAttemptDate = \Carbon\Carbon::parse($secondEvent->attempt_time)->format('d/m/Y') ?? '';
                                $secondAttemptDescription = $secondReason->description ?? '';
                                $secondAttemptDateAndTime = \Carbon\Carbon::parse($secondReason->attempt_time)->format('d/m/Y h:i A') ?? '';
                            }

                            if ($events->count() > 2) {
                                $thirdEvent = $events->skip(2)->first();
                                $thirdReason = $reasons->skip(2)->first();

                                // Ensure $thirdReason is not null before accessing properties
                                if (!is_null($thirdReason)) {
                                    $thirdAttemptDate = \Carbon\Carbon::parse($thirdEvent->attempt_time)->format('d/m/Y') ?? '';
                                    $thirdAttemptDescription = $thirdReason->description ?? '';
                                    $thirdAttemptDateAndTime = \Carbon\Carbon::parse($thirdReason->attempt_time)->format('d/m/Y h:i A') ?? '';
                                }
                            }
                        }
                    }

                    // Purchase Type
                    switch ($order->purchase_type) {
                        case '1':
                            $purchaseType = 'COD';
                            break;
                        case '2':
                            $purchaseType = 'Paid';
                            break;
                        case '3':
                            $purchaseType = 'Installment';
                            break;
                        default:
                            $purchaseType = '';
                            break;
                    }

                    fputcsv($handle, [
                        $order->company->code ?? '', // Business Unit
                        $order->sales_id ?? '', // Order ID
                        $order->courier->name ?? '', // Courier
                        $purchaseType, // Purchase Type
                        "'" . $shipping->tracking_number ?? '', // Tracking Number
                        $shippingDate->created_at->format('d/m/Y') ?? '', // Shipping Date
                        $order->customer->postcode ?? '', // Postcode
                        MY_STATES[$order->customer->state] ?? '', // State
                        $shippingDate->created_at->format('d/m/Y') ?? '', // Pickup Date
                        $shippingDate->created_at->format('h:i A') ?? '', // Pickup Time
                        $shippingDate->created_at->format('D') ?? '', // Pickup Day
                        $firstAttemptDate, // Start Date
                        $firstAttemptDate, // First Attempt Date
                        $firstAttemptDateAndTime, // Failed 1st Attempt Date & Time
                        $firstAttemptDescription, // First Attempt Status
                        $secondAttemptDate, // 2nd Attempt Date
                        $secondAttemptDateAndTime, // Failed 2nd Attempt Date & Time
                        $secondAttemptDescription, // 2nd Attempt Status
                        $thirdAttemptDate, // 3rd Attempt Date
                        $thirdAttemptDateAndTime, // Failed 3rd Attempt Date & Time
                        $thirdAttemptDescription, // 3rd Attempt Status
                        $deliveryDate != null ? $deliveryDate->created_at->format('d/m/Y') : '', // Delivery Date
                        $events->count(), // Number Attempt
                        $order->customer->city ?? '' // City
                    ]);
                }
            });

            fclose($handle);
        });

        // Set headers for download
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $date . '_attempt_order_list.csv"');

        return $response;
    }

    // Method to fetch data based on existing filter logic
    private function getDataForCSV(Request $request)
    {
        return ShippingEvent::with(['shipping.order.customer', 'shipping.order.courier'])
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('shipping_events')
                    ->groupBy('shipping_id');
            })
            ->whereHas('shipping.order', function ($query) use ($request) {
                // Apply search and date filters
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('tracking_number', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('customer', function ($query) use ($searchTerm) {
                                $query->where('phone', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhereHas('courier', function ($query) use ($searchTerm) {
                                $query->where('name', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhere('sales_id', 'like', '%' . $searchTerm . '%');
                    });
                }

                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $dateFrom = \Carbon\Carbon::parse($request->date_from)->startOfDay();
                    $dateTo = \Carbon\Carbon::parse($request->date_to)->endOfDay();

                    $query->whereHas('logs', function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery->whereRaw('order_logs.id IN (SELECT MAX(id) FROM order_logs GROUP BY order_id)')
                            ->whereBetween('created_at', [$dateFrom, $dateTo]);
                    });
                }
            })
            ->whereHas('shipping.order.logs', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('order_status_id', ORDER_STATUS_DELIVERED)
                        ->orWhere('order_status_id', ORDER_STATUS_SHIPPING);
                });
            });
    }
}
