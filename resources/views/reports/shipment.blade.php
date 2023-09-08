<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <style>
        #shipment-table {
            font-size: 0.9rem;
        }
    </style>
    <section class="section">

        <div class="card" id="filter-body">
            <div class="card-body pt-3" style="">
                <h5>Filter</h5>
                <form class="row g-3" id="order-matrix-filter" action="{{ url()->current() }}">
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
                        <select name="date_type" id="date-type" class="form-control">
                            <option value="pickup">Date Pickup</option>
                            <option value="attempt">Date Attempt</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="From" name="date_from" id="start-date"
                            value="{{ Request::has('date_from') ? Request::get('date_from') : date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                            value="{{ Request::has('date_to') ? Request::get('date_to') : date('Y-m-d') }}">
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-danger" id="filter-order">Search</button>
                    </div>
                </form><!-- End No Labels Form -->

            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button class="btn btn-warning btn-sm" id="reattempt-btn"><i class="bi bi-arrow-left-right"></i> Re Attempt</button>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm" id="upload-btn"><i class="bi bi-cloud-arrow-up-fill"></i> Upload Response</button>
                        <button class="btn btn-secondary btn-sm" id="download-btn"><i class="bi bi-cloud-arrow-down-fill"></i> Download CSV</button>
                    </div>
                </div>
                <div class="table">
                    <table id="shipment-table" class="w-100 table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                @if (Route::current()->getName() == 'reports.shipment.problematic-list')
                                    <th>
                                        <input type="checkbox" name="check_all" id="check_all"  onchange="toggleCheckboxes(this, 'check-shipment')">
                                    </th>
                                    <th>Action</th>
                                @endif
                                <th>Delivery Attempt</th>
                                <th>Customer Info</th>
                                <th>Product Info</th>
                                <th>Delivery Info</th>
                                @if (Route::current()->getName() == 'reports.shipment.unattempt-list')
                                    <th>Status</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                @if (Route::current()->getName() == 'reports.shipment.problematic-list')
                                    <td class="text-center">
                                        <input type="checkbox" name="check" id="check" class="check-shipment">
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm text-white p-0 px-2" style="background-color: #00489B;" onclick="edit_response_modal(1)">
                                            <i class="bi bi-vector-pen"></i>
                                        </button>
                                    </td>
                                @endif
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <i class="bx bxs-calendar text-success"></i>
                                        <span>{{ date('d/m/Y H:ia') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <i class="bx bxs-calendar text-primary"></i>
                                        <span>{{ date('d/m/Y H:ia') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <i class="bx bxs-calendar text-secondary"></i>
                                        <span>{{ date('d/m/Y H:ia') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <i class="bx bxs-calendar text-warning"></i>
                                        <span>{{ date('d/m/Y H:ia') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-person-circle"></i>
                                        <span>John Doe</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bx bxs-phone-call"></i>
                                        <span>08123456789</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="ri ri-map-pin-2-line"></i>
                                        <span>Jl. Raya Bogor KM 30</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-box-seam"></i>
                                        <span>
                                            <a href="javascript:void(0)">
                                                SOEH012831232
                                            </a>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-truck"></i>
                                        <span>
                                            <a href="https://www.dhl.com/my-en/home/tracking/tracking-ecommerce.html?submit=1&tracking-id=7122064497838823" target="blank">
                                            712837912371382</a>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-top gap-1">
                                        <i class="bi bi-bag"></i>
                                        <div>
                                            <div>Neloco <b>[5]</b></div>
                                            <div>Shaker FOC <b>[1]</b></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-clock-history"></i>
                                        <span>D + 1</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-ui-checks"></i>
                                        <span>1 attempt left</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-check-all"></i>
                                        <span>Delivered</span>
                                    </div>
                                </td>
                                @if (Route::current()->getName() == 'reports.shipment.unattempt-list')
                                    <td>
                                        [Status]
                                    </td>
                                @endif
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

<div class="modal fade" id="editResponseModal" tabindex="-1" aria-labelledby="editresponse-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editResponseModalLabel">Edit Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <label for="response_text">Response</label>
                    <textarea name="response_text" id="response_text" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

</div> <!-- end other website embeded modal -->

    <x-slot name="script">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // load_table();
            });

            function load_table(data = null) {
                const table = document.querySelector('#shipment-table');
                const tbody = table.querySelector('tbody');

                let body = '';
                body += '<tr>';
                body += '<td colspan="6" class="text-center">Loading...</td>';
                body += '</tr>';
                tbody.innerHTML = body;

            }

            function show_modal(id) {
                // open returnModal modal pure js
                let modal = document.getElementById(id);
                let modalLabel = document.getElementById(`${id}Label`);

                // open modal
                let myModal = new bootstrap.Modal(modal, {
                    keyboard: false
                });
                myModal.show();
            }

            function edit_response_modal(id) {
                const modal = document.querySelector('#editResponseModal');
                show_modal('editResponseModal');


            }
        </script>
    </x-slot>

</x-layout>
