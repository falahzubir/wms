<x-layout :title="$title">

    <style>
        .alert-error {
            font-size: 10pt;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    <section class="section">

        <div class="row">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filters...</h5>
                    <form id="search-form" class="row g-3" action="{{ route('settings.exchange_rate') }}" method="GET">
                        <div class="col-md-12 mb-4">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ request('search') }}">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <h5><strong>Date Range</strong></h5>
                                <div class="btn-group" data-toggle="buttons">
                                    <input type="radio" class="btn-check" id="btn-check-today" name="off">
                                    <label class="btn btn-outline-secondary rounded-pill mx-1"
                                        for="btn-check-today">Today</label>

                                    <input type="radio" class="btn-check" id="btn-check-yesterday" name="off">
                                    <label class="btn btn-outline-secondary rounded-pill mx-1"
                                        for="btn-check-yesterday">Yesterday</label>

                                    <input type="radio" class="btn-check" id="btn-check-this-month" name="off">
                                    <label class="btn btn-outline-secondary rounded-pill mx-1"
                                        for="btn-check-this-month">This Month</label>

                                    <input type="radio" class="btn-check" id="btn-check-last-month" name="off">
                                    <label class="btn btn-outline-secondary rounded-pill mx-1"
                                        for="btn-check-last-month">Last Month</label>

                                    <input type="radio" class="btn-check" id="btn-check-overall" name="off">
                                    <label class="btn btn-outline-secondary rounded-pill mx-1"
                                        for="btn-check-overall">Overall</label>
                                </div>
                            </div>
                        
                            <div class="col-md-3">
                                <input type="date" class="form-control" placeholder="From" name="date_from"
                                    id="start-date" value="{{ Request::get('date_from') ?? '' }}">
                            </div>

                            <div class="col-md-3">
                                <input type="date" class="form-control" placeholder="To" name="date_to" id="end-date"
                                    value="{{ Request::get('date_to') ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h5><strong>Currency</strong></h5>
                                <select name="currency[]" id="currency" class="form-select mt-2" placeholder="Nothing Selected" multiple>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ request('currency') != null ? (in_array($currency->id, request('currency')) ? 'selected' : '') : '' }}>
                                            {{ $currency->currency }} ({{ $currency->country->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                                            @if ($row->currencies && $row->currencies->country)
                                                {{ $row->currencies->currency }} ({{ $row->currencies->country->name }})
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <th class="align-middle">
                                            1 {{ $row->currencies->currency }} =
                                            <p class="fs-5">{{ $row->rate }} MYR</p>
                                        </th>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- Delete Button --}}
                                                @can('exchange_rate.delete')
                                                    <a class="btn btn-danger" onclick="deleteExchangeRate({{ $row->id }})"><i class='bx bxs-trash'></i></a>
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

    <!-- The Modal: Add Exchange Rate -->
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
                        <div class="row p-3 mb-3" id="add_data_group">
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

    <!-- The Modal: Edit Exchange Rate -->
    <div class="modal fade" id="editExchangeRate">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editExchangeRateForm" style="font-size: 11pt">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header d-flex justify-content-center align-items-center">
                        <h6 class="modal-title"><strong>Edit Exchange Rate</strong></h4>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-4">
                        <!-- Start Date and End Date -->
                        <div class="row p-3 mb-3" id="edit_data_group">
                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_start_date" name="edit_start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_end_date" name="edit_end_date" required>
                            </div>
                        </div>

                        <div class="container p-4 border rounded-4 bg-light shadow">
                            <!-- Currency Selection and Exchange Rate -->
                            <div class="row mb-3">
                                <div class="col-md-3 mt-3">
                                    <label for="edit_currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select name="edit_currency" class="form-select mt-2" id="edit_currency" required>
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
                                    <label for="edit_foreign_currency" class="form-label">Exchange Rate (MYR) <span class="text-danger">*</span></label>
                                </div>

                                <!-- Fixed text for 1 IDR -->
                                <div class="col-md-4">
                                    <input type="text" class="form-control text-center" value="" id="edit_foreign_rate" name="edit_foreign_rate" placeholder="(Select currency first)" disabled>
                                </div>

                                <!-- Equal sign -->
                                <div class="col-md-1 text-center">
                                    <p class="mb-0">=</p>
                                </div>

                                <!-- Exchange rate input for MYR -->
                                <div class="col-md-4">
                                    <div class="input-group" id="edit_rate">
                                        <input type="text" class="form-control text-end" name="edit_rate" placeholder="0.00" required>
                                        <span class="input-group-text">MYR</span>
                                    </div>
                                </div>
                            </div>
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
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            // Tom Select Plugin
            new TomSelect("#currency", {
                plugins: ['remove_button'],
            });

            // Auto select currency when user selects a country (For Add)
            document.getElementById('add_currency').addEventListener('change', function() {
                // Get the selected option
                const selectedOption = this.options[this.selectedIndex];
                
                // Get the currency from the data attribute
                const selectedCurrency = selectedOption.getAttribute('data-currency');
                
                // Update the foreign currency input field to display "1 <Currency>"
                document.getElementById('add_foreign_rate').value = `1 ${selectedCurrency}`;
            });

            // Auto select currency when user selects a country (For Edit)
            document.getElementById('edit_currency').addEventListener('change', function() {
                // Get the selected option
                const selectedOption = this.options[this.selectedIndex];
                
                // Get the currency from the data attribute
                const selectedCurrency = selectedOption.getAttribute('data-currency');
                
                // Update the foreign currency input field to display "1 <Currency>"
                document.getElementById('edit_foreign_rate').value = `1 ${selectedCurrency}`;
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
                        url: "{{ route('settings.exchange_rate.add') }}",
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

                                // Add error below date group
                                $('#add_data_group').removeClass('mb-3');
                                $('#add_data_group').after(`<div class="mb-3 d-flex justify-content-center align-items-center"><span class="text-danger alert-error">${response.message}</span></div>`);

                                // Add Bootstrap's invalid class to highlight the input
                                $('#add_start_date').addClass('is-invalid');
                                $('#add_end_date').addClass('is-invalid');
                                $('#add_currency').addClass('is-invalid');
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
                    const editExchangeRate = new bootstrap.Modal(document.getElementById('editExchangeRate'), {
                        keyboard: false
                    });

                    // Fetch the exchange rate data using AJAX
                    const response = await $.ajax({
                        url: "{{ route('settings.exchange_rate.show', ':id') }}".replace(':id', id),
                        type: 'GET',
                    });

                    // Make sure the data exists and has the expected structure
                    if (response && response.data) {
                        const exchangeRate = response.data;
                        
                        // Manually extract the date (ignoring time)
                        const startDate = exchangeRate.start_date.split(' ')[0]; // 'YYYY-MM-DD'
                        const endDate = exchangeRate.end_date.split(' ')[0]; // 'YYYY-MM-DD'

                        // Populate the form fields with the fetched data
                        $('input[name="edit_start_date"]').val(startDate);
                        $('input[name="edit_end_date"]').val(endDate);
                        $('select[name="edit_currency"]').val(exchangeRate.currency);

                        // Show the exchange rate value and set the foreign rate
                        $('input[name="edit_foreign_rate"]').val(`1 ${exchangeRate.currencies.currency}`);
                        $('input[name="edit_rate"]').val(exchangeRate.rate);

                        // Clear any previous hidden inputs for method and id to avoid duplication
                        $('#editExchangeRateForm input[name="_method"]').remove();
                        $('#editExchangeRateForm input[name="id"]').remove();

                        // Append hidden inputs for method and id
                        $('#editExchangeRateForm').append('<input type="hidden" name="id" value="' + id + '">');

                        // Show the modal
                        editExchangeRate.show();
                    } else {
                        throw new Error('Invalid response structure');
                    }
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
                // Prevent the default form submission
                event.preventDefault();

                // Clear previous validation messages
                $('span.alert-error').remove();

                const formData = new FormData(document.getElementById('editExchangeRateForm'));
                let edit_start_date = $('input[name="edit_start_date"]').val() || '';
                let edit_end_date = $('input[name="edit_end_date"]').val() || '';
                let edit_currency = $('select[name="edit_currency"]').val() || '';
                let edit_rate = $('input[name="edit_rate"]').val() || '';
                let valid = true;

                // Exchange rate id
                let id = $('input[name="id"]').val();

                // Check if fields are empty
                if (edit_start_date.trim() === '') {
                    $('input[name="edit_start_date"]').after('<span class="text-danger alert-error">*Please select start date</span>');
                    valid = false;
                }

                if (edit_end_date.trim() === '') {
                    $('input[name="edit_end_date"]').after('<span class="text-danger alert-error">*Please select end date</span>');
                    valid = false;
                }

                if (edit_currency.trim() === '') {
                    $('select[name="edit_currency"]').after('<span class="text-danger alert-error">*Please select currency</span>');
                    valid = false;
                }

                if (edit_rate.trim() === '') {
                    $('#edit_rate').after('<span class="text-danger alert-error">*Please enter exchange rate</span>');
                    valid = false;
                }

                if (valid) {
                    try {
                        // Submit the form via AJAX
                        const response = await $.ajax({
                            url: "{{ route('settings.exchange_rate.update', ':id') }}".replace(':id', id),
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
                                text: 'Failed to update exchange rate. Please try again. ' + response.message,
                            });
                        }
                    } catch (error) {
                        // Check if the response has validation errors
                        if (error.status === 422) {
                            let response = error.responseJSON;

                            // Add error below date group
                            $('#edit_data_group').removeClass('mb-3');
                            $('#edit_data_group').after(`<div class="mb-3 d-flex justify-content-center align-items-center"><span class="text-danger alert-error">${response.message}</span></div>`);

                            // Add Bootstrap's invalid class to highlight the input
                            $('#edit_start_date').addClass('is-invalid');
                            $('#edit_end_date').addClass('is-invalid');
                            $('#edit_currency').addClass('is-invalid');
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

            const deleteExchangeRate = async (id) => {
                Swal.fire({
                    title: 'Delete exchange rate?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    width: 650
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await $.ajax({
                                url: "{{ route('settings.exchange_rate.delete', ':id') }}".replace(':id', id),
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
                            Swal.fire('Error!', 'Failed to delete exchange rate. Please try again.', 'error');
                        }
                    }
                });
            };
        </script>
    </x-slot>

</x-layout>