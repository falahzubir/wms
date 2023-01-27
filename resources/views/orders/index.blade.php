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
                                value="{{ old('search') }}">
                        </div>
                        <div class="col-md-12">
                            <div class="btn-group" data-toggle="buttons">
                                <input type="radio" class="btn-check" id="btn-check-today" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-today">Today</label>

                                <input type="radio" class="btn-check" id="btn-check-yesterday" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-yesterday">Yesterday</label>

                                <input type="radio" class="btn-check" id="btn-check-this-month" name="off"
                                    checked>
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
                                <option selected>Date Added</option>
                                <option>Date Shipping</option>
                                <option>Date Payment Received</option>
                                <option>Date Request Shipping</option>
                                <option>Date Scan Parcel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From" name="date_from">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To" name="date_to">
                        </div>
                        <div>
                            <span role="button">
                                <strong>Advance Filter <i class="ri-arrow-down-s-fill"></i></strong>
                            </span>
                        </div>
                        <div class="expand row">
                            {{-- <x-filter_select name="courier" label="Courier(s)" id="courier-filter" class="col-4 mt-2" />
                            <x-filter_select name="purchase_type" label="Purchase Type(s)" id="purchase-type-filter" class="col-4 mt-2" />
                            <x-filter_select name="team" label="Team(s)" id="team-filter" class="col-4 mt-2" />
                            <x-filter_select name="customer_type" label="Customer Type(s)" id="customer-type-filter" class="col-4 mt-2" />
                            <x-filter_select name="product" label="Product(s)" id="product-filter" class="col-4 mt-2" />
                            <x-filter_select name="op_model" label="Operational Model(s)" id="operational-model-filter" class="col-4 mt-2" />
                            <x-filter_select name="sales_event" label="Sales Event" id="sales-event-filter" class="col-4 mt-2" /> --}}
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form><!-- End No Labels Form -->

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        @if (in_array(ACTION_ADD_TO_BUCKET, $actions))
                            <button class="btn btn-info" id="add-to-bucket-btn"><i class="bi bi-basket"></i> Add to
                                Bucket</button>
                        @endif
                        @if (in_array(ACTION_GENERATE_CN, $actions))
                            <button class="btn btn-warning" id="generate-cn-btn"><i
                                    class="bi bi-file-earmark-ruled"></i> Generate CN</button>
                        @endif
                        @if (in_array(ACTION_UPLOAD_TRACKING_BULK, $actions))
                            <!-- Button trigger modal upload csv -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#upload-csv-modal">
                                <i class="bi bi-truck"></i> Bulk Upload Tracking
                            </button>
                        @endif
                        @if (in_array(ACTION_DOWNLOAD_CN, $actions))
                            <button class="btn btn-success" id="download-cn-btn"><i class="bi bi-cloud-download"></i>
                                Download CN</button>
                        @endif
                        @if (in_array(ACTION_DOWNLOAD_ORDER, $actions))
                            <button class="btn btn-primary" id="download-order-btn"><i class="bi bi-cloud-download"></i>
                                Download CSV</button>
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
                            @foreach ($orders as $key => $order)
                                <tr style="font-size: 0.8rem;">
                                    <th scope="row">{{ $key + $orders->firstItem() }}</th>
                                    <td><input type="checkbox" name="check_order[]" class="check-order"
                                            id="" value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-warning p-0 px-1"><i
                                                class="ri-ball-pen-line"></i></a>
                                        <a href="#" class="btn btn-danger p-0 px-1"><i
                                                class="bx bx-trash"></i></a>
                                        {{-- add shipping number modal --}}
                                        @empty($order->shipping)
                                            <button type="button" class="btn btn-primary p-0 px-1 add-shipping-number"
                                                data-bs-toggle="modal" data-bs-target="#add-shipping-number-modal"
                                                data-orderid="{{ $order->id }}">
                                                <i class="bi bi-truck"></i>
                                            </button>
                                        @endempty
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <a href="#"><strong>{{ order_num_format($order) }}</strong></a>
                                        </div>
                                        <div style="font-size: 0.8rem;" data-bs-toggle="tooltip"
                                            data-bs-placement="right" data-bs-original-title="Date Inserted">
                                            <i
                                                class="bi bi-calendar"></i>&nbsp;{{ date('d/m/Y H:i', strtotime($order->created_at)) }}
                                        </div>

                                        {{-- <div>
                                            {{ date('H:i', strtotime($order->created_at)) }}
                                        </div> --}}
                                    </td>
                                    <td>
                                        <div><strong>{{ $order->customer->name }}</strong></div>
                                        <div>
                                            {{ $order->customer->phone }}
                                        </div>
                                        <div>
                                            {{ $order->customer->address }},
                                            {{ $order->customer->postcode }},
                                            {{ $order->customer->city }},
                                            {{ $order->customer->state }}
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
                                        <div>{{ currency($order->total_price, true) }}</div>
                                        <div>
                                            @switch($order->purchase_type)
                                                @case(1)
                                                    <span class="badge bg-warning text-dark">COD</span>
                                                @break

                                                @case(2)
                                                    <span class="badge bg-success text-light">Paid</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger text-light">Error</span>
                                            @endswitch
                                        </div>
                                        @isset($order->shipping)
                                            <div onclick="linkTrack('{{ $order->shipping->tracking_number }}')">

                                                <div title="Shipment Number: {{ $order->shipping->shipment_number }}">
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $order->shipping->courier }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="">
                                                        {{ $order->shipping->tracking_number }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endisset
                                    </td>
                                    <td>

                                        <x-order_status :status="$order->status" :bucket="$order->bucket" />

                                    </td>
                                </tr>
                            @endforeach
                            {{-- <tr>
                                <td colspan="100%" class="text-center">
                                    <div class="spinner-border text-secondary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                    {{ $orders->withQueryString()->links() }}
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
                                <input type="date" class="form-control" name="shipping_date" id="shipping-date"
                                    aria-describedby="dateShipping" placeholder="date" value="{{ date('Y-m-d') }}"
                                    required>
                                <small id="dateShipping" class="form-text text-muted">Shipping Date</small>
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
                                    <option selected>Choose...</option>
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


    <x-slot name="script">
        <script>
            document.querySelector('#filter-order').onclick = function() {
                document.querySelector('#order-table').style.display = 'block';
            }

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

            // generate shipping label
            document.querySelector('#generate-cn-btn').onclick = function() {
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

            // download cn
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

            // download csv
            document.querySelector('#download-order-btn').onclick = function() {
                const inputElements = [].slice.call(document.querySelectorAll('.check-order'));
                let checkedValue = inputElements.filter(chk => chk.checked).length;

                if (checkedValue == 0) {
                    Swal.fire({
                        title: 'No order selected!',
                        html: `<div>Are you sure to download {{ isset($order) ? $order->count() : 0 }} order(s).</div>
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

            document.querySelectorAll('.add-shipping-number').forEach(btn => {
                btn.onclick = function() {

                    let order_id = btn.dataset.orderid;
                    document.querySelector('#order-id').value = order_id;
                }
            })


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
                        Swal.fire({
                            title: 'Success!',
                            text: "Shipping label generated.",
                            icon: 'success',
                            confirmButtonText: 'Download',
                            showCancelButton: true,
                            cancelButtonText: 'Ok',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                download_cn(order_ids)
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


            function download_cn(checkedOrder) {
                axios({
                        url: '/api/download-consignment-note',
                        method: 'POST',
                        responseType: 'blob', // important
                        data: {
                            order_ids: checkedOrder,
                        }
                    })
                    .then(function(response) {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        //link setattribute download and rename tu ccurent time
                        let d = new Date();
                        let cnname = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + "-" + d.getHours() + d
                            .getMinutes() + d.getSeconds();
                        link.setAttribute('download', `CN_${get_current_date_time()}.pdf`);
                        document.body.appendChild(link);
                        link.click();
                        // handle success, close or download
                        Swal.fire({
                            title: 'Success!',
                            text: "Shipment Note Downloaded.",
                            icon: 'success',
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
                const params = `{!! $_SERVER['QUERY_STRING'] !!}`;
                // const param_obj = queryStringToJSON(params);
                axios.post('/api/download-order-csv?'+params, {
                        order_ids: checkedOrder,
                    })
                    .then(function(response) {
                        // handle success, close or download
                        Swal.fire({
                            title: 'Success!',
                            text: "Order CSV Downloaded.",
                            icon: 'success',
                        });
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })
            }

            dselect(document.querySelector('.tomsel'))
        </script>
    </x-slot>

</x-layout>
