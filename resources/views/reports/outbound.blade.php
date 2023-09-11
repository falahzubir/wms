<x-layout :title="$title">

    <style>
        .placeholder-wave {
            margin: 0;
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
                            value="{{ Request::has('date_to') ? Request::get('date_to') : date("Y-m-d") }}">
                    </div>
                    <div class="row mt-3">
                        <div class="col-3">
                            <label for="product-list">Product</label>
                            <select class="form-control" id="product-list" name="product">
                                <option value="">All</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @if (Request::get('product') == $product->id) selected @endif>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger" id="filter-outbound">Search</button>
                    </div>
                </form><!-- End No Labels Form -->

            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                {{-- <div class="d-flex justify-content-between">
                    <div>&nbsp;</div>
                    <div>
                        <button class="btn btn-success d-flex gap-2">
                            <i class="bi bi-download"></i>
                            <span>Download CSV</span>
                        </button>
                    </div>
                </div> --}}
                <div class="mt-3 table-responsive">
                    <table class="table table-striped table-bordered border border-light-subtle w-100" id="table-outbound">
                        <thead class="bglightblue">
                            <tr class="text-center">
                                <th>Product</th>
                                @foreach ($companies as $company)
                                    <th>{{ $company->code }}</th>
                                @endforeach
                                <th>Blast</th>
                                <th>Shopee</th>
                                <th>TikTok</th>
                                <th>Self-Collect</th>
                                <th>Total Output</th>
                            </tr>
                        </thead>
                        <tbody class="outbound-list">
                            @if(Request::get('product') == '')
                                @foreach ($product_lists as $prod)
                                    <tr>
                                        <td id="prod-{{ $prod->id }}">{{ $prod->name }}</td>
                                        <td colspan="{{ $companies->count() + 5 }}">
                                            <p class="placeholder-wave">
                                                <span class="placeholder col-12"></span>
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td id="prod-{{ Request::get('product')}}">{{ $products->find(Request::get('product'))->name }}</td>
                                        <td colspan="{{ $companies->count() + 5 }}">
                                            <p class="placeholder-wave">
                                                <span class="placeholder col-12"></span>
                                            </p>
                                        </td>
                                    </tr>
                            @endif
                        </tbody>
                    </table>
                    @if(Request::get('product') == '')
                        <div class="d-flex justify-content-end">
                            {{ $product_lists->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </section>

    <x-slot name="script">
        <script>

            document.addEventListener('DOMContentLoaded', () => {

                // Get all pagination links
                const paginationLinks = document.querySelectorAll('.pagination a');

                // Attach click event handler to each pagination link
                paginationLinks.forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior

                        //stop all ajax request
                        axios.CancelToken.source().cancel();

                        // Get the href attribute of the clicked link
                        const href = link.getAttribute('href');

                        // Add the hash fragment to the URL
                        const newUrl = href + '#table-outbound';

                        // Go to the URL
                        location.href = newUrl;

                    });
                });
                getLists();
            });

            document.querySelector('#filter-outbound').addEventListener('click', () => {

                getLists();

                // @foreach ($products as $product)

                // axios.get(`/api/reports/outbound?${params}&product_id={{ $product->id }}`)
                //     .then(function(response) {
                //         populate_table(response.data);
                //     })
                //     .catch(function(error) {
                //         console.log(error);
                //     });
                // @endforeach

            });

            async function getLists() {
                let url = new URL(window.location.href);
                let params = url.searchParams;
                params.set('date_from', document.querySelector('#start-date').value);
                params.set('date_to', document.querySelector('#end-date').value);
                if (params.page == null) {
                    params.set('page', 1);
                }

                let product_ids = {{ $product_lists->pluck('id') }};

                if(document.querySelector('#product-list').value != '') {
                    product_ids = [document.querySelector('#product-list').value];
                }

                fetchSequentially(product_ids, params)
                    .then(results => {
                        console.log('Fetched data:', product_ids);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
            async function fetchData(product_id, params) {
                try {
                    const response = await axios.get(`/api/reports/outbound?${params}&product_id=${product_id}`);
                    populate_table(response.data);
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return null;
                }
            }

            async function fetchSequentially(product_ids, params) {
                const results = [];
                // const pagination = 10;
                // let page = 1;
                // if (params.has('page')) {
                //     page = params.get('page');
                // }

                // let start_index = (page - 1) * pagination;
                // let start_index = (page - 1);
                let paginate_limit = 10;
                if (product_ids.length == 1) {
                    paginate_limit = product_ids.length;
                }
                for (let i = 0; i < paginate_limit; i++) {
                    const data = await fetchData(product_ids[i], params);
                }

                return results;
            }

            function populate_table(data) {
                const companies = {{ $companies->pluck('id') }};
                // console.log(data.total_by_company[2]);
                let row = document.querySelector(`#prod-${data.product_id}`);
                // delete next siblings
                while (row.nextElementSibling) {
                    row.nextElementSibling.remove();
                }
                let body = '';
                for (let i = 1; i <= companies.length; i++) {
                    body += `<td>${data.total_by_company[i] ?? 0 }</td>`;
                }
                body += `<td>${data.total_by_operational_model[16] ?? 0 }</td>`; //blast
                body += `<td>${data.total_by_payment_type[22] ?? 0 }</td>`; //shopee
                body += `<td>${data.total_by_payment_type[23] ?? 0 }</td>`; //tiktok
                body += `<td>${data.total_by_operational_model[6] ?? 0 }</td>`; //selfcollect
                body += `<td>${data.total_products}</td>`; //total output

                // insert new siblings

                // body += `<td>${data.product_id}</td>`;
                // body += `<td>${data.product_id}</td>`;
                // body += `<td>${data.product_id}</td>`;
                // body += `<td>${data.product_id}</td>`;
                // body += `<td>${data.total_orders}</td>`;
                row.insertAdjacentHTML('afterend', body);

                // body += `<td>${data.name}</td>`;
                // body += `</tr>`;
                // tbody.innerHTML = body;
            }
        </script>
    </x-slot>

</x-layout>
