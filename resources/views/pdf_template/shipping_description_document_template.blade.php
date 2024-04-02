<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
        }

        .a6-paper {
            width: 105mm;
            height: 148mm;
            background-color: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        th {
            padding-bottom: 10px !important;
        }

        .box {
            padding: 20px;
            --b: 5px;
            /* thickness of the border */
            --c: red;
            /* color of the border */
            --w: 20px;
            /* width of border */


            border: var(--b) solid #0000;
            /* space for the border */
            --_g: #0000 90deg, var(--c) 0;
            --_p: var(--w) var(--w) border-box no-repeat;
            background:
                conic-gradient(from 90deg at top var(--b) left var(--b), var(--_g)) 0 0 / var(--_p),
                conic-gradient(from 180deg at top var(--b) right var(--b), var(--_g)) 100% 0 / var(--_p),
                conic-gradient(from 0deg at bottom var(--b) left var(--b), var(--_g)) 0 100% / var(--_p),
                conic-gradient(from -90deg at bottom var(--b) right var(--b), var(--_g)) 100% 100% / var(--_p);
        }
    </style>
</head>
<div class="a6-paper">
    <button type="button" class="btn-close position-absolute bg-light rounded-circle"
        style="top: -3%;right:-3%;color:white" data-bs-dismiss="modal" aria-label="Close"></button>
    <h3 class="text-center" style="font-size: 1.35rem"><strong>Packing List</strong></h3>
    <div class="my-3">
        <table style="font-size: 10px;" class="table table-sm table-borderless">
            <thead style="border-bottom: 1px solid black !important;">
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total Price</th>
                </tr>
            </thead>
            @if (isset($order))
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td><span>{{ $item->product->name }}</span></td>
                            <td><span>{{ $item->quantity }}</span></td>
                            <td class="text-end"><span>RM {{ number_format($item->product->price, 2) }}</span></td>
                            <td class="text-end"><span>RM {{ number_format(($item->price/100), 2) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="border-top: 1px solid black !important;">
                    <tr>
                        <td><span>Qty Total : {{ $order->items->sum('quantity') }}</span></td>
                        <td class="text-end" colspan="2"><span>Total</span></td>
                        <td class="text-end"><span>RM {{ number_format(($order->items->sum('price')/100), 2) }}</span></td>
                    </tr>
                </tfoot>
            @else
                <tbody>
                    <tr colspan="4">
                        <td class="text-center">No data available</td>
                    </tr>
                </tbody>
            @endif
        </table>
    </div>
    <div>
        <h5 class="text-center"><strong>{{ $ship_docs->promotion_header }}</strong></h5>
        <h4 class="text-center"><strong id="modal-preview-header-box"></strong></h4>
        <div class="text-center my-5" id="qr-code-box">
            <span class="position-absolute" id="first"></span>
            <span class="position-absolute" id="second"></span>
            <div class="d-flex justify-content-center">
                <div class="box" style="--c:black;--w:40px;--b:6px">
                    {{-- <img width="150" height="150" src="{{ env('APP_URL') }}/{{ $ship_docs->content_path }}"alt="QR Code"> --}}
                    <img width="150" height="150" src="https://upload.wikimedia.org/wikipedia/commons/5/5b/Qrcode_wikipedia.jpg" alt="QR Code">

                </div>
            </div>
            <span class="position-absolute" id="third"></span>
            <span class="position-absolute" id="fourth"></span>
        </div>
        <div class="text-left" id="modal-preview-desc-box" style="font-size: 12px;">
            {!! $ship_docs->description !!}
        </div>
    </div>
</div>
