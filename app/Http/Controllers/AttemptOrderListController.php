<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttemptOrderListController extends Controller
{
    public function index(Request $request)
    {
        // Start with the Shipping model
        $shippings = Shipping::with(['order.customer', 'order.courier', 'events'])
            ->whereHas('order', function ($query) use ($request) {
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

                // Apply date filters and status for order logs
                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $query->whereHas('logs', function ($subQuery) use ($request) {
                        $subQuery->whereBetween('created_at', [
                            $request->date_from,
                            $request->date_to
                        ])->where('order_status_id', 5);
                    });
                }
            })
            ->paginate(10);

        return view('attempt_order_list.index', [
            'title' => 'Attempt Order List',
            'shippings' => $shippings
        ]);
    }

    public function filter(Request $request)
    {
        // Redirect to index with query parameters
        return redirect()->route('attempt_order_list', $request->all());
    }

    public function downloadCSV(Request $request)
    {
        $shippings = $this->getDataForCSV($request);
        $date = date('YmdHis');

        // Generate CSV file
        $response = new StreamedResponse(function () use ($shippings) {
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

            // Add data
            foreach ($shippings as $shipping) {
                // Retrieve events with specific attempt statuses
                $events = $shipping->events->whereIn('attempt_status', [77090, 'EM013', 'EM080'])->sortByDesc('attempt_time');

                // Retrieve reasons with specific attempt statuses
                $reason = $shipping->events->whereIn('attempt_status', [
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
                ])->sortByDesc('attempt_time');

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
                    $firstReason = $reason->first();
                    $firstAttemptDate = \Carbon\Carbon::parse($firstEvent->attempt_time)->format('d/m/Y');
                    $firstAttemptDescription = $firstReason->description ?? '';
                    $firstAttemptDateAndTime = \Carbon\Carbon::parse($firstReason->attempt_time)->format('d/m/Y h:i A');

                    if ($events->count() > 1) {
                        $secondEvent = $events->skip(1)->first();
                        $secondReason = $reason->skip(1)->first();
                        $secondAttemptDate = \Carbon\Carbon::parse($secondEvent->attempt_time)->format('d/m/Y');
                        $secondAttemptDescription = $secondReason->description ?? '';
                        $secondAttemptDateAndTime = \Carbon\Carbon::parse($secondReason->attempt_time)->format('d/m/Y h:i A');

                        if ($events->count() > 2) {
                            $thirdEvent = $events->skip(2)->first();
                            $thirdReason = $reason->skip(2)->first();
                            $thirdAttemptDate = \Carbon\Carbon::parse($thirdEvent->attempt_time)->format('d/m/Y');
                            $thirdAttemptDescription = $thirdReason->description ?? '';
                            $thirdAttemptDateAndTime = \Carbon\Carbon::parse($thirdReason->attempt_time)->format('d/m/Y h:i A');
                        }
                    }
                }

                // Purchase Type
                switch ($shipping->order->purchase_type) {
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
                    $shipping->order->company->code ?? '',
                    $shipping->order->sales_id ?? '',
                    $shipping->order->courier->name ?? '',
                    $purchaseType,
                    $shipping->tracking_number ?? '',
                    \Carbon\Carbon::parse($shipping->dt_request_shipping)->format('d/m/Y'),
                    $shipping->order->customer->postcode ?? '',
                    MY_STATES[$shipping->order->customer->state] ?? '',
                    \Carbon\Carbon::parse($shipping->dt_request_shipping)->format('d/m/Y'),
                    \Carbon\Carbon::parse($shipping->dt_request_shipping)->format('h:i A'),
                    \Carbon\Carbon::parse($shipping->dt_request_shipping)->format('D'),
                    $firstAttemptDate,
                    $firstAttemptDate,
                    $firstAttemptDateAndTime,  // Failed 1st Attempt Date & Time placeholder
                    $firstAttemptDescription,
                    $secondAttemptDate,
                    $secondAttemptDateAndTime,  // Failed 2nd Attempt Date & Time placeholder
                    $secondAttemptDescription,
                    $thirdAttemptDate,
                    $thirdAttemptDateAndTime,  // Failed 3rd Attempt Date & Time placeholder
                    $thirdAttemptDescription,
                    \Carbon\Carbon::parse($shipping->dt_request_shipping)->format('d/m/Y'),
                    $events->count() ?? '',
                    $shipping->order->customer->city ?? ''
                ]);
            }

            fclose($handle);
        });

        // Set headers for download
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $date . '_attempt_order_list.csv"');

        return $response;
    }

    // Method to fetch data based on existing filter logic
    private function getDataForCSV(Request $request)
    {
        // Start with the Shipping model
        $query = Shipping::with(['order.customer', 'order.courier', 'order.company', 'events'])
            ->whereHas('order', function ($query) use ($request) {
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
                            });
                    });
                }

                // Apply date filters and status for order logs
                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $query->whereHas('logs', function ($subQuery) use ($request) {
                        $subQuery->whereBetween('created_at', [
                            $request->date_from,
                            $request->date_to
                        ])->where('order_status_id', 5);
                    });
                }
            });

        return $query->get();
    }
}
