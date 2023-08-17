<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ReportController extends Controller
{
    public function index(): string
    {
        return "Hello World";
    }

    public function sla(): \Illuminate\Contracts\View\View
    {
        return view('reports.sla');
    }

    public function outbound(): \Illuminate\Contracts\View\View
    {
        $title = 'Outbound';
        $products = Product::active()->orderBy('name')->get();
        $product_lists = Product::active()->orderBy('name')->paginate(10);
        $companies = Company::all();
        return view('reports.outbound', compact('title', 'products', 'product_lists', 'companies'));
    }

    public function get_outbound(Request $request): \Illuminate\Http\Response
    {
        sleep(1); // to simulate slow response
        $start = $request->input('date_from') != null ? Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s') : Carbon::minValue()->format('Y-m-d H:i:s');
        $end = $request->input('date_to') != null ? Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s') : Carbon::maxValue()->format('Y-m-d H:i:s');
        $product_id = $request->input('product_id');

        $orders = Shipping::with('order.items')
            // ->whereHas('order.items', function ($q) use ($product_id) {
            //     $q->where('product_id', $product_id);
            // })
            ->join('orders', 'orders.id', '=', 'shippings.order_id')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('shippings.scanned_at', '>=', $start)
            ->where('shippings.scanned_at', '<=', $end)
            ->get();
            // ->where('scanned_at', '>=', $start)->where('scanned_at', '<=', $end)->get();


        $total_orders = $orders->count();
        $total_products = $orders->sum(function ($order) use ($product_id) {
            return $order->order->items->where('product_id', $product_id)->sum('quantity');
        });
        $total_by_company = $orders->groupBy('order.company_id')->map(function ($item) use ($product_id) {
            return $item->sum(function ($order) use ($product_id) {
                return $order->order->items->where('product_id', $product_id)->sum('quantity');
            });
        });

        $total_by_operational_model = $orders->groupBy('order.operational_model_id')->map(function ($item) use ($product_id) {
            return $item->sum(function ($order) use ($product_id) {
                return $order->order->items->where('product_id', $product_id)->sum('quantity');
            });
        });

        $total_by_payment_type = $orders->groupBy('order.payment_type')->map(function ($item) use ($product_id) {
            return $item->sum(function ($order) use ($product_id) {
                return $order->order->items->where('product_id', $product_id)->sum('quantity');
            });
        });

        return response(compact('product_id', 'total_orders', 'total_products', 'total_by_company', 'total_by_operational_model', 'total_by_payment_type'));
    }

    public function order_matrix(): \Illuminate\Contracts\View\View
    {
        $title = 'Order Matrix';
        $products = Product::active()->orderBy('name')->get();
        $couriers = Courier::active()->orderBy('name')->get();
        $companies = Company::all();
        return view('reports.order_matrix', compact('title', 'products', 'couriers', 'companies'));
    }

    public function get_order_matrix(Request $request) {
        $start = $request->input('date_from') != null ? Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s') : Carbon::minValue()->format('Y-m-d H:i:s');
        $end = $request->input('date_to') != null ? Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s') : Carbon::maxValue()->format('Y-m-d H:i:s');
        $filter_by = $request->input('filter_by');
        $product = $request->input('product');
        $courier = $request->input('courier');

        $total_extract = Order::active()->with(['items'])->where('bucket_added_at', '>=', $start)->where('bucket_added_at', '<=', $end);
        $total_scanned = Order::active()->with(['items','shippings'])
            ->whereHas('shippings', function ($q) use ($start, $end) {
                $q->where('scanned_at', '>=', $start)->where('scanned_at', '<=', $end);
            });
        $total_shipped = Order::active()->with(['items', 'logs'])
            ->whereHas('logs', function ($q) use ($start, $end) {
                $q->where('order_status_id', ORDER_STATUS_SHIPPING)->where('created_at', '>=', $start)->where('created_at', '<=', $end);
            });

        if($filter_by == 'product'){
            if($product != ''){
                $total_extract = $total_extract->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->count();
                $total_scanned = $total_scanned->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->count();
                $total_shipped = $total_shipped->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->count();
                // $total_extract = $total_extract->whereHas('items', function ($q) use ($product) {
                //     $q->where('product_id', $product);
                // })->count();
                // $total_scanned = $total_scanned->whereHas('items', function ($q) use ($product) {
                //     $q->where('product_id', $product);
                // })->count();
                // $total_shipped = $total_shipped->whereHas('items', function ($q) use ($product) {
                //     $q->where('product_id', $product);
                // })->count();
            }
            else{
                $total_extract = $total_extract->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id')->map(function ($item) {
                    return $item->count();
                });
                $total_scanned = $total_scanned->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id')->map(function ($item) {
                    return $item->count();
                });
                $total_shipped = $total_shipped->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id')->map(function ($item) {
                    return $item->count();
                });

                // //group and count order by product
                // $total_extract = $total_extract->groupBy('items[0].product_id')->map(function ($item) {
                //     return $item->count();
                // });

                // $total_scanned = $total_scanned->get()->groupBy('items.product_id');
                // $total_scanned = $total_scanned[''];
                // $total_shipped = $total_shipped->get()->groupBy('items.product_id');
                // $total_shipped = $total_shipped[''];
            }
        }
        if($filter_by == 'courier'){
            if($courier != ''){
                $total_extract = $total_extract->where('courier_id', $courier)->count();
                $total_scanned = $total_scanned->where('courier_id', $courier)->count();
                $total_shipped = $total_shipped->where('courier_id', $courier)->count();
            }
            else{
                $total_extract = $total_extract->get()->groupBy('courier_id')->map(function ($item) {
                    return $item->count();
                });
                $total_scanned = $total_scanned->get()->groupBy('courier_id')->map(function ($item) {
                    return $item->count();
                });
                $total_shipped = $total_shipped->get()->groupBy('courier_id')->map(function ($item) {
                    return $item->count();
                });
            }
        }
        // dd($total_extract, $total_scanned, $total_shipped);
        return response([
            'comparison' => true,
            'total_extract' => $total_extract,
            'total_scanned' => $total_scanned,
            'total_shipped' => $total_shipped,
        ]);
    }

    public function get_order_matrix_extract(Request $request){

        $start = $request->input('date_from') != null ? Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s') : Carbon::minValue()->format('Y-m-d H:i:s');
        $end = $request->input('date_to') != null ? Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s') : Carbon::maxValue()->format('Y-m-d H:i:s');
        $filter_by = $request->input('filter_by');
        $product = $request->input('product');
        $courier = $request->input('courier');
        $companies = Company::all();

        $total_extract = Order::active()->where('bucket_added_at', '>=', $start)->where('bucket_added_at', '<=', $end);

        $extract_company = [];
        $total_by_operational_model = [];
        $total_by_payment_type = [];

        if($filter_by == 'product'){
            if($product != ''){
                $total_extract = $total_extract->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->get();
                foreach($companies as $company){
                    $extract_company[$company->id] = $total_extract->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_extract->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_extract->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_extract = $total_extract->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id');

                // count by company
                foreach($total_extract as $key => $extract){
                    foreach($companies as $company){
                        $extract_company[$key][$company->id] = $extract->where('company_id', $company->id)->count();
                    }
                }
                // $extract_company = $extract_company[''];
                //count by operation model
                foreach($total_extract as $key => $extract){
                    $total_by_operational_model[$key] = $extract->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_operational_model = $total_by_operational_model[''];
                // count by payment type
                foreach($total_extract as $key => $extract){
                    $total_by_payment_type[$key] = $extract->groupBy('payment_type')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_payment_type = $total_by_payment_type[''];
            }
        }

        if($filter_by == 'courier'){
            if($courier != ''){
                $total_extract = $total_extract->where('courier_id', $courier)->get();
                foreach($companies as $company){
                    $extract_company[$company->id] = $total_extract->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_extract->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_extract->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_extract = $total_extract->get()->groupBy('courier_id');
                // count by company
                foreach($total_extract as $key => $extract){
                    foreach($companies as $company){
                        $extract_company[$key][$company->id] = $extract->where('company_id', $company->id)->count();
                    }
                }
                //count by operation model
                foreach($total_extract as $key => $extract){
                    $total_by_operational_model[$key] = $extract->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // count by payment type
                foreach($total_extract as $key => $extract){
                    $total_by_payment_type[$key] = $extract->groupBy('payment_type')->map(function ($item) {
                        return $item->count();
                    });
                }

            }
        }

        return response([
            // 'total_extract_by_courier' => $total_extract,
            'total_by_company' => $extract_company,
            'total_by_operational_model' => $total_by_operational_model,
            'total_by_payment_type' => $total_by_payment_type,
        ]);
    }

    public function get_order_matrix_pack(Request $request)
    {
        $start = $request->input('date_from') != null ? Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s') : Carbon::minValue()->format('Y-m-d H:i:s');
        $end = $request->input('date_to') != null ? Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s') : Carbon::maxValue()->format('Y-m-d H:i:s');
        $filter_by = $request->input('filter_by');
        $product = $request->input('product');
        $courier = $request->input('courier');
        $companies = Company::all();

        $total_pack = Order::active()->with(['shippings'])->whereHas('shippings', function ($q) use ($start, $end) {
            $q->where('scanned_at', '>=', $start)->where('scanned_at', '<=', $end);
        });

        $pack_company = [];
        $total_by_operational_model = [];
        $total_by_payment_type = [];

        if($filter_by == 'courier'){
            if($courier != ''){
                $total_pack = $total_pack->where('courier_id', $courier)->get();
                foreach($companies as $company){
                    $pack_company[$company->id] = $total_pack->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_pack->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_pack->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_pack = $total_pack->get()->groupBy('courier_id');
                // count by company
                foreach($total_pack as $key => $pack){
                    foreach($companies as $company){
                        $pack_company[$key][$company->id] = $pack->where('company_id', $company->id)->count();
                    }
                }
                //count by operation model
                foreach($total_pack as $key => $pack){
                    $total_by_operational_model[$key] = $pack->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // count by payment type
                foreach($total_pack as $key => $pack){
                    $total_by_payment_type[$key] = $pack->groupBy('payment_type')->map(function ($item) {
                        return $item->count();
                    });
                }

            }
        }

        if($filter_by == 'product'){
            if($product != ''){
                $total_pack = $total_pack->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->get();
                foreach($companies as $company){
                    $pack_company[$company->id] = $total_pack->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_pack->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_pack->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_pack = $total_pack->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id');
                // count by company
                foreach($total_pack as $key => $pack){
                    foreach($companies as $company){
                        $pack_company[$key][$company->id] = $pack->where('company_id', $company->id)->count();
                    }
                }
                // $pack_company = $pack_company[''];
                //count by operation model
                foreach($total_pack as $key => $pack){
                    $total_by_operational_model[$key] = $pack->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_operational_model = $total_by_operational_model[''];
                // count by payment type
                foreach($total_pack as $key => $pack){
                    $total_by_payment_type[$key] = $pack->groupBy('payment_type')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_payment_type = $total_by_payment_type[''];
            }
        }

        return response([
            // 'total_pack_by_courier' => $total_pack,
            'total_by_company' => $pack_company,
            'total_by_operational_model' => $total_by_operational_model,
            'total_by_payment_type' => $total_by_payment_type,
        ]);

    }

    public function get_order_matrix_pickup(Request $request)
    {
        $start = $request->input('date_from') != null ? Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s') : Carbon::minValue()->format('Y-m-d H:i:s');
        $end = $request->input('date_to') != null ? Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s') : Carbon::maxValue()->format('Y-m-d H:i:s');
        $filter_by = $request->input('filter_by');
        $product = $request->input('product');
        $courier = $request->input('courier');
        $companies = Company::all();

        $total_pickup = Order::active()->with(['logs'])->whereHas('logs', function ($q) use ($start, $end) {
            $q->where('order_status_id', ORDER_STATUS_SHIPPING)->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        });

        $pickup_company = [];
        $total_by_operational_model = [];
        $total_by_payment_type = [];

        if($filter_by == 'courier'){
            if($courier != ''){
                $total_pickup = $total_pickup->where('courier_id', $courier)->get();
                foreach($companies as $company){
                    $pickup_company[$company->id] = $total_pickup->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_pickup->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_pickup->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_pickup = $total_pickup->get()->groupBy('courier_id');
                // count by company
                foreach($total_pickup as $key => $pickup){
                    foreach($companies as $company){
                        $pickup_company[$key][$company->id] = $pickup->where('company_id', $company->id)->count();
                    }
                }
                //count by operation model
                foreach($total_pickup as $key => $pickup){
                    $total_by_operational_model[$key] = $pickup->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // count by payment type
                foreach($total_pickup as $key => $pickup){
                    $total_by_payment_type[$key] = $pickup->groupBy('payment_type')->map(function ($item) {
                        return
                            $item->count();
                    });
                }
            }
        }

        if($filter_by == 'product'){
            if($product != ''){
                $total_pickup = $total_pickup->join('order_items', 'orders.id', '=', 'order_items.order_id')->where('product_id', $product)->get();
                foreach($companies as $company){
                    $pickup_company[$company->id] = $total_pickup->where('company_id', $company->id)->count();
                }
                $total_by_operational_model = $total_pickup->groupBy('operational_model_id')->map(function ($item) {
                    return $item->count();
                });
                $total_by_payment_type = $total_pickup->groupBy('payment_type')->map(function ($item) {
                    return $item->count();
                });
            }
            else{
                $total_pickup = $total_pickup->join('order_items', 'orders.id', '=', 'order_items.order_id')->get()->groupBy('product_id');
                // count by company
                foreach($total_pickup as $key => $pickup){
                    foreach($companies as $company){
                        $pickup_company[$key][$company->id] = $pickup->where('company_id', $company->id)->count();
                    }
                }
                // $pickup_company = $pickup_company[''];
                //count by operation model
                foreach($total_pickup as $key => $pickup){
                    $total_by_operational_model[$key] = $pickup->groupBy('operational_model_id')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_operational_model = $total_by_operational_model[''];
                // count by payment type
                foreach($total_pickup as $key => $pickup){
                    $total_by_payment_type[$key] = $pickup->groupBy('payment_type')->map(function ($item) {
                        return $item->count();
                    });
                }
                // $total_by_payment_type = $total_by_payment_type[''];
            }
        }

        return response([
            // 'total_pickup_by_courier' => $total_pickup,
            'total_by_company' => $pickup_company,
            'total_by_operational_model' => $total_by_operational_model,
            'total_by_payment_type' => $total_by_payment_type,
        ]);
    }

    public function pending_report(): \Illuminate\Contracts\View\View
    {
        $title = 'Pending Report';
        $products = Product::active()->orderBy('name')->get();
        $couriers = Courier::all();
        $companies = Company::all();
        return view('reports.pending_report', compact('title', 'products', 'couriers', 'companies'));
    }


    public function get_pending_report(Request $request): \Illuminate\Http\Response
    {
        $orders = Order::with('items')->active()->where('status', ORDER_STATUS_PENDING);
        if ($request->input('date_from') != null) {
            $orders->where('created_at', '>=', Carbon::parse($request->input('date_from') . ' 00:00:00')->format('Y-m-d H:i:s'));
        }
        if ($request->input('date_to') != null) {
            $orders->where('created_at', '<=', Carbon::parse($request->input('date_to') . ' 23:59:59')->format('Y-m-d H:i:s'));
        }
        if ($request->input('product') != null) {
            $orders->whereHas('items', function ($q) use ($request) {
                $q->whereIn('product_id', explode(',', $request->input('product')))->where('status',1);
            });
        }
        $orders = $orders->get();

        $count_orders = $orders->count();
        $productCounts = [];
        $order_count = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->status == 1) {
                    $productId = $item->product_id;

                    if (!isset($productCounts[$productId])) {
                        $productCounts[$productId] = 0;
                    }

                    $productCounts[$productId]++;
                }
                $order_count++;
            }
        }

        $products = Product::whereIn('id', array_keys($productCounts))->get();
        $total_order_by_product = $products->map(function ($product) use ($productCounts) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_count' => $productCounts[$product->id]
            ];
        });

        return response([
            'total_orders' => $count_orders,
            'total_order_by_product' => $total_order_by_product,
        ]);
    }

    public function shipment(): \Illuminate\Contracts\View\View
    {
        return view('reports.shipment');
    }
}
