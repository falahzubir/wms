<x-layout :title="$title">

    <section class="section">

        <div class="row">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filters..</h5>

                    <!-- No Labels Form -->
                    <form class="row g-3">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-outline-secondary rounded-pill mx-1">Today</button>
                            <button type="button"
                                class="btn btn-outline-secondary rounded-pill mx-1">Yesterday</button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill mx-1">This
                                Month</button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill mx-1">Last
                                Month</button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill mx-1">Overall</button>
                        </div>
                        <div class="col-md-3">
                            <select id="inputState" class="form-select">
                                <option selected>Date Added</option>
                                <option>Date Shipping</option>
                                <option>Date Payment Received</option>
                                <option>Date Request Shipping</option>
                                <option>Date Scan Parcel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="From">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" placeholder="To">
                        </div>
                        <div>
                            <span role="button">
                                <strong>Advance Filter <i class="ri-arrow-down-s-fill"></i></strong>
                            </span>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="filter-order">Submit</button>
                        </div>
                    </form><!-- End No Labels Form -->

                </div>
            </div>

            <div class="card" style="" id="order-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <button class="btn btn-info" id="add-to-bucket-btn"><i class="bi bi-paid"></i>
                            Add to Bucket</button>
                    </div>
                    <!-- Default Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col"><input type="checkbox" name="" id="" onchange="toggleCheckboxes(this, 'check_order')"></th>
                                <th scope="col">Action</th>
                                <th scope="col">Order</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Product</th>
                                <th scope="col">Payment & Shipping</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @for ($i = 0; $i < 10; $i++)
                                <tr style="font-size: 0.8rem;">
                                    <th scope="row">{{ $i + 1 }}</th>
                                    <td><input type="checkbox" name="check_order" id=""></td>
                                    <td>
                                        <a href="#" class="btn btn-warning p-0 px-1"><i class="ri-ball-pen-line"></i></a>
                                        <a href="#" class="btn btn-danger p-0 px-1"><i class="bx bx-trash"></i></a>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <a href="#"><strong>SOE{{ Arr::random(["H","D"]) }}0000{{ sprintf('%09d', $i*12+1); }}</strong></a>
                                        </div>
                                        <div>
                                            {{ date("d/m/Y") }}
                                        </div>
                                        <div>
                                            {{ date("H:i A") }}
                                        </div>
                                    </td>
                                    <td>
                                        <div><strong>Muhamad Iqbal</strong></div>
                                        <div>
                                            +60123457890
                                        </div>
                                        <div>
                                            123, Jln 4/5, Taman 567, 89012, Kuala Lumpur
                                        </div>
                                    </td>
                                    <td>
                                        <div>Olive Tin <strong>[5]</strong></div>
                                        <div>Neloco <strong>[2]</strong></div>
                                        <div>Shaker (FOC) <strong>[2]</strong></div>
                                    </td>
                                    <td>
                                        <div>RM150.00</div>
                                        <div>
                                            @if ($i%2 == 0)
                                            <span class="badge bg-success text-light">Paid</span>
                                            @else
                                            <span class="badge bg-warning text-dark">COD</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-warning text-danger">DHL</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @switch($i)
                                            @case(1)
                                            <span class="badge btn-pending">Pending</span>
                                            @break
                                            @case(5)
                                            <span class="badge btn-bucket">On Bucket</span>
                                            <span class="badge btn-ready-to-ship">Ready to Ship</span>

                                                @break
                                                @case(8)
                                                <span class="badge btn-packing">Packing</span>
                                                @break
                                                @case(9)
                                                <span class="badge btn-shipping">Shipping</span>
                                                @break
                                                @case(10)
                                                    <span class="badge btn-delivered">Delivered</span>
                                                @break


                                                @default
                                                <span class="badge btn-returned">Returned</span>

                                            @endswitch
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            <tr>
                                <td colspan="100%" class="text-center">
                                    <div class="spinner-border text-secondary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                                </tr>
                        </tbody>
                    </table>
                    <!-- End Default Table Example -->
                </div>
            </div>

        </div>

    </section>

    <x-slot name="script">
        <script>
            document.querySelector('#filter-order').onclick = function() {
                document.querySelector('#order-table').style.display = 'block';
            }

            document.querySelector('#add-to-bucket-btn').onclick = function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Do you want to continue',
                    icon: 'error',
                    confirmButtonText: 'Cool'
                })
            }
        </script>
    </x-slot>

</x-layout>
