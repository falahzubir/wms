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

        .switch {
            position: relative;
            display: inline-block;
            width: 3em;
            height: 1.5rem;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider,
        .slider2 {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: red;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before,
        .slider2::before {
            position: absolute;
            content: "";
            height: 19px;
            width: 19px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider,
        input:checked+.slider2 {
            background-color: green;
        }

        input:focus+.slider,
        input:focus+.slider2 {
            box-shadow: 0 0 1px green;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(14px);
            -ms-transform: translateX(14px);
            transform: translateX(14px);
        }

        input:checked+.slider2:before {
            -webkit-transform: translateX(21px);
            -ms-transform: translateX(21px);
            transform: translateX(21px);
        }

        /* Rounded sliders */
        .slider.round,
        .slider2.round {
            border-radius: 34px;
        }

        .slider.round:before,
        .slider2.round:before {
            border-radius: 50%;
        }
    </style>

    <section class="section">

        <div class="row">
            <div class="card" style="font-size:0.8rem" id="courier-coverage-table">
                <div class="card-body">
                    <div class="pt-2">
                        <h6 class="fw-bolder text-decoration-underline">Default Coverage</h6>
                    </div>

                    <div class="pt-5">
                        <div class="mb-3 row">
                            <label for="stateCA" class="col-sm-4 col-form-label fw-bold">State</label>
                            <div class="col-sm-6">
                                <select onchange="selectionChange(this)" name="state" class="form-control form-control-sm" id="stateCA">
                                    <option value="">Select State</option>
                                    <option value="1">Kedah</option>
                                    <option value="2">Perlis</option>
                                    <option value="3">Pulau Pinang</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row" id="selectionDIV">
                            <label for="selection" class="col-sm-4 col-form-label fw-bold">Selection</label>
                            <div id="selection-table" class="col-sm-6">
                                <table class="table table-bordered">
                                    <thead class="text-center table-emzi">
                                        <tr class="align-middle">
                                            <th scope="col">Delivery Type</th>
                                            <th scope="col">Courier</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-selected-coverage" class="text-center">
                                        <tr>
                                            <td>COD</td>
                                            <td>
                                                <select name="courier[cod]" class="form-control" id="courier">
                                                    <option value="">Select Courier</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Non-COD</td>
                                            <td>
                                                <select name="courier[non-cod]" class="form-control" id="courier">
                                                    <option value="">Select Courier</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- save button -->
                                <div class="text-end">
                                    <button type="button" onclick="saveDefaultCoverage()" class="btn btn-primary" id="default-coverage-save-btn">Save</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="hide-too">
                        <hr>


                        <div class="pt-2">
                            <h6 class="fw-bolder text-decoration-underline">Exceptional Coverage</h6>
                        </div>

                        <div class="card-body pt-5">
                            <!-- No Labels Form -->
                            <form id="form-exceptional-coverage" class="row g-3" action="{{ url()->current() }}">
                                <div class="col-md-11">
                                    <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" onclick="loadTableExceptionalCoverage()" class="btn btn-primary" id="filter-order">Search</button>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="card-title text-end">
                                <button type="button" onclick="addCoverage()" class="btn btn-sm btn-primary" id="add-courier-coverage-btn">
                                    Add Coverage
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="upload-coverage">
                                    Upload
                                </button>
                            </div>
                            <table class="table">
                                <thead class="text-center">
                                    <tr class="align-middle">
                                        <th scope="col">Action</th>
                                        <th scope="col">
                                            <label class="switch">
                                                <input type="checkbox" ${checked}>
                                                <span class="slider round"></span>
                                            </label>
                                        </th>
                                        <th scope="col">Postcode</th>
                                        <th scope="col">Delivery Type</th>
                                        <th scope="col">Courier</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-exceptional-coverage" class="text-center">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- START MODAL ADD/EDIT COVERAGE -->
    <div id="modalAddEditCourierCoverage" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <form id="add-edit-courier-coverage" action="">
                        <div class="mb-3 row">
                            <label for="postcodeCA" class="col-sm-4 col-form-label">Postcode</label>
                            <div class="col-sm-8">
                                <input type="text" name="postcode" class="form-control" id="postcodeCA">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="deliveryTypeCA" class="col-sm-4 col-form-label">Delivery Type</label>
                            <div class="col-sm-8">
                                <select name="delivery_type" class="form-control" id="deliveryTypeCA">
                                    <option value="">Select Delivery Type</option>
                                    <option value="1">COD</option>
                                    <option value="2">NON-COD</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="courierCA" class="col-sm-4 col-form-label">Delivery Type</label>
                            <div class="col-sm-8">
                                <select name="courier" class="form-control" id="courierCA">
                                    <option value="">Select Courier</option>
                                    <option value="1">DHL</option>
                                    <option value="2">POS LAJU</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                        <label for="statusCourier" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8" style="padding-top: 7px;">
                            <label class="switch">
                                <input type="checkbox" name="status_courier" ${checked} id="statusCourier">
                                <span class="slider2 round"></span>
                            </label>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="submitCourierCoverage()" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL ADD/EDIT COVERAGE -->



    <x-slot name="script">
        <script>
            $(document).ready(function() {
                $('#selectionDIV').hide();
                $('#hide-too').hide();
            });

            // SELECTION STATE
            const selectionChange = (e) => {
                let id = e.value;
                if (id != "") {
                    let response = axios.post('/api/couriers/defaultCoverageState', {
                            state_id: id,
                        })
                        .then(function(response) {

                            $('#selectionDIV').show();
                            $('#hide-too').show();
                            loadTableExceptionalCoverage();

                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                } else {
                    $('#selectionDIV').hide();
                    $('#hide-too').hide();
                }
            }

            // LOAD TABLE EXCEPTIONAL COVERAGE
            const loadTableExceptionalCoverage = () => {
                let form = $('#form-exceptional-coverage').serialize();
                let response = axios.post('/api/couriers/exceptionalCoverage', {
                        form: form,
                    })
                    .then(function(response) {
                        renderTable(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // RENDER TABLE EXCEPTIONAL COVERAGE
            const renderTable = (data) => {
                let html = '';
                data.forEach((item, index) => {
                    let checked = item.status == 1 ? 'checked' : '';
                    html += `
                        <tr class="tr-row-${item.id}">
                            <td>
                                <button type="button" class="btn btn-sm" onclick="deleteCoverage(${item.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" ${checked}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>${item.postcode}</td>
                            <td>${item.delivery_type}</td>
                            <td>${item.courier_name}</td>
                        </tr>
                    `;
                });
                $('#tbody-exceptional-coverage').html(html);
            }

            // ADD COVERAGE
            const addCoverage = () => {
                $('#modalAddEditCourierCoverage').modal('show');
                $('#modalAddEditCourierCoverage .modal-title').html('Add Exceptional Coverage');
            }

            // DELETE COVERAGE
            const deleteCoverage = (id) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this coverage?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $(`.tr-row-${id}`).remove();

                        // let response = axios.post('/api/couriers/delete', {
                        //         id: id,
                        //     })
                        //     .then(function(response) {
                        //         $(`.tr-row-${id}`).remove();
                        //     })
                        //     .catch(function(error) {
                        //         console.log(error);
                        //     });
                    }
                })
            }
        </script>

    </x-slot>
</x-layout>