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

        #product-image-modal {
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
    </style>
    <section class="section">

        <div class="card" id="filter-body">
            <div class="card-body" style="">
                <h5 class="card-title">Filters..</h5>

                <!-- No Labels Form -->
                <form class="row g-3" action="{{ url()->current() }}">
                    <div class="col-md-12">
                        <input type="text" class="form-control" placeholder="Search" name="search"
                            value="{{ old('search', Request::get('search')) }}">
                    </div>

                    {{-- <div class="" id="accordionPanelsStayOpenExample">
                        <x-additional_filter :filter_data="$filter_data" />
                    </div> --}}
                    @if (request('bucket_id') != null)
                        <input type="hidden" name="bucket_id" value="{{ request('bucket_id') }}">
                    @endif
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger" id="filter-order">Search</button>
                    </div>
                </form><!-- End No Labels Form -->

            </div>
        </div>
        <div class="card p-3">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    {{-- download csv --}}
                </div>

                @can('product.create')
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus h4 mb-0"></i>
                        <span>Add Product</span>
                    </a>
                @endcan
            </div>
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Action</th>
                            <th>Product Details</th>
                            <th>Storage Condition</th>
                            <th>Product Category</th>
                            <th>Sub Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('product.delete')
                                            <button class="btn btn-sm btn-danger p-0 px-1"
                                                onclick="destroyProduct({{ $product->id }})">
                                                <i class="bi bi-trash-fill"></i></button>
                                        @endcan
                                        @can('product.edit')
                                            <a href="{{ route('products.show', $product->id) }}"
                                                class="btn btn-sm btn-warning p-0 px-1"><i
                                                    class="bi bi-pencil-square"></i></a>
                                        @endcan
                                        <button type="button" class="btn btn-primary p-0 px-1" data-bs-toggle="modal"
                                            data-bs-target="#view_modal" onclick="getProduct({{ $product->id }})">
                                            <i class="bi bi-card-list"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="">
                                        <div class="text-primary">
                                            [{{ $product->code }}]
                                        </div>
                                        <div>
                                            {{ $product->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ isset($product->detail->storage_cond) ? PROD_STORAGE_COND[$product->detail->storage_cond] : '' }}
                                </td>
                                <td class="text-center">
                                    {{ isset($product->detail->category) ? $product->detail->category->name : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ isset($product->detail->subcategory) ? $product->detail->subcategory->name : '-' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </section>

    <div class="modal fade" id="view_modal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Product Details</h5>
                    <button type="button" class="btn-close border" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-modal-top mb-4">
                        <table class="w-100">
                            <tr>
                                <td>Product Image :</td>
                                <td><img id="product-image-modal" src="https://placehold.co/400x400" /></td>
                            </tr>
                            <tr>
                                <td>Product Name :</td>
                                <td id="modal-product-name">Neloco</td>
                            </tr>
                            <tr>
                                <td>SKU :</td>
                                <td id="modal-product-sku">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Description :</td>
                                <td id="modal-product-desc">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Company :</td>
                                <td id="modal-product-company">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Storage Condition :</td>
                                <td id="modal-product-storage-cond">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Product Category :</td>
                                <td id="modal-product-category">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Product Sub Category :</td>
                                <td id="modal-product-subcategory">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Expiry :</td>
                                <td id="modal-product-expiry">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Shelf Life :</td>
                                <td id="modal-product-shelf-life">&nbsp;Days</td>
                            </tr>
                            <tr>
                                <td>QA/QC :</td>
                                <td id="modal-product-qa-qc">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Dimension (Product) :</td>
                                <td id="modal-product-dimension">&nbsp;m<sup>3</sup> (<span></span> m x )</td>
                            </tr>
                            <tr>
                                <td>Weight (Product) :</td>
                                <td id="modal-product-weight">&nbsp;gram</td>
                            </tr>
                            <tr>
                                <td>Case Pack :</td>
                                <td id="modal-case-pack">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Pallet Qty :</td>
                                <td id="modal-pallet-qty">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>TIHI :</td>
                                <td id="modal-tihi">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Dimension (Carton) :</td>
                                <td id="modal-cartom-dimension">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Weight (Carton) :</td>
                                <td id="modal-carton-weight">&nbsp;kg</td>
                            </tr>
                            <tr>
                                <td>Container Load Qty :</td>
                                <td id="modal-container-load">&nbsp;unit</td>
                            </tr>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center table-modal-bottom">
                        <table style="width: 80%" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center p-2" style="width:5%;">No</td>
                                    <th class="text-start p-2" style="width:70%;">Customer Name</td>
                                    <th class="text-center p-2" style="width: 25%;">Code</td>
                                </tr>
                            </thead>
                            <tbody id="modal-customer-list">
                                <tr>
                                    <td colspan="3" class="text-center">No Data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <x-slot name="script">
        <script>
            const storage_url = "{{ asset('storage') }}";

            async function getProduct(product) {
                const image = document.querySelector('#product-image-modal');
                const name = document.querySelector('#modal-product-name');
                const sku = document.querySelector('#modal-product-sku');
                const desc = document.querySelector('#modal-product-desc');
                const company = document.querySelector('#modal-product-company');
                const storageCond = document.querySelector('#modal-product-storage-cond');
                const category = document.querySelector('#modal-product-category');
                const subcategory = document.querySelector('#modal-product-subcategory');
                const expiry = document.querySelector('#modal-product-expiry');
                const shelfLife = document.querySelector('#modal-product-shelf-life');
                const qaQc = document.querySelector('#modal-product-qa-qc');
                const dimension = document.querySelector('#modal-product-dimension');
                const weight = document.querySelector('#modal-product-weight');
                const casePack = document.querySelector('#modal-case-pack');
                const palletQty = document.querySelector('#modal-pallet-qty');
                const tihi = document.querySelector('#modal-tihi');
                const cartonDimension = document.querySelector('#modal-cartom-dimension');
                const cartonWeight = document.querySelector('#modal-carton-weight');
                const containerLoad = document.querySelector('#modal-container-load');
                const customerList = document.querySelector('#modal-customer-list');

                // empty all data
                image.src = 'https://placehold.co/400x400';
                name.innerHTML = '';
                sku.innerHTML = '';
                desc.innerHTML = '';
                company.innerHTML = '';
                storageCond.innerHTML = '';
                category.innerHTML = '';
                subcategory.innerHTML = '';
                expiry.innerHTML = '';
                shelfLife.innerHTML = '';
                qaQc.innerHTML = '';
                dimension.innerHTML = '';
                weight.innerHTML = '';
                casePack.innerHTML = '';
                palletQty.innerHTML = '';
                tihi.innerHTML = '';
                cartonDimension.innerHTML = '';
                cartonWeight.innerHTML = '';
                containerLoad.innerHTML = '';
                customerList.innerHTML = '<tr><td colspan="3" class="text-center">No Data</td></tr>';


                await axios.get(`/products/get-product/${product}`)
                    .then(function(response) {
                        let data = response.data.product;
                        if (data.detail == undefined) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Product detail not found! Please update product detail.',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `/products/show/${product}`;
                                }
                            })
                            return;
                        } else {


                            image.src = data.detail.image_path != undefined ?
                                `${storage_url}/${data.detail.image_path}` : 'https://placehold.co/400x400';
                            name.innerHTML = data.name;
                            sku.innerHTML = data.code;
                            desc.innerHTML = data.description;
                            company.innerHTML = data.detail.owner.name ?? '-';
                            storageCond.innerHTML = data.detail.storage_condition ?? '-';
                            category.innerHTML = data.detail.category.name ?? '-';
                            subcategory.innerHTML = data.detail.subcategory.name ?? '-';
                            expiry.innerHTML = data.detail.expiry == 1 ? 'Yes' : 'No';
                            shelfLife.innerHTML = data.detail.shelf_life_period + ' days';
                            qaQc.innerHTML = data.detail.qa_qc == 1 ? 'Yes' : 'No';
                            dimension.innerHTML = data.detail.length * data.detail.width * data.detail.height +
                                ' m<sup>3</sup> (' + data.detail.length + ' m x ' + data.detail.width + ' m x ' + data
                                .detail.height + ' m)';
                            weight.innerHTML = data.detail.weight + ' gram';
                            casePack.innerHTML = data.detail.case_pack_carton != null ? data.detail.case_pack_carton + ' carton x ' + data.detail
                                .case_pack_box + ' box x ' + data.detail.case_pack_unit + ' unit' : '-';
                            palletQty.innerHTML = data.detail.pallet_qty + ' unit';
                            tihi.innerHTML = data.detail.tie + ' x ' + data.detail.high;
                            cartonDimension.innerHTML = data.detail.carton_length != null ? data.detail.carton_length * data.detail.carton_width * data
                                .detail.carton_height + ' m<sup>3</sup> (' + data.detail.carton_length + ' m x ' + data
                            .detail.carton_width + ' m x ' + data.detail.carton_height + ' m)' : '-';
                            cartonWeight.innerHTML = data.detail.carton_weight + ' kg';
                            containerLoad.innerHTML = data.detail.container_load != null ? data.detail.container_load + ' unit' : '-' ;

                            let customer = '';
                            if (data.customers.length > 0) {
                                data.customers.forEach((item, index) => {
                                    console.log(item);
                                    customer += `<tr>
                                                <td>${index+1}</td>
                                                <td class="text-start">${item.company.name}</td>
                                                <td>${item.company.code}</td>
                                            </tr>`;
                                });
                                customerList.innerHTML = customer;
                            }

                        }


                    })
            }

            function destroyProduct(product_id) {
                Swal.fire({
                    title: 'Delete this product?',
                    // text: "You want to delete this product?",
                    text: "Order and product detail will be affected.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/products/delete/${product_id}`)
                            .then(function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Product has been deleted.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                })
                            })
                            .catch(function(error) {
                                Swal.fire(
                                    'Failed!',
                                    'Something went wrong.',
                                    'error'
                                )
                            })
                    }
                })
            }
        </script>
    </x-slot>

</x-layout>
