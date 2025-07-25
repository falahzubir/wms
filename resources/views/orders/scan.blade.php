<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <div class="row">
        <div class="col-md-3">
            <div class="card p-4" style="min-height: 80vh">
                <div class="text-center barcode-big"><i class="bx bx-barcode-reader pulse"></i></div>
        
                <div class="container border rounded p-3 pb-4 pt-4 text-center mb-3">
                    <div class="row">
                        <div class="col-1">
                            <i style="color: #86b6ff; font-size:34px;" class="bi bi-calendar3"></i>
                        </div>
                        <div class="col-10 mt-3" style="font-weight:bold; color:#012970;">
                            Monthly Scan
                        </div>
                    </div>
                    <div class="text-center">
                        <span style="font-weight:bold;" id="monthly-id">0</span>
                        <br>
                        <small class="text-muted" style="font-size:10px;">
                            Orders
                        </small>
                    </div>
                </div>
        
                <div class="container border rounded p-3 pb-4 pt-4 text-center my-3">
                    <div class="row">
                        <div class="col-1">
                            <i style="color: #86b6ff; font-size:34px;" class="bi bi-box-arrow-right"></i>
                        </div>
                        <div class="col-10 mt-3" style="font-weight:bold; color:#012970;">
                            Daily Scan
                        </div>
                        <div class="text-center">
                            <span style="font-weight:bold;" id="daily-id">0</span>
                            <br>
                            <small class="text-muted" style="font-size:10px;">
                                Orders
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <small class="small text-muted">
                        {{ date('l') }},{{ date('d F Y') }}
                    </small>
                    <br>
                    <small class="small text-muted">
                        Last Update: {{ date('H:i:s A') }}
                    </small>
                </div>
            </div>
        </div>
    
        <div class="col-md-9">
            <section class="section">
                <div class="row">
                    <div class="card card-lg col-md-12 p-3" style="min-height: 80vh">
                        <div class="mb-2 mt-2 p-4">
                            <form action="{{ route('orders.scan') }}" method="POST" class="d-flex">
                                @csrf
                                <input value="" type="text" name="code" class="form-control" style="margin-right: 8px; font-size: 11pt;"
                                    placeholder="Scan Barcode or Enter Tracking Number" aria-label="Scan Barcode"
                                    aria-describedby="button-addon2" autofocus>
                                <button class="btn btn-primary" type="submit" id="button-addon2" style="font-size: 11pt;">Scan</button>
                            </form>
                            <div>
                        </div>
                    </div>
                    @if (session('shipping'))
                    <div class="mx-auto">
                        <div id="shipping-info">
                            <div id="shipping-info" style="font-size: 11pt;">
                                {{-- If success --}}
                                @if (session('success') && session('shipping')->order->payment_refund == 0)
                                    <div class="text-success text-start mb-3">
                                        <i class='bx bx-check-circle'></i> {{ session('success') }}
                                    </div>
                                @elseif (session('success') && session('shipping')->order->payment_refund != 0)
                                    <div class="text-danger text-start mb-3">
                                        <i class='bx bx-error-circle'></i> This parcel was eligible for a <strong>REFUND</strong>
                                    </div>
                                    <div class="text-success text-start small mb-3">
                                        <i class='bx bx-check-circle'></i> {{ session('success') }}
                                    </div>
                                {{-- If error --}}
                                @elseif (session('error') && session('shipping')->order->payment_refund == 0)
                                    <div class="text-muted text-start mb-3">
                                        {{ session('error') }}
                                    </div>
                                @else
                                    <div class="text-danger text-start mb-3">
                                        <i class='bx bx-error-circle'></i> This parcel was eligible for a <strong>REFUND</strong>
                                    </div>
                                    <div class="text-muted text-start small mb-3">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="row text-start mb-1">
                                    <div class="col-4">Order ID</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7"><strong>{{ order_num_format(session('shipping')->order_id) }}</strong></div>
                                </div>
                                <div class="row text-start mb-1">
                                    <div class="col-4">Tracking No</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7"><strong>{{ session('shipping')->tracking_number }}</strong></div>
                                </div>
                                <div class="row text-start mb-1">
                                    <div class="col-4">Product(s)</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7"><strong>
                                            @foreach (session('shipping')->order->items as $items)
                                                {{ $items->product->name }} [{{ $items->quantity }}]{{ $loop->last ? '' : ', ' }}
                                            @endforeach
                                        </strong></div>
                                </div>
                                <div class="row text-start mb-1">
                                    <div class="col-4">Price</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7">
                                        <strong>{{ currency(session('shipping')->order->total_price, true) }}</strong>
                                    </div>
                                </div>
                                <div class="row text-start mb-1 @if (session('shipping')->order->payment_refund != 0) text-danger @endif">
                                    <div class="col-4">Refund</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7">
                                        @if (session('shipping')->order->payment_refund == 0)
                                            <strong>-</strong>
                                        @else
                                            <strong>{{ currency(session('shipping')->order->payment_refund, true) }}</strong>
                                        @endif
                                    </div>
                                </div>
                                @isset(session('shipping')->scannedBy->name)
                                    <div class="row text-start mb-1">
                                        <div class="col-4">Scanned By</div>
                                        <div class="col-1">:</div>
                                        <div class="col-7"><strong>{{ session('shipping')->scannedBy->name ?? '' }}</strong></div>
                                    </div>
                                @endisset
                                <div class="row text-start mb-1">
                                    <div class="col-4">Scanned At</div>
                                    <div class="col-1">:</div>
                                    <div class="col-7" style="white-space: nowrap;">
                                        <strong>{{ date('D d/m/Y H:i A', strtotime(session('shipping')->scanned_at)) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

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
                //add loading
                monthlyId.innerHTML = '<i class="bx bx-loader bx-spin"></i>';
                dailyId.innerHTML = '<i class="bx bx-loader bx-spin"></i>';

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
                    monthlyId.innerHTML = '0';
                    dailyId.innerHTML = '0';
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
