<x-layout :title="$title">

    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
        }

        #credit-note-table td{
            vertical-align: middle !important;
        }

        .drop-area {
            position: relative;
            z-index: 10;
            transition: 0.5s ease-in-out;
        }

        .drop-area.uploaded .click-upload-trigger {
            opacity: 0;
        }

        .drop-area.uploaded:hover .click-upload-trigger {
            opacity: 1;

        }

        .drop-area.uploaded:hover #output-img {
            opacity: 0.35;
        }

        #claim-table td, #claim-table th {
            vertical-align: middle !important;
        }

        .bg-orange{
            background-color: #FD7F49 !important;
        }

        .btn-teal {
            background-color: #008080 !important;
            color: #fff !important;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- No Labels Form -->
                    <form class="row g-3" action="{{ url()->current() }}">
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
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-this-month">This Month</label>

                                <input type="radio" class="btn-check" id="btn-check-last-month" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-last-month">Last Month</label>

                                <input type="radio" class="btn-check" id="btn-check-overall" name="off">
                                <label class="btn btn-outline-secondary rounded-pill mx-1"
                                    for="btn-check-overall">Overall</label>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <select id="inputState" class="form-select" name="date_type">
                                @foreach (CLAIM_DATE_TYPES as $i => $type)
                                    <option value="{{ $i }}"
                                        {{ Request::get('date_type') == $i ? 'selected' : '' }} {{ $type[1] }}>
                                        {{ $type[0] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From" name="date_from"
                                id="start-date" value="{{ Request::get('date_from') ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                                value="{{ Request::get('date_to') ?? '' }}">
                        </div>
                        <div class="" id="accordionPanelsStayOpenExample">

                            {{-- <x-additional_filter :filter_data="$filter_data" /> --}}

                        </div>
                        @if (request('bucket_id') != null)
                            <input type="hidden" name="bucket_id" value="{{ request('bucket_id') }}">
                        @endif
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form><!-- End No Labels Form -->

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        @if (in_array(ACTION_DOWNLOAD_CLAIM, $actions))
                            @can('order.download')
                                <button class="btn btn-secondary" id="download-claim"><i
                                        class="bi bi-cloud-download"></i>
                                    Download CSV</button>
                            @endcan
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="claim-table">
                            <thead class="text-center" class="bg-secondary">
                                <tr class="align-middle">
                                    <th scope="col">#</th>
                                    <th scope="col"><input type="checkbox" name="" id=""
                                            onchange="toggleCheckboxes(this, 'check-claim')"></th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Order</th>
                                    <th scope="col">Ref No.</th>
                                    @if(Route::current()->getName() == 'claims.product.index')
                                        <th scope="col">Batch No.</th>
                                    @endif
                                    @if(Route::current()->getName() == 'claims.courier.index')
                                        <th scope="col">Tracking No.</th>
                                    @endif
                                    <th scope="col">Product (s)</th>
                                    <th scope="col">Claimant</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @if ($claims->count())
                                    @foreach ($claims as $key => $claim)
                                        <tr style="font-size: 0.8rem;">
                                            <th scope="row">{{ $key + $claims->firstItem() }}</th>
                                            <td><input type="checkbox" name="check_claim[]" class="check-claim"
                                                    id="" value="{{ $claim->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-danger p-0 px-1 m-1"
                                                        onclick="delete_claim({{ $claim->id }}, '{{ order_num_format($claim->order) }}')" title="Delete Claim"
                                                        {{ $claim->status == CLAIM_STATUS_COMPLETED ? 'disabled':'' }}><i
                                                            class="bi bi-trash"></i></button>
                                                    <button class="btn btn-success p-0 px-1 m-1"
                                                        onclick="credit_note_detail({{ $claim }}, '{{ order_num_format($claim->order) }}')"><i
                                                            class="bx bxs-message-square-detail"></i></button>
                                                    @if($claim->status == CLAIM_STATUS_PENDING)
                                                    <button class="btn bg-orange text-white p-0 px-1 m-1"
                                                        onclick="credit_note_upload({{ $claim->id }})"><i
                                                            class="bx bx-check"></i></button>
                                                    @endif
                                                    @if($claim->status == CLAIM_STATUS_COMPLETED)
                                                    <button class="btn btn-teal p-0 px-1 m-1"
                                                        onclick="download_credit_note({{ $claim }})"><i
                                                            class="bi bi-printer"></i></button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center" valign="middle">
                                                <div>
                                                    <span role="button" class="order-num text-primary"
                                                        data-sales-id="{{ $claim->order->sales_id }}"
                                                        data-order-num="{{ order_num_format($claim->order) }}"
                                                        title="Double Click to Copy">
                                                        <strong>{{ order_num_format($claim->order) }}</strong>
                                                    </span>
                                                </div>
                                                <div style="font-size: 0.75rem; white-space: nowrap;"
                                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                                    data-bs-original-title="Date Inserted">
                                                    {{ date('d/m/Y H:i', strtotime($claim->created_at)) }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $claim->reference_no ?? 'N/A' }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if(Route::current()->getName() == 'claims.product.index')
                                                    @foreach ($claim->items as $item)
                                                        @foreach (json_decode($item->batch_no) as $batch)
                                                        <div><b>{{ $batch }}</b></div>
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                                @if(Route::current()->getName() == 'claims.courier.index')

                                                        <div><b>{{ implode("<br>", json_decode($claim->order->shippings->pluck('tracking_number'))) }}</b></div>

                                                @endif
                                                {{-- <strong>{{ json_decode($claim->items->pluck('batch_no'))[0] ?? 'N/A' }}</strong> --}}
                                            </td>
                                            <td class="text-center">
                                                @foreach ($claim->items as $item)
                                                <div class="d-flex justify-content-center gap-1">
                                                    <div>
                                                        {{ $item->order_item->product->name }}
                                                    </div>
                                                    <div>
                                                        <strong>[{{ $item->quantity }}]</strong>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1 flex-column">
                                                    @if($claim->claimant == CLAIMANT_TYPE_COURIER)
                                                        {{ $claim->order->courier->name }}
                                                    @elseif ($claim->claimant == CLAIMANT_TYPE_COMPANY)
                                                        @foreach ($claim->items as $item)
                                                            <div>
                                                                {{ $item->order_item->product->detail != null ? $item->order_item->product->detail->owner->name : 'EMZI HEALTH SCIENCE SDN. BHD.' }}
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @if ($claim->status == CLAIM_STATUS_PENDING)
                                                        <span class="badge bg-warning text-black">Pending</span>
                                                    @elseif ($claim->status == CLAIM_STATUS_COMPLETED)
                                                        <span class="badge bg-orange text-light">Completed</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="100%" class="text-center">
                                            <div class="alert alert-warning" role="alert">
                                                No order found!
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $claims->firstItem() }} to {{ $claims->lastItem() }} of
                            {{ $claims->total() }} orders
                        </div>
                        {{ $claims->withQueryString()->links() }}
                    </div>
                    <!-- End Default Table Example -->
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="creditNoteModal" tabindex="-1" aria-labelledby="split-parcel-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditNoteModalLabel">Credit Note Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="creditNoteModalBody">
                    <div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-4">
                                <strong>Order ID :</strong>
                            </div>
                            <div class="col-4" id="creditNoteModalSalesId">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-4">
                                <strong>Date Added :</strong>
                            </div>
                            <div class="col-4" id="creditNoteModalDateAdded">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-4">
                                <strong>Reference No :</strong>
                            </div>
                            <div class="col-4" id="creditNoteModalReferenceNo">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-2">
                            <div class="col-4">
                                <strong>Claimant :</strong>
                            </div>
                            <div class="col-4" id="creditNoteModalClaimant">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-4">
                            <div class="col-4">
                                <strong>Notes :</strong>
                            </div>
                            <div class="col-4" id="creditNoteModalNotes">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-100">
                        <table class="table table-bordered" id="credit-note-table">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Product Name</th>
                                    <th>Defect Unit</th>
                                    <th>Batch No</th>
                                    <th>Uploaded Photo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- generated by ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> <!-- end modal-->
    <!-- Modal -->
    <div class="modal fade" id="creditNoteUploadModal" tabindex="-1" aria-labelledby="creditNoteUploadModal-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditNoteUploadModalLabel">Credit Note Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="creditNoteUploadModalBody">
                    <form id="creditNoteUploadForm">
                        @csrf
                        <input type="hidden" name="claim_id" id="creditNoteUploadClaimId">
                        <div>
                            <div class="row justify-content-center mb-4">
                                <div class="col">
                                    <label for="reference_no" class="mb-1">Reference No.</label>
                                    <input type="text" class="form-control" id="reference_no">
                                </div>
                            </div>
                            <section class="drop-area">
                                <div id="file-dropping-area">
                                    <input type="file" id="files" onchange="upload_pdf(event)" name="candidate-resume-1" accept="application/pdf" style="display:none">
                                    <input type="file" name="candidate-resume-2" accept="application/pdf" id="retest" class="hide">
                                    <label class="button" for="files">
                                        <span class="click-upload-trigger">
                                            <span>
                                                <svg width="50" height="50" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill="currentColor" d="M20.987 16a.98.98 0 0 0-.039-.316l-2-6A.998.998 0 0 0 18 9h-4v2h3.279l1.667 5H5.054l1.667-5H10V9H6a.998.998 0 0 0-.948.684l-2 6a.98.98 0 0 0-.039.316C3 16 3 21 3 21a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1s0-5-.013-5zM16 7.904c.259 0 .518-.095.707-.283a1 1 0 0 0 0-1.414L12 1.5L7.293 6.207a1 1 0 0 0 0 1.414c.189.189.448.283.707.283s.518-.094.707-.283L11 5.328V12a1 1 0 0 0 2 0V5.328l2.293 2.293a.997.997 0 0 0 .707.283z"/>
                                                </svg>
                                            </span>
                                            <small class="d-block pdf-file-name">Upload format .pdf only</small>
                                        </span>
                                    </label>
                                </div>
                            </section>
                            <div class="progress d-none">
                                <div id="uploadProgress" class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div id="output" class="text-center d-none">0%</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" onclick="submit_upload_credit_note()">Submit</button>
                </div>
            </div>
        </div>
    </div> <!-- end modal-->

    <!-- Modal -->
    <div class="modal fade" id="downloadCreditNoteModal" tabindex="-1" aria-labelledby="downloadCreditNoteModal-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadCreditNoteModalLabel">Credit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="downloadCreditNoteModalBody">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> <!-- end modal-->

    @include('orders.multiple_cn_modal')
    <x-slot name="script">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
        <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
        <script>

            const initUpload = () => {
            const dropArea = document.querySelector(".drop-area");

            const active = () => dropArea.classList.add('green');

            const inactive = () => dropArea.classList.remove("green");

            const prevents = (e) => e.preventDefault();

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evName => {
                dropArea.addEventListener(evName, prevents);
            });

            ['dragenter', 'dragover'].forEach(evName => {
                dropArea.addEventListener(evName, active);
            });

            ['dragleave', 'drop'].forEach(evName => {
                dropArea.addEventListener(evName, inactive);
            })

            dropArea.addEventListener('drop', function(e) {
                handleDrop(e)
            });

        }
        const handleDrop = (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            // console.log(files[0]);
            file = files;
            // console.log(file)
            // console.log(e);
            preview_file(file[0]);
            // document.querySelector(".drop-area").classList.add("uploaded");
        }

        function upload_pdf(event) {
            let image = document.querySelector("#output-img");
            let pdf_name = document.querySelector(".pdf-file-name")
            pdf_name.innerHTML = event.target.files[0].name;
            pdf_name.style.fontWeight = "700";
            image.src = URL.createObjectURL(event.target.files[0]);
            document.querySelector(".drop-area").classList.add("uploaded");
        }

        function preview_file(file) {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                // let pdf_name = document.querySelector(".pdf-file-name");
                // let pdf_size = document.querySelector(".pdf-file-size");
                // let pdf_type = document.querySelector(".pdf-file");
                pdf_name.innerHTML = file.name;
                // pdf_size.innerHTML = file.size + " bytes";
                pdf_name.style.fontWeight = "700";
                // pdf_size.style.fontWeight = "700";
                // pdf_size.innerHTML = file.size;
                // img.src = reader.result;
            }
        }



            // download cn
            @if (in_array(ACTION_DOWNLOAD_CN, $actions))
                document.querySelector('#download-cn-btn').onclick = function() {
                    const inputElements = [].slice.call(document.querySelectorAll('.check-claim'));
                    let checkedValue = inputElements.filter(chk => chk.checked).length;
                    // sweet alert
                    if (checkedValue == 0) {
                        Swal.fire({
                            title: 'No order selected!',
                            text: "Please select at least one order to download shipping label.",
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        })
                        return;
                    }
                    let checkedOrder = [];
                    inputElements.forEach(input => {
                        if (input.checked) {
                            checkedOrder.push(input.value);
                        }
                    });
                    //confirmation to generate cn
                    Swal.fire({
                        title: 'Are you sure to download shipping label?',
                        html: `You are about to download shipping label for ${checkedValue} order(s).`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, download it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            download_cn(checkedOrder);
                        }
                    })
                }
            @endif

            // download csv
            @if (in_array(ACTION_DOWNLOAD_CLAIM, $actions))
                document.querySelector('#download-claim').onclick = function() {
                    const inputElements = [].slice.call(document.querySelectorAll('.check-claim'));
                    let checkedValue = inputElements.filter(chk => chk.checked).length;

                    if (checkedValue == 0) {
                        Swal.fire({
                            title: 'No order selected!',
                            html: `<div>Are you sure to download {{ isset($claims) ? $claims->total() : 0 }} claims(s).</div>
                            <div class="text-danger"><small>Note: This will take a while to process.</small></div>`,
                            icon: 'warning',
                            confirmButtonText: 'Download',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let checkedOrder = [];
                                inputElements.forEach(input => {
                                    if (input.checked) {
                                        checkedOrder.push(input.value);
                                    }
                                });
                                download_csv(checkedOrder);
                            }
                        })
                    } else {
                        let checkedOrder = [];
                        inputElements.forEach(input => {
                            if (input.checked) {
                                checkedOrder.push(input.value);
                            }
                        });
                        download_csv(checkedOrder);
                    }
                }
            @endif

            function reject_order(orderId) {
                Swal.fire({
                    title: 'Are you sure to reject this order?',
                    html: `You are about to reject order ${orderId}.`,
                    icon: 'warning',
                    input: 'select',
                    inputOptions: {
                        1: 'Phone',
                        2: 'Address',
                        3: 'Product(Quantity)',
                        4: 'Product(Others)',
                        5: 'Change Purchase Type'
                    },
                    inputPlaceholder: 'Select a reason',
                    inputValidator: (value) => {
                        return new Promise((resolve) => {
                            if (value != '') {
                                reject_reason = value;
                                resolve()
                            } else {
                                resolve('You need to select one of the option')
                            }
                        })
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reject it!',
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const {
                            value: reason
                        } = await Swal.fire({
                            input: 'textarea',
                            inputLabel: 'Reject Reason',
                            showCancelButton: true,
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'You need to write something!'
                                }
                            }
                        })

                        if (reason) {
                            axios.post('/api/orders/reject', {
                                    order_id: orderId,
                                    reason,
                                    reject_reason
                                })
                                .then(function(response) {
                                    // handle success, close or download
                                    if (response.status == 200) {
                                        Swal.fire({
                                                title: 'Success!',
                                                text: "Order rejected.",
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            })
                                            .then((result) => {
                                                if (result.isConfirmed) {
                                                    location.reload();
                                                }
                                            })
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: "Something went wrong.",
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        })
                                        return;
                                    }
                                })
                                .catch(function(error) {
                                    // handle error
                                    console.log(error);
                                })
                        }
                    }

                })
            }

            function get_current_date_time() {
                var today = new Date();
                var currentMonth = ('0' + (today.getMonth() + 1)).substr(-2);
                var currentDate = ('0' + today.getDate()).substr(-2);
                var date = today.getFullYear() + currentMonth + currentDate;
                var currentHours = ('0' + today.getHours()).substr(-2);
                var currentMins = ('0' + today.getMinutes()).substr(-2);
                var currentSecs = ('0' + today.getSeconds()).substr(-2);
                var time = currentHours + currentMins + currentSecs;
                var dateTime = date + '_' + time;
                return dateTime;
            }

            function download_csv(checkedOrder) {
                // const params = `{!! $_SERVER['QUERY_STRING'] ?? '' !!}`;
                // const param_obj = queryStringToJSON(params);
                if (checkedOrder.length == 0) {
                    checkedOrder = []
                }
                axios.post('/api/download-claim-csv', {
                        claim_ids: checkedOrder,
                    })
                    .then(function(response) {
                        // handle success, close or download
                        if (response != null && response.data != null) {
                            let a = document.createElement('a');
                            a.download = response.data.file_name;
                            a.target = '_blank';
                            a.href = window.location.origin + "/storage/" + response.data.file_name;
                            a.click();
                        }
                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })
            }

            document.querySelectorAll('.tomsel').forEach((el) => {
                let settings = {
                    plugins: {
                        remove_button: {
                            title: 'Remove this item',
                        }
                    },
                    hidePlaceholder: true,
                };
                new TomSelect(el, settings);
            });


            // click to copy order id, double click to copy order number
            document.querySelectorAll('.order-num').forEach(function(el) {

                el.addEventListener("dblclick", function() {
                    let sales_id = el.getAttribute('data-sales-id');
                    let order_num = el.getAttribute('data-order-num');
                    let inside_elem = el.innerHTML;
                    navigator.clipboard.writeText(sales_id);
                    //change the text of the element
                    el.innerHTML = "Copied!";
                    //change the text back after a certain time
                    setTimeout(function() {
                        el.innerHTML = inside_elem;
                    }, 1000);

                });
            });


            // check and trim if * more than 3
            document.querySelectorAll(".customer-address").forEach(function(el) {
                let address = el.innerHTML;
                let address_arr = address.split("*");
                if (address_arr.length >= 8) {
                    let new_address = address_arr.slice(0, 8).join("*");
                    el.innerHTML = new_address;
                }
            });

            function credit_note_detail(claim, salesId) {

                let modal = document.getElementById('creditNoteModal');
                let modalBody = document.querySelector('#credit-note-table .modal-body');
                document.querySelector('#creditNoteModalSalesId').innerHTML = salesId;
                document.querySelector('#creditNoteModalDateAdded').innerHTML = moment(claim.created_at)
                    .format('DD/MM/YYYY');
                document.querySelector('#creditNoteModalReferenceNo').innerHTML = claim.reference_no ?? '-';
                document.querySelector('#creditNoteModalClaimant').innerHTML = claim.claimant == 1 ? claim
                    .order.courier.name : claim.order.company.name;
                document.querySelector('#creditNoteModalNotes').innerHTML = claim.note ?? 'N/A';

                rows = '';
                for(let i = 0; i < claim.items.length; i++){
                    rows += `<tr class="text-center">
                                <td>${i+1}</td>
                                <td class="text-start">${claim.items[i].order_item.product.name}</td>
                                <td>${claim.items[i].quantity}</td>
                                <td>${JSON.parse(claim.items[i].batch_no).join('<br>')}</td>
                                <td class="d-flex justify-content-center align-items-center gap-1">`;
                    claim.items[i].img_path.split(',').forEach(img => {
                        rows += `<a href="/storage/claims/product/${img}" class="glightbox">
                                    <img src="/storage/claims/product/${img}" alt="image" width="100" />
                                    </a>`;
                    });
                    rows += `</td>
                        </tr>`;
                }

                document.querySelector('#credit-note-table tbody').innerHTML = rows;

                const lightbox = GLightbox({
                    selector: '.glightbox'
                });

                // open modal
                let myModal = new bootstrap.Modal(modal, {
                    keyboard: false
                });
                myModal.show();
            }
            function credit_note_upload(claimId) {
                // open returnModal modal pure js
                let modal = document.getElementById('creditNoteUploadModal');
                let modalLabel = document.getElementById('creditNoteUploadModalLabel');

                document.querySelector('#creditNoteUploadClaimId').value = claimId;

                // open modal
                let myModal = new bootstrap.Modal(modal, {
                    keyboard: false
                });
                myModal.show();
            }

            function parcel_cond(cond) {
                if (cond == 1) {
                    document.querySelector('#good-cond').classList.add('outline-good');
                    document.querySelector('#bad-cond').classList.remove('outline-defect');
                    document.querySelector('#good-cond-content').classList.remove('d-none');
                    document.querySelector('#bad-cond-content').classList.add('d-none');
                } else {
                    document.querySelector('#good-cond').classList.remove('outline-good');
                    document.querySelector('#bad-cond').classList.add('outline-defect');
                    document.querySelector('#good-cond-content').classList.add('d-none');
                    document.querySelector('#bad-cond-content').classList.remove('d-none');
                }
            }

            function submit_upload_credit_note(){
                let reference_no = document.querySelector('#reference_no').value;
                let file = document.querySelector('#files').files[0];
                let claim_id = document.querySelector('#creditNoteUploadClaimId').value;
                let formData = new FormData();
                let output = document.querySelector('#output');
                let upload_progress = document.querySelector('#uploadProgress');
                formData.append('reference_no', reference_no);
                formData.append('file', file);
                formData.append('claim_id', claim_id);
                formData.append('user_id', {{ Auth::user()->id }});

                output.classList.remove('d-none');
                upload_progress.parentElement.classList.remove('d-none');

                axios.post('/api/claims/upload-credit-note', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        let percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        output.innerHTML = percentCompleted + '%';
                        upload_progress.style.width = percentCompleted + '%';
                    }
                }).then(function(response) {

                    if (response.status == 200) {
                        Swal.fire({
                                title: 'Success!',
                                text: "Credit note uploaded.",
                                icon: 'success',
                                confirmButtonText: 'OK'
                            })
                            .then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                    } else {
                        output.classList.add('d-none');
                        upload_progress.parentElement.classList.add('d-none');
                        Swal.fire({
                            title: 'Error!',
                            text: "Something went wrong.",
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                        return;
                    }
                }).catch(function(error) {
                    output.classList.add('d-none');
                    upload_progress.parentElement.classList.add('d-none');
                    Swal.fire({
                        title: 'Error!',
                        text: error.response.data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                })
            }

            function download_credit_note(claim){

                //open modal
                let modal = document.getElementById('downloadCreditNoteModal');
                let modalBody = document.querySelector('#downloadCreditNoteModal .modal-body');
                let modalLabel = document.getElementById('downloadCreditNoteModalLabel');
                let claimId = document.querySelector('#creditNoteUploadClaimId').value;
                modalLabel.innerHTML = `Credit Note Reference No - ${claim.reference_no}`;
                modalBody.innerHTML = `<iframe src="/storage/claims/credit_note/${claim.img_path}" width="100%" height="500px"></iframe>`;
                // open modal
                let myModal = new bootstrap.Modal(modal, {
                    keyboard: false
                });
                myModal.show();
            }

            function delete_claim(claimId, orderNum){
                Swal.fire({
                    title: 'Are you sure to delete claim?',
                    text: "Order " + orderNum + " will be marked as return pending.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    axios.delete('/api/claims/delete', {
                        data: {
                            claim_id: claimId,
                            user_id: {{ Auth::user()->id }}
                        }
                    })
                    .then(function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                    title: 'Success!',
                                    text: "Claim deleted.",
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                })
                                .then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: "Something went wrong. Please try again.",
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                            return;
                        }
                    }).catch(function(error) {
                        Swal.fire({
                            title: 'Error!',
                            text: error.response.data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                    })
                })
            }

        </script>

    </x-slot>
</x-layout>
