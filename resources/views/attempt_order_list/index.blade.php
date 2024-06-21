<x-layout :title="$title">
    <section class="section">
        <div class="row">
            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- Search -->
                    <form id="search-form" class="row g-3" action="{{ url()->current() }}">
                        @csrf
                        <div class="col-md-12 mb-2">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="btn-group" data-toggle="buttons">
                                <input type="radio" class="btn-check" id="btn-check-today" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-2"
                                    for="btn-check-today">Today</label>

                                <input type="radio" class="btn-check" id="btn-check-yesterday" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-2"
                                    for="btn-check-yesterday">Yesterday</label>

                                <input type="radio" class="btn-check" id="btn-check-this-month" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-2"
                                    for="btn-check-this-month">This Month</label>

                                <input type="radio" class="btn-check" id="btn-check-last-month" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-2"
                                    for="btn-check-last-month">Last Month</label>

                                <input type="radio" class="btn-check" id="btn-check-overall" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-2"
                                    for="btn-check-overall">Overall</label>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <input class="form-control bg-white" type="text" value="Date Shipping" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From" name="date_from"
                                id="start-date" value="{{ Request::get('date_from') ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                                value="{{ Request::get('date_to') ?? '' }}">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-danger" id="filter-order">Search</button>
                        </div>
                    </form>
                    <!-- End Search -->

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    {{-- Button --}}
                    <div class="card-title text-end">
                        @can('order.download')
                            <button class="btn btn-secondary" id="download-order-btn"><i class="bi bi-cloud-download"></i>
                                Download CSV</button>
                        @endcan
                    </div>

                    <!-- Table -->
                    <table class="table">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Customer Info</th>
                                <th scope="col">Product Info</th>
                                <th scope="col">Delivery Attempt Status</th>
                                <th scope="col" class="text-center">Courier</th>
                                <th scope="col">BU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($shippings->count())
                                @foreach ($shippings as $key => $shipping)
                                    <tr style="font-size: 0.8rem;">
                                        {{-- Row Numbers --}}
                                        <td scope="row" class="text-center">{{ $key + $shippings->firstItem() }}</td>

                                        {{-- Customer Info --}}
                                        <td class="text-start" style="width: 400px;">
                                            <input type="hidden" name="check_order" value="{{ $shipping->order->id }}">

                                            <div><strong>{{ $shipping->order->customer->name }}</strong></div>
                                            <div>
                                                <p class="mb-0">{{ $shipping->order->customer->phone }}</p>
                                                <p class="mb-0">{{ $shipping->order->customer->phone_2 }}</p>
                                            </div>
                                            <div>
                                                <span
                                                    class="customer-address">{{ $shipping->order->customer->address }}</span>,
                                                {{ $shipping->order->customer->postcode }},
                                                {{ $shipping->order->customer->city }},
                                                {{ MY_STATES[$shipping->order->customer->state] ?? '' }},
                                                {{ COUNTRY_ID[$shipping->order->customer->country] }}
                                            </div>
                                        </td>

                                        {{-- Product Info --}}
                                        <td>
                                            {{-- Order Number --}}
                                            <div>
                                                @isset($shipping->order->sales_id)
                                                    <i class="bi bi-box-seam"></i>
                                                    <span role="button" class="order-num text-primary"
                                                        data-sales-id="{{ $shipping->order->sales_id }}"
                                                        data-order-num="{{ order_num_format($shipping->order) }}"
                                                        title="Double Click to Copy">
                                                        <strong>{{ order_num_format($shipping->order) }}</strong>
                                                    </span>
                                                @endisset
                                            </div>

                                            {{-- Tracking Number --}}
                                            <div>
                                                <i class="bi bi-truck"></i>
                                                <span class="text-success"
                                                    data-tracking="{{ $shipping->tracking_number }}">
                                                    {{ $shipping->tracking_number }}
                                                </span>
                                            </div>

                                            {{-- Products --}}
                                            <div>
                                                @foreach ($shipping->order->items as $product)
                                                    <i class="bi bi-bag"></i>
                                                    {{ $product->product->name }}<strong>[{{ $product->quantity }}]</strong>
                                                    <br>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- Delivery Attempt Status --}}
                                        <td>
                                            @foreach ($shipping->events as $event)
                                                <div>
                                                    <i class="bi bi-stopwatch-fill"></i>
                                                    {{ \Carbon\Carbon::parse($event->attempt_time)->format('d/m/Y H:i') }}
                                                </div>

                                                @php
                                                    // Check if any of the specified attempt_status values are present
                                                    $hasUnsuccessfulAttempts = $shipping->events
                                                        ->whereIn('attempt_status', [
                                                            77090,
                                                            77098,
                                                            77101,
                                                            77102,
                                                            77171,
                                                            77191,
                                                        ])
                                                        ->isNotEmpty();
                                                @endphp

                                                @if ($hasUnsuccessfulAttempts)
                                                    <div>
                                                        {{ ordinalSuffix($shipping->events->count()) }} attempt was
                                                        <strong class="text-danger">unsuccessful</strong>
                                                    </div>
                                                @else
                                                    <div>
                                                        Item <strong class="text-success">delivered</strong>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </td>

                                        {{-- Courier --}}
                                        <td class="text-center">
                                            <p class="mb-0">{{ $shipping->order->courier->name }}</p>
                                        </td>

                                        {{-- Company --}}
                                        <td class="text-center">
                                            <p class="mb-0">{{ $shipping->order->company->code }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <div class="alert alert-warning" role="alert">
                                            No shipping found!
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $shippings->firstItem() }} to {{ $shippings->lastItem() }} of
                            {{ $shippings->total() }} orders
                        </div>
                        {{ $shippings->withQueryString()->links() }}
                    </div>
                    <!-- End Table -->
                </div>
            </div>
        </div>
    </section>

    <x-slot name="script">
        <script>
            document.querySelector('#download-order-btn').onclick = function() {

                // Get url segment
                const urlSegments = window.location.pathname.split('/');
                const status = urlSegments[2];

                fetch(`/orders/get_template_main?status=${status}`)
                    .then(response => response.json())
                    .then(options => {
                        Swal.fire({
                            title: 'Download CSV',
                            html: `
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label>Choose Template</label>
                                </div>
                                <div class="col-8">
                                    <select id="template-select" class="form-select">
                                        ${options.map(option => `<option value="${option.value}">${option.label}</option>`).join('')}
                                    </select>
                                </div>
                            </div>
                        `,
                            width: '50%',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            confirmButtonText: 'Download',
                            preConfirm: () => {
                                const templateSelect = document.getElementById('template-select');
                                const chosenTemplate = templateSelect.value;

                                const inputElements = [].slice.call(document.querySelectorAll(
                                    '.check-order'));
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
                                            download_csv(checkedOrder, chosenTemplate);
                                        }
                                    });
                                } else {
                                    let checkedOrder = [];
                                    inputElements.forEach(input => {
                                        if (input.checked) {
                                            checkedOrder.push(input.value);
                                        }
                                    });
                                    download_csv(checkedOrder, chosenTemplate);
                                }
                            },
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching template options:', error);
                    });
            };

            function download_csv(checkedOrder, chosenTemplate) {
                // const params = `{!! $_SERVER['QUERY_STRING'] ?? '' !!}`;
                // const param_obj = queryStringToJSON(params);

                axios.post('/api/download-order-csv', {
                        order_ids: checkedOrder,
                        template_id: chosenTemplate,
                    })
                    .then(function(response) {
                        // handle success, close or download
                        if (response != null && response.data != null) {
                            let a = document.createElement('a');
                            a.download = response.data.file_name;
                            a.target = '_blank';
                            a.href = window.location.origin + "/storage/" + response.data.file_name;
                            a.click();
                        }
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })
            }
        </script>
    </x-slot>
</x-layout>
