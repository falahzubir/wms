@php
$title = 'List of'. ' ' . $title;
$current_uri = request()->segments();
@endphp
<x-layout :title="$title" :crumbList="$crumbList">
    <style>
        .input-group-text {
            background-color: #fff !important;
        }

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

            <!-- START GENERAL -->
            @if ($current_uri[2] == '1')
            <div class="card" id="filter-body">
                <div class="card-body">
                    <div class="pt-5">
                        <form action="" method="post">
                            <div class="mb-3 row">
                                <label for="courierName" class="col-sm-2 col-form-label fw-normal">Courier Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="courier_name" class="form-control" id="courierName">
                                </div>
                            </div>
                            <div class="p-5"></div>
                            <hr>
                            <div class="text-end pt-4">
                                <button type="button" class="btn btn-secondary" id="filter-order">Cancel</button>
                                <button type="button" class="btn btn-primary" id="filter-order">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            <!-- END GENERAL -->

            <!-- START SLA -->
            @if ($current_uri[2] == '2')
            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <!-- No Labels Form -->
                    <form id="form-sla" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="text-end">
                            <button type="button" onclick="loadTableSLA()" class="btn btn-primary" id="filter-order">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button type="button" onclick="editSLA(null,'add')" class="btn btn-sm btn-primary" id="add-courier-btn"><i class="bi bi-plus"></i>
                            Add SLA
                        </button>
                    </div>
                    <table class="table">
                        <thead class="text-center">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Action</th>
                                <th scope="col">SLA</th>
                                <th scope="col">Postcode (s)</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="text-center">
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <!-- END SLA -->

            <!-- START COURIER COVERAGE -->
            @if ($current_uri[2] == '3')
            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <!-- No Labels Form -->
                    <form id="form-courier-coverage" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="text-end">
                            <button type="button" onclick="loadTableCourierCoverage()" class="btn btn-primary" id="filter-order">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card" style="font-size:0.8rem" id="courier-coverage-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button type="button" onclick="addCoverage()" class="btn btn-sm btn-primary" id="add-courier-coverage-btn">
                            Add Coverage
                        </button>
                        <button type="button" onclick="uploadCSVCourierCoverage()" class="btn btn-sm btn-success" id="upload-csv-courier-coverage-btn">
                            Upload CSV
                        </button>
                    </div>
                    <table class="table table-bordered ">
                        <thead class="text-center table-emzi">
                            <tr class="align-middle">
                                <th scope="col">Postcode</th>
                                <th scope="col">Area</th>
                                <th scope="col">District</th>
                                <th scope="col">State</th>
                                <th scope="col">SLA</th>
                                <th scope="col">COD</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-courier-coverage" class="text-center">
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <!-- END COURIER COVERAGE -->


        </div>
    </section>

    <!-- START MODAL ADD/EDIT SLA -->
    <div id="modalAddEditSLA" class="modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    <form id="add-edit-sla" action="">
                        <div class="mb-3 row">
                            <label for="slaName" class="col-sm-4 col-form-label">SLA</label>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">D +</span>
                                    <input type="text" name="sla_name" class="form-control" id="slaName">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="minAttempt" class="col-sm-4 col-form-label">Postcode (s)</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="postcode" id="postcode" cols="10" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-8" style="padding-top: 7px;">
                                <label class="uploadCSV">
                                    <input type="checkbox" name="" id="uploadCSV">
                                    <span><small>Upload CSV</small></span>
                                </label>
                                <br>
                                <span class="uploadFileCss">
                                    <label class="btn btn-sm btn-secondary">
                                        Upload file
                                        <input type="file" style="display: none;" id="fileInput">
                                    </label>
                                    <br>
                                    <small class="text-muted"><a style="font-size: 10px;" href="#">[Download template csv]</a></small>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL ADD/EDIT SLA -->

    <!-- START MODAL ADD/EDIT COVERAGE -->
    <div id="modalAddEditCourierCoverage" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
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
                            <label for="areaCA" class="col-sm-4 col-form-label">Area</label>
                            <div class="col-sm-8">
                                <input type="text" name="area" class="form-control" id="areaCA">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="districtCA" class="col-sm-4 col-form-label">District</label>
                            <div class="col-sm-8">
                                <input type="text" name="district" class="form-control" id="districtCA">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="stateCA" class="col-sm-4 col-form-label">State</label>
                            <div class="col-sm-8">
                                <select name="state" class="form-control" id="stateCA">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="slaName" class="col-sm-4 col-form-label">SLA</label>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">D +</span>
                                    <input type="text" name="sla_name" class="form-control" id="slaName">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label for="codCA" class="col-sm-4 col-form-label">COD available?</label>
                            <div class="col-sm-8" style="padding-top: 7px;">
                                <label>
                                    <input type="checkbox" name="codAvailable" id="codCA">
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
                let typeParam = "{{ $current_uri[2] }}";

                if (typeParam == 2) {
                    loadTableSLA();
                }

                if (typeParam == 3) {
                    loadTableCourierCoverage();
                }
                uploadCSV();

            });

            const fileInput = document.getElementById('fileInput');

            fileInput.addEventListener('change', (event) => {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    // You can display the selected file name or perform any other actions here
                    console.log('Selected file:', selectedFile.name);
                } else {
                    console.log('No file selected');
                }
            });

            // LOAD TABLE SLA
            const loadTableSLA = () => {
                let form = $('#form-sla').serialize();
                let response = axios.post('/api/couriers/listSLA', {
                        form: form,
                    })
                    .then(function(response) {
                        renderTableSLA(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // RENDER TABLE SLA
            const renderTableSLA = (data) => {
                $('#tbody').empty();
                let html = '';
                if (data && data.length > 0) {
                    let x = 1;
                    data.forEach((item, index) => {
                        let fullPostcode = item.postcode;
                        let truncatedPostcode = fullPostcode.substring(0, 47);
                        let isTruncated = fullPostcode.length > 47;

                        let readMoreLink = isTruncated ? `<a href="#" class="read-id-${item.id}" onclick="readMore(${item.id})">Read More...</a>` : '';
                        html += `
                            <tr class="tr-row-${item.id}">
                                <td>
                                    ${x++}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteSLA(${item.id})"><i class="bi bi-trash"></i></button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editSLA(${item.id},'edit')"><i class="bi bi-pencil-square"></i></button>
                                </td>
                                <td>${item.sla_name}</td>
                                <td>
                                    <span class="postcode-truncated-${item.id}">
                                    ${truncatedPostcode}
                                    </span>
                                    <br>
                                    <span>
                                    ${readMoreLink}
                                    </span>
                                    <span class="postcode-full-${item.id}" style="display:none;">
                                        ${fullPostcode}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="4" class="text-center">No data found</td>
                        </tr>
                    `;
                }
                $('#tbody').html(html);
            }

            // EDIT SLA
            const editSLA = (id, action) => {
                let x = $('#modalAddEditSLA');

                if (action == 'edit') {
                    x.find($('.modal-title')).text('Edit Service-Level Aggrement (SLA)');
                    x.find($('#slaName')).val('2');
                    x.find($('#postcode')).val('08000,08001,08002');
                    //footer button
                    x.find($('.modal-footer')).html(`
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button onclick="submitSLAFrom('edit')" type="button" class="btn btn-primary">Submit</button>
                    `);

                } else {
                    x.find($('.modal-title')).text('Add Service-Level Aggrement (SLA)');
                    //footer button
                    x.find($('.modal-footer')).html(`
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button onclick="submitSLAFrom('add')" type="button" class="btn btn-primary">Submit</button>
                    `);
                }
                x.modal('show');
            }

            // DELETE SLA
            const deleteSLA = (id) => {

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this SLA?",
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

            // READ MORE
            const readMore = (id) => {
                event.preventDefault();
                $(`.postcode-truncated-${id}`).hide();
                $(`.postcode-full-${id}`).show();
                //change to read less
                $(`.read-id-${id}`).attr('onclick', `readLess(${id})`);
                $(`.read-id-${id}`).text('Read Less...');
                $(`.read-id-${id}`).insertAfter($(`.postcode-full-${id}`));
            }

            // READ LESS
            const readLess = (id) => {
                event.preventDefault();
                $(`.postcode-truncated-${id}`).show();
                $(`.postcode-full-${id}`).hide();
                //change to read more
                $(`.read-id-${id}`).attr('onclick', `readMore(${id})`);
                $(`.read-id-${id}`).text('Read More...');
            }

            // SHOW/HIDE UPLOAD FILE
            const uploadCSV = () => {
                $('.uploadFileCss').hide();
                let isChecked = $('#uploadCSV');

                isChecked.on('change', function() {
                    if (isChecked.is(':checked')) {
                        console.log('checked');
                        $('.uploadFileCss').show();
                    } else {
                        console.log('unchecked');
                        $('.uploadFileCss').hide();
                    }
                });
            }

            // SUBMIT SLA FORM
            const submitSLAFrom = (action) => {
                let form = $('#add-edit-sla').serialize();
                let url = action == 'add' ? '/api/couriers/addSLA' : '/api/couriers/editSLA';
                let response = axios.post(url, {
                        form: form,
                    })
                    .then(function(response) {
                        response.data.status = 'failed'
                        if (response.data.status == 'success') {
                            //
                        } else {
                            //duplicate postcode
                            Swal.fire({
                                icon: 'error',
                                title: 'Duplicate Postcode',
                                html: `Some postcode already exist, are you sure to proceed? 
                                <br>
                                If yes, your postcode will be remove from previous listing. 
                                <br><br>
                                <span class="text-danger">
                                Duplicate postcode: 08000,08001,08002
                                </span>`,
                                width: '38rem',

                            })
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // LOAD TABLE COURIER COVERAGE
            const loadTableCourierCoverage = () => {
                let form = $('#form-courier-coverage').serialize();
                let response = axios.post('/api/couriers/listCoverage', {
                        form: form,
                    })
                    .then(function(response) {
                        renderTableCourierCoverage(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // RENDER TABLE COURIER COVERAGE
            const renderTableCourierCoverage = (data) => {
                $('#tbody-courier-coverage').empty();
                let html = '';
                if (data && data.length > 0) {
                    let x = 1;
                    data.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${item.postcode}</td>
                                <td>${item.area}</td>
                                <td>${item.district}</td>
                                <td>${item.state}</td>
                                <td>${item.sla}</td>
                                <td>${item.cod}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="6" class="text-center">No data found</td>
                        </tr>
                    `;
                }
                $('#tbody-courier-coverage').html(html);
            }

            // ADD COVERAGE
            const addCoverage = () => {
                let y = $('#modalAddEditCourierCoverage');
                y.find($('.modal-title')).text('Add Coverage');
                y.modal('show');
            }
        </script>

    </x-slot>
</x-layout>