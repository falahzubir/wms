<x-layout :title="$title" :crumbList="$crumbList">
    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
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

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <!-- No Labels Form -->
                    <form id="form-listCourier" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="text-end">
                            <button type="button" onclick="loadTable()" class="btn btn-primary" id="filter-order">Search</button>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button type="button" onclick="addCouriers()" class="btn btn-primary" id="add-courier-btn"><i class="bi bi-plus"></i>
                            Add Courier
                        </button>
                    </div>
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">Action</th>
                                <th scope="col">Status</th>
                                <th scope="col">Courier</th>
                                <th scope="col">Minimum Attempt</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="text-center">
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="showing">
                        </div>
                        <nav id="pagination-laravel">
                        </nav>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- START MODAL ADD COURIER -->
    <div id="modalAddCourier" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Courier</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="courierName" class="col-sm-4 col-form-label">Courier Name</label>
                        <div class="col-sm-8">
                            <select class="form-select" name="courier_name" id="courierName">
                                <option selected disabled>Select Courier</option>
                                @if($couriers && count($couriers) > 0)
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="minAttempt" class="col-sm-4 col-form-label">Minimum Attempt</label>
                        <div class="col-sm-8">
                            <input type="text" name="minimum_attempt" class="form-control" id="minAttempt">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL ADD COURIER -->



    <x-slot name="script">
        <script>
            $(document).ready(function() {
                loadTable();
            });

            // LOAD TABLE
            const loadTable = (page = null) => {
                let pagination = page ? '?page=' + page : '';
                let form = $('#form-listCourier').serialize();
                let response = axios.post('/api/couriers/listCourier' + pagination, {
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
                let newData = data.data;
                $('#tbody').empty();
                $('#pagination-laravel').empty();
                let html = '';
                if (newData && newData.length > 0) {
                    newData.forEach((item, index) => {
                        let checked = item.status == 1 ? 'checked' : '';
                        let classs = item.status == 1 ? 'opacity-10 bg-success border border-success' : 'opacity-10 bg-danger border border-danger';
                        let action = "{{ route('couriers.editPage','wmsemzi') }}" + item.hash_id;
                        html += `
                            <tr class="tr-row-${item.id}">
                                <td>
                                    <form action="${action}" method="get">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteCourier(${item.id})"><i class="bi bi-trash"></i></button>
                                        @csrf
                                        @method('POST')
                                        <button href="${action}" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></button>
                                    </form>
                                    
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" ${checked}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>${item.name}</td>
                                <td>${item.min_attempt}</td>
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
                paginationLaravel(data);
                showingTotal(data);
            }

            // PAGINATION
            const paginationLaravel = (data) => {
                let paginate = data.links;
                let paginationHTML = '';
                let page = '';
                paginationHTML += '<ul class="pagination">';
                if (paginate && paginate.length > 0) {
                    for (let i = 0; i < paginate.length; i++) {
                        page = paginate[i].label;
                        if (paginate[i].active) {
                            paginationHTML += '<li class="page-item active" onclick="handlePagination(' + page + ')"><span class="page-link">' + paginate[i].label + '</span></li>';
                        } else if (paginate[i].disabled) {
                            paginationHTML += '<li class="page-item disabled" onclick="handlePagination(' + page + ')"><span class="page-link">' + paginate[i].label + '</span></li>';
                        } else if (paginate[i].label === '...') {
                            paginationHTML += '<li class="page-item disabled"><span class="page-link">' + paginate[i].label + '</span></li>';
                        } else if (paginate[i].label === "&laquo; Previous") {
                            paginationHTML += `<li class="page-item" onclick="handlePagination('previous')"><span class="page-link" aria-hidden="true">‹</span></li>`;
                        } else if (paginate[i].label === "Next &raquo;") {
                            paginationHTML += `<li class="page-item" onclick="handlePagination('next')"><span class="page-link" aria-hidden="true">›</span></li>`;
                        } else {
                            paginationHTML += '<li class="page-item" onclick="handlePagination(' + page + ')"><span class="page-link">' + paginate[i].label + '</span></li>';
                        }
                    }
                }

                paginationHTML += '</ul>';
                $('#pagination-laravel').html(paginationHTML);
            }

            // HANDLE PAGINATION
            const handlePagination = (action) => {
                if (action === 'previous') {
                    if (currentPage > 1) {
                        currentPage--;
                    }
                } else if (action === 'next') {
                    if (currentPage < totalPages) {
                        currentPage++;
                    }
                } else {
                    currentPage = action;
                }
                loadTable(currentPage);
            }

            // SHOWING TOTAL
            const showingTotal = (data) => {
                let html = '';
                let from = data.from;
                let to = data.to;
                let total = data.total;
                html += `
                    Showing ${from} to ${to} of ${total} entries
                `;
                $('#showing').html(html);
            }

            // ADD COURIER
            const addCouriers = () => {
                $('#modalAddCourier').modal('show');
            }

            // DELETE COURIER
            const deleteCourier = (id) => {

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this courier?",
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