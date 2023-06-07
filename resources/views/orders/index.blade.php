<x-layout :title="$title">
    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- No Labels Form -->
                    <form class="row g-3" action="{{ url()->current() }}">
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
                                        <td><input type="checkbox" name="check_order[]" class="check-order"
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
                                                {{-- add shipping number modal --}}
                                                @if (Route::is('orders.processing'))
                                                   @if($order->items->sum("quantity") > MAXIMUM_QUANTITY_PER_BOX)
                                                        <button class="btn btn-warning p-0 px-1 m-1" onclick="multiple_cn({order:'{{ $order }}',ref_no:'{{ order_num_format($order) }}'})"></>
                                                            <i class="bi bi-file-earmark-ruled"></i>
                                                        </button>
                                                    @endif
                                                    {{-- @empty($order->shippings) --}}
                                                        @can('tracking.update')
                                                            <button type="button"
                                                                class="btn btn-primary p-0 px-1 add-shipping-number"
                                                                data-bs-toggle="modal" data-bs-target="#add-shipping-number-modal"
                                                                data-orderid="{{ $order->id }}"
                                                                data-couriercode={{ $order->courier->code }}>
                                                                <i class="bi bi-truck"></i>
                                                            </button>
                                                        @endcan
                                                    {{-- @endempty --}}
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
                                                @if (request('multiple_parcels') == true)
                                                    <button class="btn btn-warning p-0 px-1 split-parcels"
                                                        title="Split Parcel" data-bs-toggle="modal"
                                                        data-bs-target="#split-parcel-modal"
                                                        data-orderid="{{ $order->id }}">
                                                        <i class="bi bi-arrow-left-right"></i>
                                                    </button>
                                                @endif
                                            </div>
                                    </td>
                                    <td class="text-center">
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
                                        @if ($order->courier_id = DHL_ID && !is_digit_count($order->customer->postcode, 5))
                                            <div class="badge bg-danger text-wrap">
                                                Postcode Error
                                            </div>
                                        @endif
                                        @if (!last_two_digits_zero($order->customer->postcode))
                                            <a href="{{ route('orders.change_postcode_view') }}?sales={{ $order->sales_id }}&company={{ $order->company_id}}&current_postcode={{ $order->customer->postcode }}&redirect_to={{ urlencode(url()->full()) }}" class="badge bg-warning text-wrap text-dark">
                                                Potential DHL Postcode Error
                                            </a>
                                        @endif
                                        <div><strong>{{ $order->customer->name }}</strong></div>
                                        <div>
                                            {{ $order->customer->phone }}
                                        </div>
                                        <div>
                                            <span class="customer-address">{{ $order->customer->address }}</span>,
                                            {{ $order->customer->postcode }},
                                            {{ $order->customer->city }},
                                            {{ MY_STATES[$order->customer->state] }}
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
                                        <div class="text-danger small-text" title="Total Refund">
                                            {{ currency($order->payment_refund, true) }}</div>
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

@include("orders.multiple_cn_modal")
<x-slot name="script">
    <script>
        let start = document.querySelector('#start-date');
        let end = document.querySelector('#end-date');
        document.querySelector('#btn-check-today').onclick = function() {
            start.value = moment().format('YYYY-MM-DD');
            end.value = moment().format('YYYY-MM-DD');
        }
        document.querySelector('#btn-check-yesterday').onclick = function() {
            start.value = moment().subtract(1, 'days').format('YYYY-MM-DD');
            end.value = moment().subtract(1, 'days').format('YYYY-MM-DD');
        }
        document.querySelector('#btn-check-this-month').onclick = function() {
            start.value = moment().startOf('month').format('YYYY-MM-DD');
            end.value = moment().endOf('month').format('YYYY-MM-DD');
        }
        document.querySelector('#btn-check-last-month').onclick = function() {
            start.value = moment().subtract(1, 'months').startOf('month').format('YYYY-MM-DD');
            end.value = moment().subtract(1, 'months').endOf('month').format('YYYY-MM-DD');
        }
        document.querySelector('#btn-check-overall').onclick = function() {
            start.value = '';
            end.value = '';
        }


        document.querySelector('#filter-order').onclick = function() {
            document.querySelector('#order-table').style.display = 'block';
        }

        @if (in_array(ACTION_ADD_TO_BUCKET, $actions))
            document.querySelector('#add-to-bucket-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;

                // sweet alert
                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        text: "Please select at least one order to add to bucket.",
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    return;
                }
                //get bucket lists
                axios.get('/api/buckets')
                    .then(function(response) {
                        // handle success
                        let buckets = response.data;
                        let bucketOptions = {};
                        buckets.forEach(bucket => {
                            bucketOptions[bucket.id] = bucket.name;
                        });
                        Swal.fire({
                            title: 'Please select bucket!',
                            input: 'select',
                            inputOptions: bucketOptions,
                            inputPlaceholder: 'Select a bucket',
                            showCancelButton: true,
                            inputValidator: (value) => {
                                return new Promise((resolve) => {
                                    if (value !== '') {
                                        resolve()
                                    } else {
                                        resolve('You need to select a bucket!')
                                    }
                                })
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                //get checked order
                                let checkedOrder = [];
                                inputElements.forEach(input => {
                                    if (input.checked) {
                                        checkedOrder.push(input.value);
                                    }
                                });
                                //add to bucket
                                axios.post('/api/add-to-bucket', {
                                        bucket_id: result.value,
                                        order_ids: checkedOrder,
                                    })
                                    .then(function(response) {
                                        // handle success
                                        Swal.fire({
                                            title: 'Success!',
                                            text: "Order added to bucket.",
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.reload();
                                            }
                                        });
                                    })
                                    .catch(function(error) {
                                        // handle error
                                        console.log(error);
                                    })
                                    .then(function() {
                                        // always executed
                                    });
                            }
                        })
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })

            }
        @endif

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
                //confirmation to generate cn
                Swal.fire({
                    title: 'Are you sure to generate shipping label?',
                    html: `You are about to generate shipping label for ${checkedValue} order(s).`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, generate it!',
                    footer: '<small>Note: Order with existing Consignment Note will be ignored.</small>',
                }).then((result) => {
                    if (result.isConfirmed) {
                        generateCN();
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


        async function generateCN() {
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
            if (sameCompany != 1) {
                Swal.fire({
                    title: 'Error!',
                    text: "You have selected orders from different company.",
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                return;
            }

            axios.post('/api/request-cn', {
                    order_ids: checkedOrder,
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
                    // handle error
                    console.log(error);
                })
                .then(function() {
                    // always executed
                });

        } //end of generateCN

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
            Swal.fire({
                title: 'Are you sure to reject this order?',
                html: `You are about to reject order ${orderId}.`,
                icon: 'warning',
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
    </script>

</x-slot>
@stack("orders.multiple_cn_modal")
</x-layout>
