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
        td {
            vertical-align: middle !important;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <!-- No Labels Form if press enter click button -->
                    <form id="form-courier-coverage" class="row g-3" action="{{ url()->current() }}" onkeydown="if(event.keyCode==13) event.preventDefault()">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}" id="search">
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
                let search_data = $('#search').val();
                if(search_data == '') {
                    $('#tbody-selected-coverage').html(`
                        <tr>
                            <td colspan="3" class="text-center">Search to display data</td>
                        </tr>
                    `);
                    return;
                }
                let response = axios.post('/api/couriers/listSelectedCoverage', {
                        search: search_data
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
                // let html2 = '';
                // let html3 = '';
                if (data.couriers && data.couriers.length > 0) {
                    td_postcode = `<td rowspan="${data.couriers.length}" class="fw-bold">${data.postcode}</td>`;
                    data.couriers.forEach((item, index) => {
                        html += `
                            <tr>
                                ${index == 0 ? td_postcode : ''}
                                <td class="fw-bold">${item.type == 1 ? 'COD' : 'Non-COD'}</td>
                                <td class="fw-bold">${item.courier.name}</td>
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
