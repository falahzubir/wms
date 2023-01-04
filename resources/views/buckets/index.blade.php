<x-layout :title="$title">

    <section class="section">

        <div class="row">
            @for ($i = 1; $i < 9; $i++)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div style="font-size:0.9rem;">
                                <div class="text-center">
                                    <strong><i class="bi bi-basket"></i>&nbsp;Northen Region {{$i}} (NR{{$i}}) </strong>
                                </div>
                                <hr>
                            <div>
                                <div>On Basket: <strong>{{$i*12-11}}</strong></div>
                                <div>Last out: {{ date("d/m/Y") }}</div>
                            </div>
                            <div class="text-end">
                                <a href="/orders" class="btn btn-info rounded-pill"><i class="bi bi-list"></i></a>
                                <button class="btn btn-warning rounded-pill" id="edit-bucket"><i class="bi bi-pencil"></i></button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor

        </div>

    </section>

    <x-slot name="script">
        <script>
            // Replace this with script for individual page
            document.querySelector('#edit-bucket').addEventListener('click', function() {
                Swal.fire({
                    title: 'Edit Bucket',
                    html: `
                        <div class="mb-3">
                            <label for="bucket-name" class="form-label">Bucket Name</label>
                            <input type="text" class="form-control" id="bucket-name" value="Northen Region 1 (NR1)">
                        </div>
                        <div class="mb-3">
                            <label for="bucket-name" class="form-label">Bucket Name</label>
                            <input type="text" class="form-control" id="bucket-name" value="Northen Region 1 (NR1)">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch(`#`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                )
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: `${result.value.message}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        })
                    }
                })
            });
        </script>
    </x-slot>

</x-layout>

