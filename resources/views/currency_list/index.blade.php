<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    <form id="search-form" class="row g-3" action="{{ route('settings.currency_list') }}" method="GET">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ request('search') }}">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-start">
                        {{-- Add Button --}}
                        @can('currency_list.add')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCurrency"><i class="bi bi-plus"></i></button>
                        @endcan
                    </div>
                    <!-- Country Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Country Name</th>
                                <th scope="col">Country Code</th>
                                <th scope="col">Currency</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if ($currencies->count())
                                @foreach ($currencies as $key => $currency)
                                    <tr style="font-size: 0.8rem;">
                                        <td scope="row">{{ $key + $currencies->firstItem() }}</td>
                                        <td>
                                            {{ $currency->country->name }}
                                        </td>
                                        <th>
                                            {{ $currency->country->code }}
                                        </th>
                                        <th>
                                            {{ $currency->currency }}
                                        </th>
                                        <td class="d-flex align-middle justify-content-center gap-1">
                                            {{-- Delete Button --}}
                                            @can('currency_list.delete')
                                                <a class="btn btn-danger" onclick="deleteCurrency({{ $currency->id }})"><i class='bx bxs-trash'></i></a>
                                            @endcan

                                            {{-- Edit Button --}}
                                            @can('currency_list.edit')
                                                <a class="btn btn-warning text-white" onclick="openEditModal('{{ $currency->id }}')"><i class='bx bxs-edit'></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <div class="alert alert-warning" role="alert">
                                            No data found.
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $currencies->firstItem() }} to {{ $currencies->lastItem() }} of
                            {{ $currencies->total() }} currency
                        </div>
                        {{ $currencies->withQueryString()->links() }}
                    </div>
                    <!-- End Country Table -->
                </div>
            </div>

        </div>

    </section>

    <!-- The Modal: Add Currency -->
    <div class="modal fade" id="addCurrency">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="addCurrencyForm">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-center align-items-center">
                        <h6 class="modal-title"><strong>Add Currency</strong></h4>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-5">
                        <div class="mb-3">
                            <label><strong>Country Name: </strong></label>
                            <select name="add_country_name" class="form-select mt-2" required>
                                <option value="" selected disabled>Nothing Selected</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label><strong>Currency: </strong></label>
                            <input type="text" name="add_currency" class="form-control mt-2" value="" required>
                        </div>
                    </div>
    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="submitAddForm()">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- The Modal: Edit Currency -->
    <div class="modal fade" id="editCurrency">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editCurrencyForm">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-center align-items-center">
                        <h6 class="modal-title"><strong>Edit Currency</strong></h4>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-5">
                        <div class="mb-3">
                            <label><strong>Country Name: </strong></label>
                            <input type="text" name="edit_country_name" class="form-control mt-2" value="{{ old('edit_country_name') }}" required>
                            @error('edit_country_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label><strong>Currency: </strong></label>
                            <input type="text" name="edit_currency" class="form-control mt-2" value="{{ old('edit_currency') }}" required>
                            @error('edit_currency')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="submitEditForm()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            const submitAddForm = () => {
                // Prevent the default form submission
                event.preventDefault();

                // Clear any previous error messages
                $('span.text-danger').remove();

                // Gather form data
                const formData = new FormData(document.getElementById('addCurrencyForm'));
                let countryName = $('select[name="add_country_name"]').val() || '';
                let currency = $('input[name="add_currency"]').val() || '';
                let valid = true;

                // Check if fields are empty
                if (countryName.trim() === '') {
                    $('select[name="add_country_name"]').after('<span class="text-danger">*Please select a country name</span>');
                    valid = false;
                }

                if (currency.trim() === '') {
                    $('input[name="add_currency"]').after('<span class="text-danger">*Please enter a currency</span>');
                    valid = false;
                }

                if (valid) {
                    // Perform the AJAX request
                    $.ajax({
                        url: "{{ route('settings.currency_list.add') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // Handle success response
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message
                                }).then(() => {
                                    location.reload(); // Reload the page
                                });
                            }
                        },
                        error: function(xhr) {                        
                            // Check if the response has validation errors
                            if (xhr.status === 422) {
                                let response = xhr.responseJSON;

                                // Handle error specific to country_name
                                if (response.field === 'add_country_name') {
                                    $('select[name="add_country_name"]').after('<span class="text-danger">' + response.message + '</span>');
                                }

                                // Handle error specific to country_code
                                if (response.field === 'add_currency') {
                                    $('input[name="add_currency"]').after('<span class="text-danger">' + response.message + '</span>');
                                }
                            } else {
                                // Generic error handling for other issues
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Something went wrong!'
                                });
                            }
                        }
                    });
                }
            };

            const openEditModal = async (id) => {
                try {
                    // Initialize the modal
                    const editCountry = new bootstrap.Modal(document.getElementById('editCountry'), {
                        keyboard: false
                    });

                    // Fetch the country data using AJAX
                    const response = await $.ajax({
                        url: "{{ route('settings.currency_list.show', ':id') }}".replace(':id', id),
                        type: 'GET',
                    });

                    // Populate the form fields with the country data
                    $('input[name="edit_country_name"]').val(response.country.name);
                    $('input[name="edit_country_code"]').val(response.country.code);

                    // Remove any previous hidden input fields for the method and id to avoid duplication
                    $('#editCountryForm input[name="_method"]').remove();
                    $('#editCountryForm input[name="id"]').remove();

                    // Add hidden inputs for method and id
                    $('#editCountryForm').append('<input type="hidden" name="id" value="' + id + '">');

                    // Show the modal
                    editCountry.show();

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to fetch data. Please try again.'
                    });
                }
            };

            const submitEditForm = async () => {
                event.preventDefault(); // Prevent the default form submission

                const formData = new FormData(document.getElementById('editCountryForm'));
                let countryName = $('input[name="edit_country_name"]').val() || '';
                let countryCode = $('input[name="edit_country_code"]').val() || '';
                let valid = true;

                // Country id
                let id = $('input[name="id"]').val();

                // Clear previous validation messages
                $('.text-danger').remove();

                // Check if fields are empty
                if (countryName.trim() === '') {
                    $('input[name="edit_country_name"]').after('<span class="text-danger">*This field is required.</span>');
                    valid = false;
                }

                if (countryCode.trim() === '') {
                    $('input[name="edit_country_code"]').after('<span class="text-danger">*This field is required.</span>');
                    valid = false;
                }

                if (valid) {
                    try {
                        // Submit the form via AJAX
                        const response = await $.ajax({
                            url: "{{ route('settings.currency_list.update', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                        });

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update country. Please try again. ' + response.message,
                            });
                        }
                    } catch (error) {
                        // Check if the response has validation errors
                        if (error.status === 422) {
                            let response = error.responseJSON;

                            // Handle error specific to country_name
                            if (response.field === 'edit_country_name') {
                                $('input[name="edit_country_name"]').after('<span class="text-danger">' + response.message + '</span>');
                            }

                            // Handle error specific to country_code
                            if (response.field === 'edit_country_code') {
                                $('input[name="edit_country_code"]').after('<span class="text-danger">' + response.message + '</span>');
                            }
                        } else {
                            // Generic error handling for other issues
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong!'
                            });
                        }
                    }
                }
            };

            const deleteCurrency = async (id) => {
                Swal.fire({
                    title: 'Delete currency?',
                    html: `<div class="text-danger"><i class="bx bxs-error text-warning fs-5"></i> <strong class="fs-6">This action will delete the item and affect other related data! <br>Are you sure you want to proceed?</strong></div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    width: 650
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await $.ajax({
                                url: "{{ route('settings.currency_list.delete', ':id') }}".replace(':id', id),
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                            });

                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                }).then(() => {
                                    location.reload(); // Reload the page
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        } catch (error) {
                            Swal.fire('Error!', 'Failed to delete currency. Please try again.', 'error');
                        }
                    }
                });
            };
        </script>
    </x-slot>

</x-layout>