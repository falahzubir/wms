<x-layout :title="$title">
    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
        }
        .outline-good, .outline-defect {
            position: relative;
        }

        .outline-good {
            border: 1px solid #00ff00 !important;
        }

        .outline-defect {
            border: 1px solid #ff0000 !important;
        }

        .outline-good::before {
            content: "";
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            left: 5px;
            border: 1px solid #00ff00;
            box-shadow: 0 0 0 5px #CDF7E3;
        }

        .outline-defect::before {
            content: "";
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            left: 5px;
            border: 1px solid #ff0000;
            box-shadow: 0 0 0 5px #F9C2C2;
        }

        #returnModal table {
            font-size: 0.8rem;
        }

        .img-50{
            max-width: 50px;
            max-height: 50px;
        }

        .btn-purple {
            background-color: purple;
            color: white; /* Set the text color to white or another contrasting color */
        }

        .btn-purple:hover {
            background-color: #4b2b6b;
            color: white; /* Set the text color to white or another contrasting color */
        }

        .bg-susu {
            background-color: #FF8244;
        }
        .swal2-styled.swal2-custom {
            border: 0;
            border-radius: .25em;
            /* background: initial; */
            font-size: 1em;
            border: 1px solid #cecece;
            box-shadow: 1px 1px 0px 0px #cecece;
        }

        .swal2-dhl-ecommerce {
            background-color: #FFCC00;
            color: #D40510;
        }

        .swal2-posmalaysia {
            background-color: #fff;
            color: #FF0000;
        }

        .swal2-tiktok {
            background-color: #000;
            color: #fff;
        }

        .swal2-shopee {
            background-color: #E74A2B;
            color: #fff;
        }

        .swal2-styled.swal2-custom {
            border: 0;
            border-radius: .25em;
            /* background: initial; */
            font-size: 1em;
            border: 1px solid #cecece;
            box-shadow: 1px 1px 0px 0px #cecece;
        }
        .swal2-dhl-ecommerce {
            background-color: #FFCC00;
            color: #D40510;
        }

        .swal2-posmalaysia {
            background-color: #fff;
            color: #FF0000;
        }

        .swal2-tiktok {
            background-color: #000;
            color: #fff;
        }

        .swal2-shopee {
            background-color: #E74A2B;
            color: #fff;
        }

        .bg-purple {
            background-color: purple;
        }

        .btn-teal {
            background-color: #3B8C9E;
            color: #fff;
        }

        .btn-teal:hover {
            background-color: #2d6a75;
            color: #fff;
        }

        .small-check-box {
            margin: 4px 0 0;
            line-height: normal;
            width: 14px;
            height: 14px;
        }
        .custom-btn-width {
            width: 100%; /* Adjust this value as needed */
        }


    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- No Labels Form -->
                    <form id="search-form" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="col-md-12">
                            <div class="btn-group" data-toggle="buttons">
                                <input type="radio" class="btn-check" id="btn-check-today" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-today">Today</label>

                                <input type="radio" class="btn-check" id="btn-check-yesterday" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-yesterday">Yesterday</label>

                                <input type="radio" class="btn-check" id="btn-check-this-month" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-this-month">This Month</label>

                                <input type="radio" class="btn-check" id="btn-check-last-month" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-last-month">Last Month</label>

                                <input type="radio" class="btn-check" id="btn-check-overall" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-overall">Overall</label>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <select id="inputState" class="form-select" name="date_type">
                                @foreach (ORDER_DATE_TYPES as $i => $type)
                                    <option value="{{ $i }}"
                                        {{ Request::get('date_type') == $i ? 'selected' : '' }} {{ $type[1] }}>
                                        {{ $type[0] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From" name="date_from"
                                id="start-date" value="{{ Request::get('date_from') ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                                value="{{ Request::get('date_to') ?? '' }}">
                        </div>
                        <div class="" id="accordionPanelsStayOpenExample">

                            <x-additional_filter :filter_data="$filter_data" />

                        </div>
                        @if (request('bucket_id') != null)
                            <input type="hidden" name="bucket_id" value="{{ request('bucket_id') }}">
                        @endif
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form><!-- End No Labels Form -->

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">

                        @if (in_array(ACTION_ARRANGE_SHIPMENT, $actions))
                            @can('order.download')
                                <button class="btn btn-purple" id="arrange-shipment-btn"><i
                                        class="bi bi-box2"></i>
                                    Arrange Shipment</button>
                            @endcan
                        @endif

                        @if (in_array(ACTION_APPROVE_AS_SHIPPED, $actions))
                            @can('order.approve_for_shipping')
                                <button class="btn btn-success" id="mark-as-shipped-btn"><i class="bi bi-truck"></i> Mark as
                                    Shipped</button>
                            @endcan
                        @endif
                        @if (in_array(ACTION_GENERATE_PICKING, $actions))
                            @can('picking_list.generate')
                                <button class="btn btn-primary" id="generate-picking-btn"><i
                                        class="bi bi-file-earmark-ruled"></i> Generate Picking</button>
                            @endcan
                        @endif
                        @if (in_array(ACTION_ADD_TO_BUCKET, $actions))
                            <button class="btn btn-info" id="add-to-bucket-btn"><i class="bi bi-basket"></i> Add to
                                Bucket</button>
                        @endif
                        @if (in_array(ACTION_GENERATE_CN, $actions))
                            @can('consignment_note.generate')
                                <button class="btn btn-warning" id="generate-cn-btn"><i
                                        class="bi bi-file-earmark-ruled"></i> Generate CN</button>
                            @endcan
                        @endif
                        @if (in_array(ACTION_UPLOAD_TRACKING_BULK, $actions))
                            @can('tracking.upload')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#upload-csv-modal">
                                    <i class="bi bi-truck"></i> Bulk Upload Tracking
                                </button>
                            @endcan
                        @endif
                        @if (in_array(ACTION_DOWNLOAD_CN, $actions))
                        @can('consignment_note.download')
                                <button class="btn btn-success" id="download-cn-btn"><i class="bi bi-cloud-download"></i>
                                    Download CN</button>
                            @endcan
                        @endif
                        @if (in_array(ACTION_DOWNLOAD_ORDER, $actions))
                            @can('order.download')
                                <button class="btn btn-secondary" id="download-order-btn"><i
                                        class="bi bi-cloud-download"></i>
                                    Download CSV</button>
                            @endcan
                        @endif


                    </div>
                    <!-- Default Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col"><input type="checkbox" name="" id=""
                                        onchange="toggleCheckboxes(this, 'check-order')"></th>
                                <th scope="col">Action</th>
                                <th scope="col">Order</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Product</th>
                                <th scope="col">Payment & Shipping</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if ($orders->count())
                                @foreach ($orders as $key => $order)
                                    <tr style="font-size: 0.8rem;">
                                        <th scope="row">{{ $key + $orders->firstItem() }}</th>
                                        <td><input onclick="removeOldTicked()" type="checkbox" name="check_order[]" class="check-order"
                                                id="" value="{{ $order->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @if (Route::is('orders.pending') || Route::is('orders.processing') || Route::is('orders.packing'))
                                                    @can('order.reject')
                                                            <button class="btn btn-danger p-0 px-1 m-1"><i class="bx bx-trash"
                                                                    onclick="reject_order({{ $order->id }})"></i></button>
                                                    @endcan
                                                @endif
                                                @if (Route::is('orders.returned'))
                                                    <button class="btn p-0 px-1 m-1 text-white" style="background-color: #006E9B"><i class="bi bi-box-seam"
                                                            onclick="return_modal({{ $order->id }}, {{ $order->items }})"></i></button>
                                                @endif
                                                {{-- add shipping number modal --}}
                                                @if (Route::is('orders.processing'))
                                                   {{-- @if($order->is_multiple_carton) --}}
                                                        <button class="btn btn-warning p-0 px-1 m-1" onclick="multiple_cn({order:`{{ $order }}`,ref_no:`{{ order_num_format($order) }}`})"></>
                                                            <i class="bi bi-file-earmark-ruled"></i>
                                                        </button>
                                                    {{-- @endif --}}
                                                    {{-- @empty($order->shippings) --}}
                                                        @can('tracking.update')
                                                            <button type="button"
                                                                class="btn btn-primary p-0 px-1 m-1 add-shipping-number"
                                                                data-bs-toggle="modal" data-bs-target="#add-shipping-number-modal"
                                                                data-orderid="{{ $order->id }}"
                                                                data-couriercode={{ $order->courier->code }}>
                                                                <i class="bi bi-truck"></i>
                                                            </button>
                                                        @endcan
                                                    {{-- @endempty --}}
                                                    <a href="{{ route('orders.change_postcode_view') }}?sales={{ $order->sales_id }}&company={{ $order->company_id}}&current_postcode={{ $order->customer->postcode }}&redirect_to={{ urlencode(url()->full()) }}" class="btn btn-warning p-0 px-1 m-1" title="Change Postcode">
                                                        <i class="bx bxs-map" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                @if (Route::is('orders.packing') || Route::is('orders.readyToShip'))
                                                    @can('shipping.cancel')
                                                        @if ($order->shippings->count())
                                                            <button class="btn btn-danger p-0 px-1 m-1 cancel-shipping" data-id="{{ $order->id }}"
                                                                data-shipping-auto-generated="{{ $order->shippings->first()->shipment_number ? 1:0 }}">
                                                                <i class="bi bi-truck"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                @endif
                                            </div>
                                    </td>
                                    <td class="text-center">
                                        @unless($order->duplicate_orders == null)
                                            <div class="badge bg-purple text-wrap" onclick="duplicateModal('{{ $order->duplicate_orders }}')" style="cursor: pointer;">
                                                Possible Duplicate
                                            </div>
                                        @endunless
                                        <div>
                                            <span role="button" class="order-num text-primary" data-sales-id="{{ $order->sales_id }}" data-order-num="{{ order_num_format($order) }}"
                                                title="Double Click to Copy">
                                                <strong>{{ order_num_format($order) }}</strong>
                                            </span>
                                        </div>
                                        <div style="font-size: 0.75rem; white-space: nowrap;" data-bs-toggle="tooltip"
                                            data-bs-placement="right" data-bs-original-title="Date Inserted">
                                            {{ date('d/m/Y H:i', strtotime($order->created_at)) }}
                                        </div>

                                        @if ($order->logs->where('order_status_id', '=', ORDER_STATUS_READY_TO_SHIP)->count() > 0)
                                            <div style="font-size: 0.75rem;" data-bs-toggle="tooltip"
                                                data-bs-placement="right" data-bs-original-title="Date Scanned">
                                                {{ $order->logs->where('order_status_id', '=', ORDER_STATUS_READY_TO_SHIP)->first()->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                        @if ($order->logs->where('order_status_id', '=', ORDER_STATUS_SHIPPING)->count() > 0)
                                            <div style="font-size: 0.75rem;" data-bs-toggle="tooltip"
                                                data-bs-placement="right" data-bs-original-title="Date Shipping">
                                                {{ $order->logs->where('order_status_id', '=', ORDER_STATUS_SHIPPING)->first()->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                        @if ($order->logs->where('order_status_id', '=', ORDER_STATUS_DELIVERED)->count() > 0)
                                            <div style="font-size: 0.75rem;" data-bs-toggle="tooltip"
                                                data-bs-placement="right" data-bs-original-title="Date Delivered">
                                                {{ $order->logs->where('order_status_id', '=', ORDER_STATUS_DELIVERED)->first()->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif

                                        {{-- <div>
                                            {{ date('H:i', strtotime($order->created_at)) }}
                                        </div> --}}
                                    </td>
                                    <td class="text-start">
                                        @if($order->courier_id == DHL_ID && ($order->customer->country == 1 || $order->customer->country == 2))
                                            @if ($order->courier_id == DHL_ID && !is_digit_count($order->customer->postcode, 5))
                                                <div class="badge bg-danger text-wrap">
                                                    Postcode Error
                                                </div>
                                            @endif
                                        @elseif($order->courier_id == DHL_ID && $order->customer->country == 3)
                                            @if ($order->courier_id == DHL_ID && !is_digit_count($order->customer->postcode, 6))
                                                <div class="badge bg-danger text-wrap">
                                                    Postcode Error
                                                </div>
                                            @endif
                                        @endif
                                        @if (!last_two_digits_zero($order->customer->postcode))
                                            <a href="{{ route('orders.change_postcode_view') }}?sales={{ $order->sales_id }}&company={{ $order->company_id}}&current_postcode={{ $order->customer->postcode }}&redirect_to={{ urlencode(url()->full()) }}" class="badge bg-warning text-wrap text-dark">
                                                Potential DHL Postcode Error
                                            </a>
                                        @endif
                                        <div><strong>{{ $order->customer->name }}</strong></div>
                                        <div>
                                            <p class="mb-0">{{ $order->customer->phone }}</p>
                                            <p class="mb-0">{{ $order->customer->phone_2 }}</p>
                                        </div>
                                        <div>
                                            <span class="customer-address">{{ $order->customer->address }}</span>,
                                            {{ $order->customer->postcode }},
                                            {{ $order->customer->city }},
                                            {{ MY_STATES[$order->customer->state] ?? '' }},
                                            {{ COUNTRY_ID[$order->customer->country] }}
                                        </div>
                                    </td>
                                    <td>
                                        @foreach ($order->items as $product)
                                            <div>{{ $product->product->name }}
                                                <strong>[{{ $product->quantity }}]</strong>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="small-text" title="Total Price">
                                            {{ currency($order->total_price, true) }}</div>
                                        <span class="badge small-text {{ $order->payment_refund > 0 ? "bg-danger" : "text-danger"}}" title="Total Refund">
                                            {{ currency($order->payment_refund, true) }}</span>
                                        <div>
                                            @switch($order->purchase_type)
                                                @case(1)
                                                    <span class="badge bg-warning text-dark">COD</span>
                                                @break

                                                @case(2)
                                                    <span class="badge bg-success text-light">Paid</span>
                                                @break

                                                @case(3)
                                                    <span class="badge bg-primary text-light">Installment</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger text-light">Error</span>
                                            @endswitch
                                        </div>
                                        <div>
                                        @if($order->payment_type != null)
                                            <span class="badge bg-primary-light text-dark">
                                                {{ $order->paymentType->payment_type_name }}
                                            </span>
                                        @endif
                                        </div>
                                        <span class="badge bg-warning text-dark">
                                            {{ $order->courier->name }}
                                        </span>

                                        @isset($order->shippings)
                                            @if($order->items->sum("quantity") > MAXIMUM_QUANTITY_PER_BOX) <!-- check if order has more than 40 quantity-->
                                                @isset($order->shippings->first()->tracking_number) <!-- check if order has at least 1 CN -->
                                                @foreach ($order->shippings as $shipping)

                                                <div>
                                                    <span class="phantom"
                                                    data-tracking="{{ $shipping->tracking_number }}">
                                                    {{ $shipping->tracking_number }}
                                                </span>
                                            </div>
                                            @endforeach
                                                @endisset
                                            @else
                                                @foreach ($order->shippings as $shipping)
                                                    <div>
                                                        <span class="phantom"
                                                            data-tracking="{{ $shipping->tracking_number }}">
                                                            {{ $shipping->tracking_number }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endisset
                                        @isset($order->sales_remarks)
                                            @if($order->sales_remarks != null)
                                                <div class="small-text font-weight-bold">
                                                    {{ str_replace('<br>', '', urldecode($order->sales_remarks)) }}
                                                </div>
                                                @endif
                                        @endisset
                                    </td>
                                    <td>
                                        <x-order_status :order="$order" />

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="100%" class="text-center">
                                    <div class="alert alert-warning" role="alert">
                                        No order found!
                                    </div>
                                </td>
                            </tr>
                        @endif
                        {{-- <tr>
                                <td colspan="100%" class="text-center">
                                    <div class="spinner-border text-secondary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr> --}}
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of
                        {{ $orders->total() }} orders
                    </div>
                    {{ $orders->withQueryString()->links() }}
                </div>
                <!-- End Default Table Example -->
            </div>
        </div>
    </div>
</section>

<!-- upload csv modal -->
<div class="modal fade" id="upload-csv-modal" tabindex="-1" aria-labelledby="upload-csv-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upload-csv-modalLabel">Upload CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="text-start mb-3 very-small-text font-weight-bold">
                    <div>
                        + Easily add tracking number bulk by hitting the "Upload CSV" button. System will
                        automatically
                        update the tracking number for the selected order.
                    </div>
                    <div>
                        + Feel free to change the Shipping Date accordingly.
                    </div>
                </div>
                <form action="{{ route('shipping.upload_bulk_tracking') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-center align-items-center g-2">
                        <div class="col">
                            <select name="company" id="company" class="form-control">
                                @foreach ($filter_data->companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <small id="dateShipping" class="form-text text-muted">Choose company</small>
                        </div>
                        <div class="col">
                            <input class="form-control" type="file" id="csv-file" name="file"
                                accept=".csv" aria-describedby="uploadCsv" required>
                            <small id="uploadCsv" class="form-text text-muted">Upload CSV File</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <!-- button submit show loading on submit -->
                        <button type="submit" class="btn btn-primary mt-3" id="upload-csv-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="d-none">Loading...</span>
                            <span class="d-inline">Upload CSV</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-start">
                <span class="text-danger">Accept only CSV file</span>
                <span>Download sample CSV file <a href="https://bosemzi.com/document/template/template_add_tracking_new.csv">here</a></span>
            </div>
        </div>
    </div>
</div> <!-- end upload csv modal -->

<!-- add shipping number to order modal -->
<div class="modal fade" id="add-shipping-number-modal" tabindex="-1"
    aria-labelledby="add-shipping-number-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-shipping-number-modalLabel">Add Tracking Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="add-shipping-number-modal-body">

                <form action="{{ route('shipping.update_tracking') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="order-id">
                    <div class="justify-content-center align-items-center g-2">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="input-select-courier">Courier</label>
                            <select class="form-select" id="input-select-courier" name="courier">
                                @foreach (get_couriers() as $courier)
                                    <option value="{{ $courier->code }}">{{ $courier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="input-tracking-number">Tracking No</label>
                            <input type="text" class="form-control" name="tracking_number"
                                id="input-tracking-number" aria-describedby="trackingNumber"
                                placeholder="Tracking Number" required>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="input-shipment-date">Shipment Date</label>
                            <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                name="shipment_date" id="input-shipment-date" aria-describedby="shipmentDate"
                                placeholder="Shipment Date" required>
                        </div>

                    </div>
                    <div class="mb-3">

                        <button type="submit" class="btn btn-primary mt-3" id="add-shipping-number-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="d-none">Loading...</span>
                            <span class="d-inline">Add Tracking Number</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end add shipping number to order modal -->

<!-- other website embeded modal -->
<div class="modal fade" id="phantom-modal" tabindex="-1" aria-labelledby="phantom-modalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="phantom-modalLabel">Phantom Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div id="phantomLoad"></div>
                </div>

            </div>
        </div>
    </div>

</div> <!-- end other website embeded modal -->

<!-- modal for split parcels -->
<div class="modal fade" id="split-parcel-modal" tabindex="-1" aria-labelledby="split-parcel-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-shipping-number-modalLabel">Split Parcels</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="add-shipping-number-modal-body">

                <form action="{{ route('shipping.dhl_label_single') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="split-order-id">
                    <div class="justify-content-center align-items-center g-2">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="split-parcel-count">Parcel Count</label>
                            <input type="number" class="form-control" id="split-parcel-count"
                                name="parcel_count" required>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="split-parcel-weight">Weight Per Parcel</label>
                            <input type="text" class="form-control" name="parcel_weight"
                                id="split-parcel-weight" placeholder="Parcel Weight" required readonly>
                            <label class="input-group-text" for="split-parcel-weight">kg</label>
                        </div>
                        <input type="hidden" id="split-parcel-weight-total">

                    </div>
                    <div class="mb-3">

                        <button type="submit" class="btn btn-primary mt-3" id="add-shipping-number-btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="d-none">Loading...</span>
                            <span class="d-inline">Confirm</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end modal for split parcels -->

  <!-- Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h1 class="modal-title fs-5" id="returnModalLabel">Parcel Condition</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="upload" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="parcel_condition" id="parcel_condition">
                    <input type="hidden" name="order_id" id="returnModalOrderId">
                    <div class="modal-body">
                        <div class="row mx-5 mb-3">
                            <div class="col">
                                <div id="good-cond" class="border mx-3 text-center p-4" role="button" onclick="parcel_cond(1)">
                                    Good
                                </div>
                            </div>
                            <div class="col">
                                <div id="bad-cond" class="border mx-3 text-center p-4" role="button" onclick="parcel_cond(0)">
                                    Defect
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5">
                            <div id="good-cond-content" class="d-none">
                                <div class="small text-center mb-3">
                                    <i class="bi bi-info-circle-fill text-warning"></i>
                                    All returned products were in good condition without defective units
                                </div>
                                <div class="row mx-5">
                                    <div class="table d-flex justify-content-center align-items-center">
                                        <table class="table table-bordered w-100">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Product Name</th>
                                                    <th>Total Unit</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div id="bad-cond-content" class="d-none">
                                <div class="small text-center mb-3">
                                    <i class="bi bi-info-circle-fill text-warning"></i>
                                    At least one of the returned products was found to be defective
                                </div>
                                <div class="row">

                                    <div class="mb-3">
                                        <label for="claim_type" class="form-label ms-2"><strong>Claim</strong></label>
                                        <select name="claim_type" id="claim_type" class="form-control" onchange="claim_type_click(this)">
                                            <option value="1">Product</option>
                                            <option value="2">Courier Cost</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="claim_from" class="form-label ms-2"><strong>Claim From</strong></label>
                                        <select name="claim_from" id="claim_from" class="form-control">
                                            <option value="1">Courier</option>
                                            <option value="2">Company</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="claim_note" class="form-label ms-2"><strong>Claim Note</strong></label>
                                        <textarea name="claim_note" id="claim_note" rows="2" class="form-control"></textarea>
                                    </div>
                                    <div class="mx-0">
                                        <div class="table table-responsive w-100">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Product Name</th>
                                                        <th>Total Unit</th>
                                                        <th>Defect Unit</th>
                                                        <th>Batch No</th>
                                                        <th>Upload Photo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="parcel_cond_submit()">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Start Bucket Category Modal --}}
    <div class="modal fade" id="modal-open-bucket-category" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="" id="submit-open-bucket-category">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="category-title">Add to Bucket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="pt-3 pb-3">
                            <p class="fw-bold" style="font-size: 20px;" for="category-id">Please select bucket Category!</p>
                            <div style="display: flex ;justify-content: center;">
                                <select onchange="selectCategory(this)" class="form-select" id="category-id" name="category_id" style="width: 80%" data-live-search="true">
                                    <option value="">Select a Category</option>
                                    @foreach ($filter_data->bucket_categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="submit-bucket-category" onclick="proceedModal()" type="button"
                            class="btn btn-warning text-white" disabled>Proceed</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- End Bucket Category Modal --}}

    {{-- Start Proceed Modal --}}
    <div class="modal fade" id="modal-proceed-bucket" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="" id="submit-proceed-bucket">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="category-title">Add to Bucket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4 pb-4">

                        {{-- Button --}}
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-12 col-md-4 text-center pb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark custom-btn-width" disabled>
                                        <span style="font-size: 16px;">
                                            TOTAL ORDER: <span id="totalOrderPending" class="fw-bold text-dark">0</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="col-12 col-md-4 text-center pb-2">
                                    <button type="button" class="btn btn-lg btn-outline-dark custom-btn-width" disabled>
                                        <span style="font-size: 16px;">
                                            REMAINING ORDER: <span id="remainingOrderPending" class="fw-bold text-dark">0</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="col-12 col-md-4 text-center pb-2">
                                    <button type="button" onclick="distribute()" class="btn btn-lg btn-teal custom-btn-width">
                                        <i class="ri-share-forward-fill"></i>
                                        <span style="font-size: 16px;">
                                            Redistribute
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="row m-0 w-100 pt-3" id="submit-proceed-bucket-modal-body">
                            </div>
                            <input type="hidden" id="order_ids" name="order_ids">
                        </div>
                        {{-- Button --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="submit-proceed-bucket-button" onclick="submitAddToBucket()" type="button"
                            class="btn btn-primary text-white">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- End Bucket Category Modal --}}

@include("orders.multiple_cn_modal")
<x-slot name="script">
    <script>
        let checkedOrder = [];
        let categoryBucket;
        let arrange_shipment_platform = {
            'shopee' : 'Shopee',
            'tiktok' : 'TikTok'
        };

        let generate_cn_couriers = {
            'dhl-ecommerce' : 'DHL Ecommerce',
            'posmalaysia' : 'POS Malaysia',
            'shopee' : 'Shopee',
            'tiktok' : 'TikTok'
        };

        document.querySelector('#filter-order').onclick = function() {
            document.querySelector('#order-table').style.display = 'block';
        }

        claim_type_click(document.querySelector('#claim_type'));

        @if (in_array(ACTION_ADD_TO_BUCKET, $actions))
            document.querySelector('#add-to-bucket-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));

                let modalOne = new bootstrap.Modal(document.getElementById('modal-open-bucket-category'), {
                    keyboard: false
                });

                inputElements.forEach(input => {
                    if (input.checked) {
                        checkedOrder.push(input.value);
                    }
                });

                document.querySelector('#category-id').value = "";
                document.querySelector('#submit-bucket-category').disabled = true;
                if (checkedOrder.length == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        text: "All order in the list will be added to bucket.",
                        icon: 'warning',
                        confirmButtonText: 'Yes, Select overall',
                        confirmButtonColor: '#3085d6',
                        cancelButtonText: 'Cancel',
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            modalOne.show();
                        }
                    })
                    return;
                }else{
                    modalOne.show();
                }
            }
        @endif

        const removeOldTicked = () =>
        {
            checkedOrder = []; //reset array
        }

        const selectCategory = (el) =>
        {
            let val = el.value;

            if(val != ""){
                document.querySelector('#submit-bucket-category').disabled = false;
            }else{
                document.querySelector('#submit-bucket-category').disabled = true;
            }

            categoryBucket = val;

        }

        const proceedModal = async () =>
        {
            let modal = new bootstrap.Modal(document.getElementById('modal-proceed-bucket'), {
                keyboard: false
            });

            let html = '';

            let formData = new FormData(document.querySelector('#search-form'));
            formData.append('category_id', categoryBucket);
            formData.append('order_ids', checkedOrder);
            let response = await axios.post('/api/buckets/get-bucket-by-category',formData).then(res => {

                let totalOrder = res.data.totalOrder;
                document.querySelector('#totalOrderPending').innerHTML = totalOrder;
                document.querySelector('#order_ids').value = res.data.order_ids;
                // let remainingOrder = res.data.remainingOrder;
                // document.querySelector('#remainingOrderPending').innerHTML = remainingOrder;


                for (let index = 0; index < res.data.categoryBucket.length; index++) {
                    const categoryBucket = res.data.categoryBucket[index];
                    html += `
                    <div class="col-12 col-md-6 text-center pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="d-flex align-items-center">
                                    <input onclick="tickBucket(this)" type="checkbox" class="form-check-input" id="check-all-${categoryBucket.bucket_id}" checked>
                                    <span class="d-inline-block mx-2"><i class="bi bi-basket"></i></span>
                                    <label class="form-check-label mx-2" for="check-all">
                                        ${categoryBucket.bucket.name}:
                                    </label>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex">
                                <i onclick="minusNumber(this)" class="bi bi-dash-circle-fill text-primary fs-5"></i>
                                <input name="bucket_id[${categoryBucket.bucket_id}]" oninput="constantNumber(this)" type="number" id="input-number-${categoryBucket.bucket_id}" class="form-control form-control-sm mx-2" style="width: 5rem; text-align: center;" value="0">
                                <i onclick="plusNumber(this)" class="bi bi-plus-circle-fill text-primary fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    `
                }

                document.querySelector('#submit-proceed-bucket-modal-body').innerHTML = html;

                modal.show();
                distribute();

            }).catch(err => {
                console.log(err);
            });

        }

        const constantNumber = (el) =>
        {
            let remainingOrder = parseInt(document.querySelector('#remainingOrderPending').innerHTML);
            let newVal = el.value;
            let oldValue = el.getAttribute('data-value');

            //only on ticked bucket
            let tickedBucket = el.parentElement.parentElement.parentElement.querySelector('input[type="checkbox"]:checked');
            if(tickedBucket == null)
            {
                return; //stop function
            }

            if(newVal > remainingOrder)
            {
                console.log('more');
                el.value = newVal;
                let currentNewVal = oldValue - newVal + remainingOrder;
                document.querySelector('#remainingOrderPending').innerHTML = currentNewVal; //set remaining order
                el.setAttribute('data-value', newVal); //set data attribute
                validationInputBucket(el);
            }
            else if(newVal < remainingOrder)
            {
                console.log('less');
                el.value = newVal;
                let currentNewVal = oldValue - newVal + remainingOrder;
                document.querySelector('#remainingOrderPending').innerHTML = currentNewVal; //set remaining order
                el.setAttribute('data-value', newVal); //set data attribute
                validationInputBucket(el);
            }
            else
            {
                console.log('equal');
                el.value = newVal;
                let currentNewVal = oldValue - newVal + remainingOrder;
                document.querySelector('#remainingOrderPending').innerHTML = currentNewVal; //set remaining order
                el.setAttribute('data-value', newVal); //set data attribute
                validationInputBucket(el);
            }
        }

        const minusNumber = (el) =>
        {
            let remainingOrder = document.querySelector('#remainingOrderPending').innerHTML;
            let val = el.parentElement.querySelector('input[type="number"]').value;

            if(val == 0)
            {
                el.parentElement.querySelector('input[type="number"]').value = 0;
            }
            else
            {
                el.parentElement.querySelector('input[type="number"]').value = parseInt(val) - 1; //set value
                document.querySelector('#remainingOrderPending').innerHTML = parseInt(remainingOrder) + 1; //set remaining order
                el.parentElement.querySelector('input[type="number"]').setAttribute('data-value', parseInt(val) - 1); //set data attribute
            }
        }

        const plusNumber = (el) =>
        {
            let remainingOrder = document.querySelector('#remainingOrderPending').innerHTML;
            let val = el.parentElement.querySelector('input[type="number"]').value;

            if(remainingOrder != 0)
            {
                document.querySelector('#remainingOrderPending').innerHTML = parseInt(remainingOrder) - 1; //set remaining order
                el.parentElement.querySelector('input[type="number"]').value = parseInt(val) + 1; //set value
                el.parentElement.querySelector('input[type="number"]').setAttribute('data-value', parseInt(val) + 1); //set data attribute
            }
            else
            {
                return; //stop function
            }
        }

        const distribute = () =>
        {
            let totalOrder = document.querySelector('#totalOrderPending').innerHTML;
            let bucketModal = document.querySelector('#submit-proceed-bucket-modal-body');

            let buckets = bucketModal.querySelectorAll('input[type="number"]'); //all bucket
            let bucketsActive = bucketModal.querySelectorAll('input[type="checkbox"]:checked'); //ticked bucket

            let divide = Math.floor(totalOrder / bucketsActive.length); //take only ticked bucket
            let remainder = totalOrder % bucketsActive.length; //take only ticked bucket

            document.querySelector('#remainingOrderPending').innerHTML = remainder; //set remaining order

            // set value for each bucket
            buckets.forEach(bucket => {
                if(bucket.parentElement.parentElement.parentElement.querySelector('input[type="checkbox"]').checked)
                {
                    bucket.value = divide; //divide value equally
                    bucket.setAttribute('data-value', divide); //set data attribute
                }
            });

        }

        const validationInputBucket = (el) =>
        {
            let remainingOrder = parseInt(document.querySelector('#remainingOrderPending').innerHTML);
            if(remainingOrder < 0)
            {
                el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').style.borderColor = 'red'; //set border color red
                document.querySelector('#submit-proceed-bucket-button').disabled = true; //disable submit button
            }
            else
            {
                el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').style.borderColor = '#ced4da'; // reset border color
                document.querySelector('#submit-proceed-bucket-button').disabled = false; //enable submit button
            }
        }

        const tickBucket = (el) =>
        {
            if(!el.checked)
            {
                //add old value to remaining order
                let remainingOrder = document.querySelector('#remainingOrderPending').innerHTML;
                document.querySelector('#remainingOrderPending').innerHTML = parseInt(remainingOrder) + parseInt(el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').value);
                //reset value after untick
                el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').value = 0;
                el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').setAttribute('data-value', 0);
            }
            else
            {
                //remove old value from remaining order
                let remainingOrder = document.querySelector('#remainingOrderPending').innerHTML;
                document.querySelector('#remainingOrderPending').innerHTML = parseInt(remainingOrder) - parseInt(el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').value);

                let newVal = el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').value;
                //set attribute value
                el.parentElement.parentElement.parentElement.querySelector('input[type="number"]').setAttribute('data-value', newVal);
            }
            validationInputBucket(el);
        }

        const submitAddToBucket = async() =>
        {
            Swal.fire({
                title: 'Please wait!',
                html: 'Adding to bucket...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });

            let form = document.querySelector('#submit-proceed-bucket');
            let formData = new FormData(form);

            let response = await axios.post('/api/buckets/add-to-bucket', formData).then(res => {
                Swal.fire({
                    title: 'Success!',
                    html: res.data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            }).catch(err => {
                Swal.fire({
                    title: 'Error!',
                    html: err.response.data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            });
        }

        // generate shipping label
        @if (in_array(ACTION_GENERATE_CN, $actions))
            document.querySelector('#generate-cn-btn').onclick = async function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;
                // sweet alert
                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        text: "Please select at least one order to generate shipping label.",
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    return;
                }

            //get checked order
            let checkedOrder = [];
            inputElements.forEach(input => {
                if (input.checked) {
                    checkedOrder.push(input.value);
                }
            });


            const res = await axios.post('/api/shippings/check-multiple-parcels', {
                        order_ids: checkedOrder,
            })

            if(res.data != null && res.data.multiple_parcels){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This order have multiple parcels. Please generate CN manually.',
                    confirmButtonText: `OK`,
                })
                return;
            }


            //sweetalert courier options
            Swal.fire({
                title: 'Please select courier!',
                html: `
                <div class="swal2-actins" style="display: flex; flex-direction: column; gap: 10px;">
                    <div class="swal2-loader"></div>
                    ${Object.keys(generate_cn_couriers).map((key) => {
                        return `<button type="button" class="swal2-custom swal2-${key} swal2-styled" style="display: inline-block;"
                            aria-label="" onclick="conformationDownloadCN('${key}', '${checkedValue}')" >
                            ${generate_cn_couriers[key]}
                            </button>`;
                    }).join('')}
                    </div>
                </div>
                `,
                showCancelButton: true,
                showConfirmButton: false,

            })

        }

        const conformationDownloadCN = (type,checkedValue) => {

            Swal.fire({
                title: `Are you sure to generate ${generate_cn_couriers[type]} shipping label?`,
                html: `You are about to generate shipping label for ${checkedValue} order(s).`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!',
                footer: '<small>Note: Order with existing Consignment Note will be ignored.</small>',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Please select courier!',

                    })
                    generateCN(type);
                }
            })
        }
        @endif

        @if (in_array(ACTION_APPROVE_AS_SHIPPED, $actions))
            @can('order.approve_for_shipping')
                document.querySelector('#mark-as-shipped-btn').onclick = function() {
                    const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                    let checkedValue = inputElements.filter(chk => chk.checked).length;
                    // sweet alert
                    if (checkedValue == 0) {
                        Swal.fire({
                            title: 'No order selected!',
                            text: "Please select at least one order to approve as shipped.",
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        })
                        return;
                    }

                    let checkedOrder = [];
                    inputElements.forEach(input => {
                        if (input.checked) {
                            checkedOrder.push(input.value);
                        }
                    });

                    //confirmation to generate cn
                    Swal.fire({
                        title: 'Are you sure to approve as shipped?',
                        html: `You are about to approve ${checkedValue} order(s) as shipped.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, approve it!',
                        footer: '<small>Please check orders from Shopee only.</small>',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            approveAsShipped(checkedOrder);
                        }
                    })
                }
            @endcan
        @endif

        // download cn
        @if (in_array(ACTION_DOWNLOAD_CN, $actions))
            document.querySelector('#download-cn-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;
                // sweet alert
                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        text: "Please select at least one order to download shipping label.",
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    return;
                }
                let checkedOrder = [];
                inputElements.forEach(input => {
                    if (input.checked) {
                        checkedOrder.push(input.value);
                    }
                });
                //confirmation to generate cn
                Swal.fire({
                    title: 'Are you sure to download shipping label?',
                    html: `You are about to download shipping label for ${checkedValue} order(s).`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, download it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        download_cn(checkedOrder);
                    }
                })
            }
        @endif

        // download csv
        @if (in_array(ACTION_DOWNLOAD_ORDER, $actions))
            document.querySelector('#download-order-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;

                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        html: `<div>Are you sure to download {{ isset($orders) ? $orders->total() : 0 }} order(s).</div>
                            <div class="text-danger"><small>Note: This will take a while to process.</small></div>`,
                        icon: 'warning',
                        confirmButtonText: 'Download',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let checkedOrder = [];
                            inputElements.forEach(input => {
                                if (input.checked) {
                                    checkedOrder.push(input.value);
                                }
                            });
                            download_csv(checkedOrder);
                        }
                    })
                } else {
                    let checkedOrder = [];
                    inputElements.forEach(input => {
                        if (input.checked) {
                            checkedOrder.push(input.value);
                        }
                    });
                    download_csv(checkedOrder);
                }
            }
        @endif

        document.querySelectorAll('.add-shipping-number').forEach(btn => {
            btn.onclick = function() {

                let order_id = btn.dataset.orderid;
                let couriercode = btn.dataset.couriercode;
                console.log(order_id)
                document.querySelector('#order-id').value = order_id;
                document.querySelector(`#input-select-courier option[value="${couriercode}"]`).setAttribute(
                    'selected', 'selected');
            }
        });

        @if (in_array(ACTION_GENERATE_PICKING, $actions))
            document.querySelector('#generate-picking-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;
                let bucket_id = {{ $_GET['bucket_id'] }};
                // sweet alert
                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        text: "Please select at least one order to generate picking list.",
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                } else {
                    //cnfirmation to generate picking list
                    Swal.fire({
                        title: 'Are you sure to generate picking list separately?',
                        html: `You are about to generate picking list for ${checkedValue} order(s).`,
                        footer: '<small>Note: Shipment Note will be generated separately.</small>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, generate it!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let checkedOrder = [];
                            inputElements.forEach(input => {
                                if (input.checked) {
                                    checkedOrder.push(input.value);
                                }
                            });
                            axios.post(`{{ route('buckets.generate_pl') }}`, {
                                    order_ids: checkedOrder,
                                    bucket_id: bucket_id
                                })
                                .then(function(response) {
                                    window.location.href = '/bucket-batches/download_pl/' + response.data
                                        .batch_id;
                                    // reload when click ok
                                    Swal.fire({
                                        title: 'Success',
                                        text: 'Picking list generated successfully.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.querySelectorAll('.check-order').forEach(
                                                input => {
                                                    input.checked = false;
                                                })
                                            location.reload();
                                        }
                                    })
                                })
                                .catch(function(error) {
                                    if (error.response) {
                                        Swal.fire('Error', error.response.data.message, 'error')
                                    }
                                })
                        }
                    })
                }
            }
        @endif

        @if (in_array(ACTION_ARRANGE_SHIPMENT, $actions))
            document.querySelector('#arrange-shipment-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;

                //check if checked order is empty return and remove Swal.showLoading()
                if(checkedValue == 0){
                    Swal.fire({
                        title: 'Error!',
                        text: "Please select at least one order to arrange shipment.",
                        icon: 'error',
                        confirmButtonText: 'OK'

                    })
                    return;
                }

                //get checked order
                let checkedOrder = [];
                inputElements.forEach(input => {
                    if (input.checked) {
                        checkedOrder.push(input.value);
                    }
                });

                //sweetalert platform options
                Swal.fire({
                    title: 'Please select platform!',
                    html: `
                    <div class="swal2-actins" style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="swal2-loader"></div>
                        ${Object.keys(arrange_shipment_platform).map((key) => {
                            return `<button type="button" class="swal2-custom swal2-${key} swal2-styled" style="display: inline-block;"
                                aria-label="" onclick="confirmationArrange('${key}', '${checkedOrder}')" >
                                ${arrange_shipment_platform[key]}
                                </button>`;
                        }).join('')}
                        </div>
                    </div>
                    `,
                    showCancelButton: true,
                    showConfirmButton: false,

                });
            }
        @endif


        async function generateCN(type) {
            //show loading modal
            Swal.fire({
                title: 'Generating shipping label...',
                html: 'Please wait while we are generating shipping label for your order(s).',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
            });
            const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
            let checkedValue = inputElements.filter(chk => chk.checked).length;
            //get checked order
            let checkedOrder = [];
            inputElements.forEach(input => {
                if (input.checked) {
                    checkedOrder.push(input.value);
                }
            });

            let sameCompany = await check_same_company(checkedOrder);

            if(type == 'posmalaysia'){
               requestCNPOS(type, checkedOrder);
               return;
            }

            requestCN(type, checkedOrder);

        } //end of generateCN

        // request CN
        const requestCN = (type, checkedOrder) => {
            axios.post('/api/request-cn', {
                order_ids: checkedOrder,
                type: type,
            })
            .then(function(response) {

                let text = "Shipping label generated."
                if (response.data == 0) {
                    Swal.fire({
                        title: 'Error!',
                        text: "Selected order already has CN.",
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    return;
                }

                if (!response.data.success)
                {
                    // let message = response.data.error ?? response.data.message;
                    let message = JSON.stringify(response.data);
                    Swal.fire({
                        title: 'Error!',
                        html: `${message}` ?? "Fail to generate CN",
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    return;
                }

                // handle success, close or download
                if(response.data != null ){
                    if(response.data.error != null){
                        text = "Shipping label generated.However has "+response.data.error;
                    }

                    if(response.data.all_fail){
                        if(typeof response.data.all_fail == "boolean"){
                            Swal.fire({
                                title: 'Error!',
                                text: "Fail to generate CN",
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: "Fail to generate CN "+response.data.all_fail,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }

                        return;
                    }
                }

                Swal.fire({
                    title: 'Success!',
                    text: text,
                    icon: 'success',
                    confirmButtonText: 'Download',
                    showCancelButton: true,
                    cancelButtonText: 'Ok',
                }).then((result) => {
                    if (result.isConfirmed) {
                        download_cn(checkedOrder)
                    } else {
                        location.reload();
                    }
                });
            })
            .catch(function(error) {
                Swal.fire({
                    title: 'Error!',
                    html: `Fail to generate CN, Please contact admin`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            })
        }

        async function requestCNPOS(type, checkedOrder) {
            // sweet alert
            Swal.fire({
                title: 'Generating shipping label...',
                html: `<div id="generateConnoteModalPOS">
                        <div id="generateConnotePOS">
                            Generate Consignment Notes
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="generatePl9POS" class="d-none">
                            Generate PL9
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="generateCnPOS" class="d-none">
                            Generate Consignment Notes
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="mergeCnPOS" class="d-none">
                            Merging Consignment Notes
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="downloadCnPOS" class="d-none">
                            <div class="mt-3">Download Request CN Successful.</div>
                        </div>
                    </div>`,
                allowOutsideClick: false,
                showConfirmButton: false,
            });


            function generateConnote(item) {
                return new Promise((resolve) => {
                    axios.post('/api/pos/generate-connote', {
                        order_ids: item,
                        type: type,
                    }).then(function(response){
                        if(response.data.status && response.data.status == 'success'){
                            document.querySelector('#generateConnotePOS').innerHTML = `
                                Generate Consignment Notes
                                <i class="bi bi-check-circle-fill text-success"></i>
                            `;
                            document.querySelector('#generatePl9POS').classList.remove('d-none');
                        }
                        else {
                            document.querySelector('#generateConnotePOS').innerHTML = `
                                Generate Consignment Notes
                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                <br>
                                <small class="text-danger">
                                ${response.data.message.join('<br>')}
                                </small>
                            `;
                            document.querySelector('#generatePl9POS').classList.remove('d-none');
                            // Swal.fire({
                            //     title: 'Error!',
                            //     html: response.data.message.join('<br>'),
                            //     icon: 'error',
                            //     confirmButtonText: 'OK'
                            // });

                        }
                        resolve();
                    }).catch(function(error) {
                        Swal.fire({
                            title: 'Error!',
                            html: `Fail to generate CN, Please contact admin`,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        resolve();
                    });
                });
            }

            function generatePl9(item) {
                return new Promise((resolve) => {
                    axios.post('/api/pos/generate-pl9', {
                        order_ids: item,
                        type: type,
                    }).then(function(response){
                        if(response.data.status && response.data.status == 'success'){
                            document.querySelector('#generatePl9POS').innerHTML = `
                                Generate PL9
                                <i class="bi bi-check-circle-fill text-success"></i>
                            `;
                            document.querySelector('#generateCnPOS').classList.remove('d-none');
                        }
                        else{
                            document.querySelector('#generatePl9POS').innerHTML = `
                                Generate PL9
                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                <br>
                                <small class="text-danger">
                                </small>
                            `;
                            document.querySelector('#generateCnPOS').classList.remove('d-none');
                        }
                        resolve();
                    });
                });
            }

            function generateCn(item) {
                return new Promise((resolve) => {
                    axios.post('/api/pos/download-connote', {
                        order_ids: item,
                        type: type,
                    }).then(function(response){
                        if(response.data.status && response.data.status == 'success'){
                            document.querySelector('#generateCnPOS').innerHTML = `
                                Generate Consignment Notes
                                <i class="bi bi-check-circle-fill text-success"></i>
                            `;
                            document.querySelector('#mergeCnPOS').classList.remove('d-none');
                        }
                        if(response.data.status && response.data.status == 'error'){
                            document.querySelector('#generateCnPOS').innerHTML = `
                                Generate Consignment Notes
                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                <br>
                                <small class="text-danger">
                                ${response.data.message.join('<br>')}
                                </small>
                            `;
                            document.querySelector('#mergeCnPOS').classList.remove('d-none');
                        }
                        resolve();
                    });
                });
            }

            function downloadCn(item) {
                return new Promise((resolve) => {
                    axios({
                    url: '/api/download-consignment-note',
                        method: 'POST',
                        responseType: 'json', // important
                        data: {
                            order_ids: item,
                        }
                    })
                    .then(function(res) {
                        if(!res.data.status && res.data.download_url == false){
                            Swal.fire({
                                title: 'Error!',
                                html: res.data.error ?? "Fail to generate CN",
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                            return;
                        }

                        const fileName = String(res.data.download_url).split("/").pop();
                        let a = document.createElement('a');
                        a.download = fileName;
                        a.target = '_blank';
                        a.download = fileName;
                        a.href = res.data.download_url;
                        a.click();
                        document.querySelector('#mergeCnPOS').innerHTML = `
                                Merging Consignment Notes
                                <i class="bi bi-check-circle-fill text-success"></i>
                            `;
                            document.querySelector('#downloadCnPOS').classList.remove('d-none');
                            document.querySelector('#downloadCnPOS').innerHTML = `
                            <div class="mt-3">Download Request CN Successful.</div>
                            <div>Click <a href="${a.href}" target="_blank" download="${fileName}">here</a> if CN not downloaded.</div>
                        `;
                        resolve(res.data);
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                        Swal.fire({
                            title: 'Success!',
                            html: `Failed to generate pdf`,
                            allowOutsideClick: false,
                            icon: 'error',
                        });
                        resolve();
                    });
                });
            }

            // generate connote
            await generateConnote(checkedOrder);
            // generate pl9
            await generatePl9(checkedOrder);
            // generate cn
            await generateCn(checkedOrder);
            // download cn
            await downloadCn(checkedOrder)
            .then(function(download){
                Swal.update({
                    title: 'Success!',
                    html: document.querySelector('#generateConnoteModalPOS').innerHTML,
                    footer: '<small class="text-danger">Please enable popup if required</small>',
                    allowOutsideClick: false,
                    icon: 'success',
                    showConfirmButton: true,
                })
                // if confirm button clicked reload
                Swal.getConfirmButton().onclick = function(){
                    location.reload();
                }
            })

        }

        function approveAsShipped(checkedOrders) {
            axios({
                    url: '/api/orders/approve-for-shipping',
                    method: 'POST',
                    responseType: 'json', // important
                    data: {
                        order_ids: checkedOrders,
                        user_id: {{ Auth::user()->id }}
                    }
                })
                .then(function(res) {
                    // handle success, close or download
                    if (res.data.success == 'ok') {
                        Swal.fire({
                            title: 'Success!',
                            text: "Order(s) approved as shipped.",
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.querySelectorAll('.check-order').forEach(
                                    input => {
                                        input.checked = false;
                                    })
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: "Error occured while approving order(s) as shipped.",
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                    }
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }

        function download_cn(checkedOrder) {
            axios({
                    url: '/api/download-consignment-note',
                    method: 'POST',
                    responseType: 'json', // important
                    data: {
                        order_ids: checkedOrder,
                    }
                })
                .then(function(res) {
                    if(!res.data.status && res.data.download_url == null){
                        Swal.fire({
                            title: 'Error!',
                            html: res.data.error ?? "Fail to download CN",
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                        return;
                    }

                    const fileName = String(res.data.download_url).split("/").pop();
                    let a = document.createElement('a');
                    a.download = fileName;
                    a.target = '_blank';
                    a.download = fileName;
                    a.href = res.data.download_url;
                    a.click();
                    // handle success, close or download
                    Swal.fire({
                        title: 'Success!',
                        html: `<div>Download Request CN Successful.</div>
                        <div>Click <a href="${res.data.download_url}" target="_blank" download="${fileName}">here</a> if CN not downloaded.</div>`,
                        footer: '<small class="text-danger">Please enable popup if required</small>',
                        allowOutsideClick: false,
                        icon: 'success',
                    });
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                    Swal.fire({
                        title: 'Success!',
                        html: `Failed to generate pdf`,
                        allowOutsideClick: false,
                        icon: 'error',
                    });
                })
                .then(function() {
                    // always executed
                });
        }

        function reject_order(orderId) {
            console.log(213);
            Swal.fire({
                title: 'Are you sure to reject this order?',
                html: `You are about to reject order ${orderId}.`,
                icon: 'warning',
                input: 'select',
                inputOptions: {
                    1: 'Phone',
                    2: 'Address',
                    3: 'Product(Quantity)',
                    4: 'Product(Others)',
                    5: 'Change Purchase Type'
                },
                inputPlaceholder: 'Select a reason',
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                    if (value != '') {
                        reject_reason = value;
                        resolve()
                    } else {
                        resolve('You need to select one of the option')
                    }
                    })
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const { value: reason } = await Swal.fire({
                        input: 'textarea',
                        inputLabel: 'Reject Reason',
                        showCancelButton: true,
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to write something!'
                            }
                        }
                    })

                    if (reason) {
                        axios.post('/api/orders/reject', {
                            order_id: orderId,
                            reason,
                            reject_reason
                        })
                            .then(function (response) {
                                // handle success, close or download
                                if (response.status == 200) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: "Order rejected.",
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    })
                                        .then((result) => {
                                            if (result.isConfirmed) {
                                                location.reload();
                                            }
                                        })
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: "Something went wrong.",
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    })
                                    return;
                                }
                            })
                            .catch(function (error) {
                                // handle error
                                console.log(error);
                            })
                    }
                }

            })
        }

        async function check_same_company(checkedOrder) {
            let result = await axios.post('/api/check-cn-company', {
                order_ids: checkedOrder,
            });
            return result.data;
        }

        function get_current_date_time() {
            var today = new Date();
            var currentMonth = ('0' + (today.getMonth() + 1)).substr(-2);
            var currentDate = ('0' + today.getDate()).substr(-2);
            var date = today.getFullYear() + currentMonth + currentDate;
            var currentHours = ('0' + today.getHours()).substr(-2);
            var currentMins = ('0' + today.getMinutes()).substr(-2);
            var currentSecs = ('0' + today.getSeconds()).substr(-2);
            var time = currentHours + currentMins + currentSecs;
            var dateTime = date + '_' + time;
            return dateTime;
        }

        function download_csv(checkedOrder) {
            // const params = `{!! $_SERVER['QUERY_STRING'] ?? '' !!}`;
            // const param_obj = queryStringToJSON(params);
            if (checkedOrder.length == 0) {
                checkedOrder = @json($order_ids);
            }
            axios.post('/api/download-order-csv', {
                    order_ids: checkedOrder,
                })
                .then(function(response) {
                    // handle success, close or download
                    if(response != null && response.data != null){
                        let a = document.createElement('a');
                        a.download = response.data.file_name;
                        a.target = '_blank';
                        a.href = window.location.origin + "/storage/"+response.data.file_name;
                        a.click();
                    }
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
        }

        document.querySelectorAll('.tomsel').forEach((el) => {
            let settings = {
                plugins: {
                    remove_button: {
                        title: 'Remove this item',
                    }
                },
                hidePlaceholder: true,
            };
            new TomSelect(el, settings);
        });

        document.querySelectorAll(".phantom").forEach(function(el) {
            el.addEventListener("click", function() {
                let tracking = el.getAttribute('data-tracking');
                // fetch(`https://phantom.emzi.com.my/showItemTracking?tracking_id=${tracking}`)
                fetch(`http://127.0.0.1:8001/showItemTracking?tracking_id=7022066391821682`)
                    .then(response => response.text())
                    .then(content => {
                        // Update the content of the target element
                        document.querySelector("#phantomLoad").innerHTML = content;
                    });

            });
        });

        document.querySelectorAll('.split-parcels').forEach(function(q) {
            q.addEventListener("click", function() {
                let order_id = q.getAttribute('data-orderId');
                axios.post('/api/orders/split-parcels', {
                        order_id: order_id,
                    })
                    .then(function(response) {
                        // handle success, close or download
                        if (response.data.success == 'ok') {
                            let count = response.data.count;
                            document.querySelector('#split-order-id').value = order_id;
                            document.querySelector('#split-parcel-count').value = count;
                            document.querySelector('#split-parcel-weight').value = (response.data
                                .weight / 1000 / count).toFixed(3);
                            document.querySelector('#split-parcel-weight-total').value = (response.data
                                .weight / 1000).toFixed(3);
                        }
                    })
            })
        })
        document.querySelector('#split-parcel-count').addEventListener('change', function() {
            let weight = document.querySelector('#split-parcel-weight-total').value;
            let count = document.querySelector('#split-parcel-count').value;
            document.querySelector('#split-parcel-weight').value = (weight / count).toFixed(3);
        })

        // click to copy order id, double click to copy order number
        document.querySelectorAll('.order-num').forEach(function(el) {

            el.addEventListener("dblclick", function() {
                let sales_id = el.getAttribute('data-sales-id');
                let order_num = el.getAttribute('data-order-num');
                let inside_elem = el.innerHTML;
                navigator.clipboard.writeText(sales_id);
                //change the text of the element
                el.innerHTML = "Copied!";
                //change the text back after a certain time
                setTimeout(function() {
                    el.innerHTML = inside_elem;
                }, 1000);

            });
        });

        // cancel shipping
        document.querySelectorAll('.cancel-shipping').forEach(function(el) {
            el.addEventListener("click", function() {
                let order_id = el.getAttribute('data-id');
                let shipping_auto_generated = el.getAttribute('data-shipping-auto-generated');

                    // confirm cancel shipping alert
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to cancel shipping for this order? Tracking number will be removed and order will go to pending.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, cancel it!',
                        footer: 'Auto generated shipping will be cancelled automatically.'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(`{{ route('shipping.cancel_shipment') }}`, {
                                    order_id: order_id,
                                })
                                .then(function(response) {
                                    // handle success, close or download
                                    if (response.data.success == 'ok') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: "Shipping cancelled.",
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        })
                                            .then((result) => {
                                                if (result.isConfirmed) {
                                                    location.reload();
                                                }
                                            })
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: "Something went wrong.",
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        })
                                        return;
                                    }
                                })
                                .catch(function(error) {
                                    // handle error
                                    console.log(error);
                                })

                        }
                    })


            })
        })
        // check and trim if * more than 3
        document.querySelectorAll(".customer-address").forEach(function(el) {
            let address = el.innerHTML;
            let address_arr = address.split("*");
            if (address_arr.length >= 8) {
                let new_address = address_arr.slice(0, 8).join("*");
                el.innerHTML = new_address;
            }
        });

        function return_modal(orderId, items){
            document.querySelector('#good-cond').click();
            // open returnModal modal pure js
            let modal = document.getElementById('returnModal');
            let modalBody = document.querySelector('#returnModal .modal-body');
            let modalTableBodyGood = document.querySelector('#good-cond-content table tbody');
            let modalTableBodyBad = document.querySelector('#bad-cond-content table tbody');
            let body_good = '';
            let body_bad = '';

            document.querySelector('#returnModalOrderId').value = orderId;

            for(let i = 0; i < items.length; i++){
                body_good += `<tr>
                        <td class="px-2">${items[i].product.name}</td>
                        <td class="text-center">${items[i].quantity}</td>
                    </tr>`;

                body_bad += `<tr>
                        <td>${items[i].product.name}</td>
                        <td class="text-center">${items[i].quantity}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <input type="number" name="defect_unit[${items[i].id}]" class="form-control form-control-sm text-center" size="4" value="0" required>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 mb-1 batch-field">
                                <input type="text" name="batch_no[${items[i].id}][]" class="form-control form-control-sm batch-no" required>
                                <button class="btn btn-danger p-0 px-1" type="button" disabled><i class="bi bi-trash"></i></button>
                            </div>
                            <div>
                                <a href="javascript:void(0)" onclick="add_batch_no(this)">
                                    <i class="bi bi-plus-circle-fill text-success"></i>
                                    Add batch No.
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <input type="file" id="uploadPhoto${items[i].id}" name="upload_photo[${items[i].id}][]" class="form-control form-control-sm d-none" accept="image/*" onchange="read_photo(this)" multiple />
                            <div id="photo-${items[i].id}-preview"></div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm text-success" type="button" onclick="document.querySelector('#uploadPhoto${items[i].id}').click()">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    Upload</button>
                            </div>
                        </td>
                    </tr>`;

            }
            modalTableBodyGood.innerHTML = body_good;
            modalTableBodyBad.innerHTML = body_bad;

            // open modal
            let myModal = new bootstrap.Modal(modal, {
                keyboard: false
            });
            myModal.show();
        }

        function add_batch_no(el){
            // clone previous element
            let prev = el.parentNode.previousElementSibling;
            let clone = prev.cloneNode(true);
            // clear value
            clone.querySelector('.batch-no').value = '';
            clone.querySelector('button').disabled = false;
            // append to parent
            el.parentNode.parentNode.insertBefore(clone, el.parentNode);

            // add event listener to delete button
            clone.querySelector('.btn-danger').addEventListener('click', function(){
                clone.remove();
            });
        }

        function read_photo(el){
            console.log(el.files)
            let id = el.id.split('uploadPhoto')[1];
            let photo = document.querySelector(`#photo-${id}-preview`);
            photo.innerHTML = '';


            // if(el.files && el.files[0]){
                for(let i=0;i<el.files.length;i++){

                    let reader = new FileReader();
                    reader.onload = function(e){

                        photo.insertAdjacentHTML('beforeend', `<img src='${e.target.result}' class="img-fluid img-thumbnail img-50" />`);
                    }
                    reader.readAsDataURL(el.files[i]);
                }
            // }

        }

        function parcel_cond(cond){
            document.querySelector('#parcel_condition').value = cond;
            if(cond == 1){
                document.querySelector('#good-cond').classList.add('outline-good');
                document.querySelector('#bad-cond').classList.remove('outline-defect');
                document.querySelector('#good-cond-content').classList.remove('d-none');
                document.querySelector('#bad-cond-content').classList.add('d-none');
            }else{
                document.querySelector('#good-cond').classList.remove('outline-good');
                document.querySelector('#bad-cond').classList.add('outline-defect');
                document.querySelector('#good-cond-content').classList.add('d-none');
                document.querySelector('#bad-cond-content').classList.remove('d-none');
            }
        }

        function parcel_cond_submit(){
            let form = document.querySelector('#returnModal form');
            let formData = new FormData(form);
            formData.append('user_id', {{ Auth::user()->id }});
            //validate form
            console.log(formData);
            let error = '';
            let parcel_condition = document.querySelector('#parcel_condition').value;
            let defect_unit = document.querySelectorAll('input[name^="defect_unit"]');
            let batch_no = document.querySelectorAll('input[name^="batch_no"]');
            let upload_photo = document.querySelectorAll('input[name^="upload_photo"]');
            let defect_unit_total = 0;
            let batch_no_total = 0;
            let upload_photo_total = 0;

            if(parcel_condition == 0){
                defect_unit.forEach(el => {
                    defect_unit_total += parseInt(el.value);
                });
                batch_no.forEach(el => {
                    if(el.value != ''){
                        batch_no_total++;
                    }
                });
                upload_photo.forEach(el => {
                    if(el.files.length > 0){
                        upload_photo_total++;
                    }
                });
            }

            if(parcel_condition == 0 && defect_unit_total == 0){
                error += 'Defect unit must be more than 0.<br>';
            }
            else{
                //find non zero defect unit array index
                let defect_unit_arr = [];
                defect_unit.forEach(el => {
                    if(parseInt(el.value) > 0){
                        defect_unit_arr.push(el);
                    }
                });
                //check if batch no is empty
                defect_unit_arr.forEach(el => {
                    let index = el.name.split('[')[1].split(']')[0];
                    let batch_no_el = document.querySelector(`input[name="batch_no[${index}][]"]`);
                    if(batch_no_el.value == ''){
                        error += 'Please enter batch no for defect unit.<br>';
                    }
                });
                //check if upload photo is empty
                defect_unit_arr.forEach(el => {
                    let index = el.name.split('[')[1].split(']')[0];
                    let upload_photo_el = document.querySelector(`input[name="upload_photo[${index}][]"]`);
                    if(upload_photo_el.files.length == 0){
                        error += 'Please upload photo for defect unit.<br>';
                    }
                });

            }

            if(error != ''){
                Swal.fire({
                    title: 'Error!',
                    html: error,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                return;
            }

            axios.post('/api/claims/create', formData)
                .then(function(response) {
                    if (response.data.success == 'ok') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        })
                        .then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: "Something went wrong.",
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                        return;
                    }
                })
                .catch(function(error) {
                    Swal.fire({
                        title: 'Error!',
                        html: error.response.data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                })

        }

        function claim_type_click(el){
            let claim_type = el.value;
            let claim_from = document.querySelector('#claim_from');

            if(claim_type == 2){ // courier cost claim
                claim_from.querySelector('option[value="2"]').disabled = true;
                claim_from.querySelector('option[value="1"]').selected = true;
            }else{
                claim_from.querySelector('option[value="2"]').disabled = false;
            }
        }

        const confirmationArrange = (type,checkedValue) => {
            //change checkedValue to array

            if(typeof checkedValue == "string"){
                checkedValue = checkedValue.split(',');
            }

            Swal.fire({
                title: `Are you sure to arrange shipment for ${checkedValue.length} order(s)?`,
                html: `You are about to arrange shipment for ${checkedValue.length} order(s) on ${arrange_shipment_platform[type]}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, arrange it!',
                footer: '<small>Note: Order status will be changed to "Pending Shipment".</small>',
            }).then((result) => {
                if (result.isConfirmed) {

                    // add loading to button
                    Swal.fire({
                        title: 'Arranging shipment...',
                        html: 'Please wait while we are arranging shipment for your order(s).',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                    });

                    let response = axios.post('/api/arrange-shipment', {
                        order_ids: checkedValue,
                        platform: type,
                    })
                    .then(function(response) {
                        Swal.fire({
                            title: 'Success!',
                            html: `${response.data.message}`,
                            icon: 'success',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }).catch(function(error) {
                        // handle error
                        Swal.fire({
                            title: 'Error!',
                            html: 'Something went wrong Please contact admin',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                    })

                }
            });
        }
        const duplicateModal = (ids) => {
            ids = ids.split(',');
            let count = ids.length;
            Swal.fire({
                title: `Possible ${count-1} duplicate order(s) found`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `View`,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/orders/overall?ids=${ids}`;
                }
            })
        }

    </script>

</x-slot>
@stack("orders.multiple_cn_modal")
</x-layout>
