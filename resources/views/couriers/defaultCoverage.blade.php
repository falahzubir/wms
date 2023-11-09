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

        .border-dotted {
            border: 1px dotted #000;
        }

        .bg-lightblue {
            background-color: #e7f0f7;
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
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
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
                                                <select name="courier[cod]" class="form-control" id="courier-cod">
                                                    <option value="">Select Courier</option>
                                                    @foreach ($couriers as $courier)
                                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Non-COD</td>
                                            <td>
                                                <select name="courier[non-cod]" class="form-control" id="courier-non-cod">
                                                    <option value="">Select Courier</option>
                                                    @foreach ($couriers as $courier)
                                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                                    @endforeach
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
                            <form id="form-exceptional-coverage" class="row g-3" action="{{ url()->current() }}" onsubmit="event.preventDefault()">
                                <div class="col-md-11">
                                    <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}" id="search">
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
                                <button type="button" class="btn btn-sm btn-success" id="upload-coverage" data-bs-toggle="modal" data-bs-target="#modalUploadCoverage">
                                    Upload
                                </button>
                            </div>
                            <table class="table">
                                <thead class="text-center">
                                    <tr class="align-middle">
                                        <th scope="col">Action</th>
                                        <th scope="col">
                                            {{-- <label class="switch">
                                                <input type="checkbox" ${checked}>
                                                <span class="slider round"></span>
                                            </label> --}}
                                        </th>
                                        <th scope="col">Postcode</th>
                                        <th scope="col">Delivery Type</th>
                                        <th scope="col">Courier</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-exceptional-coverage" class="text-center">
                                    <tr>
                                        <td colspan="10">Search to display data</td>
                                    </tr>
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
                                    <option value="0">NON-COD</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="courierCA" class="col-sm-4 col-form-label">Delivery Type</label>
                            <div class="col-sm-8">
                                <select name="courier" class="form-control" id="courierCA">
                                    <option value="">Select Courier</option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                    @endforeach
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

    <!-- START MODAL UPLOAD COVERAGE -->
    <div id="modalUploadCoverage" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center font-weight-bold my-2">
                        <h5><strong>Upload</strong></h5>
                    </div>
                    <div class="border border-dotted text-center m-5 mb-3" style="height: 200px" onclick="$('#upload-file').click()">
                        <div>
                            <i class="bi bi-arrow-bar-up" style="font-size: 100px; padding-top: 50px"></i>
                        </div>
                        <div>
                            Upload format .csv only
                        </div>
                    </div>
                    <input type="file" class="form-control" id="upload-file" style="display: none" accept=".csv">
                    <div class="text-center w-100 d-none px-5 mb-3" id="file-name-div">
                        <div id="file-name" class="bg-lightblue rounded p-2"></div>
                    </div>
                    <div class="small text-center">
                        <a href="/assets/template/exceptional_coverage_template.csv" download>Download Template Here</a>
                    </div>
                    <div class="small text-center text-danger">
                        Caution: Existing postcode will be replaced with new data
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="uploadExceptionalCoverage(this)" type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            $(document).ready(function() {
                $('#selectionDIV').hide();
                $('#hide-too').hide();

                //trigger state selection
                @if(Request::get('state'))
                    $('#stateCA').val({{ Request::get('state') }});
                    $('#stateCA').trigger('change');
                @endif
            });


            $('#upload-file').change(function() {
                let file = $(this)[0].files[0];
                $('#file-name').html(file.name);
                $('#file-name-div').removeClass('d-none');
            });
            // SELECTION STATE
            const selectionChange = (e) => {
                $('#search').val('');
                let id = e.value;
                window.history.pushState("", "", `/couriers/default-coverage?state=${id}`);
                if (id != "") {
                    let response = axios.post('/api/couriers/defaultCoverageState', {
                            state_id: id,
                        })
                        .then(function(response) {

                            $('#selectionDIV').show();
                            $('#hide-too').show();
                            if(response.data.cod_courier_id == null) {
                                $('#courier-cod').val('');
                            }
                            else{
                                $('#courier-cod').val(response.data.cod_courier_id);
                            }
                            if(response.data.non_cod_courier_id == null) {
                                $('#courier-non-cod').val('');
                            }
                            else{
                                $('#courier-non-cod').val(response.data.non_cod_courier_id);
                            }
                            // loadTableExceptionalCoverage();

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
                $('#tbody-exceptional-coverage').html('<tr><td colspan="100" class="text-center">Loading...</td></tr>');
                let search_data = $('#search').val();
                let state_id = $('#stateCA').val();
                let response = axios.post('/api/couriers/exceptionalCoverage', {
                        search: search_data,
                        state: state_id,
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
                if(data.length > 0){
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
                                        <input type="checkbox" onchange="updateStatusExceptionalCoverage(this, ${item.id})" ${checked}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>${item.postcode}</td>
                                <td>${item.type == '1' ? 'COD':'Non-COD'}</td>
                                <td>${item.courier.name }</td>
                            </tr>
                        `;
                    });
                }
                else{
                    html += `
                        <tr>
                            <td colspan="100" class="text-center">No Data</td>
                        </tr>
                    `;
                }
                $('#tbody-exceptional-coverage').html(html);
            }

            const saveDefaultCoverage = () => {
                let state_id = $('#stateCA').val();
                let cod = $('#courier-cod').val();
                let non_cod = $('#courier-non-cod').val();

                let response = axios.put('/api/couriers/defaultCoverageState', {
                        state_id: state_id,
                        cod_courier_id: cod,
                        non_cod_courier_id: non_cod,
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Default Coverage Saved',
                        });
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            const submitCourierCoverage = () => {
                let postcode = $('#postcodeCA').val();
                let delivery_type = $('#deliveryTypeCA').val();
                let courier = $('#courierCA').val();
                //status courier checked
                let status_courier = $('#statusCourier').is(':checked') ? 1 : 0;
                let state_id = $('#stateCA').val();
                let response = axios.post('/api/couriers/addExceptionalCoverage', {
                        postcode: postcode,
                        delivery_type: delivery_type,
                        courier: courier,
                        status_courier: status_courier,
                        state_id: state_id,
                    })
                    .then(function(response) {
                        //empty form
                        $('#postcodeCA').val('');
                        $('#deliveryTypeCA').val('');
                        $('#courierCA').val('');
                        $('#statusCourier').prop('checked', true);
                        $('#modalAddEditCourierCoverage').modal('hide');
                        loadTableExceptionalCoverage();
                    })
                    .catch(function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.response.data.message,
                        });
                    });
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
                        axios.delete('/api/couriers/exceptionalCoverage', {
                            data: {
                                id: id,
                            }
                        })
                        .then(function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Coverage Deleted',
                            });
                            $(`.tr-row-${id}`).remove();
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                    }
                })
            }

            const updateStatusExceptionalCoverage = (el, id) => {
                //status courier checked
                let status_courier = $(el).is(':checked') ? 1 : 0;
                axios.put('/api/couriers/exceptionalCoverage', {
                        id: id,
                        status_courier: status_courier,
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Status Updated',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            const uploadExceptionalCoverage = (el) => {
                el.disabled = true;
                let file = $('#upload-file')[0].files[0];
                let formData = new FormData();
                formData.append('file', file);
                formData.append('state_id', $('#stateCA').val());
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                axios.post('/api/couriers/uploadExceptionalCoverage', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Coverage Uploaded',
                        });
                        $('#modalUploadCoverage').modal('hide');
                        $('#upload-file').val('');
                        $('#file-name-div').addClass('d-none');
                        el.disabled = false;
                        loadTableExceptionalCoverage();
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        </script>

    </x-slot>
</x-layout>
