<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">
        <section class="section">

            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <h5 class="card-title">List of Operational Models</h5>
                        </div>

                        <div class="col-12">
                            {{-- list of copanies table --}}
                            <div class="table-responsive">
                                <table class="table table-striped" id="table1">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="text-center">
                                                #
                                            </th>
                                            <th>Name</th>
                                            <th>Short Code</th>
                                            <th>Default Company</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($op_models as $model)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $model->name }}</td>
                                                <td class="text-center">{{ $model->short_name }}</td>
                                                <td class="text-center">{{ $model->company->name ?? 'None' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-info p-0 px-1 opModelModal" data-bs-toggle="modal" data-bs-target="#opmodel" data-bs-op-id="{{ $model->id }}">
                                                        <i class="bx bx-pencil"></i></button>
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

        <div class="modal fade" id="opmodel" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Operation Model</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="opmodel-form" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="form_op_name" value="" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="short_name" class="form-label">Short Name</label>
                                <input type="text" name="short_name" class="form-control" id="form_op_short_name" value="">
                            </div>
                            <div class="mb-3">
                                <label for="default_company" class="form-label">Default Company</label>
                                <select name="default_company" id="form_op_default_company" class="form-control">
                                    <option value="0">None</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateOPModel">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <x-slot name="script">
        <script>
            document.querySelectorAll('.opModelModal').forEach(item => {
                item.addEventListener('click', event => {
                    let id = item.getAttribute('data-bs-op-id');
                    let form = document.querySelector('#opmodel-form');
                    form.setAttribute('action', '/operational_models/' + id);
                    axios.get('/operational_models/' + id)
                        .then(function(response) {
                            let data = response.data;
                            form.querySelector('#form_op_name').value = data.name;
                            form.querySelector('#form_op_short_name').value = data.short_name;
                            form.querySelector('#form_op_default_company').value = data.default_company_id;
                        })
                        .catch(function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.response.data.message,
                            })
                        })
                })
            });

            document.querySelector('#updateOPModel').addEventListener('click', function() {
            let form = document.querySelector('#opmodel-form');
            let formData = new FormData(form);
            let id = form.getAttribute('action').split('/')[2];

            axios.post('/operational_models/' + id, formData)
                .then(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.data.message,
                    })
                    .then(function() {
                        window.location.reload();
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
