<x-layout :title="$title" :crumbList="$crumbList">
    <style>
        .table-emzi {
            --bs-table-color: #fff;
            --bs-table-bg: orange;
            --bs-table-border-color: black;
            --bs-table-striped-bg: #f2e7c3;
            --bs-table-striped-color: #000;
            --bs-table-active-bg: black;
            --bs-table-active-color: #000;
            --bs-table-hover-bg: #ece1be;
            --bs-table-hover-color: #000;
            color: var(--bs-table-color);
            border-color: var(--bs-table-border-color);
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <!-- No Labels Form -->
                    <form id="form-courier-coverage" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="text-end">
                            <button type="button" onclick="loadTableSelectedCoverage()" class="btn btn-primary" id="filter-order">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card" style="font-size:0.8rem" id="courier-coverage-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <a type="button" href="{{ route('couriers.defaultCoverage') }}" class="btn btn-sm btn-primary" id="add-courier-coverage-btn">
                            Setup Coverage
                        </a>
                    </div>
                    <table class="table table-bordered ">
                        <thead class="text-center table-emzi">
                            <tr class="align-middle">
                                <th scope="col">Postcode</th>
                                <th scope="col">Delivery Type</th>
                                <th scope="col">Courier</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-selected-coverage" class="text-center">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>



    <x-slot name="script">
        <script>
            $(document).ready(function() {
                loadTableSelectedCoverage();
            });

            // LOAD TABLE
            const loadTableSelectedCoverage = () => {
                let form = $('#form-courier-coverage').serialize();
                let response = axios.post('/api/couriers/listSelectedCoverage', {
                        form: form,
                    })
                    .then(function(response) {
                        renderTable(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // RENDER TABLE
            const renderTable = (data) => {
                $('#tbody-selected-coverage').empty();
                let html = '';
                let html2 = '';
                let html3 = '';
                if (data && data.length > 0) {
                    data.forEach((item, index) => {
                        item.couriers.forEach((item2, index2) => {
                            html2 += `
                                <tr>
                                    <td class="fw-bold">${item2.delivery_type}</td>
                                    <td class="fw-bold">${item2.courier_name}</td>
                                </tr>
                            `;
                        });

                        html += `
                            <tr class="tr-row-${item.id}">
                                <td rowspan="3" class="fw-bold">${item.postcode}</td>
                                ${html2}
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="3" class="text-center">No data found</td>
                        </tr>
                    `;
                }
                $('#tbody-selected-coverage').html(html);
            }

        </script>

    </x-slot>
</x-layout>