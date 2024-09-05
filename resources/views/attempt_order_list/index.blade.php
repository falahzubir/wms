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
                            <button class="btn btn-secondary" onclick="downloadCSV(this);">
                                <i class="bi bi-cloud-download"></i> Download CSV
                            </button>
                        @endcan
                    </div>

                    <!-- Table -->
                    <table class="table">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Customer Info</th>
                                <th scope="col">Product Info</th>
                                <th scope="col" style="width: 250px;">Delivery Attempt Status</th>
                                <th scope="col" class="text-center">Courier</th>
                                <th scope="col">BU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($shippingEvents->count())
                                @foreach ($shippingEvents as $key => $event)
                                    <tr style="font-size: 0.8rem;">
                                        {{-- Row Numbers --}}
                                        <td scope="row" class="text-center">{{ $key + $shippingEvents->firstItem() }}
                                        </td>

                                        {{-- Customer Info --}}
                                        <td class="text-start" style="width: 400px;">
                                            <input type="hidden" name="check_order"
                                                value="{{ $event->shipping->order->id }}">

                                            <div><strong>{{ $event->shipping->order->customer->name }}</strong></div>
                                            <div>
                                                <p class="mb-0">{{ $event->shipping->order->customer->phone }}</p>
                                                <p class="mb-0">{{ $event->shipping->order->customer->phone_2 }}</p>
                                            </div>
                                            <div>
                                                <span
                                                    class="customer-address">{{ $event->shipping->order->customer->address }}</span>,
                                                {{ $event->shipping->order->customer->postcode }},
                                                {{ $event->shipping->order->customer->city }},
                                                {{ MY_STATES[$event->shipping->order->customer->state] ?? '' }},
                                                {{ COUNTRY_ID[$event->shipping->order->customer->country] }}
                                            </div>
                                        </td>

                                        {{-- Product Info --}}
                                        <td>
                                            {{-- Order Number --}}
                                            <div>
                                                @isset($event->shipping->order->sales_id)
                                                    <i class="bi bi-box-seam"></i>
                                                    <span role="button" class="order-num text-primary"
                                                        data-sales-id="{{ $event->shipping->order->sales_id }}"
                                                        data-order-num="{{ order_num_format($event->shipping->order) }}"
                                                        title="Double Click to Copy">
                                                        <strong>{{ order_num_format($event->shipping->order) }}</strong>
                                                    </span>
                                                @endisset
                                            </div>

                                            {{-- Tracking Number --}}
                                            <div>
                                                @if ($event->shipping->order->courier->code == 'dhl-ecommerce' || $event->shipping->order->courier->code == 'dhl')
                                                    <i class="bi bi-truck"></i>
                                                    <a class="text-success"
                                                        href="https://www.dhl.com/us-en/home/tracking/tracking-ecommerce.html?submit=1&tracking-id={{ $event->shipping->tracking_number }}">{{ $event->shipping->tracking_number }}</a>
                                                @elseif ($event->shipping->order->courier->code == 'poslaju' || $event->shipping->order->courier->code == 'posmalaysia')
                                                    <i class="bi bi-truck"></i>
                                                    <a class="text-success"
                                                        href="https://tracking.pos.com.my/tracking/{{ $event->shipping->tracking_number }}">{{ $event->shipping->tracking_number }}</a>
                                                @else
                                                    <i class="bi bi-truck"></i>
                                                    <a class="text-success"
                                                        href="https://www.tracking.my/">{{ $event->shipping->tracking_number }}</a>
                                                @endif
                                            </div>

                                            {{-- Products --}}
                                            <div>
                                                @foreach ($event->shipping->order->items as $product)
                                                    <i class="bi bi-bag"></i>
                                                    {{ $product->product->name }}<strong>[{{ $product->quantity }}]</strong>
                                                    <br>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- Delivery Attempt Status --}}
                                        <td>
                                            @php
                                                // Retrieve the latest event based on attempt_time
                                                $latestEvent = $event->shipping->events
                                                    ->sortByDesc('created_at')
                                                    ->first();

                                                // Retrieve the latest log based on created_at
                                                $latestLog = $event->shipping->order->logs
                                                    ->whereIn('order_status_id', [5, 6])
                                                    ->sortByDesc('created_at')
                                                    ->first();

                                                // Count of all events for the current shipping
                                                $eventCount = $event->shipping->events
                                                    ->whereIn('attempt_status', [77090, 'EM013', 'EM080'])
                                                    ->count();

                                                // Check if any of the specified attempt_status values are present
                                                $attempt = $event->shipping->events
                                                    ->whereIn('attempt_status', [77090, 'EM013', 'EM080'])
                                                    ->isNotEmpty();

                                                $reason = $event->shipping->events
                                                    ->whereIn('attempt_status', [
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
                                                    ])
                                                    ->isNotEmpty();
                                            @endphp

                                            @if ($latestEvent)
                                                @if ($latestLog && $latestLog->order_status_id == 6)
                                                    <div>
                                                        <i class="bi bi-stopwatch-fill"></i>
                                                        {{ \Carbon\Carbon::parse($latestLog->created_at)->format('d/m/Y H:i') }}
                                                    </div>

                                                    <div>
                                                        Item <strong class="text-success">delivered</strong>
                                                    </div>
                                                @else
                                                    @if ($attempt)
                                                        <div>
                                                            <i class="bi bi-stopwatch-fill"></i>
                                                            {{ \Carbon\Carbon::parse($latestEvent->attempt_time)->format('d/m/Y H:i') }}
                                                        </div>

                                                        <div>
                                                            {{ ordinalSuffix($eventCount) }} attempt was
                                                            <strong class="text-danger">unsuccessful</strong>
                                                        </div>

                                                        @if ($reason)
                                                            <div>
                                                                <i class="bi bi-exclamation-circle-fill"></i>
                                                                {{ $latestEvent->description }}
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        </td>

                                        {{-- Courier --}}
                                        <td class="text-center">
                                            <p class="mb-0">{{ $event->shipping->order->courier->name }}</p>
                                        </td>

                                        {{-- Company --}}
                                        <td class="text-center">
                                            <p class="mb-0">{{ $event->shipping->order->company->code }}</p>
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
                            Showing {{ $shippingEvents->firstItem() }} to {{ $shippingEvents->lastItem() }} of
                            {{ $shippingEvents->total() }} orders
                        </div>
                        {{ $shippingEvents->withQueryString()->links() }}
                    </div>
                    <!-- End Table -->
                </div>
            </div>
        </div>
    </section>

    <x-slot name="script">
        @can('order.download')
        <script>
            const downloadCSV = (e) => {
                event.preventDefault();
                e.disabled = true;
                //download animation
                e.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Downloading...';

                const formData = new FormData(document.getElementById('search-form'));
                const nowDate = new Date();
                nowDate.setHours(nowDate.getHours() + 8);
                const nowDateUnix = nowDate.toISOString().slice(0, 10).replace(/-/g, '') + nowDate.toISOString().slice(11, 19).replace(/:/g, '');

                axios.get(`{{ route('download_csv') }}`, {
                    params: Object.fromEntries(formData),
                    responseType: 'blob',
                })
                    .then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `${nowDateUnix}_attempt_order_list.csv`);
                        document.body.appendChild(link);
                        link.click();
                        e.disabled = false;
                        e.innerHTML = '<i class="bi bi-cloud-download"></i> Download CSV';
                    })
                    .catch((error) => {
                        e.disabled = false;
                        e.innerHTML = '<i class="bi bi-cloud-download"></i> Download CSV';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                        console.error(error);
                    });
            }
        </script>
        @endcan
    </x-slot>
</x-layout>
