<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <style>
        table>tbody>tr>td {
            vertical-align: middle;
        }
    </style>
    <section class="section">

        <div class="card p-3">
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">FOC?</th>
                            <th class="text-center">Max in Box</th>
                        </tr>
                    </thead>
                    @foreach ($products as $product)
                        <tr>
                            <form action="{{ route('products.update', $product->id) }}" method="post">
                                @csrf
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-center">{{ $product->price }}</td>
                                <td class="text-center">{{ $product->weight }}</td>
                                <td class="text-center">
                                    <input type="checkbox" name="" id=""
                                        {{ $product->is_foc == 1 ? 'checked' : '' }} disabled="disabled">
                                </td>
                                <td class="text-center">
                                    <input class="text-center form-control form-control-sm" type="text" name="max_box"
                                        size="1" value="{{ $product->max_box }}" onchange="this.form.submit()">
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

    <x-slot name="script">
        <script>
            // Replace this with script for individual page
        </script>
    </x-slot>

</x-layout>
