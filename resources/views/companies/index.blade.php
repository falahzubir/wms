<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">
        <section class="section">

            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <h5 class="card-title">List of Companies
                                @can('company.create')
                                    <a href="{{ route('companies.add') }}" class="">
                                        <i class="bx bx-plus-circle"></i>
                                    </a>
                                @endcan
                            </h5>
                        </div>

                        <div class="col-12">
                            {{-- list of copanies table --}}
                            <div class="table-responsive">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr>
                                            <th class="text-center">
                                                #
                                            </th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Phone</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($companies as $company)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $company->code }}</td>
                                                <td>{{ $company->name }}</td>
                                                <td>{{ $company->address . ', ' . $company->address2 . ', ' . $company->address3 }}
                                                </td>
                                                <td>{{ $company->phone }}</td>
                                                <td>
                                                    <a href="{{ route('companies.edit', $company->id) }}"
                                                        class="btn btn-warning btn-sm p-2 py-1">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    @can('permission.update')
                                                        <button class="btn btn-info btn-sm accessTokenModal "
                                                            data-bs-toggle="modal" data-bs-target="#accessTokenModal"
                                                            data-bs-id="{{ $company->id }}"
                                                            data-bs-name="{{ $company->name }}">
                                                            <i class="bx bx-key"></i>
                                                        </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </section>

        <div class="modal fade" id="accessTokenModal" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vertically Centered</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="access-token-form" method="post">
                            <div class="accordion accordion-flush mb-3" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                            aria-expanded="false" aria-controls="flush-collapseOne">
                                            DHL
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse collapse"
                                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label for="">Client ID</label>
                                                    <input type="text" id="dhl-client-id" name="dhl_client_id"
                                                        class="form-control">
                                                </div>
                                                <div class="col-6">
                                                    <label for="">Client Secret</label>
                                                    <input type="text" id="dhl-client-secret"
                                                        name="dhl_client_secret" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="accordion accordion-flush mb-3" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                            aria-expanded="false" aria-controls="flush-collapseTwo">
                                            Pos Malaysia
                                        </button>
                                    </h2>
                                    <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="flush-collapseTwo" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            {{-- <div class="row mb-1">
                                                <div class="col-6">
                                                    <label for="">Client ID</label>
                                                    <input type="text" id="dhl-client-id" name="posmalaysia_client_id"
                                                        class="form-control">
                                                </div>
                                                <div class="col-6">
                                                    <label for="">Client Secret</label>
                                                    <input type="text" id="dhl-client-secret"
                                                        name="posmalaysia_client_secret" class="form-control">
                                                </div>
                                            </div> --}}
                                            <div class="row mb-1">
                                                <div class="col-12">
                                                    <label for="">Subscribtion Code</label>
                                                    <input type="text" id="posmalaysia-subscribtion-code"
                                                        name="posmalaysia_subscribtion_code" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    {{-- <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                            Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the second item's accordion body. Let's imagine this being filled with some actual content.</div>
                                        </div>
                                        </div>
                                        <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                            Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the third item's accordion body. Nothing more exciting happening here in terms of content, but just filling up the space to make it look, at least at first glance, a bit more representative of how this would look in a real-world application.</div>
                                        </div>
                                        </div> --}}
                                </div><!-- End Accordion without outline borders -->
                                <div class="accordion accordion-flush mb-3" id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#flush-collapseThree"
                                                aria-expanded="false" aria-controls="flush-collapseThree">
                                                EMZI Express
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse"
                                            aria-labelledby="flush-collapseThree" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <div class="row mb-1">
                                                    <div class="col-12">
                                                        <label for="">Client ID</label>
                                                        <input type="text" id="emziexpress-client-id"
                                                            name="emziexpress_client_id" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input class="me-2" type="checkbox" name="sync" id="sync-token"><label
                                    for="sync-token">Sync Access Token on Save</label>
                            </div>
                        </form>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateToken">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <x-slot name="script">
        <script>
            document.querySelectorAll('.accessTokenModal').forEach(item => {
                item.addEventListener('click', event => {
                    document.querySelector('#dhl-client-id').value = '';
                    document.querySelector('#dhl-client-secret').value = '';

                    let id = item.getAttribute('data-bs-id');
                    let name = item.getAttribute('data-bs-name');
                    let form = document.querySelector('#access-token-form');
                    form.setAttribute('action', '/access-tokens/' + id);
                    document.querySelector('.modal-title').innerHTML = name;

                    axios.get('/access-tokens/' + id)
                        .then(function(response) {
                            const data = response.data.data;
                            const dhl = data.filter(item => item.type === 'dhl');
                            const emziexpress = data.filter(item => item.type === 'emzi-express');
                            const company = response.data.company;

                            document.querySelector('#dhl-client-id').value = dhl[0].client_id;
                            document.querySelector('#dhl-client-secret').value = dhl[0].client_secret;
                            document.querySelector('#posmalaysia-subscribtion-code').value = company.posmalaysia_subscribtion_code;
                            document.querySelector('#emziexpress-client-id').value = emziexpress[0].client_id;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                })
            });

            document.querySelector('#updateToken').addEventListener('click', function() {
            let form = document.querySelector('#access-token-form');
            let formData = new FormData(form);
            let id = form.getAttribute('action').split('/')[2];

            axios.post('/access-tokens/' + id, formData)
                .then(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.data.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                })
                .catch(function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.message,
                    })
                })
            });
        </script>
    </x-slot>

</x-layout>
