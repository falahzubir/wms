<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">Update</h5>

                    <!-- Multi Columns Form -->
                    <form class="row g-3" action="{{ route('companies.update', $company->id) }}" method="post">
                        @csrf
                        <div class="col-md-12">
                            <label for="inputName5" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $company->name }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="inputEmail5" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code"
                                value="{{ $company->code }}" required>
                        </div>
                        <div class="col-md-10">
                            <label for="inputPassword5" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $company->email }}">
                        </div>
                        <div class="col-12">
                            <label for="inputAddress5" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ $company->address }}" required>
                        </div>
                        <div class="col-12">
                            <label for="inputAddress2" class="form-label">Address 2</label>
                            <input type="text" class="form-control" id="address2" name="address2"
                                value="{{ $company->address2 }}">
                        </div>
                        <div class="col-12">
                            <label for="inputAddress3" class="form-label">Address 3</label>
                            <input type="text" class="form-control" id="address3" name="address3"
                                value="{{ $company->address3 }}">
                        </div>
                        <div class="col-md-4">
                            <label for="inputCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ $company->city }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="inputState" class="form-label">State</label>
                            <select id="inputState" class="form-select" name="state" required>
                                @foreach (MY_STATES as $state)
                                    <option value="{{ $state }}"
                                        {{ $state == $company->state ? 'selected' : '' }}>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="postcode" class="form-label">Postcode</label>
                            <input type="text" class="form-control" id="postcode" name="postcode"
                                value="{{ $company->postcode }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="inputZip" class="form-label">Country</label>
                            <select id="inputState" class="form-select" name="country">
                                @foreach (COUNTRIES as $code => $country)
                                    <option value="{{ $code }}"
                                        {{ $code == $company->country ? 'selected' : '' }}>{{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <h5 class="card-title">Shipping PIC</h5>
                        <div class="col-md-5">
                            <label for="pic" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="pic" name="contact_person"
                                value="{{ $company->contact_person }}" required>
                        </div>
                        <div class="col-md-5">
                            <label for="pic" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ $company->phone }}" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </form><!-- End Multi Columns Form -->

                </div>
            </div>
        </div>

    </section>

    <x-slot name="script">
        <script>
            // Replace this with script for individual page
            console.log('Replace this with script for individual page');
        </script>
    </x-slot>

</x-layout>
