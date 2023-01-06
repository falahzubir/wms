<x-layout :title="$title">
<style>
#myBar {
    width: 10%;
    height: 30px;
    background-color: #04AA6D;
    text-align: center; /* To center it horizontally (if you want) */
    line-height: 30px; /* To center it vertically */
    color: white;
}
</style>
    <section class="section">
        @if(session()->has('success'))
            <x-toasts message="{{ session('success') }}" bg="success" />
        @endif
        <div class="row">
            @php
                $title = ['Northen Region 1 (NR1)', 'Southen Region 2 (SR2)', 'Easten Region 3 (ER3)', 'Westen Region 4 (WR4)', 'Northen Region 5 (NR5)', 'Southen Region 6 (SR6)', 'Easten Region 7 (ER7)', 'Westen Region 8 (WR8)', 'Northen Region 9 (NR9)'];
            @endphp
            <div class="col-md-4">
                <div class="card" style="height: 85%" role="button" data-bs-toggle="modal"
                    data-bs-target="#bucket-modal" onclick="add_bucket()">
                    <div class="card-body p-3 btn-ready-to-ship">
                        <div style="font-weight:bold">
                            <div class="text-center">
                                <div>
                                    <strong>
                                        <i class="bi bi-plus" style="font-size: 3rem;"></i>
                                    </strong>
                                </div>
                                <div>Add Bucket</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($buckets as $bucket)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div style="font-size:0.9rem;">
                                <div class="text-center">
                                    <strong><i class="bi bi-basket"></i>&nbsp; {{ $bucket->name }} </strong>
                                </div>
                                <hr>
                                <div>
                                    <div>Pending: <strong><span id="pending-count">{{ '23' }}</span></strong></div>
                                    <div>Last out: {{ date('d/m/Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary rounded-pill" id="download-consignment"><i class="bi bi-download"></i></button>
                                    <a href="/orders/overall" class="btn btn-info rounded-pill"><i
                                            class="bi bi-list"></i></a>
                                    <button class="btn btn-warning rounded-pill" class="edit-bucket" onclick="edit_bucket(this)" data-bs-toggle="modal"
                                        data-bs-target="#bucket-modal" data-bucket-id="{{ $bucket->id }}"><i class="bi bi-pencil"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </section>
    <div class="modal fade" id="bucket-modal" tabindex="-1">
        <form action="/" method="POST" id="bucket-form">
            @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bucket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="bucket-name" class="form-label">Bucket Name</label>
                            <input type="text" name="name" class="form-control" id="bucket-name">
                        </div>
                        {{-- <div class="col-md-6">
                            <label for="bucket-region" class="form-label">Region / State</label>
                            <select class="form-control" id="bucket-region-edit">
                                <option selected disabled>Select region...</option>
                                <optgroup label="Regions">
                                    <option>Northen Region</option>
                                    <option>Southen Region</option>
                                    <option>Easten Region</option>
                                    <option>Westen Region</option>
                                </optgroup>
                                <optgroup label="States">
                                    <option>Perlis</option>
                                    <option>Kedah</option>
                                    <option>Penang</option>
                                    <option>Perak</option>
                                    <option>Kelantan</option>
                                    <option>Terengganu</option>
                                    <option>Pahang</option>
                                    <option>Selangor</option>
                                    <option>Negeri Sembilan</option>
                                    <option>Melaka</option>
                                    <option>Johor</option>
                                    <option>Sabah</option>
                                    <option>Sarawak</option>
                                </optgroup>
                            </select>
                        </div> --}}
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="bucket-city" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="bucket-description" rows="5">
                            </textarea>
                        </div>
                        {{-- <div class="col">
                            <div class="mb-3">
                                <label for="bucket-event" class="form-label">Event</label>
                                <select class="form-control" id="bucket-event">
                                    <option selected disabled>Select event...</option>
                                    <option>None</option>
                                    <option>Early Bird Neloco</option>
                                    <option>Shocking Sales 9.9</option>
                                    <option>Shocking Sales 11.11</option>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Auto import</label>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Delete Bucket Button -->

                    <a href="#" class="text-danger" onclick="delete_bucket(this)"><i class="bi bi-trash"></i> Delete</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
    </div><!-- End Vertically centered Modal-->

    <x-slot name="script">
        <script>


            // add bucket
            function add_bucket() {
                document.querySelector('#bucket-form').setAttribute('action', '/buckets/store');
                document.querySelector('.modal-title').innerHTML = 'Add Bucket';
                document.querySelector('#bucket-name').value = '';
                document.querySelector('#bucket-description').innerHTML = '';
            }

            // edit bucket
            function edit_bucket(params) {
                bucket_id = params.attributes['data-bucket-id'].value;
                document.querySelector('#bucket-form').setAttribute('action', '/buckets/update/'+bucket_id);
                document.querySelector('.modal-title').innerHTML = 'Edit bucket';
                axios.get('api/bucket/' + bucket_id)
                    .then(function(response) {
                        console.log(response.data);
                        document.querySelector('#bucket-name').value = response.data.name;
                        document.querySelector('#bucket-description').innerHTML = response.data.description;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }

            // delete bucket
            function delete_bucket(params) {
                Swal.fire({
                    title: 'Are you sure you want to delete this bucket?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Delete`,
                    denyButtonText: `Don't delete`,
                    showCancelButton: false,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        console.log(params);
                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                })
            }

            // pending-count onclick
            document.querySelector('#download-consignment').onclick = function() {
                //sweet alert html
                Swal.fire({
                    title: 'Download consignment',
                    html: `<div class="mb-3">
                        <div>Bucket Name: Southern Region (SR2)</div>
                        <div>Download Count: 56</div>
                        <div id="myProgress">
                            <button onclick="move()" id="download-btn">Download</button>
                            <div id="myBar" style="display:none;">10%</div>
                        </div>
                        </div>`,
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    }
                ).then((result) => {
                    setInterval(() => {
                        if (result.isConfirmed) {
                            move();
                            Swal.fire({
                                title: 'Downloaded!',
                                text: 'Your file has been downloaded.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    }, 4000);
                })
            };

            var i = 0;
            function move() {
                document.querySelector('#download-btn').style.display = 'none';
                document.querySelector('#myBar').style.display = 'block';
                if (i == 0) {
                    i = 1;
                    var elem = document.getElementById("myBar");
                    var width = 10;
                    var id = setInterval(frame, 10);
                    function frame() {
                    if (width >= 100) {
                        clearInterval(id);
                        i = 0;
                    } else {
                        width++;
                        elem.style.width = width + "%";
                        elem.innerHTML = width + "%";
                    }
                    }
                }
            }
        </script>
    </x-slot>

</x-layout>
