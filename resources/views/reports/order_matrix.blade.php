<x-layout :title="$title">

    <style>
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

        .active-1 {
            background-color: #E2F1FF !important;
            border: 4px #A0CFFF solid !important;
            color: #012970 !important;
        }

        .list-group-item {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-text {
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.1), transparent);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            /* display: inline-block; */
        }

        @keyframes loading {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>
    <section class="section">
        <div class="card" id="filter-body">
            <div class="card-body pt-5" style="">
                <!-- No Labels Form -->
                <form class="row g-3" id="order-matrix-filter" action="{{ url()->current() }}">
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
                            <label for="product-list">Filter By</label>
                            <select class="form-control" id="filter-by" name="filter_by" onchange="filter_change(this)"
                                required>
                                <option value="">Please Select Filter </option>
                                <option value="product" {{ Request::get('filter_by') == 'product' ? 'selected' : '' }}>
                                    Product</option>
                                <option value="courier" {{ Request::get('filter_by') == 'courier' ? 'selected' : '' }}>
                                    Courier</option>
                            </select>
                        </div>
                        <div class="col-3 {{ Request::get('filter_by') == 'product' ? '' : 'd-none' }}" id="filter-product">
                            <label for="product-list">Products</label>
                            <select class="form-control" id="filter-product-field" name="product">
                                <option value="">All</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ Request::get('product') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 {{ Request::get('filter_by') == 'courier' ? '' : 'd-none' }}" id="filter-courier">
                            <label for="courier-list">Couriers</label>
                            <select class="form-control" id="filter-courier-field" name="courier">
                                <option value="">All</option>
                                @foreach ($couriers as $courier)
                                    <option value="{{ $courier->id }}"
                                        {{ Request::get('courier') == $courier->id ? 'selected' : '' }}>
                                        {{ $courier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-danger" id="filter-order">Search</button>
                    </div>
                </form><!-- End No Labels Form -->

            </div>
        </div>

        <div class="card">
            <div class="card-body p-5">
                <div class="mb-5 row ms-0">
                    <div class="list-group list-group-horizontal table-responsive" id="totals">
                        <a href="#totals" id="total-extract"
                            class="list-group-item list-group-item-action">Total Extract</a>
                        <a href="#totals" id="total-pack" class="list-group-item list-group-item-action">Total Pack</a>
                        <a href="#totals" id="total-pickup" class="list-group-item list-group-item-action">Total
                            Pickup</a>
                        <a href="#totals" id="total-comparison"
                            class="list-group-item list-group-item-action">Comparison Total</a>
                    </div>
                </div>

                <div class="mt-3 table-responsive">
                    <table class="table table-striped table-bordered border border-light-subtle w-100">
                        <thead class="bglightblue">
                            <tr id="matrix-header" class="text-center">
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
                            <tr id="matrix-header-comparison" class="text-center d-none">
                                <th>Product</th>
                                <th>Total Extract</th>
                                <th>Total Pack</th>
                                <th>Total Pickup</th>
                                <th>Comparison</th>
                            </tr>
                        </thead>
                        <tbody id="matrix-list">
                            {{-- @unless (Request::get('filter_by') == 'product' || Request::get('filter_by') == 'courier') --}}
                                <tr>
                                    <td colspan="{{ $companies->count() + 6 }}" class="text-center">Please Select Option Above to Show Data</td>
                                </tr>
                            {{-- @endunless --}}
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>


                    {{-- <div class="d-flex justify-content-end mt-3">
                        <!-- pagination by ajax -->
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div> --}}
                </div>
            </div>
        </div>


    </section>

    <x-slot name="script">
        <script>
            // Onclick filter button
            document.querySelector('#filter-order').addEventListener('click', () => {

                document.querySelector(".active-1").click();

                // serialize form data
                let params = new URLSearchParams();
                let form = document.querySelector('#order-matrix-filter');
                for (const pair of new FormData(form)) {
                    params.append(pair[0], pair[1]);
                }

                // let url = new URL(window.location.href);
                // let params = url.searchParams;
                // params.set('date_from', document.querySelector('#start-date').value);
                // params.set('date_to', document.querySelector('#end-date').value);
                // if (params.page == null) {
                //     params.set('page', 1);
                // }
                // if (document.querySelector('#filter-type').value != '') {
                //     params.set('type', document.querySelector('#filter-type').value);
                // }
                // if (document.querySelector('#filter-product').value != '') {
                //     params.set('product', document.querySelector('#filter-product').value);
                // }
                // if (document.querySelector('#filter-courier').value != '') {
                //     params.set('courier', document.querySelector('#filter-courier').value);
                // }

                // window.history.replaceState({}, '', `${url.origin}${url.pathname}?${params}`);

                //get data
                // let data = {
                //     date_from: document.querySelector('#start-date').value,
                //     date_to: document.querySelector('#end-date').value,
                //     page: params.page
                // }

                // fetchData(params)
                //     .then(results => {
                //         console.log('Fetched data:', product_ids);
                //     })
                //     .catch(error => {
                //         console.error('Error:', error);
                //     });

            });

            async function fetchData(params, url) {
                // loding icon
                document.querySelector('#matrix-list').innerHTML = `<tr>
                                                                        <td colspan="100" class="text-center loading-text">
                                                                            <div>
                                                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>`;
                window.history.replaceState({}, '', `${window.location.origin}${window.location.pathname}?${params}`);
                try {
                    const response = await axios.get(`${url}?${params}`);
                    if (params.get('product') != '' || params.get('courier') != '') {
                        populate_table(response.data, true);
                    } else {
                        populate_table(response.data);
                    }
                    // populate_table(response.data);
                } catch (error) {
                    console.error('Error fetching data:', error);
                    return null;
                }
            }

            function populate_table(data, single = false) {
                const tbody = document.querySelector('#matrix-list');
                const type = document.querySelector('#filter-by').value;
                const companies = @json($companies);
                let body = '';
                if (type == 'product') {
                    const products = @json($products);
                    if(data.comparison == undefined){
                        if (single == true) {
                            const product_id = document.querySelector('#filter-product-field').value;
                            body += `<tr>
                                        <td>${products.find(product => product.id == product_id).name}</td>`;
                            for (let j = 0; j < companies.length; j++) {
                                body +=
                                    `<td>${data.total_by_company != undefined ? data.total_by_company[companies[j].id] != 0 ? data.total_by_company[companies[j].id] : '-':'-'}</td>`;
                                sum = 0;
                                for (let k = 0; k < companies.length; k++) {
                                    sum += data.total_by_company != undefined ? data.total_by_company[companies[k].id] : 0;
                                }

                            }
                            body +=
                                `<td>${data.total_by_operational_model != undefined ? data.total_by_operational_model[16] != 0 ? data.total_by_operational_model[16]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_payment_type != undefined ? data.total_by_operational_model[22] != 0 ? data.total_by_operational_model[22]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_payment_type != undefined ? data.total_by_operational_model[23] != 0 ? data.total_by_operational_model[23]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_operational_model != undefined ? data.total_by_operational_model[6] != 0 ? data.total_by_operational_model[6]??'-' : '-':'-'}</td>`;
                            body += `<td>${sum}</td>`;
                            body += `</tr>`;


                        } else {
                            for (let i = 0; i < products.length; i++) {
                                body += `<tr>
                                            <td>${products[i].name}</td>`;
                                for (let j = 0; j < companies.length; j++) {
                                    body +=
                                        `<td>${data.total_by_company[products[i].id] != undefined ? data.total_by_company[products[i].id][companies[j].id] != 0 ? data.total_by_company[products[i].id][companies[j].id] : '-':'-'}</td>`;
                                    sum = 0;
                                    for (let k = 0; k < companies.length; k++) {
                                        sum += data.total_by_company[products[i].id] != undefined ? data.total_by_company[
                                            products[i].id][companies[k].id] : 0;
                                    }

                                }
                                body +=
                                    `<td>${data.total_by_operational_model[products[i].id] != undefined ? data.total_by_operational_model[products[i].id][16] != 0 ? data.total_by_operational_model[products[i].id][16]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_payment_type[products[i].id] != undefined ? data.total_by_operational_model[products[i].id][22] != 0 ? data.total_by_operational_model[products[i].id][22]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_payment_type[products[i].id] != undefined ? data.total_by_operational_model[products[i].id][23] != 0 ? data.total_by_operational_model[products[i].id][23]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_operational_model[products[i].id] != undefined ? data.total_by_operational_model[products[i].id][6] != 0 ? data.total_by_operational_model[products[i].id][6]??'-' : '-':'-'}</td>`;
                                body += `<td>${sum}</td>`;
                                body += `</tr>`;
                            }
                        }
                    }
                    if(data.comparison === true){
                        if (single == true) {
                            const product_id = document.querySelector('#filter-product-field').value;
                            body += `<tr>
                                        <td>${products.find(product => product.id == product_id).name}</td>`;
                            body += `<td>${data.total_extract != undefined ? data.total_extract != 0 ? data.total_extract : '-':'-'}</td>`;
                            body += `<td>${data.total_scanned != undefined ? data.total_scanned != 0 ? data.total_scanned : '-':'-'}</td>`;
                            body += `<td>${data.total_shipped != undefined ? data.total_shipped != 0 ? data.total_shipped : '-':'-'}</td>`;
                            body += `<td>${(data.total_extract == data.total_scanned && data.total_scanned == data.total_shipped) ? '<span class="text-success">Tally</span>' : '<span class="text-danger">Not Tally</span>'}</td>`;
                            body += `</tr>`;

                        } else {
                            for (let i = 0; i < products.length; i++) {
                                body += `<tr>
                                            <td>${products[i].name}</td>`;
                                body += `<td>${data.total_extract[products[i].id] != undefined ? data.total_extract[products[i].id] != 0 ? data.total_extract[products[i].id] : '-':'-'}</td>`;
                                body += `<td>${data.total_scanned[products[i].id] != undefined ? data.total_scanned[products[i].id] != 0 ? data.total_scanned[products[i].id] : '-':'-'}</td>`;
                                body += `<td>${data.total_shipped[products[i].id] != undefined ? data.total_shipped[products[i].id] != 0 ? data.total_shipped[products[i].id] : '-':'-'}</td>`;
                                body += `<td>${(data.total_extract[products[i].id] == data.total_scanned[products[i].id] && data.total_scanned[products[i].id] == data.total_shipped[products[i].id]) ? '<span class="text-success">Tally</span>' : '<span class="text-danger">Not Tally</span>'}</td>`;
                                body += `</tr>`;
                            }
                        }
                    }

                }
                // console.log(body);
                if (type == 'courier') {
                    const couriers = @json($couriers);
                    if (data.comparison == undefined){
                        if (single == true) {
                            const courier_id = document.querySelector('#filter-courier-field').value;
                            body += `<tr>
                                        <td>${couriers.find(courier => courier.id == courier_id).name}</td>`;
                            for (let j = 0; j < companies.length; j++) {
                                body +=
                                    `<td>${data.total_by_company != undefined ? data.total_by_company[couriers[j].id] != 0 ? data.total_by_company[couriers[j].id] : '-':'-'}</td>`;
                                sum = 0;
                                for (let k = 0; k < companies.length; k++) {
                                    sum += data.total_by_company != undefined ? data.total_by_company[couriers[k].id] : 0;
                                }

                            }
                            body +=
                                `<td>${data.total_by_operational_model != undefined ? data.total_by_operational_model[16] != 0 ? data.total_by_operational_model[16]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_payment_type != undefined ? data.total_by_operational_model[22] != 0 ? data.total_by_operational_model[22]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_payment_type != undefined ? data.total_by_operational_model[23] != 0 ? data.total_by_operational_model[23]??'-' : '-':'-'}</td>`;
                            body +=
                                `<td>${data.total_by_operational_model != undefined ? data.total_by_operational_model[6] != 0 ? data.total_by_operational_model[6]??'-' : '-':'-'}</td>`;
                            body += `<td>${sum}</td>`;
                            body += `</tr>`;
                        } else {
                            for (let i = 0; i < couriers.length; i++) {
                                body += `<tr>
                                    <td>${couriers[i].name}</td>`;
                                for (let j = 0; j < companies.length; j++) {
                                    body +=
                                        `<td>${data.total_by_company[couriers[i].id] != undefined ? data.total_by_company[couriers[i].id][companies[j].id] != 0 ? data.total_by_company[couriers[i].id][companies[j].id] : '-':'-'}</td>`;
                                    sum = 0;
                                    for (let k = 0; k < companies.length; k++) {
                                        sum += data.total_by_company[couriers[i].id] != undefined ? data.total_by_company[couriers[
                                            i].id][companies[k].id] : 0;
                                    }

                                }
                                body +=
                                    `<td>${data.total_by_operational_model[couriers[i].id] != undefined ? data.total_by_operational_model[couriers[i].id][16] != 0 ? data.total_by_operational_model[couriers[i].id][16]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_payment_type[couriers[i].id] != undefined ? data.total_by_operational_model[couriers[i].id][22] != 0 ? data.total_by_operational_model[couriers[i].id][22]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_payment_type[couriers[i].id] != undefined ? data.total_by_operational_model[couriers[i].id][23] != 0 ? data.total_by_operational_model[couriers[i].id][23]??'-' : '-':'-'}</td>`;
                                body +=
                                    `<td>${data.total_by_operational_model[couriers[i].id] != undefined ? data.total_by_operational_model[couriers[i].id][6] != 0 ? data.total_by_operational_model[couriers[i].id][6]??'-' : '-':'-'}</td>`;
                                body += `<td>${sum}</td>`;
                                body += `</tr>`;
                            }
                        }
                    }

                    if(data.comparison == true){
                        if(single == true){
                            const courier_id = document.querySelector('#filter-courier-field').value;
                            body += `<tr>
                                        <td>${couriers.find(courier => courier.id == courier_id).name}</td>`;
                            body += `<td>${data.total_extract != undefined ? data.total_extract != 0 ? data.total_extract : '-':'-'}</td>`;
                            body += `<td>${data.total_scanned != undefined ? data.total_scanned != 0 ? data.total_scanned : '-':'-'}</td>`;
                            body += `<td>${data.total_shipped != undefined ? data.total_shipped != 0 ? data.total_shipped : '-':'-'}</td>`;
                            body += `<td>${(data.total_extract == data.total_scanned && data.total_scanned == data.total_shipped) ? '<span class="text-success">Tally</span>' : '<span class="text-danger">Not Tally</span>'}</td>`;
                            body += `</tr>`;
                        } else {
                            for (let i = 0; i < couriers.length; i++) {
                                body += `<tr>
                                            <td>${couriers[i].name}</td>`;
                                body += `<td>${data.total_extract[couriers[i].id] != undefined ? data.total_extract[couriers[i].id] != 0 ? data.total_extract[couriers[i].id] : '-':'-'}</td>`;
                                body += `<td>${data.total_scanned[couriers[i].id] != undefined ? data.total_scanned[couriers[i].id] != 0 ? data.total_scanned[couriers[i].id] : '-':'-'}</td>`;
                                body += `<td>${data.total_shipped[couriers[i].id] != undefined ? data.total_shipped[couriers[i].id] != 0 ? data.total_shipped[couriers[i].id] : '-':'-'}</td>`;
                                body += `<td>${(data.total_extract[couriers[i].id] == data.total_scanned[couriers[i].id] && data.total_scanned[couriers[i].id] == data.total_shipped[couriers[i].id]) ? '<span class="text-success">Tally</span>' : '<span class="text-danger">Not Tally</span>'}</td>`;
                                body += `</tr>`;
                            }
                        }
                    }
                    // console.log(body);
                }
                tbody.innerHTML = body;
            }

            document.querySelectorAll('.list-group-item').forEach(item => {
                item.addEventListener('click', event => {
                    if(document.querySelector('#filter-by').value == ''){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please select filter by field to show data!',
                        })
                        return;
                    }
                    let params = new URLSearchParams();
                    let form = document.querySelector('#order-matrix-filter');
                    for (const pair of new FormData(form)) {
                        params.append(pair[0], pair[1]);
                    }
                    if (event.target.id == 'total-comparison') {
                        document.querySelector('#matrix-header').classList.add('d-none');
                        document.querySelector('#matrix-header-comparison').classList.remove('d-none');

                        fetchData(params, '/api/reports/order-matrix/comparison')
                            // .then(results => {
                            //     console.log('Fetched data:', results);
                            // })
                            // .catch(error => {
                            //     console.error('Error:', error);
                            // });
                    } else {
                        document.querySelector('#matrix-header').classList.remove('d-none');
                        document.querySelector('#matrix-header-comparison').classList.add('d-none');
                        let url = '';
                        switch (event.target.id) {
                            case 'total-extract':
                                url = '/api/reports/order-matrix/extract';
                                break;
                            case 'total-pack':
                                url = '/api/reports/order-matrix/pack';
                                break;
                            case 'total-pickup':
                                url = '/api/reports/order-matrix/pickup';
                                break;
                        }

                        fetchData(params, url)
                        // .then(results => {
                        //     console.log('Fetched data:', results);
                        // })
                        // .catch(error => {
                        //     console.error('Error:', error);
                        // });
                    }
                    document.querySelectorAll('.list-group-item').forEach(item => {
                        item.classList.remove('active-1');
                    });
                    item.classList.add('active-1');
                });
            });

            function filter_change(el) {
                const filter_courier = document.querySelector('#filter-courier');
                const filter_product = document.querySelector('#filter-product');
                // empty value
                document.querySelector('#filter-courier-field').value = '';
                document.querySelector('#filter-product-field').value = '';
                if (el.value == 'all') {
                    filter_courier.classList.add('d-none');
                    filter_product.classList.add('d-none');
                } else if (el.value == 'courier') {
                    filter_courier.classList.remove('d-none');
                    filter_product.classList.add('d-none');
                } else if (el.value == 'product') {
                    filter_courier.classList.add('d-none');
                    filter_product.classList.remove('d-none');
                }
            }
        </script>
    </x-slot>

</x-layout>
