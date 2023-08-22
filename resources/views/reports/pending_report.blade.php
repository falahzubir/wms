<x-layout :title="$title">

    <style>
        .bglightblue {
            background-color: #E2F1FF;
        }

        .info-card {
            font-size: 28px;
            color: #012970;
            font-weight: 700;
            margin: 0;
            padding: 0;
        }

        th {
            padding: 0.3rem;
        }

        .datatable-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* Styles for the pagination navigation */
        .datatable-pagination {
            display: flex;
            justify-content: right;
        }

        /* Styles for the pagination list */
        .datatable-pagination-list {
            list-style: none;
            display: flex;
            align-items: center;
        }

        /* Styles for pagination list items */
        .datatable-pagination-list-item {
            margin: 0 5px;
        }

        /* Styles for pagination links */
        .datatable-pagination-list-item-link {
            display: block;
            padding: 5px 10px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Styles for active page link */
        .datatable-active .datatable-pagination-list-item-link {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
        }

        /* Styles for disabled page link */
        .datatable-disabled .datatable-pagination-list-item-link {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
    <section class="section">
        <div class="card" id="filter-body">
            <div class="card-body pt-5" style="">
                <!-- No Labels Form -->
                <form class="row g-3" action="{{ url()->current() }}">
                    <div class="col-md-12">
                        <div class="btn-group" data-toggle="buttons">
                            <input type="radio" class="btn-check" id="btn-check-today" name="off">
                            <label class="btn btn-outline-secondary rounded-pill mx-1"
                                for="btn-check-today">Today</label>

                            <input type="radio" class="btn-check" id="btn-check-yesterday" name="off">
                            <label class="btn btn-outline-secondary rounded-pill mx-1"
                                for="btn-check-yesterday">Yesterday</label>

                            <input type="radio" class="btn-check" id="btn-check-this-month" name="off">
                            <label class="btn btn-outline-secondary rounded-pill mx-1" for="btn-check-this-month">This
                                Month</label>

                            <input type="radio" class="btn-check" id="btn-check-last-month" name="off">
                            <label class="btn btn-outline-secondary rounded-pill mx-1" for="btn-check-last-month">Last
                                Month</label>

                            <input type="radio" class="btn-check" id="btn-check-overall" name="off">
                            <label class="btn btn-outline-secondary rounded-pill mx-1"
                                for="btn-check-overall">Overall</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="From" name="date_from" id="start-date"
                            value="{{ Request::has('date_from') ? Request::get('date_from') : date("Y-m-d") }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                            value="{{ Request::has('date_from') ? Request::get('date_from') : date("Y-m-d") }}">
                    </div>
                    <div class="row mt-3">
                        <div class="col-3">
                            <label for="product-list">Product</label>
                            <select class="form-control tomsel" id="product-list" name="product[]" multiple>
                                <option value="">All</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @if (Request::get('product') == $product->id) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-danger" id="filter-pending">Search</button>
                    </div>
                </form><!-- End No Labels Form -->

            </div>
        </div>

        <div class="card">
            <div class="card-body p-5">
                <div class="mb-3 row">
                    <div class="col-md-4">
                        <div class="border border-secondary-subtle p-1 bglightblue rounded">
                            <div class="border border-secondary-subtle p-2 bg-white rounded">
                                <div class="d-flex justify-content-between">
                                    <strong>Total Pending Order</strong>
                                    <div class="text-right">
                                        <span class="bg-light rounded">
                                            <i class="bi bi-clock-history"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span id="total-pending" class="info-card p-2">
                                        <!-- rotating animation bi-hourglass-split -->
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table table-responsive">
                    <table class="table table-striped table-bordered border border-light-subtle w-100 simple-datatables"
                        id="pending-table">
                        <thead class="bglightblue text-center">
                            <tr>
                                <th width="50%">Product</th>
                                <th width="50%">Total</th>
                            </tr>
                        </thead>
                        <tbody id="pending-table-body">
                            <tr>
                                <td colspan="2" class="text-center">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </section>

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
            // document ready
            document.addEventListener("DOMContentLoaded", function() {
                let url = new URL(window.location.href);
                let params = url.searchParams;
                //defaut date this month
                document.querySelector('#start-date').value = "{{ Request::get('date_from') ?? date('Y-m-d') }}";
                document.querySelector('#end-date').value = "{{ Request::get('date_to') ?? date('Y-m-d') }}";

                params.set('date_from', document.querySelector('#start-date').value);
                params.set('date_to', document.querySelector('#end-date').value);
                params.set('product', document.querySelector('#product-list').value);

                window.history.replaceState({}, '', `${url.origin}${url.pathname}?${params}`);

                fetchData(params)
                    .then(results => {
                        console.log('Fetched data:', results);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

            });

            document.querySelector('#filter-pending').addEventListener('click', () => {
                let url = new URL(window.location.href);
                let params = url.searchParams;
                params.set('date_from', document.querySelector('#start-date').value);
                params.set('date_to', document.querySelector('#end-date').value);
                // mult select option
                let selected = [];
                for (let option of document.querySelector('#product-list').options) {
                    if (option.selected) {
                        selected.push(option.value);
                    }
                }

                params.set('product', selected);

                window.history.replaceState({}, '', `${url.origin}${url.pathname}?${params}`);

                fetchData(params)
                    .then(results => {
                        console.log('Fetched data:', results);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

            });

            async function fetchData(params) {
                try {
                    const response = await axios.get(`/api/reports/pending-report?${params}`);
                    populate_table(response.data);
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return null;
                }
            }


            function populate_table(data) {
                const totalPending = document.querySelector('#total-pending');
                const table = document.querySelector('#pending-table');
                const tbody = document.querySelector('#pending-table tbody');
                tbody.innerHTML = '';
                totalPending.innerHTML = data.total_orders;
                let body = '';
                if(data.total_order_by_product.length == 0){
                    body += `<tr>`;
                    body += `<td colspan="2" class="text-center">No data avsailable</td>`;
                    body += `</tr>`;
                }
                else{
                    data.total_order_by_product.forEach((item, index) => {
                        body += `<tr>`;
                        body += `<td>${item.product_name}</td>`;
                        body += `<td>${item.product_count}</td>`;
                        body += `</tr>`;
                    });
                }
                tbody.innerHTML = body;

                let dataTable = new DataTable(table, {
                    perPage: 10,
                    searchable: false,
                    perPageSelect: false,
                    columns: [{
                            select: 0,
                            sortable: true
                        },
                        {
                            select: 1,
                            sortable: true
                        }
                    ]
                });


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

        </script>
    </x-slot>

</x-layout>
