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
                            <input value="7122015153441663" type="text" name="code" class="form-control" placeholder="Scan Barcode" aria-label="Scan Barcode" aria-describedby="button-addon2" >
                            <button class="btn btn-primary" type="submit" id="button-addon2">Scan</button>
                    </form>
                </div>
            </div>
                @if (session('order'))
                    <div class="row">
                        <div class="col-3">Order ID</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>{{ order_num_format(session('order')) }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-3">Tracking No</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>{{ session('order')->shipping->tracking_number }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-3">Product(s)</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>
                            @foreach (session('order')->items as $items)
                                {{ $items->product->name }} [{{ $items->quantity }}]{{ $loop->last ? '' : ', ' }}
                            @endforeach
                        </strong></div>
                    </div>
                    <div class="row">
                        <div class="col-3">Price</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>{{ currency(session('order')->total_price, true) }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-3">Scanned By</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>{{ session('order')->shipping->scannedBy->name }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-3">Scanned At</div>
                        <div class="col-1">:</div>
                        <div class="col-8"><strong>{{ date("D d/m/Y H:i A", strtotime(session('order')->shipping->scanned_at)) }}</strong></div>
                    </div>
                @endif
            <div>

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

