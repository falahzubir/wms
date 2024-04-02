<x-layout :title="$title" :crumbList="$crumbList ?? ''">
    <style>
        .custom-card {
            border: 1px solid blue;
            border-radius: 5px;
            padding: 60px;
            margin: 10px;
            cursor: pointer;
        }

        i {
            font-size: 24px;
        }

        .text-custom {
            font-size: 20px;
            font-weight: 600;
        }

        .icon-position-custom {
            display: flex;
            justify-content: center;
        }

        .container {
            padding-top: 140px;
            padding-bottom: 140px;
        }

        /*small mobile  */
        @media (max-width: 567px) {

            .custom-card {
                border: 1px solid blue;
                border-radius: 5px;
                height: 200px;
                cursor: pointer;
            }

            .text-custom {
                font-size: 16px;
                font-weight: 600;
            }

            i {
                font-size: 20px;
            }

            .icon-position-custom {
                /* icon inline */
                display: inline-flex;
                justify-content: center;
            }

            .container {
                padding-top: 40px;
                padding-bottom: 40px;
            }
        }

        /* medium mobile */
        @media (min-width: 568px) and (max-width: 768px) {

            .custom-card {
                border: 1px solid blue;
                border-radius: 5px;
                padding: 30px;
                margin: 10px;
                cursor: pointer;
            }

            .text-custom {
                font-size: 18px;
                font-weight: 600;
            }

            i {
                font-size: 22px;
            }

            .icon-position-custom {
                /* icon inline */
                display: inline-flex;
                justify-content: center;
            }

            .container {
                padding-top: 80px;
                padding-bottom: 80px;
            }
        }


        /* Desktop */
        @media (min-width: 991px) {}
    </style>

    <section class="section">

        <div class="row">

            <div class="card" id="filter-body">
                <div class="card-body">
                    <div class="container">
                        <div class="row row-cols-2">
                            <div class="col">
                                <div class="card custom-card" onclick="generalSetting(1)">
                                    <div class="icon-position-custom align-items-center">
                                        <span class="d-inline-block">
                                            <i class="bi bi-gear-fill"></i>
                                        </span>
                                        <span class="d-inline-block ms-2 text-custom">
                                            General
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card custom-card" onclick="generalSetting(2)">
                                    <div class="icon-position-custom align-items-center">
                                        <span class="d-inline-block">
                                            <i class="bi bi-truck-front-fill"></i>
                                        </span>
                                        <span class="d-inline-block ms-2 text-custom">
                                            Service-Level Aggrement (SLA)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col">
                                <div class="card custom-card" onclick="generalSetting(3)">
                                    <div class="icon-position-custom align-items-center">
                                        <span class="d-inline-block">
                                            <i class="bi bi-map-fill"></i>
                                        </span>
                                        <span class="d-inline-block ms-2 text-custom">
                                            Courier Coverage
                                        </span>
                                    </div>
                                </div>
                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <x-slot name="script">
        <script>
            $(document).ready(function() {

            });

            const generalSetting = (type) => {
                const cour_id = {{ $item['courier_id'] }};
                window.location.href = `/couriers/general-setting/${cour_id}/` + type;
            }

        </script>

    </x-slot>
</x-layout>
