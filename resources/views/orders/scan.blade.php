<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card card-lg col-md-12 p-3" style="min-height: 70vh">

                <div class="text-center barcode-big my-3"><i class="bx bx-barcode-reader pulse"></i></div>

                <div class="mb-2">
                    <form action="{{ route('orders.scan') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input value="" type="text" name="code" class="form-control"
                                placeholder="Scan Barcode or Enter Tracking Number" aria-label="Scan Barcode"
                                aria-describedby="button-addon2" autofocus>
                            <button class="btn btn-primary" type="submit" id="button-addon2">Scan</button>
                    </form>
                </div>
            </div>
            @if (session('shipping'))
                <div id="shipping-info">
                    @if (session('error'))
                        <div class="text-danger text-center mb-3">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-4">Order ID</div>
                        <div class="col-1">:</div>
                        <div class="col-7"><strong>{{ order_num_format(session('shipping')->order_id) }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-4">Tracking No</div>
                        <div class="col-1">:</div>
                        <div class="col-7"><strong>{{ session('shipping')->tracking_number }}</strong></div>
                    </div>
                    <div class="row">
                        <div class="col-4">Product(s)</div>
                        <div class="col-1">:</div>
                        <div class="col-7"><strong>
                                @foreach (session('shipping')->order->items as $items)
                                    {{ $items->product->name }} [{{ $items->quantity }}]{{ $loop->last ? '' : ', ' }}
                                @endforeach
                            </strong></div>
                    </div>
                    <div class="row">
                        <div class="col-4">Price</div>
                        <div class="col-1">:</div>
                        <div class="col-7">
                            <strong>{{ currency(session('shipping')->order->total_price, true) }}</strong>
                        </div>
                    </div>
                    <div class="row text-danger">
                        <div class="col-4">Refund</div>
                        <div class="col-1">:</div>
                        <div class="col-7">
                            <strong>{{ currency(session('shipping')->order->payment_refund, true) }}</strong>
                        </div>
                    </div>
                    @isset(session('shipping')->scannedBy->name)
                        <div class="row">
                            <div class="col-4">Scanned By</div>
                            <div class="col-1">:</div>
                            <div class="col-7"><strong>{{ session('shipping')->scannedBy->name ?? '' }}</strong></div>
                        </div>
                    @endisset
                    <div class="row">
                        <div class="col-4">Scanned At</div>
                        <div class="col-1">:</div>
                        <div class="col-7">
                            <strong>{{ date('D d/m/Y H:i A', strtotime(session('shipping')->scanned_at)) }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <x-slot name="script">
        <script>
            // process scanned barcode
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector("input[name=code]").focus();
            });

            @if (session('shipping'))
                setTimeout(function() {
                    document.querySelector('#shipping-info').classList.add('fade')
                    // document.querySelector('#shipping-info').style.display = 'none';
                }, 5000);
            @endif
        </script>
    </x-slot>

</x-layout>
