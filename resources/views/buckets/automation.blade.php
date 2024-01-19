<x-layout :title="$title">

    <style>
        th,
        td {
            vertical-align: middle !important;
            padding: 0.5rem !important;
        }
    </style>
    <section class="section">

        <div class="row">

            <div class="card card-lg col-md-12 p-3" style="min-height: 70vh">

                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formAddModal">
                        Add Automation
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="formAddModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Automation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formAdd">
                                    @csrf
                                    <div class="row" id="ruleList">
                                        <div class="col-6 mb-2">
                                            <label class="">Company</label>
                                            <select class="form-control" name="company" id="formAddCompany"
                                                onchange="retrieveEvent()">
                                                <option value="">Not Related</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}">{{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Shipment Type</label>
                                            <select class="form-control" name="shipment_type" id="formAddShipmentType">
                                                <option value="">Not Related</option>
                                                @foreach ($shipment_types as $id => $shipment_type)
                                                    <option value="{{ $id }}">{{ $shipment_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Operational Model</label>
                                            <select class="form-control" name="operational_model"
                                                id="formAddOperationalModel">
                                                <option value="">Not Related</option>
                                                @foreach ($operational_models as $operational_model)
                                                    <option value="{{ $operational_model->id }}">
                                                        {{ $operational_model->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Platform</label>
                                            <select class="form-control" name="platform" id="formAddPlatform">
                                                <option value="">Not Related</option>
                                                @foreach ($platforms as $id => $platform)
                                                    <option value="{{ $id }}">{{ $platform }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Courier</label>
                                            <select class="form-control" name="courier" id="formAddCourier">
                                                <option value="">Not Related</option>
                                                @foreach ($couriers as $courier)
                                                    <option value="{{ $courier->id }}">{{ $courier->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Event</label>
                                            <select class="form-control" name="event" id="formAddEvent">
                                                <option value="">Select Company First</option>
                                                <!-- will be filled by retrieveEvent() js function -->
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="mb-2">
                                            <div class="d-flex gap-4">
                                                <label class="form-control border-0">Assign to Bucket: </label>
                                                <select class="form-control" name="bucket" id="formAddBucket" required>
                                                    <option value="">Select Bucket</option>
                                                    @foreach ($buckets as $bucket)
                                                        <option value="{{ $bucket->id }}">{{ $bucket->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" onclick="submitForm()" class="btn btn-primary">Save
                                    changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Modal -->
                <div class="modal fade" id="formUpdateModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Automation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formUpdate">
                                    {{-- @csrf --}}
                                    <input type="hidden" name="id" id="formUpdateId">
                                    <div class="row" id="ruleList">
                                        <div class="col-6 mb-2">
                                            <label class="">Company</label>
                                            <select class="form-control" name="company" id="formUpdateCompany"
                                                onchange="retrieveEvent()">
                                                <option value="">Not Related</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}">{{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Shipment Type</label>
                                            <select class="form-control" name="shipment_type"
                                                id="formUpdateShipmentType">
                                                <option value="">Not Related</option>
                                                @foreach ($shipment_types as $id => $shipment_type)
                                                    <option value="{{ $id }}">{{ $shipment_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Operational Model</label>
                                            <select class="form-control" name="operational_model"
                                                id="formUpdateOperationalModel">
                                                <option value="">Not Related</option>
                                                @foreach ($operational_models as $operational_model)
                                                    <option value="{{ $operational_model->id }}">
                                                        {{ $operational_model->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Platform</label>
                                            <select class="form-control" name="platform" id="formUpdatePlatform">
                                                <option value="">Not Related</option>
                                                @foreach ($platforms as $id => $platform)
                                                    <option value="{{ $id }}">{{ $platform }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Courier</label>
                                            <select class="form-control" name="courier" id="formUpdateCourier">
                                                <option value="">Not Related</option>
                                                @foreach ($couriers as $courier)
                                                    <option value="{{ $courier->id }}">{{ $courier->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="">Event</label>
                                            <select class="form-control" name="event" id="formUpdateEvent">
                                                <option value="">Select Company First</option>
                                                <!-- will be filled by retrieveEvent() js function -->
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="mb-2">
                                            <div class="d-flex gap-4">
                                                <label class="form-control border-0">Assign to Bucket: </label>
                                                <select class="form-control" name="bucket" id="formUpdateBucket"
                                                    required>
                                                    <option value="">Select Bucket</option>
                                                    @foreach ($buckets as $bucket)
                                                        <option value="{{ $bucket->id }}">{{ $bucket->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" onclick="submitUpdateForm()" class="btn btn-primary">Save
                                    changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">

                    <div id="tableContainerPlaceholder">
                        <x-loading />
                    </div>
                    <div id="tableContainer" class="table-responsive hide">
                        <table class="w-100 table table-striped table-hover table-bordered text-center"
                            id="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-start">Conditions</th>
                                    <th>Bucket</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="sort">
                                <!-- will be filled by getBuckets js function -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-slot name="script">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
            integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                getBuckets();
            });

            const buckets = {!! json_encode($buckets) !!};
            const events = {!! json_encode($events) !!};
            const operational_models = {!! json_encode($operational_models) !!};
            const shipment_types = {!! json_encode($shipment_types) !!};
            const couriers = {!! json_encode($couriers) !!};
            const companies = {!! json_encode($companies) !!};

            const tableContainerPlaceholder = document.querySelector('#tableContainerPlaceholder');
            const tableContainer = document.querySelector('#tableContainer');

            let automation_data;

            var sortArray = [];

            var fixHelperModified = function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            };

            function updateSort(table) {
                sortArray = [];
                $(table + ' tbody tr').each(function() {
                    sortArray.push({
                        id: $(this).attr('id'),
                        priority: $(this).index() + 1
                    });
                });
                return sortArray;
            }

            $(function() {
                $("#table tbody").sortable({
                        helper: fixHelperModified,
                        update: function(event, ui) {
                            sortArray = updateSort('#table');
                            axios.post(`{{ route('settings.bucket_automation_setting.update_priority') }}`, {
                                    sort: sortArray
                                })
                                .then(response => {

                                    document.querySelectorAll('#table tbody tr').forEach((tr, index) => {
                                        tr.querySelector('.priority').innerHTML = index + 1;
                                    });
                                    Swal.fire({
                                        text: 'Priority updated successfully',
                                        timer: 1000,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                    });

                                })
                                .catch(error => {
                                    console.log(error);
                                })
                        }
                    })
                    .disableSelection();
            });


            const showTable = (status) => {
                if (status == true) {
                    tableContainerPlaceholder.classList.add('hide');
                    tableContainer.classList.remove('hide');
                } else {
                    tableContainerPlaceholder.classList.remove('hide');
                    tableContainer.classList.add('hide');
                }
            }

            const getBuckets = () => {
                showTable(false);
                axios.get(`{{ route('settings.bucket_automation_setting.get') }}`)
                    .then(response => {
                        let data = response.data.data;
                        automation_data = data;
                        let list = '';
                        for (let index = 0; index < data.length; index++) {
                            sortArray.push({
                                id: data[index].id,
                                priority: index + 1
                            });
                            list += `<tr id="${data[index].id}" data-id="${data[index].id}">
                                        <td class="priority">${index + 1}</td>
                                        <td class="text-start">
                                            <ul class="mb-0">`;
                            if (data[index].company_id != null) {
                                list +=
                                    `<li>${companies.find(company => company.id == data[index].company_id).name}</li>`;
                            }
                            if (data[index].shipment_type != null) {
                                list += `<li>${data[index].shipment_type_desc}</li>`;
                            }
                            if (data[index].operational_model_id != null) {
                                list +=
                                    `<li>${operational_models.find(operational_model => operational_model.id == data[index].operational_model_id).name}</li>`;
                            }
                            if (data[index].payment_type_id != null) {
                                list += `<li>${data[index].platform}</li>`;
                            }
                            if (data[index].courier_id != null) {
                                list +=
                                    `<li>${couriers.find(courier => courier.id == data[index].courier_id).name}</li>`;
                            }
                            if (data[index].event_id != null) {
                                list += `<li>${events.find(event => event.id == data[index].event_id).event_name}</li>`;
                            }
                            list += `</ul>
                                        </td>
                                        <td>${buckets.find(bucket => bucket.id == data[index].bucket_id).name}</td>
                                        <td><span class="badge ${data[index].is_active == 1 ? 'text-bg-success' : 'text-bg-danger'}" data-id="${data[index].id}" type="button" onclick="changeStatus(this)"> ${data[index].is_active == 1 ? 'Active' : 'Inactive'}</span></td>
                                        <td>
                                            <button class="btn btn-warning p-1 px-2" type="button"><i
                                                    class="bx bx-pencil" onclick="editRow(${data[index].id})"></i></button>
                                            <button class="btn btn-danger p-1 px-2" type="button"><i class="bx bx-trash"
                                                    onclick="deleteRow(${data[index].id})"></i></button>
                                        </td>
                                    </tr>`;
                        }
                        document.querySelector('#table tbody').innerHTML = list;
                        showTable(true);
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }

            const deleteRow = (id) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`{{ route('settings.bucket_automation_setting.delete') }}`, {
                            data: {
                                id: id
                            }
                        }).then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Automation deleted successfully',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            });
                            document.querySelector(`tr[data-id="${id}"]`).remove();
                        }).catch(error => {
                            console.log(error);
                        });
                    }
                });
            }

            const submitForm = () => {
                event.preventDefault();
                document.querySelectorAll('#ruleList select').forEach(select => {
                    select.classList.remove('is-invalid');
                });
                document.querySelector('#formAddBucket').classList.remove('is-invalid');
                let error = '';
                if (document.querySelector('#formAddBucket').value == '') {
                    error += '<li>Please select a bucket!</li>'
                    document.querySelector('#formAddBucket').classList.add('is-invalid');
                }
                if (document.querySelector('#formAddCompany').value == '' &&
                    document.querySelector('#formAddShipmentType').value == '' &&
                    document.querySelector('#formAddOperationalModel').value == '' &&
                    document.querySelector('#formAddPlatform').value == '' &&
                    document.querySelector('#formAddCourier').value == '' &&
                    document.querySelector('#formAddEvent').value == '') {
                    error += '<li>Please select at least one condition!</li>'
                    document.querySelectorAll('#ruleList select').forEach(select => {
                        select.classList.add('is-invalid');
                    });
                }

                if (error != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: `<ul class="text-center">${error}</ul>`,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    return;
                }

                const form = document.querySelector('#formAdd');
                const data = new FormData(form);
                const modal = bootstrap.Modal.getInstance(document.querySelector('#formAddModal'));

                axios.post(`{{ route('settings.bucket_automation_setting.create') }}`, data)
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Automation created successfully',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        modal.hide();
                        form.reset();
                        getBuckets();

                    })
                    .catch(error => {
                        console.log(error);
                    })

            }

            const retrieveEvent = () => {
                const company = document.querySelector('#formAddCompany').value;
                const event = document.querySelector('#formAddEvent');

                if (company == '') {
                    event.innerHTML = '<option value="">Select Company First</option>';
                    return;
                }

                $list_events = '';
                events.forEach(event => {
                    if (event.company_id == company) {
                        $list_events += `<option value="${event.id}">${event.event_name}</option>`;
                    }
                });
                event.innerHTML = '<option value="">Not Related</option>';
                event.innerHTML += $list_events;
            }

            const editRow = (id) => {
                const data = automation_data.find(automation => automation.id == id);
                document.querySelector('#formUpdateId').value = data.id;
                document.querySelector('#formUpdateCompany').value = data.company_id ?? '';
                retrieveEvent();
                document.querySelector('#formUpdateShipmentType').value = data.shipment_type ?? '';
                document.querySelector('#formUpdateOperationalModel').value = data.operational_model_id ?? '';
                document.querySelector('#formUpdatePlatform').value = data.payment_type_id ?? '';
                document.querySelector('#formUpdateCourier').value = data.courier_id ?? '';
                document.querySelector('#formUpdateEvent').value = data.event_id ?? '';
                document.querySelector('#formUpdateBucket').value = data.bucket_id ?? '';

                const modal = new bootstrap.Modal(document.querySelector('#formUpdateModal'));
                modal.show();
            }

            const submitUpdateForm = () => {
                event.preventDefault();
                document.querySelectorAll('#formUpdate select').forEach(select => {
                    select.classList.remove('is-invalid');
                });
                document.querySelector('#formUpdateBucket').classList.remove('is-invalid');
                let error = '';
                if (document.querySelector('#formUpdateBucket').value == '') {
                    error += '<li>Please select a bucket!</li>'
                    document.querySelector('#formUpdateBucket').classList.add('is-invalid');
                }
                if (document.querySelector('#formUpdateCompany').value == '' &&
                    document.querySelector('#formUpdateShipmentType').value == '' &&
                    document.querySelector('#formUpdateOperationalModel').value == '' &&
                    document.querySelector('#formUpdatePlatform').value == '' &&
                    document.querySelector('#formUpdateCourier').value == '' &&
                    document.querySelector('#formUpdateEvent').value == '') {
                    error += '<li>Please select at least one condition!</li>'
                    document.querySelectorAll('#ruleList select').forEach(select => {
                        select.classList.add('is-invalid');
                    });
                }

                if (error != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: `<ul class="text-center">${error}</ul>`,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    return;
                }

                const form = document.querySelector('#formUpdate');
                const data = new FormData(form);
                const modal = bootstrap.Modal.getInstance(document.querySelector('#formUpdateModal'));

                axios.post(`{{ route('settings.bucket_automation_setting.update') }}`, data)
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Automation updated successfully',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        modal.hide();
                        getBuckets();

                    })
                    .catch(error => {
                        console.log(error);
                    })
            }

            const changeStatus = (el) => {
                el.innerHTML = '<i class="bx bx-loader bx-spin"></i>';
                const id = el.dataset.id;
                const status = el.classList.contains('text-bg-success') ? 0 : 1; // inverse the status
                axios.put(`{{ route('settings.bucket_automation_setting.update_status') }}`, {
                        id,
                        status
                    })
                    .then(response => {
                        if (status == 1) {
                            el.classList.remove('text-bg-danger');
                            el.classList.add('text-bg-success');
                            el.innerHTML = 'Active';
                        } else {
                            el.classList.remove('text-bg-success');
                            el.classList.add('text-bg-danger');
                            el.innerHTML = 'Inactive';
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })

            }
        </script>
    </x-slot>

</x-layout>
