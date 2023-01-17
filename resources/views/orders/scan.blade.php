<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card col-md-6 offset-md-3 p-3">

                <div class="text-center barcode-big mb-2"><i class="bx bx-barcode-reader pulse"></i></div>

                <div class="mb-2">
                    <form action="/orders/scan" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="barcode" class="form-control" placeholder="Scan Barcode" aria-label="Scan Barcode" aria-describedby="button-addon2">
                            <button class="btn btn-primary" type="submit" id="button-addon2">Scan</button>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <x-slot name="script">
        <script>
            // process scanned barcode
            window.onload = function() {
                document.querySelector("input[name=barcode]").focus();
            }
        </script>
    </x-slot>

</x-layout>

