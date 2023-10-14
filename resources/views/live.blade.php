<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ config('app.name') }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    {{-- font arial --}}
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|arial:300,300i,400,400i,600,600i,700,700i"
        rel="stylesheet">
    {{-- Vite Processing --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CDN CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        body {
            width: 100%;
            height: 100vh;
            font-family: 'arial';
            padding: 0 5rem;
            padding-top: 5rem;
        }

        .info-card {
            color: white;
            font-weight: bold;
            height: 175px;
        }

        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .performance-month img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .icon-path-position {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }

        .icon-clock-first {
            position: absolute;
            top: 60px;
            right: 80px;
        }

        .icon-clock-second {
            position: absolute;
            top: 60px;
            right: 10px;
        }
        .crown {
            color: #FFD700;
        }

        .pending-count {
            font-size: 2.5rem;
            font-weight: bolder;
        }
    </style>
</head>

<body>

    <div class="containers p-5">
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <h1><strong>Packer Productivity Dashboard</strong></h1>
                <div>{{ date('l, j F Y') }}</div>
                <div class="small">Last updated: <span id="lastUpdated">Fetching...</span></div>
            </div>
            <div class="fullscreen-button">
                <button class="btn btn-light" onclick="toggleFullScreen(this)"><i class="bi bi-fullscreen"></i></button>
            </div>
        </div>
        <div class="cards row mb-5">
            <div class="col-4 pe-4">
                <div class="card info-card position-relative" role="button" style="background-color: #EE855B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="icon-clock-first"><path fill="currentColor" d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2m4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3Z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" viewBox="0 0 24 24" class="icon-path-position"><path fill="currentColor" d="M20.23 7.24L12 12L3.77 7.24a1.98 1.98 0 0 1 .7-.71L11 2.76c.62-.35 1.38-.35 2 0l6.53 3.77c.29.173.531.418.7.71z" opacity=".25"/><path fill="currentColor" d="M12 12v9.5a2.09 2.09 0 0 1-.91-.21L4.5 17.48a2.003 2.003 0 0 1-1-1.73v-7.5a2.06 2.06 0 0 1 .27-1.01L12 12z" opacity=".5"/><path fill="currentColor" d="M20.5 8.25v7.5a2.003 2.003 0 0 1-1 1.73l-6.62 3.82c-.275.13-.576.198-.88.2V12l8.23-4.76c.175.308.268.656.27 1.01z"/></svg>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title "><strong>PENDING ORDER<br>(PROCESSING)</strong></h5>
                        <div class="d-flex align-items-center">
                            <div class="info-value-position">
                                <h3 class="pending-count text-center" id="pendingOrder">
                                    Loading...
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 px-2">
                <div class="card info-card position-relative" role="button" style="background-color: #693879;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" viewBox="0 0 24 24" class="icon-path-position"><path fill="currentColor" d="M8.066 4.065H3.648a1.732 1.732 0 0 0-.963.189a1.368 1.368 0 0 0-.619 1.226v4.585a.5.5 0 0 0 1 0v-4.28a1.794 1.794 0 0 1 .014-.518c.077-.236.319-.2.514-.2h4.472a.5.5 0 0 0 0-1Zm-6.003 9.872v4.418a1.733 1.733 0 0 0 .189.963a1.369 1.369 0 0 0 1.227.619h4.584a.5.5 0 0 0 0-1h-4.28a1.831 1.831 0 0 1-.518-.014c-.236-.077-.2-.319-.2-.514v-4.472a.5.5 0 0 0-1 0Zm13.871 5.998h4.418a1.732 1.732 0 0 0 .963-.189a1.368 1.368 0 0 0 .619-1.226v-4.585a.5.5 0 0 0-1 0v4.28a1.794 1.794 0 0 1-.014.518c-.077.236-.319.2-.514.2h-4.472a.5.5 0 0 0 0 1Zm6.003-9.872V5.645a1.733 1.733 0 0 0-.189-.963a1.369 1.369 0 0 0-1.227-.619h-4.584a.5.5 0 0 0 0 1h4.28a1.831 1.831 0 0 1 .518.014c.236.077.2.319.2.514v4.472a.5.5 0 0 0 1 0Z"/><rect width="1" height="8.709" x="10.999" y="7.643" fill="currentColor" rx=".5"/><rect width="1" height="8.709" x="14.249" y="7.643" fill="currentColor" rx=".5"/><rect width="1" height="8.709" x="16.499" y="7.643" fill="currentColor" rx=".5"/><rect width="1" height="8.709" x="6.499" y="7.643" fill="currentColor" rx=".5"/><rect width="1.5" height="8.709" x="8.499" y="7.643" fill="currentColor" rx=".75"/></svg>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title"><strong>TOTAL SCAN<br>ORDER</strong></h5>
                        <div class="d-flex align-items-center">
                            <div class="info-value-position">
                                <h3 class="pending-count text-center" id="totalScan">
                                    Loading...
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 ps-4">
                <div class="card info-card position-relative" role="button" style="background-color: #776ED1;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="icon-clock-second"><path fill="currentColor" d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2m4.2 14.2L11 13V7h1.5v5.2l4.5 2.7l-.8 1.3Z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" viewBox="0 0 32 32" class="icon-path-position"><path fill="currentColor" d="M4 16h12v2H4zm-2-5h10v2H2z"/><path fill="currentColor" d="m29.919 16.606l-3-7A.999.999 0 0 0 26 9h-3V7a1 1 0 0 0-1-1H6v2h15v12.556A3.992 3.992 0 0 0 19.142 23h-6.284a4 4 0 1 0 0 2h6.284a3.98 3.98 0 0 0 7.716 0H29a1 1 0 0 0 1-1v-7a.997.997 0 0 0-.081-.394ZM9 26a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm14-15h2.34l2.144 5H23Zm0 15a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm5-3h-1.142A3.995 3.995 0 0 0 23 20v-2h5Z"/></svg>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title"><strong>PENDING SHIPPING</strong></h5>
                        <div class="d-flex align-items-center">
                            <div class="info-value-position">
                                <h3 class="pending-count text-center" id="pendingShipping">
                                    Loading...
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4 pe-4" id="performanceMonthContainer">
                <div class="card bot-card" role="button" style="background-color: #A2CFD3;">
                    <div class="card-body">
                        <h5 class="card-title text-black mb-2"><strong>MONTHLY PERFORMANCE</strong></h5>
                        <div class="d-flex justify-content-between w-100 mb-2" style="border-bottom: 1px solid black;">
                            <div>Ranking</div>
                            <div>Scan Order</div>
                        </div>
                        <div id="performanceMonth">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8 ps-2">
                <div class="card bot-card" role="button" style="background-color: #F5F5F5;">
                    <div class="card-body">
                        <h6 class="card-title text-black"><strong>Total Scan Order</strong></h6>
                        <div id="chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>

        //determine height performanceMonthContainer
        const performanceMonthContainerHeight = document.getElementById('performanceMonthContainer').offsetHeight;

        let chart_options = {
            chart: {
                type: 'bar',
                height: performanceMonthContainerHeight,
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: ['#693877'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '50%'
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'middle',
                offsetX: 10,
                formatter: function(val) {
                    return val + ' scans'; // Customize the label format here
                },
                style: {
                    textAlign: 'left',
                    fontSize: '12px',
                },
                align: 'left',
            },
            series: [{
                data: []
            }]
        }

        const chart = new ApexCharts(document.querySelector("#chart"), chart_options);

        chart.render();

        //doc ready
        document.addEventListener('DOMContentLoaded', function() {
            getPerformanceMonth();
        });

        setInterval(() => {
            getPerformanceMonth();
        }, 30000);



        const toggleFullScreen = (el) => {
            if (!document.fullscreenElement) {
                el.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    el.innerHTML = '<i class="bi bi-fullscreen"></i>';
                    document.exitFullscreen();
                }
            }
        }

        const pendingOrder = document.getElementById('pendingOrder');
        const totalScan = document.getElementById('totalScan');
        const pendingShipping = document.getElementById('pendingShipping');

        const performanceMonth = document.getElementById('performanceMonth');

        const getPerformanceMonth = () => {
            const year = new Date().getFullYear();
            const month = new Date().getMonth() + 1;
            const day = new Date().getDate();
            fetch(`/api/scanned-parcel/${year}/${month}/${day}`)
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#lastUpdated').innerHTML = moment().format('hh:mm:ss A');
                    performanceMonth.innerHTML = '';
                    pendingOrder.innerHTML = number_format(data.current_process[2]);
                    pendingShipping.innerHTML = number_format(data.current_process[4]);

                    let data_scans = data.scans;
                    let data_scans_today = data.scans_today;
                    let total_scans = 0;
                    let highest_scan = 0;
                    data_scans.sort((a, b) => {
                        return b.count - a.count;
                    });
                    data_scans.forEach((item, index) => {
                        if (item.count > highest_scan) {
                            highest_scan = item.count;
                        }
                        total_scans += item.count;
                        performanceMonth.innerHTML += `
                        <div class="d-flex performance-month justify-content-between my-2">
                                <div class="d-flex align-items-center gap-2 w-100">
                                    <div>
                                        <img src="/storage/image/${item.img}" class="img-fluid"
                                            alt="">
                                    </div>
                                    <div class="pack-count-${item.count} d-flex gap-2">
                                        ${item.name}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    ${number_format(item.count)}
                                </div>
                            </div>
                    `;
                    })

                    document.querySelectorAll(`.pack-count-${highest_scan}`).forEach((item, index) => {
                        item.style.fontWeight = 'bold';
                        item.insertAdjacentHTML('beforeend', `<div class="crown"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 640 512"><path fill="currentColor" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48c0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8c0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8c26.5 0 48-21.5 48-48s-21.5-48-48-48z"/></svg></div>`);
                    });

                    totalScan.innerHTML = number_format(total_scans);

                    // convert data to chart
                    const data_converted = [];
                    data_scans_today.forEach((item, index) => {
                        data_converted.push({
                            x: item.name,
                            y: item.count
                        })
                    });

                    updateGraph(data_converted);

                });

        }

        const updateGraph = (data) => {

            chart.updateSeries([{
                data: data
            }])
        }

        const number_format = (num) => {
            //if num not integer
            if (num % 1 != 0) {
                return num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>
</body>

</html>
