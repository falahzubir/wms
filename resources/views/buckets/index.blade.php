<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">
            @for ($i = 1; $i < 9; $i++)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div style="font-size:0.9rem;">
                                <strong><i class="bi bi-basket"></i>&nbsp;Northen Region {{$i}} (NR{{$i}}) </strong>
                                <hr>
                            <div>
                                On Basket: <strong>{{$i*12-11}}</strong>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-info rounded-pill"><i class="bi bi-list"></i></button>
                                <button class="btn btn-warning rounded-pill"><i class="bi bi-pencil"></i></button>
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
            console.log('Replace this with script for individual page');
        </script>
    </x-slot>

</x-layout>

