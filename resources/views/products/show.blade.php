<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <style>
        table>tbody>tr>td {
            vertical-align: middle;
        }

        .table-modal-top table>tbody>tr>td {
            font-size: 0.9rem;
            padding: .3rem;
        }

        .table-modal-top table>tbody>tr>td:first-child {
            width: 25%;
            font-weight: 600;
            white-space: nowrap;
        }

        .product-image-modal {
            max-width: 100px;
        }

        .table-modal-bottom table>tbody>tr>td,
        .table-modal-bottom table>tbody>tr>th {
            font-size: 0.9rem;
            padding: .3rem;
            padding-right: 1rem;
            padding-left: 1rem;
            border: 0.5px solid #dee2e6;
            text-align: center;
        }

        .input-group span {
            background: white;
        }

        .btn[disabled] {
            background-color: gray;
            color: white;
            border: 1px solid gray;
        }

        h6 {
            font-weight: 800;
            font-size: 1.2rem;
            text-decoration: underline;
            margin-bottom: 1rem;
        }

        #galeria {
            display: flex;
        }

        #galeria img {
            width: 100px;
            height: 100px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }

        .ts-control {
            border: none;
        }
    </style>
    <section class="section">
        <form id="product_form"
            action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}"
            method="post" enctype="multipart/form-data">
            @csrf
            <div class="card p-3">
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Company <sup class="text-danger">*</sup></div>
                    <div class="col-md-9">
                        <select name="company" id="company_id" class="form-control form-control tomsel"
                            placeholder="Please select a company" required>
                            <option value="">Please select a company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @if (isset($product->detail->company_id) && $product->detail->company_id == $company->id) selected @endif>
                                    {{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Storage Condition <sup class="text-danger">*</sup></div>
                    <div class="col-md-9">
                        <select name="storage_condition" id="company_id" class="form-control form-control tomsel"
                            placeholder="Please select storage condition" required>
                            <option value="">Please select storage condition</option>
                            @foreach (PROD_STORAGE_COND as $key => $value)
                                <option value="{{ $key }}" @if (isset($product->detail->storage_cond) && $product->detail->storage_cond == $key) selected @endif>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Product Category <sup class="text-danger">*</sup></div>
                    <div class="col-md-9">
                        <select name="product_category" id="product_category" class="form-control form-control tomsel"
                            placeholder="Please select product category" required>
                            <option value="">Please select product category</option>
                            @foreach ($product_categories as $category)
                                <option value="{{ $category->id }}" @if (isset($product->detail->category_id) && $product->detail->category_id == $category->id) selected @endif>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-4 d-none">
                    <div class="col-md-3 fw-bold">Product Sub Category </div>
                    <div class="col-md-9">
                        <select name="product_subcategory" id="product_subcategory"
                            placeholder="Please select product sub category" class="form-control form-control tomsel">
                            <option value="">Please select product sub category</option>
                            @foreach ($product_sub_categories as $subcategory)
                                <option value="{{ $subcategory->id }} "
                                    @if (isset($product->detail->sub_category_id) && $product->detail->sub_category_id == $subcategory->id) selected @endif>{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-4" id="expiry">
                    <div class="col-md-3 fw-bold">Expiry <sup class="text-danger">*</sup></div>
                    <div class="col-md-9 d-flex gap-5">
                        <div class="d-flex gap-2">
                            <input type="radio" name="expiry" value="1" id="expiry_yes"
                                @if (isset($product->detail->expiry) && $product->detail->expiry == 1) checked @endif>
                            <label for="expiry_yes">Yes</label>
                        </div>
                        <div class="d-flex gap-2">
                            <input type="radio" name="expiry" value="0" id="expiry_no"
                                @if ((isset($product->detail->expiry) && $product->detail->expiry == 0) || !isset($product->detail->expiry)) checked @endif>
                            <label for="expiry_no">No</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4 @if ((isset($product->detail->expiry) && $product->detail->expiry == 0) || !isset($product->detail->expiry)) d-none @endif" id="shelf-life">
                    <div class="col-md-3 fw-bold">Shelf Life <sup class="text-danger">*</sup></div>
                    <div class="col-md-9 d-flex flex-column gap-3">
                        <div class="d-flex gap-5">
                            <div class="d-flex gap-2">
                                <input type="radio" name="shelf_life" value="1" id="shelf_life_yes"
                                    @if (isset($product->detail->shelf_life) && $product->detail->shelf_life == 1) checked @endif>
                                <label for="shelf_life_yes">Yes</label>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="radio" name="shelf_life" value="0" id="shelf_life_no"
                                    @if ((isset($product->detail->shelf_life) && $product->detail->shelf_life == 0) || !isset($product->detail->shelf_life)) checked @endif>
                                <label for="shelf_life_no">No</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group @if ((isset($product->detail->shelf_life) && $product->detail->shelf_life == 0) || !isset($product->detail->shelf_life)) d-none @endif"
                                    id="shelf_life_days">
                                    <input type="text" name="shelf_life_period" class="form-control" placeholder=""
                                        value="{{ $product->detail->shelf_life_period ?? old('shelf_life_period') }}">
                                    <span class="input-group-text">days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4 @if ((isset($product->detail->expiry) && $product->detail->expiry == 0) || !isset($product->detail->expiry)) d-none @endif" id="qa-qc">
                    <div class="col-md-3 fw-bold">QA/QC <sup class="text-danger">*</sup></div>
                    <div class="col-md-9 d-flex gap-5">
                        <div class="d-flex gap-2">
                            <input type="radio" name="qaqc" value="1" id="qaqc_yes"
                                @if (isset($product->detail->qaqc) && $product->detail->qaqc == 1) checked @endif>
                            <label for="qaqc_yes">Yes</label>
                        </div>
                        <div class="d-flex gap-2">
                            <input type="radio" name="qaqc" value="0" id="qaqc_no"
                                @if ((isset($product->detail->qaqc) && $product->detail->qaqc == 0) || !isset($product->detail->qaqc)) checked @endif>
                            <label for="qaqc_no">No</label>
                        </div>
                    </div>
                </div>
                <hr>
                <h6>Product Details</h6>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Product Image <sup class="text-danger">*</sup></div>
                    <div class="col-md-9">
                        <!-- Hidden file input linked to the label -->
                        <input type="file" id="product-image" style="display: none;"
                            onchange="previewImage(event)" accept="image/*" name="image"
                            @if (!isset($product->detail->image_path)) required @endif>

                        <!-- Label linked to the file input -->
                        <label for="product-image" id="imageInputLabel">
                            <div id="galeria" style="cursor: pointer">
                                <img id="imagePreview" alt="Image Preview"
                                    src="{{ isset($product->detail->image_path) ? asset('storage/' . $product->detail->image_path) : asset('assets/img/no-image-placeholder.png') }}"
                                    style="max-width: 100px; max-height: 100px;">
                            </div>
                        </label>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Product Name <sup class="text-danger">*</sup></div>
                    <div class="col-md-9 d-flex gap-5">
                        <input type="text" name="name" id="name" class="form-control form-control"
                            value="{{ $product->name ?? old('name') }}" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">SKU <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Product Code"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5">
                        <input type="text" name="code" id="code" class="form-control form-control"
                            value="{{ $product->code ?? old('code') }}" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Description <sup class="text-danger">*</sup></div>
                    <div class="col-md-9 d-flex gap-5">
                        <textarea name="description" id="description" rows="4" class="form-control">{{ $product->description ?? old('description') }}</textarea>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Dimension (Product) <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Dimension for Product"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        <div class="input-group">
                            <input type="text" name="product_dimension_length" class="form-control"
                                placeholder="Length"
                                value="{{ $product->detail->length ?? old('product_dimension_length') }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="product_dimension_width" class="form-control"
                                placeholder="Width"
                                value="{{ $product->detail->width ?? old('product_dimension_width') }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="product_dimension_height" class="form-control"
                                placeholder="Height"
                                value="{{ $product->detail->height ?? old('product_dimension_height') }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Weight (Product) <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Weight for Product"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" name="product_weight" class="form-control" placeholder=""
                                value="{{ $product->detail->weight ?? old('product_weight') }}" required>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Case Pack
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="A group of products"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        <div class="input-group">
                            <input type="text" name="case_pack_carton" class="form-control" placeholder=""
                                value="{{ $product->detail->case_pack_carton ?? old('case_pack_carton') }}">
                            <span class="input-group-text">carton</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="case_pack_box" class="form-control" placeholder=""
                                value="{{ $product->detail->case_pack_box ?? old('case_pack_box') }}">
                            <span class="input-group-text">box</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="case_pack_unit" class="form-control" placeholder=""
                                value="{{ $product->detail->case_pack_unit ?? old('case_pack_unit') }}">
                            <span class="input-group-text">unit</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">TIHI <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Number of boxes stored on a layer and number of layers high that these will be stacked on the pallet"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" id="pallet-tie" name="pallet_tie" class="form-control"
                                placeholder="Tie"
                                @if (isset($product->detail->tie)) value="{{ $product->detail->tie }}" @endif
                                required>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" id="pallet-high" name="pallet_high" class="form-control"
                                placeholder="High"
                                @if (isset($product->detail->high)) value="{{ $product->detail->high }}" @endif
                                required>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Pallet Qty <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Total quantity on a pallet"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" id="pallet-qty" name="pallet_qty" class="form-control"
                                placeholder=""
                                @if (isset($product->detail->pallet_qty)) value="{{ $product->detail->pallet_qty }}" @endif
                                required>
                            <span class="input-group-text">unit</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Dimension (Carton)
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Dimension for Carton"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" name="carton_dimension_length" class="form-control"
                                placeholder="Length"
                                value="{{ $product->detail->carton_length ?? old('carton_dimension_length') }}">
                            <span class="input-group-text">m</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="carton_dimension_width" class="form-control"
                                placeholder="Width"
                                value="{{ $product->detail->carton_width ?? old('carton_dimension_width') }}">
                            <span class="input-group-text">m</span>
                        </div>
                        x
                        <div class="input-group">
                            <input type="text" name="carton_dimension_height" class="form-control"
                                placeholder="Height"
                                value="{{ $product->detail->carton_height ?? old('carton_dimension_height') }}">
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Weight (Carton) <sup class="text-danger">*</sup>
                        <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Weight for Carton"></i>
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" name="carton_weight" class="form-control" placeholder=""
                                value="{{ $product->detail->carton_weight ?? old('carton_weight') }}">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Container Load Qty
                    </div>
                    <div class="col-md-9 d-flex gap-5 align-items-center">
                        {{-- input group --}}
                        <div class="input-group">
                            <input type="text" name="container_load_qty" class="form-control" placeholder=""
                                value="{{ $product->detail->container_load_qty ?? old('container_load_qty') }}">
                            <span class="input-group-text">unit</span>
                        </div>
                    </div>
                </div>
                <hr>
                <h6>Customer</h6>

                <div class="mb-5">
                    <table class="table table-bordered w-100">
                        <thead class="text-center bg-warning text-white">
                            <tr>
                                <th>Action</th>
                                <th>Customer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="d-flex justify-content-center align-items-center">
                                    <button class="btn btn-sm btn-danger p-0 px-1 first-index"
                                        onclick="deleteRow(this)"><i class="bi bi-trash-fill"></i></button>
                                </td>
                                <td>
                                    <select name="customers[]" id="customers"
                                        class="form-control form-control-sm tomsel">
                                        <option value="" disabled>Nothing Selected</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" id="addCustomer">Add Customer</button>
                </div>
                <hr>
                <div class="d-flex flex-row-reverse gap-2">
                    <button type="button" class="btn btn-primary" style="width: 100px;"
                        id="submit_form_btn">Submit</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>

                </div>
            </div>
        </form>
    </section>


    <x-slot name="script">
        <script>
            // disable delete button on first row
            document.querySelectorAll('.first-index').forEach((btn, index) => {
                if (index == 0) {
                    btn.disabled = true;
                }
            });

            document.querySelector("#expiry_yes").addEventListener('click', function() {
                document.querySelector('#shelf-life').classList.remove('d-none');
                document.querySelector('#qa-qc').classList.remove('d-none');
            });

            document.querySelector("#expiry_no").addEventListener('click', function() {
                document.querySelector('#shelf-life').classList.add('d-none');
                document.querySelector('#qa-qc').classList.add('d-none');
            });

            document.querySelector("#shelf_life_yes").addEventListener('click', function() {
                document.querySelector('#shelf_life_days').classList.remove('d-none');
            });

            document.querySelector("#shelf_life_no").addEventListener('click', function() {
                document.querySelector('#shelf_life_days').classList.add('d-none');
            });


            // delete row
            function deleteRow(elem) {
                elem.parentElement.parentElement.remove();
            }

            document.querySelector("#product_category").addEventListener('change', function() {
                if (this.value == 2) {
                    document.querySelector('#product_subcategory').parentElement.parentElement.classList.remove(
                        'd-none');
                } else {
                    document.querySelector('#product_subcategory').value = '';
                    document.querySelector('#product_subcategory').parentElement.parentElement.classList.add('d-none');
                }
            });

            document.querySelector("#addCustomer").addEventListener('click', function() {
                let table = document.querySelector('table tbody');
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td class="d-flex justify-content-center align-items-center">
                        <button class="btn btn-sm btn-danger p-0 px-1" onclick="deleteRow(this)"><i class="bi bi-trash-fill"></i></button>
                    </td>
                    <td>
                        <select name="customers[]" id="" class="form-control form-control-sm tolsel">
                            <option value="" disabled>Nothing Selected</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </td>
                `;
                table.appendChild(row);
                document.querySelectorAll('.delete-company').forEach((btn, index) => {
                    if (index == 0) {
                        btn.disabled = true;
                    }
                });
            });

            document.querySelector("#submit_form_btn").addEventListener('click', function() {
                event.preventDefault();
                // check if required fields are filled and highlight them
                let requiredFields = document.querySelectorAll('[required]');
                let error = false;
                requiredFields.forEach((field) => {
                    if (field.value == '') {
                        field.classList.add('is-invalid');
                        error = true;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                if (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Detected!',
                        html: 'Please fill in all required fields.<br>Required fields marked with <span class="text-danger">*</span>'
                    });
                    return;
                }


                const form = document.querySelector('#product_form');
                const formData = new FormData(form);
                axios.post(form.action, formData)
                    .then(function(response) {
                        if (response.data.status == 'success') {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.data.message,
                                })
                                .then((result) => {
                                    window.location.href = "{{ route('products.index') }}";
                                });
                        }
                    })
                    .catch(function(error) {
                        let errors = error.response.data.errors;
                        let errorList = '';
                        for (const [key, value] of Object.entries(errors)) {
                            errorList += `${value} <br>`;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Detected!',
                            html: `${errorList}`,
                        });

                    });

            });

            function previewImage(event) {
                var saida = document.getElementById("product-image");
                var quantos = saida.files.length;
                for (i = 0; i < quantos; i++) {
                    var urls = URL.createObjectURL(event.target.files[i]);
                    document.getElementById("galeria").innerHTML = '<img src="' + urls + '">';
                }
            }

            document.querySelectorAll("#pallet-tie, #pallet-high").forEach((input) => {
                input.addEventListener('keyup', function() {
                    let tie = document.querySelector("#pallet-tie").value;
                    let high = document.querySelector("#pallet-high").value;
                    let qty = tie * high;
                    document.querySelector("#pallet-qty").value = qty;
                });
            });

            document.querySelectorAll('.tomsel').forEach((el) => {
                let settings = {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    hidePlaceholder: true,
                };

                new TomSelect(el, settings);
            });
        </script>
    </x-slot>

</x-layout>
