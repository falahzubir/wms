<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section mt-5">

        <form action="{{ route('orders.change_postcode') }}" method="POST">
            @csrf
            <div class="col-10 col-md-6 mx-auto">
                {{-- if success /fail --}}
                @if (session('success'))
                    <div class="alert alert-success" style="color: green">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger" style="color: red">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="sales_id">Sales ID</label>
                        <input type="text" name="sales_id" placeholder="Sales ID" class="form-control" value="{{ $_GET['sales'] ?? old('sales_id') }}">
                        @error('sales_id')
                        <div class="alert alert-danger" style="color: red">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label for="company_id">Company</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                @if (old('company_id') == $company->id || (isset($_GET['company']) && $_GET['company'] == $company->id))
                                    selected
                                @endif
                                >{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="alert alert-danger" style="color: red">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div>
                        <label for="postcode">Postcode</label>
                        <input type="text" name="postcode" placeholder="Postcode" class="form-control" value="{{ old('postcode') ?? $_GET['current_postcode'] ?? ''}}">
                        @error('postcode')
                            <div class="alert alert-danger" style="color: red">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <input type="hidden" name="redirect" value="{{ $_GET['redirect_to'] ?? ''}}">
                    <input type="submit" class="btn btn-info" value="Change Postcode">
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


