<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">
        <section class="section">

            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">List</h5>

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
                                            <th>Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($companies as $company)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $company->code }}</td>
                                                <td>{{ $company->name }}</td>
                                                <td>{{ $company->address . ', ' . $company->address2 . ', ' . $company->address3 }}</td>
                                                <td>{{ $company->phone }}</td>
                                                <td>
                                                    {{-- <a href="{{ route('companies.show', $company->id) }}"
                                                        class="btn btn-primary btn-sm">Lihat</a> --}}
                                                    <a href="{{ route('companies.edit', $company->id) }}"
                                                        class="btn btn-warning btn-sm">Kemaskini</a>
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

    </section>

    <x-slot name="script">
        <script>
            // Replace this with script for individual page
            console.log('Replace this with script for individual page');
        </script>
    </x-slot>

</x-layout>
