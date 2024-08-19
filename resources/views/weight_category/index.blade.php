<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <style>
        .rmBorder {
            border: none;
        }
        .customBtnSave {
            background-color: #7166e0;
        }
    </style>
    <section class="section">
        <div class="card p-3">
            <section id="searchForm" class="mb-3">
                <form action="" method="get">
                    <p>Filters...</p>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="flex-grow-1 form-control" name="search" placeholder="Search"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
        <div class="card p-3">
            <section id="addWeightCategoryButton" class="mb-3">
                <div>
                    @can('shipping_cost.create')
                    <button class="btn btn-primary" onclick="addWeightCategory()"><strong>+</strong></button>
                    @endcan
                </div>
            </section>
            <section id="stateGroupList">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center align-middle">
                            <th id="number-text">#</th>
                            <th>Weight Category</th>
                            <th>Weight Range</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($weightCategories as $row)
                            <tr class="text-center align-middle">
                                <td>
                                    {{ $loop->iteration + $weightCategories->firstItem() - 1 }}
                                </td>
                                <td>
                                    {{ $row->name }}
                                </td>
                                <td>
                                    <strong>{{ $row->weight_range }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('shipping_cost.delete')
                                        <button class="btn btn-danger p-1 px-2"
                                            onclick="deleteShippingCost({{ $row->id }})"><i
                                                class="bi bi-trash"></i></button>
                                        @endcan
                                        @can('shipping_cost.edit')
                                        <button class="btn btn-warning p-1 px-2"
                                            onclick="editShippingCost('{{ $row->id }}','{{ $row->name }}','{{ $row->min_weight }}','{{ $row->max_weight }}')"><i
                                                class="bi bi-pencil"></i></button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex">
                    {{ $weightCategories->links() }}
                </div>
            </section>
        </div>
    </section>

    <div class="modal fade" id="addWeightCategory" tabindex="-1" aria-labelledby="addWeightCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="addWeightCategoryLabel"><strong>Create Weight Category</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label for="category_name_add" class="form-label fs-6"><strong>Weight Category Name:</strong></label>
                        <input type="text" id="category_name_add" name="category_name" class="form-control">
                        <div class="error-message text-danger"></div>
                    </div>

                    <div>
                        <label for="weightRange_add" class="form-label fs-6"><strong>Weight Range:</strong></label>
                        <div class="input-group">
                            <input type="number" id="min_weight_add" name="min_weight" class="form-control" placeholder="0.0"/>
                            <span class="input-group-text">kg</span>

                            <div class="mx-2 align-self-center">
                                <span>-</span>
                            </div>

                            <input type="number" id="max_weight_add" name="max_weight" class="form-control" placeholder="0.0"/>
                            <span class="input-group-text">kg</span>
                        </div>
                        <div class="error-message text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="storeWeightCategory()" class="btn btn-primary customBtnSave">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editWeightCategory" tabindex="-1" aria-labelledby="editWeightCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="editWeightCategoryLabel"><strong>Edit Weight Category</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <input type="hidden" id="category_id">
                        <label for="category_name_edit" class="form-label fs-6"><strong>Weight Category Name:</strong></label>
                        <input type="text" id="category_name_edit" name="category_name" class="form-control"/>
                        <div class="error-message text-danger"></div>
                    </div>

                    <div>
                        <label for="weightRange_edit" class="form-label fs-6"><strong>Weight Range:</strong></label>
                        <div class="input-group">
                            <input type="number" id="min_weight_edit" name="min_weight" class="form-control" />
                            <span class="input-group-text">kg</span>

                            <div class="mx-2 align-self-center">
                                <span>-</span>
                            </div>

                            <input type="number" id="max_weight_edit" name="max_weight" class="form-control"/>
                            <span class="input-group-text">kg</span>
                        </div>
                        <div class="error-message text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="updateWeightCategory()" class="btn btn-primary customBtnSave">Update</button>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            let weightCategories = @json($weightCategories);
            document.querySelectorAll('.tomsel').forEach((el) => {
                let settings = {
                    maxOptions: 500,
                    plugins: {
                        remove_button: {
                            title: 'Remove this item',
                        }
                    },
                    hidePlaceholder: true,
                };
                new TomSelect(el, settings);
            });

            function addWeightCategory()
            {
                //reset all input
                let addWeightCategoryModal = $('#addWeightCategory');
                addWeightCategoryModal.find('input[name="category_name"]').val('');
                addWeightCategoryModal.find('input[name="min_weight"]').val('');
                addWeightCategoryModal.find('input[name="max_weight"]').val('');
                addWeightCategoryModal.modal('show');
            }

            function storeWeightCategory() 
            {
                let addWeightCategoryModal = $('#addWeightCategory');
                let categoryName = addWeightCategoryModal.find('input[name="category_name"]').val();
                let minWeight = addWeightCategoryModal.find('input[name="min_weight"]').val();
                let maxWeight = addWeightCategoryModal.find('input[name="max_weight"]').val();

                // Clear previous error messages
                document.querySelectorAll('#addWeightCategory .error-message').forEach(el => el.innerText = '');

                // Initialize error tracking
                let hasErrors = false;
                let errors = {};

                // Validation: Check if any fields are empty
                switch (true) {
                    case !categoryName && !minWeight && !maxWeight:
                        errors.category_name = ["*Please enter a weight category name"];
                        errors.min_weight = ["*Please enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                    case !categoryName:
                        errors.category_name = ["*Please enter a weight category name"];
                        hasErrors = true;
                        break;
                    case !minWeight:
                        errors.min_weight = ["*lease enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                    case !maxWeight:
                        errors.max_weight = ["*Please enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                }

                // If any errors, display them
                if (hasErrors) {
                    displayErrors(errors, '#addWeightCategory');
                    return; // Stop execution if validation fails
                }

                axios.post('/api/weight-category/store', {
                        category_name: categoryName,
                        min_weight: minWeight,
                        max_weight: maxWeight,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            addWeightCategoryModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Weight category created successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors, '#addWeightCategory');
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response?.data?.message || 'An unexpected error occurred.',
                                'error'
                            );
                        }
                    });
            }

            function editShippingCost(category_id, category_name, min_weight, max_weight)
            {
                let editWeightCategoryModal = $('#editWeightCategory');
                editWeightCategoryModal.find('#category_id').val(category_id);
                editWeightCategoryModal.find('#category_name_edit').val(category_name);
                editWeightCategoryModal.find('#min_weight_edit').val(min_weight/1000);
                editWeightCategoryModal.find('#max_weight_edit').val(max_weight/1000);
                editWeightCategoryModal.modal('show');
            }

            function updateWeightCategory() {
                let editWeightCategoryModal = $('#editWeightCategory');
                let id = editWeightCategoryModal.find('#category_id').val();
                let categoryName = editWeightCategoryModal.find('#category_name_edit').val().trim();
                let minWeight = editWeightCategoryModal.find('#min_weight_edit').val().trim();
                let maxWeight = editWeightCategoryModal.find('#max_weight_edit').val().trim();

                // Clear previous error messages
                document.querySelectorAll('#editWeightCategory .error-message').forEach(el => el.innerText = '');

                // Initialize error tracking
                let hasErrors = false;
                let errors = {};

                // Validation: Check if any fields are empty
                switch (true) {
                    case !categoryName && !minWeight && !maxWeight:
                        errors.category_name = ["*Please enter a weight category name"];
                        errors.min_weight = ["*Please enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                    case !categoryName:
                        errors.category_name = ["*Please enter a weight category name"];
                        hasErrors = true;
                        break;
                    case !minWeight:
                        errors.min_weight = ["*Please enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                    case !maxWeight:
                        errors.max_weight = ["*Please enter a range greater than 0.00"];
                        hasErrors = true;
                        break;
                }

                // If any errors, display them
                if (hasErrors) {
                    displayErrors(errors, '#editWeightCategory');
                    return; // Stop execution if validation fails
                }

                // If validation passes, proceed with the POST request
                axios.post('/api/weight-category/update', {
                        id: id,
                        category_name: categoryName,
                        min_weight: minWeight,
                        max_weight: maxWeight,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            editWeightCategoryModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Weight category updated successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors, '#editWeightCategory');
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response?.data?.message || 'An unexpected error occurred.',
                                'error'
                            );
                        }
                    });
            }

            function deleteShippingCost(category_id)
            {
                Swal.fire({
                    title: 'Delete weight category?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/api/weight-category/delete/${category_id}`, {
                                _token: '{{ csrf_token() }}'
                            })
                            .then(response => {
                                if (response.data.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Weight category deleted successfully!',
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    error.response.data.message,
                                    'error'
                                )
                            });
                    }
                })
            }

            const displayErrors = (errors, modalSelector) => {
                // Clear previous error messages
                document.querySelectorAll(`${modalSelector} .error-message`).forEach(el => el.innerText = '');

                for (let field in errors) {
                    let errorMessage = errors[field][0];

                    // Find the input element by name and display the error below it
                    let inputElement = document.querySelector(`${modalSelector} input[name="${field}"]`);

                    if (inputElement) {
                        let errorDiv;

                        // Special handling for min_weight and max_weight to target the error div after the input-group
                        if (field === 'min_weight' || field === 'max_weight') {
                            errorDiv = inputElement.closest('.input-group').nextElementSibling;
                        } else {
                            errorDiv = inputElement.parentElement.querySelector('.error-message');
                        }

                        if (errorDiv) {
                            errorDiv.innerText = errorMessage;
                        }
                    }
                }
            };

        </script>
    </x-slot>

</x-layout>
