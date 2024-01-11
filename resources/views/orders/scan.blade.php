<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <div>
        <small class="small text-muted">
            {{ date('l') }},{{ date('d F Y') }}
        </small>
        <br>
        <small class="small text-muted">
            Last Update: {{ date('H:i:s A') }}
        </small>
    </div>

    <div class="row pt-3">
        <div class="col-md-3">
            <div class="card p-2 pb-4 pt-4">
               <div class="row">
                    <div class="col-2">
                        <i style="color: #86b6ff; font-size:34px;" class="bi bi-calendar3"></i>
                    </div>
                    <div class="col-6" style="font-weight:bold; color:#012970;">
                        Monthly <br> Scan
                    </div>
                    <div class="col-4 text-center">
                        <span style="font-weight:bold;" id="monthly-id">0</span>
                        <small class="text-muted" style="font-size:10px;">
                            Orders
                        </small>
                    </div>
               </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-2 pb-4 pt-4">
               <div class="row">
                    <div class="col-2">
                        <i style="color: #86b6ff; font-size:34px;" class="bi bi-box-arrow-right"></i>
                    </div>
                    <div class="col-6" style="font-weight:bold; color:#012970;">
                        Daily <br> Scan
                    </div>
                    <div class="col-4 text-center">
                        <span style="font-weight:bold;" id="daily-id">0</span>
                        <small class="text-muted" style="font-size:10px;">
                            Orders
                        </small>
                    </div>
               </div>
            </div>
        </div>
    </div>

    <section class="section pt-3">

        <div class="row">

            <div class="card card-lg col-md-12 p-3" style="min-height: 60vh">

                <div class="text-center barcode-big my-3"><i class="bx bx-barcode-reader pulse"></i></div>

                <div class="mb-2 p-4">
                    <form action="{{ route('orders.scan') }}" method="POST" class="d-flex">
                        @csrf
                        <input value="" type="text" name="code" class="form-control" style="margin-right: 8px;"
                            placeholder="Scan Barcode or Enter Tracking Number" aria-label="Scan Barcode"
                            aria-describedby="button-addon2" autofocus>
                        <button class="btn btn-primary" type="submit" id="button-addon2">Scan</button>
                    </form>
                    <div>
                </div>
            </div>
            @if (session('shipping'))
            <div class="mx-auto">
                <div id="shipping-info">
                    <div id="shipping-info">
                        @if (session('error'))
                            <div class="text-danger text-start mb-3">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                        <div class="row text-start">
                            <div class="col-4">Order ID</div>
                            <div class="col-1">:</div>
                            <div class="col-7"><strong>{{ order_num_format(session('shipping')->order_id) }}</strong></div>
                        </div>
                        <div class="row text-start">
                            <div class="col-4">Tracking No</div>
                            <div class="col-1">:</div>
                            <div class="col-7"><strong>{{ session('shipping')->tracking_number }}</strong></div>
                        </div>
                        <div class="row text-start">
                            <div class="col-4">Product(s)</div>
                            <div class="col-1">:</div>
                            <div class="col-7"><strong>
                                    @foreach (session('shipping')->order->items as $items)
                                        {{ $items->product->name }} [{{ $items->quantity }}]{{ $loop->last ? '' : ', ' }}
                                    @endforeach
                                </strong></div>
                        </div>
                        <div class="row text-start">
                            <div class="col-4">Price</div>
                            <div class="col-1">:</div>
                            <div class="col-7">
                                <strong>{{ currency(session('shipping')->order->total_price, true) }}</strong>
                            </div>
                        </div>
                        <div class="row text-start text-danger">
                            <div class="col-4">Refund</div>
                            <div class="col-1">:</div>
                            <div class="col-7">
                                <strong>{{ currency(session('shipping')->order->payment_refund, true) }}</strong>
                            </div>
                        </div>
                        @isset(session('shipping')->scannedBy->name)
                            <div class="row text-start">
                                <div class="col-4">Scanned By</div>
                                <div class="col-1">:</div>
                                <div class="col-7"><strong>{{ session('shipping')->scannedBy->name ?? '' }}</strong></div>
                            </div>
                        @endisset
                        <div class="row text-start">
                            <div class="col-4">Scanned At</div>
                            <div class="col-1">:</div>
                            <div class="col-7">
                                <strong>{{ date('D d/m/Y H:i A', strtotime(session('shipping')->scanned_at)) }}</strong>
                            </div>
                        </div>
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
                parcelsDaily('individual');
            });

            const parcelsDaily = async(type) =>
            {
                let monthlyId = document.querySelector('#monthly-id');
                let dailyId = document.querySelector('#daily-id');

                let response = await axios.post('/api/orders/parcels',{
                    type: type,
                    user_id: '{{ auth()->user()->id }}'
                })
                .then(function(response) {
                    if( response.data.data != '' ){
                        let postData = response.data.data;
                        monthlyId.innerHTML = postData.total;
                        dailyId.innerHTML = postData.daily;
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
            }

            //@if (session('shipping'))
            //     setTimeout(function() {
            //         document.querySelector('#shipping-info').classList.add('fade')
            //         // document.querySelector('#shipping-info').style.display = 'none';
            //     }, 5000);
            // @endif
        </script>
    </x-slot>

</x-layout>
