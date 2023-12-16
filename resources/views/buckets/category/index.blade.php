<x-layout :title="$title">
    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- Filter Card -->
                    <form class="row g-3" method="GET" action="{{ route('settings.bucket_category') }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="{{ old('search', Request::get('search')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="input-status" class="form-label">Status</label>
                            <select id="input-status" class="form-select" name="status">
                                <option selected value="">All</option>
                                @forelse ($statuses as $key => $status)
                                    <option value="{{ $key }}"
                                        {{ old('status', Request::get('status')) == $key ? 'selected' : '' }}>
                                        {{ $status }}</option>
                                @empty
                                    <option value="">No Status Found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">


                    <div class="card-title text-end">
                        <button class="btn btn-primary" id="add-bucket-category" data-bs-toggle="modal"
                            data-bs-target="#modal-add-bucket-category">
                            <i class="bi bi-xl bi-plus"></i> &nbsp;Add Bucket Category</button>
                    </div>


                    <!-- Default Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Status</th>
                                <th scope="col">Category Name</th>
                                <th scope="col">Bucket (s)</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse ($categories as $index => $category)
                                <tr class="align-middle">
                                    <th scope="row">{{ $categories->firstItem() + $index }}</th>
                                    <td>
                                        @if ($category->category_status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        {{ $category->categoryBuckets->pluck('bucket')->pluck('name')->implode(', ') }}
                                    </td>
                                    <td>
                                        <button onclick="editBucket({{ json_encode($category) }})" type="button"
                                            class="btn btn-warning p-0 px-1 m-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button onclick="deleteBucket({{ $category->id }})" type="button"
                                            class="btn btn-danger p-0 px-1 m-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Category Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of
                            {{ $categories->total() }} categories
                        </div>
                        {{ $categories->withQueryString()->links() }}
                    </div>
                    <!-- End Default Table Example -->
                </div>

            </div>
    </section>
    <!-- Start Modal Add Bucket Category -->
    <div class="modal fade" id="modal-add-bucket-category" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="" id="submit-category-bucket">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="category-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-status">Status :</label>
                            <input type="hidden" name="category_status" value="2">
                            <div class="col-sm-9 mt-2">
                                <label class="switch">
                                    <input type="checkbox" id="category-status" name="category_status" value="1">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-name">Category Name :</label>
                            <div class="col-sm-9 mt-2">
                                <input type="text" class="form-control" id="category-name" placeholder=""
                                    name="category_name" aria-describedby="error-category-name">
                            </div>
                        </div>
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-bucket">Bucket (s) :</label>
                            <div class="col-sm-9 mt-2">
                                <select class="form-select" id="category-bucket" name="category_bucket[]" multiple
                                    placeholder="Nothing Selected" aria-describedby="error-category-bucket">
                                    @foreach ($buckets as $bucket)
                                        <option value="{{ $bucket->id }}">{{ $bucket->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="submit-button-category" onclick="submitCategory('add')" type="button"
                            class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Modal Add Bucket Category -->

    <!-- Start Modal Edit Bucket Category -->
    <div class="modal fade" id="modal-edit-bucket-category" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="" id="submit-edit-category-bucket">
            @csrf
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="category-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category-id">
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-status">Status :</label>
                            <input type="hidden" name="category_status" value="2">
                            <div class="col-sm-9 mt-2">
                                <label class="switch">
                                    <input type="checkbox" id="category-status" name="category_status"
                                        value="1">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-name">Category Name :</label>
                            <div class="col-sm-9 mt-2">
                                <input type="text" class="form-control" id="category-name" placeholder=""
                                    name="category_name" aria-describedby="error-category-name">
                            </div>
                        </div>
                        <div class="row mb-3 p-3">
                            <label class="col-sm-3 col-form-label fw-bold" for="category-bucket">Bucket (s) :</label>
                            <div class="col-sm-9 mt-2">
                                <select class="form-select" id="category-bucket" name="category_bucket[]" multiple
                                    placeholder="Nothing Selected" aria-describedby="error-category-bucket">
                                    @foreach ($buckets as $bucket)
                                        <option value="{{ $bucket->id }}">{{ $bucket->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="submit-button-category" onclick="submitCategory('edit')" type="button"
                            class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Modal Edit Bucket Category -->

    <x-slot name="script">
        <script>
            let eventHandler = function(name) {
                console.log('attaching event', name);
            };
            let settings = {
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
            };
            document.querySelectorAll('.form-select').forEach((el)=>{
                new TomSelect(el,settings);
            });

            const submitCategory = async (type, params = null) => {
                if (type == 'add') {
                    let formData = new FormData(document.getElementById('submit-category-bucket'));
                    const response = await axios.post('/api/buckets/add-category', formData).
                    then(response => {

                            if (response.data.status == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.data.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                })
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                let errors = error.response.data.errors;
                                displayErrors(errors);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                })
                            }
                        });
                } else {
                    let formData = new FormData(document.getElementById('submit-edit-category-bucket'));
                    const response = await axios.post('/api/buckets/edit-category', formData).
                    then(response => {
                            if (response.data.status == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.data.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                })
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                let errors = error.response.data.errors;
                                displayErrors(errors);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                })
                            }
                        });
                }
            }

            const displayErrors = (errors) => {
                let message = [];
                for (let field in errors) {
                    let errorMessage = errors[field][0];
                    message.push(errorMessage);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message.join('<br>'),
                })
            }

            const deleteBucket = (id) => {
                Swal.fire({
                    title: "Delete bucket category?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    // cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete it!',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/api/buckets/delete-category`, {
                                category_id: id
                            })
                            .then(response => {
                                if (response.data.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.data.message,
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    })
                                }
                            })
                            .catch(error => {
                                if (error.response.status === 422) {
                                    let errors = error.response.data.errors;
                                    displayErrors(errors);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Something went wrong!',
                                    })
                                }
                            });
                    }
                })
            }

            function editBucket(json) {
                let editModal = document.getElementById('modal-edit-bucket-category');
                let category = JSON.parse(JSON.stringify(json));
                let bucket = category.category_buckets.map(function(bucket) {
                    return bucket.bucket_id;
                });
                let status = category.category_status;
                let name = category.category_name;
                let id = category.id;

                // Update elements using vanilla JavaScript
                // console.log(editModal)
                editModal.querySelector('#category-name').value = name;
                editModal.querySelector('#category-id').value = id;
                if (status == 1) {
                    editModal.querySelector('#category-status').checked = true;
                } else {
                    editModal.querySelector('#category-status').checked = false;
                }

                // let categoryBucket = editModal.querySelector('#category-bucket');
                let categoryBucket = editModal.querySelector('#category-bucket');

                //destroy tom select
                if (categoryBucket.tomselect)
                {
                    categoryBucket.tomselect.destroy();
                }

                for (let i = 0; i < categoryBucket.options.length; i++) {
                    if (bucket.includes(parseInt(categoryBucket.options[i].value))) {
                        categoryBucket.options[i].selected = true;
                    }
                }

                //initialize tom select
                new TomSelect(categoryBucket, settings);

                let myModal = new bootstrap.Modal(editModal);
                myModal.show()
            }
        </script>
    </x-slot>
</x-layout>
