<x-layout :title="$title">

    <style>
        .alert-error {
            font-size: 10pt;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    <form id="search-form" class="row g-3" action="{{ route('settings.exchange_rate') }}" method="GET">
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
                        @can('exchange_rate.add')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExchangeRate"><i class="bi bi-plus"></i></button>
                        @endcan
                    </div>
                    <!-- Exchange Rate Table -->
                    <table class="table table-striped">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Currency</th>
                                <th scope="col">Exchange Rate (MYR)</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if ($exchangeRate->count())
                                @foreach ($exchangeRate as $key => $row)
                                    <tr style="font-size: 0.8rem;">
                                        <td scope="row" class="align-middle">{{ $key + $exchangeRate->firstItem() }}</td>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::parse($row->end_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $row->currencies->currency }} ({{ $row->currencies->country->name }})
                                        </td>
                                        <th class="align-middle">
                                            1 {{ $row->currencies->currency }} =
                                            <p class="fs-5">{{ $row->rate }} MYR</p>
                                        </th>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- Delete Button --}}
                                                @can('exchange_rate.delete')
                                                    <a class="btn btn-danger" onclick="deleteCurrency({{ $row->id }})"><i class='bx bxs-trash'></i></a>
                                                @endcan

                                                {{-- Edit Button --}}
                                                @can('exchange_rate.edit')
                                                    <a class="btn btn-warning text-white" onclick="openEditModal('{{ $row->id }}')"><i class='bx bxs-edit'></i></a>
                                                @endcan
                                            </div>
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
                            Showing {{ $exchangeRate->firstItem() }} to {{ $exchangeRate->lastItem() }} of
                            {{ $exchangeRate->total() }} exchange rate
                        </div>
                        {{ $exchangeRate->withQueryString()->links() }}
                    </div>
                    <!-- End Exchange Rate Table -->
                </div>
            </div>

        </div>

    </section>

    <!-- The Modal: Add Currency -->
    <div class="modal fade" id="addExchangeRate">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addExchangeRateForm" style="font-size: 11pt">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-center align-items-center">
                        <h6 class="modal-title"><strong>Add Exchange Rate</strong></h4>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-4">
                        <!-- Start Date and End Date -->
                        <div class="row p-3 mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_start_date" name="add_start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="add_end_date" name="add_end_date" required>
                            </div>
                        </div>

                        <div class="container p-4 border rounded-4 bg-light shadow">
                            <!-- Currency Selection and Exchange Rate -->
                            <div class="row mb-3">
                                <div class="col-md-3 mt-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select name="add_currency" class="form-select mt-2" id="add_currency" required>
                                        <option value="" selected disabled>Nothing Selected</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" data-currency="{{ $currency->currency }}">{{ $currency->currency }} ({{ $currency->country->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Exchange Rate (MYR) -->
                            <div class="row d-flex align-items-center">
                                <!-- 1 IDR Section -->
                                <div class="col-md-3">
                                    <label for="add_foreign_currency" class="form-label">Exchange Rate (MYR) <span class="text-danger">*</span></label>
                                </div>

                                <!-- Fixed text for 1 IDR -->
                                <div class="col-md-4">
                                    <input type="text" class="form-control text-center" value="" id="add_foreign_rate" name="add_foreign_rate" placeholder="(Select currency first)" disabled>
                                </div>

                                <!-- Equal sign -->
                                <div class="col-md-1 text-center">
                                    <p class="mb-0">=</p>
                                </div>

                                <!-- Exchange rate input for MYR -->
                                <div class="col-md-4">
                                    <div class="input-group" id="add_rate">
                                        <input type="text" class="form-control text-end" id="add_rate" name="add_rate" placeholder="0.00" required>
                                        <span class="input-group-text">MYR</span>
                                    </div>
                                </div>
                            </div>
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
    {{-- <div class="modal fade" id="editCurrency">
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
                            <select name="edit_country_name" class="form-select mt-2" required>
                                <option value="" selected disabled>Nothing Selected</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
                                @endforeach
                            </select>
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
    </div> --}}

    <x-slot name="script">
        <script>
            // Auto select currency when user select country
            document.getElementById('add_currency').addEventListener('change', function() {
                // Get the selected option
                const selectedOption = this.options[this.selectedIndex];
                
                // Get the currency from the data attribute
                const selectedCurrency = selectedOption.getAttribute('data-currency');
                
                // Update the foreign currency input field to display "1 <Currency>"
                document.getElementById('add_foreign_rate').value = `1 ${selectedCurrency}`;
            });

            const submitAddForm = () => {
                // Prevent the default form submission
                event.preventDefault();

                // Clear any previous error messages
                $('span.alert-error').remove();

                // Gather form data
                const formData = new FormData(document.getElementById('addExchangeRateForm'));
                let start_date = $('input[name="add_start_date"]').val() || '';
                let end_date = $('input[name="add_end_date"]').val() || '';
                let add_currency = $('select[name="add_currency"]').val() || '';
                let add_rate = $('input[name="add_rate"]').val() || '';
                let valid = true;

                // Check if fields are empty
                if (start_date.trim() === '') {
                    $('input[name="add_start_date"]').after('<span class="text-danger alert-error">*Please select start date</span>');
                    valid = false;
                }

                if (end_date.trim() === '') {
                    $('input[name="add_end_date"]').after('<span class="text-danger alert-error">*Please select end date</span>');
                    valid = false;
                }

                if (add_currency.trim() === '') {
                    $('select[name="add_currency"]').after('<span class="text-danger alert-error">*Please select currency</span>');
                    valid = false;
                }

                if (add_rate.trim() === '') {
                    $('#add_rate').after('<span class="text-danger alert-error">*Please enter exchange rate</span>');
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

                                // Show input error
                                if (response.field === 'add_start_date') {
                                    $('input[name="add_start_date"]').after('<span class="text-danger alert-error">' + response.message + '</span>');
                                }

                                if (response.field === 'add_end_date') {
                                    $('input[name="add_end_date"]').after('<span class="text-danger alert-error">' + response.message + '</span>');
                                }
                                
                                if (response.field === 'add_currency') {
                                    $('select[name="add_currency"]').after('<span class="text-danger alert-error">' + response.message + '</span>');
                                }

                                if (response.field === 'add_rate') {
                                    $('#add_rate').after('<span class="text-danger alert-error">' + response.message + '</span>');
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

            // const openEditModal = async (id) => {
            //     try {
            //         // Initialize the modal
            //         const editCurrency = new bootstrap.Modal(document.getElementById('editCurrency'), {
            //             keyboard: false
            //         });

            //         // Fetch the currency data using AJAX
            //         const response = await $.ajax({
            //             url: "{{ route('settings.currency_list.show', ':id') }}".replace(':id', id),
            //             type: 'GET',
            //         });

            //         // Make sure data is available and matches the expected structure
            //         if (response && response.data) {
            //             const currency = response.data;
                        
            //             // Populate the form fields with the fetched data
            //             $('select[name="edit_country_name"]').val(currency.country_id);
            //             $('input[name="edit_currency"]').val(currency.currency);

            //             // Clear any previous hidden inputs for method and id to avoid duplication
            //             $('#editCurrencyForm input[name="_method"]').remove();
            //             $('#editCurrencyForm input[name="id"]').remove();

            //             // Append hidden inputs for method and id
            //             $('#editCurrencyForm').append('<input type="hidden" name="id" value="' + id + '">');

            //             // Show the modal
            //             editCurrency.show();
            //         } else {
            //             throw new Error('Invalid response structure');
            //         }
            //     } catch (error) {
            //         console.error('Error:', error);
            //         Swal.fire({
            //             icon: 'error',
            //             title: 'Oops...',
            //             text: 'Failed to fetch data. Please try again.'
            //         });
            //     }
            // };

            // const submitEditForm = async () => {
            //     event.preventDefault(); // Prevent the default form submission

            //     const formData = new FormData(document.getElementById('editCurrencyForm'));
            //     let countryName = $('select[name="edit_country_name"]').val() || '';
            //     let currency = $('input[name="edit_currency"]').val() || '';
            //     let valid = true;

            //     // Country id
            //     let id = $('input[name="id"]').val();

            //     // Clear previous validation messages
            //     $('.text-danger').remove();

            //     // Check if fields are empty
            //     if (countryName.trim() === '') {
            //         $('input[select="edit_country_name"]').after('<span class="text-danger">*Please select a country name</span>');
            //         valid = false;
            //     }

            //     if (currency.trim() === '') {
            //         $('input[name="edit_currency"]').after('<span class="text-danger">*Please enter a currency</span>');
            //         valid = false;
            //     }

            //     if (valid) {
            //         try {
            //             // Submit the form via AJAX
            //             const response = await $.ajax({
            //                 url: "{{ route('settings.currency_list.update', ':id') }}".replace(':id', id),
            //                 type: 'POST',
            //                 data: formData,
            //                 contentType: false,
            //                 processData: false,
            //             });

            //             if (response.success) {
            //                 Swal.fire({
            //                     icon: 'success',
            //                     title: 'Success!',
            //                     text: response.message,
            //                 }).then(() => {
            //                     location.reload();
            //                 });
            //             } else {
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Error!',
            //                     text: 'Failed to update country. Please try again. ' + response.message,
            //                 });
            //             }
            //         } catch (error) {
            //             // Check if the response has validation errors
            //             if (error.status === 422) {
            //                 let response = error.responseJSON;

            //                 // Handle error specific to country_name
            //                 if (response.field === 'edit_country_name') {
            //                     $('select[name="edit_country_name"]').after('<span class="text-danger">' + response.message + '</span>');
            //                 }

            //                 // Handle error specific to country_code
            //                 if (response.field === 'edit_currency') {
            //                     $('input[name="edit_currency"]').after('<span class="text-danger">' + response.message + '</span>');
            //                 }
            //             } else {
            //                 // Generic error handling for other issues
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Error!',
            //                     text: 'Something went wrong!'
            //                 });
            //             }
            //         }
            //     }
            // };

            // const deleteCurrency = async (id) => {
            //     Swal.fire({
            //         title: 'Delete currency?',
            //         html: `<div class="text-danger"><i class="bx bxs-error text-warning fs-5"></i> <strong class="fs-6">This action will delete the item and affect other related data! <br>Are you sure you want to proceed?</strong></div>`,
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#d33',
            //         confirmButtonText: 'Yes, delete it!',
            //         width: 650
            //     }).then(async (result) => {
            //         if (result.isConfirmed) {
            //             try {
            //                 const response = await $.ajax({
            //                     url: "{{ route('settings.currency_list.delete', ':id') }}".replace(':id', id),
            //                     type: 'DELETE',
            //                     data: {
            //                         _token: '{{ csrf_token() }}'
            //                     },
            //                 });

            //                 if (response.success) {
            //                     // Show success message
            //                     Swal.fire({
            //                         icon: 'success',
            //                         title: response.message,
            //                     }).then(() => {
            //                         location.reload(); // Reload the page
            //                     });
            //                 } else {
            //                     Swal.fire('Error!', response.message, 'error');
            //                 }
            //             } catch (error) {
            //                 Swal.fire('Error!', 'Failed to delete currency. Please try again.', 'error');
            //             }
            //         }
            //     });
            // };
        </script>
    </x-slot>

</x-layout>