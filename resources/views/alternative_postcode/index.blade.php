<x-layout :title="$title">

    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
        }

        .modal-body, .form-select, .form-control {
            font-size: 10pt;
        }

        .arrow {
            width: 40px;
        }

        .right-button {
            float: right;
            background-color: red !important;
            color: white;
        }

        .left-button {
            float: left;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>

                    <!-- Filter Card -->
                    <form class="row g-3" action="{{ route('search') }}" method="GET">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search" @if (isset($searchTerm)) value="{{ $searchTerm }}" @endif>
                        </div>

                        <div class="col-md-4">
                            <label>State</label>
                            <select id="filter_state" class="form-select" name="filter_state">
                                <option selected disabled>Nothing Selected</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" {{ isset($stateFilter) && $state->id == $stateFilter ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form>

                </div>
            </div>

            {{-- List --}}
            <div class="card" style="font-size:0.8rem" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNew">
                            <i class='bx bx-plus-medical'></i> Add New
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="claim-table">
                            <thead class="text-center" class="bg-secondary">
                                <tr class="align-middle">
                                    <th scope="col">#</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">State</th>
                                    <th scope="col">Actual Postcode</th>
                                    <th scope="col">Actual City</th>
                                    <th scope="col">Alternative Postcode</th>
                                    <th scope="col">Alternative City</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($alternativePostcodes->where('delete_status', '!=', 1)->isEmpty())
                                    <tr class="text-center">
                                        <td colspan="8" class="bg-light">No Data</td>
                                    </tr>
                                @else
                                    @foreach ($alternativePostcodes as $index => $postcode)
                                        <tr class="text-center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('alternative_postcode.delete', ['id' => $postcode->id]) }}" class="btn btn-danger delete-link"><i class='bx bxs-trash'></i></a>
                                                <a class="btn btn-warning text-white edit-postcode"
                                                    data-id="{{ $postcode->id }}"
                                                    data-state-id="{{ $postcode->state }}"
                                                    data-state-name="{{ $postcode->state_name }}"
                                                    data-actual-postcode="{{ $postcode->actual_postcode }}"
                                                    data-actual-city="{{ $postcode->actual_city }}"
                                                    data-alternative-postcode="{{ $postcode->alternative_postcode }}"
                                                    data-alternative-city="{{ $postcode->alternative_city }}"
                                                >
                                                    <i class='bx bxs-edit'></i>
                                                </a>
                                            </td>
                                            <td>{{ $postcode->state_name }}</td>
                                            <td>{{ $postcode->actual_postcode }}</td>
                                            <td>{{ $postcode->actual_city }}</td>
                                            <td><strong>{{ $postcode->alternative_postcode }}</strong></td>
                                            <td>{{ $postcode->alternative_city }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $alternativePostcodes->firstItem() }} to {{ $alternativePostcodes->lastItem() }} of {{ $alternativePostcodes->total() }} results
                        </div>                        
                        {{ $alternativePostcodes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- The Modal: Add New -->
    <div class="modal fade" id="addNew">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addForm" method="POST" action="{{ route('alternative_postcode.save') }}">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h6 class="modal-title"><strong>Add Alternative Postcode</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-5">
                        <div class="row">
                            <div class="col-md-2">
                                <label>State: </label>
                            </div>
                            <div class="col-md-10">
                                <select id="add-state" class="form-select" name="state">
                                    <option selected disabled>Nothing Selected</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
    
                        <div class="row mt-5">
                            <div class="col-md-5">
                                <div class="col-md-12 mb-5 text-center">
                                    <label>Actual Postcode: </label>
                                    <input type="text" name="actual_postcode" class="form-control mt-2" value="{{ old('actual_postcode') }}" required>
                                    @error('actual_postcode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="col-md-12 text-center">
                                    <label>Actual City: </label>
                                    <input type="text" name="actual_city" class="form-control mt-2" value="{{ old('actual_city') }}" required>
                                    @error('actual_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="col-md-2 text-center" style="margin-top: 70px;">
                                <img src="{{ asset("assets/img/transfer.png") }}" class="arrow">
                            </div>
    
                            <div class="col-md-5">
                                <div class="col-md-12 mb-5 text-center">
                                    <label>Alternative Postcode: </label>
                                    <input type="text" name="alternative_postcode" class="form-control mt-2" value="{{ old('alternative_postcode') }}" required>
                                    @error('alternative_postcode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="col-md-12 text-center">
                                    <label>Alternative City: </label>
                                    <input type="text" name="alternative_city" class="form-control mt-2" value="{{ old('alternative_city') }}" required>
                                    @error('alternative_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="submitForm()">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- The Modal: Edit Postcode -->
    <div class="modal fade" id="editPostcode">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editForm" method="POST" action="{{ route('alternative_postcode.update') }}">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h6 class="modal-title"><strong>Edit Alternative Postcode</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
    
                    <!-- Modal body -->
                    <div class="modal-body p-5">
                        {{-- Row ID --}}
                        <input type="hidden" name="id" id="id" value="{{ old('id') }}">

                        <div class="row">
                            <div class="col-md-2">
                                <label>State: </label>
                            </div>
                            <div class="col-md-10">
                                <select id="edit-state" class="form-select" name="state">
                                    <option id="value_from_db" value="" selected>Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
    
                        <div class="row mt-5">
                            <div class="col-md-5">
                                <div class="col-md-12 mb-5 text-center">
                                    <label>Actual Postcode: </label>
                                    <input type="text" name="actual_postcode" id="actual_postcode" class="form-control mt-2" value="{{ old('actual_postcode') }}" required>
                                    @error('actual_postcode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="col-md-12 text-center">
                                    <label>Actual City: </label>
                                    <input type="text" name="actual_city" id="actual_city" class="form-control mt-2" value="{{ old('actual_city') }}" required>
                                    @error('actual_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="col-md-2 text-center" style="margin-top: 70px;">
                                <img src="{{ asset("assets/img/transfer.png") }}" class="arrow">
                            </div>
    
                            <div class="col-md-5">
                                <div class="col-md-12 mb-5 text-center">
                                    <label>Alternative Postcode: </label>
                                    <input type="text" name="alternative_postcode" id="alternative_postcode" class="form-control mt-2" value="{{ old('alternative_postcode') }}" required>
                                    @error('alternative_postcode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
    
                                <div class="col-md-12 text-center">
                                    <label>Alternative City: </label>
                                    <input type="text" name="alternative_city" id="alternative_city" class="form-control mt-2" value="{{ old('alternative_city') }}" required>
                                    @error('alternative_city')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="editForm()">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
    
    <x-slot name="script">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
        <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            // Handle Sumbit Modal
            function submitForm() {
                // Use JavaScript to validate the form before submitting
                var form = document.getElementById('addForm');
                var stateSelect = document.getElementById('add-state');

                if (form.checkValidity() && stateSelect.value !== '') {
                    // If the form is valid, submit it
                    form.submit();
                } else {
                    // If the form is invalid, display an error alert and retain the entered values
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to add new Alternative Postcode!',
                        text: 'All fields are required!',
                    });

                    // Retain the entered values in the form fields
                    form.querySelectorAll('[name]').forEach(function(element) {
                        if (element.type !== 'select-one') {
                            element.value = element.value.trim();
                        }
                    });
                }
            }

            // Handle Edit Modal
            $(document).on('click', '.edit-postcode', function () {
                // Capture data attributes from the clicked button
                var id = $(this).data('id');
                var stateId = $(this).data('state-id');
                var stateName = $(this).data('state-name');
                var actualPostcode = $(this).data('actual-postcode');
                var actualCity = $(this).data('actual-city');
                var alternativePostcode = $(this).data('alternative-postcode');
                var alternativeCity = $(this).data('alternative-city');

                $('#id').val(id);

                // Set the value and text of the "Select State" option
                $('#value_from_db').val(stateId).text(stateName);

                $('#actual_postcode').val(actualPostcode);
                $('#actual_city').val(actualCity);
                $('#alternative_postcode').val(alternativePostcode);
                $('#alternative_city').val(alternativeCity);

                // Show the modal
                $('#editPostcode').modal('show');
            });

            // Handle Submit Edit
            function editForm() {
                var form = document.getElementById('editForm');
                var stateSelect = document.getElementById('edit-state');

                if (form.checkValidity() && stateSelect.value !== '') {
                    // If the form is valid, submit it
                    form.submit();
                } else {
                    // If the form is invalid, display an error alert and retain the entered values
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to edit Alternative Postcode!',
                        text: 'All fields are required!',
                    });

                    // Retain the entered values in the form fields
                    form.querySelectorAll('[name]').forEach(function(element) {
                        if (element.type !== 'select-one') {
                            element.value = element.value.trim();
                        }
                    });
                }
                }
       

            // Handle Delete Button
            $('.delete-link').on('click', function (e) {
                e.preventDefault();
                const deleteLink = $(this);
                Swal.fire({
                    title: 'Delete Alternative Postcode?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete it',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'right-button',
                        cancelButton: 'left-button'
                    },
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteLink.attr('href');
                    }
                });
            });
        </script>
    </x-slot>
</x-layout>
