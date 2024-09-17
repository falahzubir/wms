<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body" style="">
                    <h5 class="card-title">Filters..</h5>
                    <form id="search-form" class="row g-3" action="{{ url()->current() }}">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="{{ old('search', Request::get('search')) }}">
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
                        <button class="btn btn-primary" id="add-country-btn"><i class="bi bi-plus"></i></button>
                    </div>
                    <!-- Country Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Country Name</th>
                                <th scope="col">Country Code</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @if ($countries->count())
                                @foreach ($countries as $key => $country)
                                    <tr style="font-size: 0.8rem;">
                                        <th scope="row">{{ $key + $countries->firstItem() }}</th>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <div class="d-flex">
                                                
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <div class="alert alert-warning" role="alert">
                                            No country found!
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $countries->firstItem() }} to {{ $countries->lastItem() }} of
                            {{ $countries->total() }} countries
                        </div>
                        {{ $countries->withQueryString()->links() }}
                    </div>
                    <!-- End Country Table -->
                </div>
            </div>

        </div>

    </section>

    <x-slot name="script">
    </x-slot>

</x-layout>