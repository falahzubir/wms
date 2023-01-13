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
                    <form class="row g-3">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search">
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
                            <select id="inputState" class="form-select">
                                <option selected>Date Added</option>
                                <option>Date Shipping</option>
                                <option>Date Payment Received</option>
                                <option>Date Request Shipping</option>
                                <option>Date Scan Parcel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To">
                        </div>
                        <div>
                            <span role="button">
                                <strong>Advance Filter <i class="ri-arrow-down-s-fill"></i></strong>
                            </span>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form><!-- End No Labels Form -->

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button class="btn btn-info" id="add-to-bucket-btn"><i class="bi bi-paid"></i>
                            Add to Bucket</button>
                        <button class="btn btn-warning" id="generate-cn-btn"><i class="bi bi-packing"></i>
                            Generate CN</button>
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
                            @foreach ($orders as $order)
                                <tr style="font-size: 0.8rem;">
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td><input type="checkbox" name="check_order[]" class="check-order" id="" value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-warning p-0 px-1"><i
                                                class="ri-ball-pen-line"></i></a>
                                        <a href="#" class="btn btn-danger p-0 px-1"><i
                                                class="bx bx-trash"></i></a>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <a
                                                href="#"><strong>{{ order_num_format($order) }}</strong></a>
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
                                            @switch($order->payment_type)
                                                @case(1)
                                                    <span class="badge bg-warning text-dark">COD</span>
                                                @break

                                                @case(2)
                                                    <span class="badge bg-success text-light">Paid</span>
                                                @break

                                                @case(3)
                                                    <span class="badge bg-warning text-dark">COD</span>
                                                @break

                                                @default
                                                    <span class="badge bg-success text-light">Paid</span>
                                            @endswitch
                                        </div>
                                        @isset($order->shipping)
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
                                        @endisset
                                    </td>
                                    <td>
                                        {{-- <x-order_status :status="$order->status" :bucket="$order->bucket->name" /> --}}

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
                    <!-- End Default Table Example -->
                </div>
            </div>

        </div>

    </section>

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
                        console.log(response.data);
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
                                        console.log(response.data);
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
                    .then(function() {
                        // always executed
                    });
                // Swal.fire({
                //     title: 'Please select bucket!',
                //     input: 'select',
                //     inputOptions: {
                //         '1': 'Bucket 1',
                //         '2': 'Bucket 2',
                //         '3': 'Bucket 3',
                //         '4': 'Bucket 4',
                //         '5': 'Bucket 5',
                //         '6': 'Bucket 6',
                //         '7': 'Bucket 7',
                //         '8': 'Bucket 8',
                //         '9': 'Bucket 9',
                //         '10': 'Bucket 10',
                //     },
                //     inputPlaceholder: 'Select a bucket',
                //     showCancelButton: true,
                //     inputValidator: (value) => {
                //         return new Promise((resolve) => {
                //             if (value !== '') {
                //                 resolve()
                //             } else {
                //                 resolve('You need to select a bucket!')
                //             }
                //         })
                //     }
                // }).then((result) => {
                //     if (result.isConfirmed) {
                //         Swal.fire({
                //             title: 'Added to bucket!',
                //             text: "You have added " + checkedValue + " order(s) to bucket " + result.value,
                //             icon: 'success',
                //             confirmButtonText: 'OK'
                //         })
                //     }
                // })
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
                    text: `You are about to generate shipping label for ${checkedValue} order(s).`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, generate it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generateCN();
                    }
                })
            }

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
                if(sameCompany != 1){
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
                        // handle success
                        Swal.fire({
                            title: 'Success!',
                            text: "Shipping label generated.",
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

            } //end of generateCN

            async function check_same_company(checkedOrder) {
                let result = await axios.post('/api/check-cn-company', {
                    order_ids: checkedOrder,
                });
                return result.data;
            }
        </script>
    </x-slot>

</x-layout>
