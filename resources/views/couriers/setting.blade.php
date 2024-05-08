@php
$title = 'List of'. ' ' . $title;
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
            @if ($type == '1')
            <div class="card" id="filter-body">
                <div class="card-body">
                    <div class="pt-5">
                        <form method="post" id="form-general-setting" method="POST">
                            @csrf
                            <div class="mb-3 row">
                                <label for="courierName" class="col-sm-2 col-form-label fw-normal">Courier Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="courier_name" class="form-control" id="courierName" value="{{ $courier['name'] }}">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="courierMinAttempt" class="col-sm-2 col-form-label fw-normal">Minimum Attempt</label>
                                <div class="col-sm-10">
                                    <input type="text" name="min_attempt" class="form-control" id="courierMinAttempt" value="{{ $courier['min_attempt'] }}">
                                </div>
                            </div>
                            <div class="p-5"></div>
                            <hr>
                            <div class="text-end pt-4">
                                <button type="button" onclick="goBack()" class="btn btn-secondary" id="filter-order">Cancel</button>
                                <button type="button" onclick="submitGeneralSetting()" class="btn btn-primary" id="filter-order">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            <!-- END GENERAL -->

            <!-- START SLA -->
            @if ($type == '2')
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
            @if ($type == '3')
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
                                <textarea class="form-control" name="postcode" id="postcode" cols="10" rows="10"></textarea>
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
                                        <input type="file" style="display: none;" id="fileInput" accept=".csv" onchange="populateTextareaPostcode(this)">
                                    </label>
                                    <br>
                                    <small class="text-muted"><a style="font-size: 10px;" href="/assets/template/postcode_template.csv">[Download template csv]</a></small>
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
                                    @foreach ($states as $id => $state)
                                        <option value="{{ $id }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="slaName" class="col-sm-4 col-form-label">SLA</label>
                            <div class="col-sm-8">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">D +</span>
                                    <input type="text" name="slaPeriod" class="form-control" id="slaPeriod">
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
                let typeParam = "{{ $type }}";

                if (typeParam == 2) {
                    loadTableSLA();
                }

                if (typeParam == 3) {
                    loadTableCourierCoverage();
                }
                uploadCSV();

            });

            const convert_errors = (errors) => {
                errors = Object.values(errors.response.data.errors);
                let html = ''
                errors.forEach((item, index) => {
                    html += `<i class="bi bi-dot"></i> ${item[0]}<br>`;
                });
                return html;
            }

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

            const goBack = () => {
                const cour_id = {{ $courier['id'] }};
                window.location.href = `/couriers/edit-page/${cour_id}`;
            }

            // LOAD TABLE SLA
            const loadTableSLA = () => {
                let search = $(`#form-sla input[name="search"]`).val();
                let response = axios.get('/api/sla/list/' + {{ $courier['id'] }}, {
                        params: {
                            search: search,
                        }
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
                        let fullPostcode = item.postcodes;
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
                x.find($('#slaName')).val('');
                x.find($('#postcode')).val('');
                x.find($('#uploadCSV')).prop('checked', false);
                if (action == 'edit') {
                    x.find($('#slaName')).val('Loading...');
                    x.find($('#postcode')).val('Loading...');
                    x.find($('.modal-footer')).html(``);

                    axios.get('/api/sla/show/'+id).then(
                        function(response) {
                            let data = response.data;
                            x.find($('#slaName')).val(data.days);
                            x.find($('#slaName')).attr('readonly', true);
                            x.find($('#postcode')).val(data.postcodes);
                        }
                    )
                    x.find($('.modal-title')).text('Edit Service-Level Aggrement (SLA)');

                    //footer button
                    x.find($('.modal-footer')).html(`
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button onclick="submitSLAFrom('edit', ${id})" type="button" class="btn btn-primary">Submit</button>
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
                        axios.delete('/api/sla', {
                                data: {
                                    id: id
                                }
                            })
                            .then(function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'SLA has been deleted.',
                                    'success'
                                );
                                $(`.tr-row-${id}`).remove();
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
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
            const submitSLAFrom = (action, id = null) => {
                // loading
                Swal.fire({
                    title: 'Please wait...',
                    html: 'Checking duplicate',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    },
                });

                axios.post('/api/sla/check-duplicate/' + {{ $courier['id'] }} + `/${id}`, {
                    sla_name: $('#slaName').val(),
                    postcode: $('#postcode').val(),
                })
                .then(function(response) {
                    if(response.data.duplicate.length == 0){
                        proceedSubmitSLAForm(action, id);
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Postcode',
                            html: `
                            <div>Some postcode already exist, are you sure to proceed?</div>
                            <div>If yes, your postcode will be remove from previous listing.</div>
                            <div class="text-danger mt-2">
                            Duplicate postcode: ${response.data.duplicate.join(', ')}
                            </div>`,
                            width: '38rem',
                            showCancelButton: true,
                            confirmButtonText: 'Proceed',
                            cancelButtonText: 'Cancel',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                proceedSubmitSLAForm(action, id, response.data.duplicate);
                            }
                        })
                        .catch(function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                html: convert_errors(error),
                            })
                        });
                    }
                }).catch(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: convert_errors(error),
                    })
                });
            }

            const proceedSubmitSLAForm = (action, id, duplicate = []) => {

                Swal.fire({
                    title: 'Please wait...',
                    html: 'Saving data',
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    },
                });
                let sla_name = $('#slaName').val();
                let postcode = $('#postcode').val();
                let url = (action == 'add' ? '/api/sla/add/' + {{ $courier['id'] }} : '/api/sla/update/' + id);
                let response = axios.post(url, {
                        sla_name: sla_name,
                        postcode: postcode,
                        duplicate: duplicate,
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'SLA added',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        loadTableSLA();
                        $('#modalAddEditSLA').modal('hide');
                    })
                    .catch(function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: error.response.data.message,
                        });
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

            const submitGeneralSetting = () => {
                let response = axios.post('/api/couriers/updateGeneralSettings', {
                        courier_id: "{{ $courier['id'] }}",
                        courier_name: $('#courierName').val(),
                        min_attempt: $('#courierMinAttempt').val(),
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'General setting updated',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    })
                    .catch(function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: convert_errors(error),
                        });
                    });
            }

            const submitCourierCoverage = () => {
               axios.post('/api/couriers/addCoverage', {
                        postcode: $('#postcodeCA').val(),
                        area: $('#areaCA').val(),
                        district: $('#districtCA').val(),
                        state: $('#stateCA').val(),
                        sla: $('#slaPeriod').val(),
                        cod: $('#codCA').is(':checked') ? 1 : 0,
                    })
                    .then(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Coverage added',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            const populateTextareaPostcode = (el) => {
                console.log(el);
                let file = el.files[0];
                let reader = new FileReader();
                reader.readAsText(file);
                reader.onload = function(event) {
                    let csv = event.target.result;
                    let data = csv.split(/\r?\n|\r/);
                    data = data.filter(function(str) {
                        return /\S/.test(str);
                    });
                    data = data.sort();
                    let csvData = data.join(',');
                    $('#postcode').val(csvData);

                };
            }
        </script>

    </x-slot>
</x-layout>
