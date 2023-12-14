<x-layout :title="$title">
    <style>
        #myBar {
            width: 10%;
            height: 30px;
            background-color: #04AA6D;
            text-align: center;
            /* To center it horizontally (if you want) */
            line-height: 30px;
            /* To center it vertically */
            color: white;
        }
    </style>
    <section class="section">
        {{-- @if (session()->has('success'))
            <x-toasts message="{{ session('success') }}" bg="success" />
        @endif --}}
        <div class="row">
            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- Filter Card -->
                    <form class="row g-3" method="GET" action="{{ route('buckets.index') }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="category-bucket" class="form-label">Bucket Category</label>
                            <select id="category-bucket" class="form-select" name="category_id[]" multiple>
                                <option selected value="">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') != null ? (in_array($category->id, request('category_id')) ? 'selected' : '') : '' }}>
                                        {{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form>

                </div>
            </div>
            @php
            $title = ['Northen Region 1 (NR1)', 'Southen Region 2 (SR2)', 'Easten Region 3 (ER3)', 'Westen Region 4 (WR4)', 'Northen Region 5 (NR5)', 'Southen Region 6 (SR6)', 'Easten Region 7 (ER7)', 'Westen Region 8 (WR8)', 'Northen Region 9 (NR9)'];
            @endphp
            <div class="col-md-4">
            <div class="card" style="height: 85%" role="button" data-bs-toggle="modal" data-bs-target="#bucket-modal"
                    onclick="add_bucket()">
                    <div class="card-body p-3 btn-ready-to-ship">
                        <div style="font-weight:bold">
                            <div class="text-center">
                                <div>
                                    <strong>
                                        <i class="bi bi-plus" style="font-size: 3rem;"></i>
                                    </strong>
                                </div>
                                <div>Add Bucket</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($buckets as $bucket)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div style="font-size:0.9rem;">
                            <div class="text-center">
                                <strong><i class="bi bi-basket"></i>&nbsp; {{ $bucket->name }} </strong>
                                <i class="bi bi-arrow-down"></i>
                            </div>
                            <hr>
                            <div class="mb-3 text-center">
                            <div>Processing: <strong><span
                                    id="pending-count">{{ $bucket->processingOrders->count() }}</span></strong>
                                </div>

                                <div>&nbsp;
                                    @if($bucket->processingOrders->sum('payment_refund'))
                                        Refund: <strong><span class="text-danger"
                                            id="refund-count">RM{{ currency($bucket->processingOrders->sum('payment_refund')) }} ({{$bucket->processingOrders->where('payment_refund', '>', 0)->count()}})</span></strong>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                {{-- modal button --}}
                                @can('picking_list.generate')
                                <button class="btn btn-primary rounded-pill" title="Generate Picking List"
                                            id="generate-pl"
                                            onclick="generate_pl({{ $bucket->id }},{{ $bucket->processingOrders->pluck('id') }})">
                                            <i class="bi bi-card-text"></i>
                                        </button>
                                @endcan
                                @can('consignment_note.generate')
                                <button class="btn btn-warning rounded-pill generate-cn" title="Generate CN"
                                            onclick="generate_cn({{ $bucket->id }}, {{ $bucket->processingOrders->pluck('id') }})"
                                            data-bucketId="{{ $bucket->id }}">
                                            <i class="bi bi-truck"></i>
                                        </button>
                                @endcan
                                <a href="/orders/processing?bucket_id={{ $bucket->id }}&status={{ ORDER_STATUS_PROCESSING }}"
                                        class="btn btn-info rounded-pill" title="Order List">
                                        <i class="bi bi-list"></i>
                                    </a>
                                    <button class="btn btn-warning rounded-pill" class="edit-bucket"
                                        title="Edit/Delete Bucket" onclick="edit_bucket(this)" data-bs-toggle="modal"
                                        data-bs-target="#bucket-modal" data-bucket-id="{{ $bucket->id }}"><i
                                            class="bi bi-pencil"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

    </section>
    <div class="modal fade" id="bucket-modal" tabindex="-1">
        <form action="/" method="POST" id="bucket-form">
            @csrf
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bucket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">

                            <div class="col-md-6">
                                <label for="bucket-name" class="form-label">Bucket Name</label>
                                <input type="text" name="name" class="form-control" id="bucket-name">
                            </div>
                            {{-- <div class="col-md-6">
                            <label for="bucket-region" class="form-label">Region / State</label>
                            <select class="form-control" id="bucket-region-edit">
                                <option selected disabled>Select region...</option>
                                <optgroup label="Regions">
                                    <option>Northen Region</option>
                                    <option>Southen Region</option>
                                    <option>Easten Region</option>
                                    <option>Westen Region</option>
                                </optgroup>
                                <optgroup label="States">
                                    <option>Perlis</option>
                                    <option>Kedah</option>
                                    <option>Penang</option>
                                    <option>Perak</option>
                                    <option>Kelantan</option>
                                    <option>Terengganu</option>
                                    <option>Pahang</option>
                                    <option>Selangor</option>
                                    <option>Negeri Sembilan</option>
                                    <option>Melaka</option>
                                    <option>Johor</option>
                                    <option>Sabah</option>
                                    <option>Sarawak</option>
                                </optgroup>
                            </select>
                        </div> --}}
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="bucket-city" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="bucket-description" rows="5">
                            </textarea>
                            </div>
                            {{-- <div class="col">
                            <div class="mb-3">
                                <label for="bucket-event" class="form-label">Event</label>
                                <select class="form-control" id="bucket-event">
                                    <option selected disabled>Select event...</option>
                                    <option>None</option>
                                    <option>Early Bird Neloco</option>
                                    <option>Shocking Sales 9.9</option>
                                    <option>Shocking Sales 11.11</option>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Auto import</label>
                            </div>
                        </div> --}}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- Delete Bucket Button -->
                        @can('bucket.delete')
                        <a href="#" class="text-danger" data-bucketId="" id="delete-bucket"><i
                                    class="bi bi-trash"></i>
                                Delete</a>
                        @endcan
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div><!-- End Vertically centered Modal-->

    <div class="modal fade" id="download-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bucket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">

                        <div class="col">
                            Bucket Name: <span id="bucket-name-dl"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Delete Bucket Button -->
                    <form action="/buckets/download_cn" method="post">
                        @csrf
                        <input type="hidden" name="bucket_id" id="bucket-id-dl">
                        <button type="submit" class="btn btn-primary">Download CN</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script>

        var eventHandler = function(name) {
            return function() {
                // console.log(name, arguments);
            };
        };

        new TomSelect("#category-bucket", {
            onInitialize: eventHandler('onInitialize'),
            onChange: eventHandler('onChange'),
            onItemAdd: eventHandler('onItemAdd'),
            plugins: {
                remove_button: {
                    title: 'Remove this item',
                }
            },
            hidePlaceholder: true,
            create: false,
        });

            // add bucket
            function add_bucket() {
                document.querySelector('#bucket-form').setAttribute('action', '/buckets/store');
                document.querySelector('.modal-title').innerHTML = 'Add Bucket';
                document.querySelector('#bucket-name').value = '';
                document.querySelector('#bucket-description').innerHTML = '';
            }

            // edit bucket
            function edit_bucket(params) {
                bucket_id = params.attributes['data-bucket-id'].value;
                document.querySelector('#bucket-form').setAttribute('action', '/buckets/update/' + bucket_id);
                document.querySelector('.modal-title').innerHTML = 'Edit bucket';
                axios.get('api/buckets/show/' + bucket_id)
                    .then(function(response) {
                        console.log(response.data);
                        document.querySelector('#bucket-name').value = response.data.name;
                        document.querySelector('#bucket-description').innerHTML = response.data.description;
                        document.querySelector('#delete-bucket').setAttribute('data-bucket-id', response.data.id);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            document.querySelector('#delete-bucket').addEventListener('click', function() {
                Swal.fire({
                    title: 'Are you sure you want to delete this bucket?',
                    text: 'Make sure this bucket is empty before deleting it',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Delete`,
                    denyButtonText: `Don't delete`,
                    showCancelButton: false,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        // print data
                        bucket_id = document.querySelector('#delete-bucket').getAttribute('data-bucket-id');
                        axios.post('/api/buckets/delete', {
                                bucket_id: bucket_id
                            })
                            .then(function(response) {
                                if (response.data.status == 'success') {
                                    Swal.fire('Deleted!', '', 'success').then((result) => {
                                        location.reload();
                                    })
                                    // location.reload();
                                } else {
                                    Swal.fire('Failed!', '', 'error')
                                }
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    } else if (result.isDenied) {
                        return;
                    }
                })
            });

            // generate cn
            async function generate_cn(bucket_id, order_ids) {
                if (order_ids.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No order in this bucket',
                    })
                    return false;
                }

                const response = await axios.post('/api/shippings/check-multiple-parcels', {
                    order_ids: order_ids,
                }).catch(function(error) {
                    console.log(error);
                });

                if (response.data.multiple_parcels == true) {
                    Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Some orders in this bucket have multiple parcels. Please generate CN manually.',
                            confirmButtonText: `Split Parcel`,
                            showCancelButton: true,
                            cancelButtonText: `Cancel`,
                        })
                        .then((result) => {
                            if (result.isConfirmed) {
                                // print data
                                window.location.href = '/orders/processing?bucket_id=' + bucket_id +
                                    '&order_id=' + response.data.order_id + '&multiple_parcels=true';
                            } else if (result.isDenied) {
                                return;
                            }
                        })

                    return false;
                }

                Swal.fire({
                    title: 'Are you sure you want to generate CN?',
                    text: 'All orders in this bucket will be included in one batch on CN.',
                    showCancelButton: true,
                    confirmButtonText: `Generate`,
                    // show loading animation
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('/api/buckets/check-empty-batch', {
                                bucket_id: bucket_id,
                            })
                            .then(function(response) {
                                //download cn
                                if (response.data.status == 'stop') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.data.message,
                                    })
                                    return false;
                                }
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    },

                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Generating shipping label...',
                            html: 'Please wait while we are generating shipping label for this bucket.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            },
                        });
                        console.log(order_ids);
                        axios.post('/api/request-cn', {
                                order_ids: order_ids,
                            })
                            .then(function(response) {
                                //download cn
                                console.log(response);
                                let text = 'CN generated successfully.';

                                if (response.data != null) {
                                    if (response.data.error != null) {
                                        text = "CN generated successfully.However has " + response.data.error;
                                    }

                                    if(response.data.all_fail){
                            if(typeof response.data.all_fail == "boolean"){
                                Swal.fire({
                                    title: 'Error!',
                                    text: "Fail to generate CN",
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                })
                            }else{
                                Swal.fire({
                                    title: 'Error!',
                                    text: "Fail to generate CN "+response.data.all_fail,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                })
                            }

                            return;
                        }
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: text,
                                    footer: `<small class="text-danger">Order with item count more than {{ MAXIMUM_QUANTITY_PER_BOX }} are ignored.</small>`,
                                    confirmButtonText: 'Download CN',
                                }).then((result) => {

                                    if (result.isConfirmed) {
                                        axios({
                                                url: '/api/download-consignment-note',
                                                method: 'POST',
                                                responseType: 'json', // important
                                                data: {
                                                    order_ids: order_ids,
                                                }
                                            })
                                            .then(function(res) {
                                                // redirect
                                                const fileName = String(res.data.download_url).split("/").pop();
                                                let a = document.createElement('a');
                                                a.target = '_blank';
                                                a.download = fileName;
                                                a.href = res.data.download_url;
                                                a.click();
                                                // window.location.href = res.data.download_url;
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success',
                                                    html: `<div>Download Request CN Successful.</div>
                                                    <div>Click <a href="${res.data.download_url}" target="_blank" download="${fileName}">here</a> if CN not downloaded.</div>`,
                                                    footer: '<small class="text-danger">Please enable popup if required</small>',
                                                    allowOutsideClick: false
                                                }).then((result) => {
                                                    location.reload();
                                                })

                                                //         const url = window.URL.createObjectURL(new Blob([response
                                                //             .data.download_url
                                                //         ]));
                                                //         const link = document.createElement('a');
                                                //         link.href = url;
                                                //         //link setattribute download and rename tu ccurent time
                                                //         let d = new Date();
                                                //         let cnname = d.getFullYear() + "-" + (d.getMonth() + 1) +
                                                //             "-" + d.getDate() + "-" + d.getHours() + d
                                                //             .getMinutes() + d.getSeconds();
                                                //         // link.setAttribute('download', `CN_${get_current_date_time()}.pdf`);
                                                //         document.body.appendChild(link);
                                                //         link.click();
                                                //         // handle success, close or download
                                                //         Swal.fire({
                                                //             title: 'Success!',
                                                //             text: "Shipment Note Downloaded.",
                                                //             icon: 'success',
                                                //         });
                                                //     })
                                                //     .catch(function(error) {
                                                //         // handle error
                                                //         console.log(error);
                                                //     })
                                                //     .then(function() {
                                                //         // always executed
                                            }).catch(() => {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    html: `Failed to generate pdf`,
                                                    allowOutsideClick: false,
                                                    icon: 'error',
                                                });

                                            })
                                        // window.location.reload();
                                    }
                                })

                            })
                            .catch(function(error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: error.response.data.error + ' Please contact admin.',
                                });
                            });
                    }
                })
            }

            function generate_pl(bucket, ids) {
                if (ids.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No order in this bucket',
                    })
                    return false;
                }
                Swal.fire({
                    title: 'Are you sure you want to generate picking list?',
                    text: 'All orders in this bucket will be included in one batch on picking list.',
                    showDenyButton: true,
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: `Generate`,
                    denyButtonText: `No. Pick individually`,
                    denyButtonColor: '#ffc107',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        axios.post('/bucket-batches/generate_pl', {
                                bucket_id: bucket,
                                order_ids: ids
                            })
                            .then(function(response) {
                                //download picking list
                                window.location.href = '/bucket-batches/download_pl/' + response.data.batch_id;

                                Swal.fire('Picking list generated', '', 'success')
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    } else if (result.isDenied) {
                        //redirect
                        window.location.href = '/orders/overall?bucket_id=' + bucket + '&status=2';
                    }
                })
            }

            // pending-count onclick
            /*
            function download_consignment(bucket_id) {
                document.querySelector('#download-modal').setAttribute('data-bucket-id', bucket_id);
                axios.get('api/show/' + bucket_id)
                    .then(function(response) {
                        document.querySelector('#bucket-name-dl').innerHTML = response.data.name;
                        document.querySelector('#bucket-id-dl').value = response.data.id;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
            */

            var i = 0;

            function move() {
                document.querySelector('#download-btn').style.display = 'none';
                document.querySelector('#myBar').style.display = 'block';
                if (i == 0) {
                    i = 1;
                    var elem = document.getElementById("myBar");
                    var width = 10;
                    var id = setInterval(frame, 10);

                    function frame() {
                        if (width >= 100) {
                            clearInterval(id);
                            i = 0;
                        } else {
                            width++;
                            elem.style.width = width + "%";
                            elem.innerHTML = width + "%";
                        }
                    }
                }
            }
        </script>
    </x-slot>

</x-layout>
