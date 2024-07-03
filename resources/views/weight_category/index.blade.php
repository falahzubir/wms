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
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="flex-grow-1 form-control" name="search" placeholder="Search"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mt-3">
                            <label class="fw-bold pb-2" for="weight-category">Weight Category</label>
                            <select name="weight_category[]" class="form-control tomsel" id="weight-category" multiple>
                                <option value="">Select Weight Category</option>
                                @foreach ($weightCategories as $category)
                                <option value="{{ $category->id }}" @if (in_array($category->id, request('weight_category', []))) selected @endif>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fw-bold pb-2" for="courier">Courier</label>
                            <select name="courier[]" class="form-control tomsel" id="courier" multiple>
                                <option value="">Select Courier</option>
                                @foreach ($couriers as $courier)
                                <option value="{{ $courier->id }}" @if (in_array($courier->id, request('courier', []))) selected @endif>
                                    {{ $courier->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fw-bold pb-2" for="state_group">State Group</label>
                            <select name="state_group_id[]" class="form-control tomsel" id="state_group" multiple>
                                <option value="">Select State Group</option>
                                @foreach ($stateGroups as $stateGroup)
                                <option value="{{ $stateGroup->id }}" @if (in_array($stateGroup->id, request('state_group_id', []))) selected @endif>
                                    {{ $stateGroup->name }}
                                </option>
                                @endforeach
                            </select>
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
                    @can('weight_category.create')
                    <button class="btn btn-primary" onclick="addWeightCategory()"><strong>+</strong></button>
                    @endcan
                </div>
            </section>
            <section id="stateGroupList">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center align-middle">
                            <th id="number-text">#</th>
                            <th>Weight Category Name</th>
                            <th>Courier</th>
                            <th>State Group</th>
                            <th>Weight Range</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shippingCosts as $shipping_cost)
                            <tr class="text-center align-middle">
                                <td>
                                    {{ $loop->iteration + $shippingCosts->firstItem() - 1 }}
                                </td>
                                <td>
                                    {{ $shipping_cost->weight_category->name }}
                                </td>
                                <td>
                                    {{ $shipping_cost->couriers->name }}
                                </td>
                                <td>
                                    {{ $shipping_cost->state_groups->name }}
                                </td>
                                <td>
                                    {{ $shipping_cost->weight_category->weight_range }}
                                </td>
                                <td>
                                    RM {{ number_format($shipping_cost->price/1000,2) }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('weight_category.delete')
                                        <button class="btn btn-danger p-1 px-2"
                                            onclick="deleteShippingCost({{ $shipping_cost->id }})"><i
                                                class="bi bi-trash"></i></button>
                                        @endcan
                                        @can('weight_category.edit')
                                        <button class="btn btn-warning p-1 px-2"
                                            onclick="editShippingCost('{{ $shipping_cost->id }}','{{ $shipping_cost->couriers->id }}','{{ $shipping_cost->state_groups->id }}','{{ $shipping_cost->weight_category->id }}', '{{ $shipping_cost->price }}')"><i
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
                    {{ $shippingCosts->links() }}
                </div>
            </section>
        </div>
    </section>

    <div class="modal fade" id="addWeightCategory" tabindex="-1" aria-labelledby="addWeightCategoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="addWeightCategoryLabel"><strong>Create Shipping Cost</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label for="weightCategoryID" class="form-label fs-6"><strong>Weight Category Name:</strong></label>
                        <select class="form-control" id="weightCategoryID" name="weight_category_id" onchange="selectionWeightID(this,'add')">
                            <option value="">Select Weight Category</option>
                            @foreach ($weightCategories as $weightCategory)
                            <option value="{{ $weightCategory->id }}">
                                {{ $weightCategory->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="courier-filterr" class="form-label fs-6"><strong>Courier:</strong></label>
                        <select name="courier-filter" class="form-control" id="courier-filter">
                            <option value="">Select Courier</option>
                            @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">
                                {{ $courier->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stateGroup-filter" class="form-label fs-6"><strong>State Group:</strong></label>
                        <select name="stateGroup-filter" class="form-control" id="stateGroup-filter">
                            <option value="">Select State Group</option>
                            @foreach ($stateGroups as $stateGroup)
                            <option value="{{ $stateGroup->id }}">
                                {{ $stateGroup->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="weightRange" class="form-label fs-6"><strong>Weight Range:</strong></label>
                        <div class="input-group">
                            <input type="text" name="min_weight" class="form-control" placeholder="0.0"/ disabled/>
                            <span class="input-group-text">kg</span>

                            <div class="mx-2 align-self-center">
                                <span>-</span>
                            </div>

                            <input type="text" name="max_weight" class="form-control" placeholder="0.0"/ disabled/>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div>
                        <label for="price" class="form-label fs-6"><strong>Price:</strong></label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="text" class="form-control" placeholder="0.00"/ id="price">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="storeWeightCategory()" class="btn btn-primary customBtnSave">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editWeightCategory" tabindex="-1" aria-labelledby="editWeightCategoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="editWeightCategoryLabel"><strong>Update Shipping Cost</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <input type="hidden" id="shippingCostID" name="shipping_cost_id">
                        <label for="weightCategoryID" class="form-label fs-6"><strong>Weight Category Name:</strong></label>
                        <select class="form-control" id="weightCategoryID" name="weight_category_id" onchange="selectionWeightID(this,'edit')">
                            <option value="">Select Weight Category</option>
                            @foreach ($weightCategories as $weightCategory)
                            <option value="{{ $weightCategory->id }}">
                                {{ $weightCategory->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="courier-filterr" class="form-label fs-6"><strong>Courier:</strong></label>
                        <select name="courier-filter" class="form-control" id="courier-filter">
                            <option value="">Select Courier</option>
                            @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">
                                {{ $courier->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stateGroup-filter" class="form-label fs-6"><strong>State Group:</strong></label>
                        <select name="stateGroup-filter" class="form-control" id="stateGroup-filter">
                            <option value="">Select State Group</option>
                            @foreach ($stateGroups as $stateGroup)
                            <option value="{{ $stateGroup->id }}">
                                {{ $stateGroup->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="weightRange" class="form-label fs-6"><strong>Weight Range:</strong></label>
                        <div class="input-group">
                            <input type="text" name="min_weight" class="form-control" placeholder="0.0"/ disabled/>
                            <span class="input-group-text">kg</span>

                            <div class="mx-2 align-self-center">
                                <span>-</span>
                            </div>

                            <input type="text" name="max_weight" class="form-control" placeholder="0.0"/ disabled/>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div>
                        <label for="price" class="form-label fs-6"><strong>Price:</strong></label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="text" class="form-control" placeholder="0.00"/ id="price">
                        </div>
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
                addWeightCategoryModal.find('#weightCategoryID').val('');
                addWeightCategoryModal.find('#courier-filter').val('');
                addWeightCategoryModal.find('#stateGroup-filter').val('');
                addWeightCategoryModal.find('input[name="min_weight"]').val('');
                addWeightCategoryModal.find('input[name="max_weight"]').val('');
                addWeightCategoryModal.find('#price').val('');
                addWeightCategoryModal.modal('show');
            }

            function storeWeightCategory()
            {
                let addWeightCategoryModal = $('#addWeightCategory');
                let weight_category_id = addWeightCategoryModal.find('#weightCategoryID').val();
                let courier = addWeightCategoryModal.find('#courier-filter').val();
                let stateGroup = addWeightCategoryModal.find('#stateGroup-filter').val();
                let minWeight = addWeightCategoryModal.find('input[name="min_weight"]').val();
                let maxWeight = addWeightCategoryModal.find('input[name="max_weight"]').val();
                let price = addWeightCategoryModal.find('#price').val();

                axios.post('/api/weight-category/store', {
                        weight_category_id: weight_category_id,
                        courier_id: courier,
                        state_group_id: stateGroup,
                        min_weight: minWeight,
                        max_weight: maxWeight,
                        price: price,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            addWeightCategoryModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Shipping cost created successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors);
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response.data.message,
                                'error'
                            )
                        }
                    });
            }

            function editShippingCost(id, courier_id, state_group_id, weight_category_id, price)
            {
                let editWeightCategoryModal = $('#editWeightCategory');
                editWeightCategoryModal.find('#shippingCostID').val(id);
                editWeightCategoryModal.find('#weightCategoryID').val(weight_category_id);
                editWeightCategoryModal.find('#courier-filter').val(courier_id);
                editWeightCategoryModal.find('#stateGroup-filter').val(state_group_id);
                //run onchange function to set min and max weight
                selectionWeightID(editWeightCategoryModal.find('#weightCategoryID')[0],'edit');
                editWeightCategoryModal.find('#price').val((price/1000).toFixed(2));
                editWeightCategoryModal.modal('show');
            }

            function updateWeightCategory()
            {
                let editWeightCategoryModal = $('#editWeightCategory');
                let id = editWeightCategoryModal.find('#shippingCostID').val();
                let weight_category_id = editWeightCategoryModal.find('#weightCategoryID').val();
                let courier = editWeightCategoryModal.find('#courier-filter').val();
                let stateGroup = editWeightCategoryModal.find('#stateGroup-filter').val();
                let minWeight = editWeightCategoryModal.find('input[name="min_weight"]').val();
                let maxWeight = editWeightCategoryModal.find('input[name="max_weight"]').val();
                let price = editWeightCategoryModal.find('#price').val();

                axios.post('/api/weight-category/update', {
                        id: id,
                        weight_category_id: weight_category_id,
                        courier_id: courier,
                        state_group_id: stateGroup,
                        min_weight: minWeight,
                        max_weight: maxWeight,
                        price: price,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            editWeightCategoryModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Shipping cost updated successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors);
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response.data.message,
                                'error'
                            )
                        }
                    });
            }

            function deleteShippingCost(id)
            {
                Swal.fire({
                    title: 'Delete shipping cost?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/api/weight-category/delete/${id}`, {
                                _token: '{{ csrf_token() }}'
                            })
                            .then(response => {
                                if (response.data.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Shipping cost deleted successfully!',
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

            function selectionWeightID(el,type)
            {
                let modalER = type == 'add' ? $('#addWeightCategory') : $('#editWeightCategory');
                let weightCategory = weightCategories.find(weightCategory => weightCategory.id == el.value);

                if(typeof weightCategory == 'undefined') {
                    //reset all input
                    modalER.find('input[name="min_weight"]').val('');
                    modalER.find('input[name="max_weight"]').val('');
                    return;
                }

                let minWeight = (weightCategory.min_weight/1000).toFixed(2);
                let maxWeight = (weightCategory.max_weight/1000).toFixed(2);
                modalER.find('input[name="min_weight"]').val(minWeight);
                modalER.find('input[name="max_weight"]').val(maxWeight);
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

        </script>
    </x-slot>

</x-layout>
