<x-layout :title="$title">
    @can('view.dashboard')
    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <x-dashboard_infocard label="Pending" id="current-pending" icon="bi bi-clock-history" class="sales-card"
                        :url="route('orders.pending')" />

                    <x-dashboard_infocard label="Processing" id="current-processing" icon="bx bx-loader"
                        class="revenue-card" :url="route('buckets.index')" />

                    <x-dashboard_infocard label="Packing" id="current-packing" icon="bi bi-box-seam"
                        class="customers-card" :url="route('orders.packing')" />

                    <x-dashboard_infocard label="Pending Shipping" id="current-pending-shipping"
                        icon="bi bi-truck-flatbed" class="orders-card" :url="route('orders.readyToShip')" />

                    <x-dashboard_infocard label="In Transit" id="current-shipping" icon="bi bi-truck" class="orders-card"
                        :url="route('orders.shipping')" />



                    <!-- Recent Sales -->
                    {{-- <div class="col-12">

                        <div class="card recent-sales overflow-auto ">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>
                                    <li><a class="dropdown-item stats-time" id="stats-yesterday"
                                            data-start="{{ Carbon::yesterday()->startOfDay() }}"
                                            data-end="{{ Carbon::yesterday()->endOfDay() }}"
                                            data-type="Yesterday">Yesterday</a></li>
                                    <li><a class="dropdown-item stats-time" id="stats-today"
                                            data-start="{{ Carbon::now()->startOfDay() }}"
                                            data-end="{{ Carbon::now()->endOfDay() }}" data-type="Today">Today</a></li>
                                    <li><a class="dropdown-item stats-time" id="stats-this-month"
                                            data-start="{{ Carbon::now()->startOfMonth() }}"
                                            data-end="{{ Carbon::now()->endOfMonth() }}" data-type="This Month">This
                                            Month</a></li>
                                    <li><a class="dropdown-item stats-time" id="stats-last-month"
                                            data-start="{{ Carbon::now()->subMonth()->startOfMonth() }}"
                                            data-end="{{ Carbon::now()->subMonth()->endOfMonth() }}"
                                            data-type="Last Month">Last Month</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Order Statistics <span id="stats-time">| Today</span></h5>
                                <div class="row">
                                    <x-dashboard_infocard label="Received" type="child" id="stats-pending"
                                        class="" />
                                    <x-dashboard_infocard label="Processed" type="child" id="stats-processing"
                                        class="" />
                                    <x-dashboard_infocard label="CN Generated" type="child" id="stats-cn-generated"
                                        class="" />
                                    <x-dashboard_infocard label="Scanned" type="child" id="stats-parcel-scan"
                                        class="" />
                                    <x-dashboard_infocard label="Shipping" type="child" id="stats-shipping"
                                        class="" />
                                </div>
                            </div>
                        </div>

                    </div><!-- End Recent Sales --> --}}

                    <!-- Reports -->
                    {{-- <div class="col-12">
                        <div class="card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Reports <span>/Today</span></h5>

                                <!-- Line Chart -->
                                <div id="reportsChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#reportsChart"), {
                                            series: [{
                                                name: 'Sales',
                                                data: [31, 40, 28, 51, 42, 82, 56],
                                            }, {
                                                name: 'Revenue',
                                                data: [11, 32, 45, 32, 34, 52, 41]
                                            }, {
                                                name: 'Customers',
                                                data: [15, 11, 32, 18, 9, 24, 11]
                                            }],
                                            chart: {
                                                height: 350,
                                                type: 'area',
                                                toolbar: {
                                                    show: false
                                                },
                                            },
                                            markers: {
                                                size: 4
                                            },
                                            colors: ['#008080', '#2eca6a', '#ff771d'],
                                            fill: {
                                                type: "gradient",
                                                gradient: {
                                                    shadeIntensity: 1,
                                                    opacityFrom: 0.3,
                                                    opacityTo: 0.4,
                                                    stops: [0, 90, 100]
                                                }
                                            },
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth',
                                                width: 2
                                            },
                                            xaxis: {
                                                type: 'datetime',
                                                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z",
                                                    "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z",
                                                    "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                                                    "2018-09-19T06:30:00.000Z"
                                                ]
                                            },
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy HH:mm'
                                                },
                                            }
                                        }).render();
                                    });
                                </script>
                                <!-- End Line Chart -->

                            </div>

                        </div>
                    </div><!-- End Reports -->


                    <!-- Top Selling -->
                    <div class="col-12">
                        <div class="card top-selling overflow-auto">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Today</a></li>
                                    <li><a class="dropdown-item" href="#">This Month</a></li>
                                    <li><a class="dropdown-item" href="#">This Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body pb-0">
                                <h5 class="card-title">Top Selling <span>| Today</span></h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">Preview</th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Sold</th>
                                            <th scope="col">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-1.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa
                                                    voluptas nulla</a></td>
                                            <td>$64</td>
                                            <td class="fw-bold">124</td>
                                            <td>$5,828</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-2.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Exercitationem
                                                    similique doloremque</a></td>
                                            <td>$46</td>
                                            <td class="fw-bold">98</td>
                                            <td>$4,508</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-3.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Doloribus nisi
                                                    exercitationem</a></td>
                                            <td>$59</td>
                                            <td class="fw-bold">74</td>
                                            <td>$4,366</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-4.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint
                                                    rerum error</a></td>
                                            <td>$32</td>
                                            <td class="fw-bold">63</td>
                                            <td>$2,016</td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><a href="#"><img src="assets/img/product-5.jpg"
                                                        alt=""></a></th>
                                            <td><a href="#" class="text-primary fw-bold">Sit unde debitis
                                                    delectus repellendus</a></td>
                                            <td>$79</td>
                                            <td class="fw-bold">41</td>
                                            <td>$3,239</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Top Selling --> --}}

                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

                <!-- Website Traffic -->
                {{-- <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Website Traffic <span>| Today</span></h5>

                        <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                    </div>
                </div><!-- End Website Traffic --> --}}

                <!-- Recent Activity -->
                {{-- <div class="card">

                    <div class="card-body">
                        <h5 class="card-title">Bucket Activity </h5>

                        <div class="activity">

                            @foreach ($batches as $batch)
                                <x-dashboard_activity :time="$batch->created_at->diffForHumans()"
                                    msg='Created <a href="#" class="fw-bold text-dark">{{ number_formatter($batch->batch_id) }}</a> batch' />
                            @endforeach

                        </div>

                    </div>
                </div><!-- End Recent Activity --> --}}

                {{-- <!-- Budget Report -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Budget Report <span>| This Month</span></h5>

                        <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                                    legend: {
                                        data: ['Allocated Budget', 'Actual Spending']
                                    },
                                    radar: {
                                        // shape: 'circle',
                                        indicator: [{
                                                name: 'Sales',
                                                max: 6500
                                            },
                                            {
                                                name: 'Administration',
                                                max: 16000
                                            },
                                            {
                                                name: 'Information Technology',
                                                max: 30000
                                            },
                                            {
                                                name: 'Customer Support',
                                                max: 38000
                                            },
                                            {
                                                name: 'Development',
                                                max: 52000
                                            },
                                            {
                                                name: 'Marketing',
                                                max: 25000
                                            }
                                        ]
                                    },
                                    series: [{
                                        name: 'Budget vs spending',
                                        type: 'radar',
                                        data: [{
                                                value: [4200, 3000, 20000, 35000, 50000, 18000],
                                                name: 'Allocated Budget'
                                            },
                                            {
                                                value: [5000, 14000, 28000, 26000, 42000, 21000],
                                                name: 'Actual Spending'
                                            }
                                        ]
                                    }]
                                });
                            });
                        </script>

                    </div>
                </div><!-- End Budget Report --> --}}




            </div><!-- End Right side columns -->

        </div>
    </section>
    @endcan
    <x-slot name="script">
        <script>
            // DOM on load
            document.addEventListener("DOMContentLoaded", () => {
                axios.get('/api/dashboard/current-process')
                    .then(function(response) {
                        document.querySelector('#current-pending').innerHTML = response.data.count[
                            {{ ORDER_STATUS_PENDING }}].toLocaleString('en-US');
                        document.querySelector('#current-processing').innerHTML = response.data.count[
                            {{ ORDER_STATUS_PROCESSING }}].toLocaleString('en-US');
                        document.querySelector('#current-packing').innerHTML = response.data.count[
                            {{ ORDER_STATUS_PACKING }}].toLocaleString('en-US');
                        document.querySelector('#current-pending-shipping').innerHTML = response.data.count[
                            {{ ORDER_STATUS_READY_TO_SHIP }}].toLocaleString('en-US');
                        document.querySelector('#current-shipping').innerHTML = response.data.count[
                            {{ ORDER_STATUS_SHIPPING }}].toLocaleString('en-US');

                        // echarts.init(document.querySelector("#trafficChart")).setOption({
                        //     tooltip: {
                        //         trigger: 'item'
                        //     },
                        //     legend: {
                        //         top: '5%',
                        //         left: 'center'
                        //     },
                        //     series: [{
                        //         name: 'Access From',
                        //         type: 'pie',
                        //         radius: ['40%', '70%'],
                        //         avoidLabelOverlap: false,
                        //         label: {
                        //             show: false,
                        //             position: 'center'
                        //         },
                        //         emphasis: {
                        //             label: {
                        //                 show: true,
                        //                 fontSize: '18',
                        //                 fontWeight: 'bold'
                        //             }
                        //         },
                        //         labelLine: {
                        //             show: false
                        //         },
                        //         data: [{
                        //                 value: response.data.count[
                        //                     {{ ORDER_STATUS_PENDING }}].toLocaleString('en-US') ?? 0,
                        //                 name: 'Pending'
                        //             },
                        //             {
                        //                 value: response.data.count[
                        //                     {{ ORDER_STATUS_PROCESSING }}].toLocaleString('en-US') ?? 0,
                        //                 name: 'Processing'
                        //             },
                        //             {
                        //                 value: response.data.count[
                        //                     {{ ORDER_STATUS_PACKING }}].toLocaleString('en-US') ?? 0,
                        //                 name: 'Packing'
                        //             },
                        //             {
                        //                 value: response.data.count[
                        //                     {{ ORDER_STATUS_READY_TO_SHIP }}].toLocaleString('en-US') ?? 0,
                        //                 name: 'Pending Shipment'
                        //             },
                        //             {
                        //                 value: response.data.count[
                        //                     {{ ORDER_STATUS_SHIPPING }}].toLocaleString('en-US') ?? 0,
                        //                 name: 'Shipping'
                        //             }
                        //         ]
                        //     }]
                        // });
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
                // axios.post(`api/dashboard/statistics`, {
                //         start: '{{ Carbon::now()->startOfDay()->startOfDay() }}',
                //         end: '{{ Carbon::now()->endOfDay()->endOfDay() }}'
                //     })
                //     .then(function(response) {
                //         document.querySelector('#stats-pending').innerHTML = response.data[
                //             {{ ORDER_STATUS_PENDING }}] ?? 0;
                //         document.querySelector('#stats-processing').innerHTML = response.data[
                //             {{ ORDER_STATUS_PROCESSING }}] ?? 0;
                //         document.querySelector('#stats-cn-generated').innerHTML = response.data[
                //             {{ ORDER_STATUS_PACKING }}] ?? 0;
                //         document.querySelector('#stats-parcel-scan').innerHTML = response.data[
                //             {{ ORDER_STATUS_READY_TO_SHIP }}] ?? 0;
                //         document.querySelector('#stats-shipping').innerHTML = response.data[
                //             {{ ORDER_STATUS_SHIPPING }}] ?? 0;
                //     })
                //     .catch(function(error) {
                //         console.log(error);
                //     });
            });

            // let stats_time = document.querySelector('#stats-time');
            // document.querySelectorAll('.stats-time').forEach((time) => {
            //     time.addEventListener('click', (e) => {
            //         stats_time.innerHTML = `| ${time.getAttribute('data-type')}`;
            //         axios.post(`/api/dashboard/statistics`, {
            //                 start: time.getAttribute('data-start'),
            //                 end: time.getAttribute('data-end')
            //             })
            //             .then(function(response) {
            //                 document.querySelector('#stats-pending').innerHTML = response.data[
            //                     {{ ORDER_STATUS_PENDING }}] ?? 0;
            //                 document.querySelector('#stats-processing').innerHTML = response
            //                     .data[
            //                         {{ ORDER_STATUS_PROCESSING }}] ?? 0;
            //                 document.querySelector('#stats-cn-generated').innerHTML = response
            //                     .data[
            //                         {{ ORDER_STATUS_PACKING }}] ?? 0;
            //                 document.querySelector('#stats-parcel-scan').innerHTML = response
            //                     .data[
            //                         {{ ORDER_STATUS_READY_TO_SHIP }}] ?? 0;
            //                 document.querySelector('#stats-shipping').innerHTML = response.data[
            //                     {{ ORDER_STATUS_SHIPPING }}] ?? 0;
            //             })
            //         // .catch(function(error) {
            //         //     console.log(error);
            //         // });

            //     });
            // });

            // // if one of info-card clicked
            // document.querySelectorAll('.info-card').forEach((card) => {
            //     card.addEventListener('click', (e) => {
            //         e.preventDefault();
            //         let status = card.getAttribute('data-url');
            //         window.location.href = status;
            //     });
            // });
        </script>
    </x-slot>

</x-layout>
