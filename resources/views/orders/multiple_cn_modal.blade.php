<div class="modal fade" id="multiple-cn-modal" tabindex="-1" aria-labelledby="multiple-cn-modalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="multiple-cn-modalLabel"></h1>
                <div class="d-flex">
                    <button class="btn btn-danger me-2" data-bs-dismiss="modal">Exit</button>
                    <button class="btn btn-primary" onclick="addCn()">Add CN</button>
                </div>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-9">
                        <b></b>
                    </div>
                    <div class="col-3">

                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div id="multiple-cn-modal-body"></div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Bal</th>
                                    <th class="text-center">CN</th>
                                </tr>
                            </thead>
                            <!-- <form action="" method="post"> -->
                            <tbody id="multiple-cn-modal-table-footer-body">

                            </tbody>
                            <!-- </form> -->
                        </table>

                        <div id="multiple-cn-modal-foot-note"></div>

                        <div class="float-end mt-3">
                            <button class="btn btn-success" onclick="generateMultipleCN()">Generate CN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
    </style>
</div>
@push("orders.multiple_cn_modal")
<script>
    let _order;

    function multiple_cn(data) {
        _order = JSON.parse(data.order);
        if(_order.bucket_batch_id == null){
            Swal.fire({
                title: 'Error!',
                text: 'Please assign bucket batch first',
                icon: 'error',
                confirmButtonText: 'OK'
            })
            return;
        }

        if (_order.purchase_type == {{ PURCHASE_TYPE_COD }}) {
            if (_order.courier_id == {{ DHL_ID }}) {
                if (_order.total_price > {{ MAX_DHL_COD_PER_PARCEL }}) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'DHL COD amount exceeds the limit',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            } else if (_order.courier_id == {{ NINJAVAN_MALAYSIA_ID }}) {
                if (_order.total_price > {{ MAX_NINJAVAN_COD_PER_PARCEL }}) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'NinjaVan COD amount exceeds the limit',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }
        }

        const myModal = new bootstrap.Modal('#multiple-cn-modal', {
            backdrop: 'static',
            keyboard: false
        });
        document.getElementById('multiple-cn-modal').addEventListener('show.bs.modal', render(data))
        document.getElementById('multiple-cn-modal').addEventListener('hidden.bs.modal', function() {
            const body = document.getElementById("multiple-cn-modal-body");
            const tableBody = document.getElementById("multiple-cn-modal-table-footer-body");
            body.innerHTML = "";
            tableBody.innerHTML = "";
        })
        myModal.show()
    }

    function render(data) {
        const body = document.getElementById("multiple-cn-modal-body");
        const label = document.getElementById("multiple-cn-modalLabel");
        const tableBody = document.getElementById("multiple-cn-modal-table-footer-body");
        const footNote = document.getElementById("multiple-cn-modal-foot-note");
        if(data.courier_id == {{ POSMALAYSIA_ID }}){
            footNote.innerHTML = "<small class='text-danger'>Note: For Pos Malaysia, maximum 20 CN per order allowed.</small>";
        }
        label.innerHTML = data.ref_no;
        body.insertAdjacentHTML('beforeend', cn());
        tableBody.innerHTML = tableFooter();
        document.querySelectorAll(".multiple-cn-input").forEach(function(el) {
            el.addEventListener("keyup", function(e) {
                recalculateBalance();
            })
        })
        resquenceCN();
    }

    function addCn() {
        const body = document.getElementById("multiple-cn-modal-body");
        const tableBody = document.getElementById("multiple-cn-modal-table-footer-body");

        body.insertAdjacentHTML('beforeend', cn(true));
        tableBody.innerHTML = tableFooter();
        document.querySelectorAll(".multiple-cn-input").forEach(function(el) {
            el.addEventListener("keyup", function(e) {
                recalculateBalance();
            })
        })
        document.querySelectorAll(".delete-cn").forEach(function(el) {
            el.addEventListener("click", function(e) {
                const id = e.currentTarget.dataset.id;
                if (document.getElementById(`row-${id}`) == null) {
                    return;
                }
                document.getElementById(`row-${id}`).remove();
                resquenceCN();
                recalculateBalance();
            })
        })
        resquenceCN();
    }

    function cn(allowDelete = false) {
        const id = Math.random().toString(36).replace(/[^a-z]+/g, '');
        let html = `<div class="card mb-3 multiple-cn-card" id="row-${id}" data-order-id="${_order.id}" data-courier-id="${_order.courier_id}">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        Consignment Note <span class="cn_sequence">0</span>
                    </div>`

        if (allowDelete) {
            html += `<div class="col-6">
                        <i class="text-danger bx bx-trash float-end delete-cn" style="font-size: 25px;cursor:pointer" data-id="${id}"></i>
                    </div>`
        }

        html += `</div>
            </div>`

        for (const item of _order.items) {
            html += `<div class="card-body p-2">
                <div class="row m-2">
                    <div class="col-8 my-auto">
                        <div class="row ">
                            <div class="col-9 ">
                                ${item.product.name}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control multiple-cn-input" data-id="${item.id}" style="border: 0;outline: 0; background: transparent; border-bottom: 1px solid #ced4da; border-radius: 0 !important;box-shadow:none!important" placeholder="0">
                    </div>
                </div>
            </div>`
        }

        html += `<div class="card-footer" style="font-size:12px;">
                Leave the product with 0 quantity if you want to exclude it from CN
            </div>
        </div>`

        return html;
    }

    function tableFooter() {
        let html = "";
        for (let i = 0; i < _order.items.length; i++) {
            const item = _order.items[i];
            html += `<tr id="row-${item.id}">
                <td>${item.product.name}</td>
                <td class="text-center" id="quantity">${item.quantity}</td>
                <td class="text-center" id="balance">${item.cn_qty ?? 0}</td>`
            if (i == 0) {
                html += `<td class="text-center align-middle" rowspan="${_order.items.length}">${document.querySelectorAll('.multiple-cn-card').length}</td>`
            }
            html += `</tr>`
        }

        return html;
    }

    function resquenceCN() {
        const list = document.querySelectorAll(".cn_sequence");

        for (let i = 0; i < list.length; i++) {
            const element = list[i];
            element.innerHTML = i + 1;
        }
    }

    function recalculateBalance() {
        const sum = [];

        for (const element of document.querySelectorAll(".multiple-cn-input")) {
            const id = element.dataset.id;
            const index = sum.findIndex(s => s.id == id);
            let val = parseInt(element.value);

            if (Number.isNaN(val)) {
                continue;
            }

            if (index == -1) {
                sum.push({
                    id: id,
                    t: val
                });
            } else {
                sum[index].t = sum[index].t + val;
            }
        }

        for (const s of sum) {
            const i = _order.items.findIndex(i => i.id == s.id);
            let val = _order.items[i].quantity - s.t;

            if (val < 1) {
                val = 0;
            }

            _order.items[i].cn_qty = val;

        }

        const tableBody = document.getElementById("multiple-cn-modal-table-footer-body");
        tableBody.innerHTML = tableFooter();
    }

    function generateMultipleCN() {
            // validate
            const errors = [];
            var arr_data = []; //for all CN
            var order_id = 0;
            var courier_id = 0;

            for (const [i, card] of document.querySelectorAll(".multiple-cn-card").entries()) {
                let total = 0;
                var arr_item = []; //for each CN
                order_id = card.getAttribute('data-order-id');
                courier_id = card.getAttribute('data-courier-id');

                for (const input of card.querySelectorAll(".multiple-cn-input")) {
                    const val = parseInt(input.value);
                    const order_item_id = input.getAttribute('data-id');
                    var item = [];

                    //store in array first
                    quantity = Number.isNaN(val) ? 0 : val; //if quantity NaN change to 0
                    arr_item.push({
                        order_item_id: order_item_id,
                        quantity: quantity
                    });

                    if (Number.isNaN(val)) {
                        continue;
                    }

                    if (val < 1) {
                        continue;
                    }

                    total += val;
                }

                if (total == 0) {
                    errors.push(`CN ${(i+1)} all quantity can't be empty`);
                } else {
                    arr_data.push(arr_item);
                }
            }

            if (errors.length > 0) {
                // has Error;
                Swal.fire({
                    title: 'Empty quantity!',
                    html: errors.join('<br>'),
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
            } else {
                //check balance
                const tableBody = document.querySelector("#multiple-cn-modal-table-footer-body");
                const rows = tableBody.querySelectorAll('tr');
                quantity = 0;
                balance = 0;
                overall_balance = 0;

                rows.forEach(row => {
                    quantity += Number(row.querySelector('#quantity').textContent);
                    balance += Number(row.querySelector('#balance').textContent);
                });
                overall_balance = quantity - balance;

                if (overall_balance != quantity) {
                    console.log(overall_balance);
                    Swal.fire({
                        title: 'Balance Available',
                        html: 'There are available balance for this order',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {

                    //safekeeping checkbox value
                    let inc_packing_list_generated_cn_multiple_cn_checkbox_value;
                    Swal.fire({
                        title: `Are you sure to generate multiple Consignment Note?`,
                        html: `<p class="text-secondary" style="font-size:0.75rem">You are about to generate shipping label for this order.</p><label style="font-size:0.8rem"><input type="checkbox" name="inc_packing_list_generated_cn_multiple_cn" id="inc-packing-list-generated-cn-multiple-cn" checked> <span class="ms-2" for="inc-packing-list-generated-cn-multiple-cn">Include packing list</span></label>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, generate it!',
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            let inc_packing_list_generated_cn_multiple_cn = document.getElementById('inc-packing-list-generated-cn-multiple-cn');
                            inc_packing_list_generated_cn_multiple_cn_checkbox_value = inc_packing_list_generated_cn_multiple_cn.checked;
                            Swal.fire({
                                title: 'Generating shipping label...',
                                html: 'Please wait while we are generating shipping labels for this order.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                            });

                            axios.post(`{{ route('shipping.generate_cn_multiple') }}`, {
                                    order_id: order_id,
                                    courier_id: courier_id,
                                    cn_data: arr_data
                                })
                                .then(response => {
                                    Swal.close();
                                    if (response.data.status == 'error' || response.data.success == false) {
                                        const errorMessages = response.data.data.map(err => err.message).join('\n');
                                        Swal.fire({
                                            title: 'Error!',
                                            text: errorMessages || response.data.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        })
                                        return;
                                    } else if (response.data.status == 'success' || response.data.success == true) {
                                        Swal.fire({
                                            title: 'Success!',
                                            html: `<div class="text-muted" style="font-size:0.8rem;color:#777">${inc_packing_list_generated_cn_multiple_cn_checkbox_value ? 'Shipping label and packing list generated' : 'Shipping label generated'}</div>`,
                                            icon: 'success',
                                            confirmButtonText: 'Download',
                                            cancelButtonText: 'OK', // Change close button text to 'OK'
                                            showCancelButton: true, // Show close button
                                            showCloseButton: true,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                axios({
                                                        url: '/api/download-consignment-note',
                                                        method: 'POST',
                                                        responseType: 'json', // important
                                                        data: {
                                                            order_ids: [order_id],
                                                            inc_packing_list: inc_packing_list_generated_cn_multiple_cn_checkbox_value
                                                        }
                                                    })
                                                    .then(function(res) {
                                                        console.log(res);
                                                        // redirect
                                                        const fileName = String(res.data
                                                            .download_url).split("/").pop();
                                                        let a = document.createElement('a');
                                                        a.target = '_blank';
                                                        a.download = fileName;
                                                        a.href = res.data.download_url;
                                                        a.click();
                                                        // window.location.href = res.data.download_url;
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Success',
                                                            html: `<div>${inc_packing_list_generated_cn_multiple_cn_checkbox_value ? 'Download Request CN and Packing List Successful' : 'Download Request CN Successful'}.</div>
                                             <div>Click <a href="${res.data.download_url}" target="_blank" download="${fileName}">here</a> if items not downloaded.</div>`,
                                                            // footer: '<small class="text-danger">Please enable popup if required</small>',
                                                            allowOutsideClick: false
                                                        }).then((result) => {
                                                            location.reload();
                                                        })

                                                    }).catch(() => {
                                                        Swal.fire({
                                                            title: 'Success!',
                                                            html: `Failed to generate pdf`,
                                                            allowOutsideClick: false,
                                                            icon: 'error',
                                                        });

                                                    })
                                            }
                                        })
                                    }
                                })
                                .catch(error => {
                                    const errorDetails = error.response.data?.data || [];
                                    const errorMessages = errorDetails.map(err => err.message).join('\n');
                                    Swal.fire({
                                        title: 'Error!',
                                        text: errorMessages || error.response.data.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    })
                                });
                            // Simulating AJAX call (replace this with your actual AJAX call)
                            // setTimeout(() => {
                            //     // Close the loading Swal alert after AJAX call is complete
                            //     Swal.close();
                            //     // Here you can place your actual AJAX call
                            // }, 2000);
                        } else if (result.isDenied) {
                            // Swal.fire("Changes are not saved", "", "info");
                        }
                    });




                    // Swal.fire({
                    //     title: 'Generating shipping label...',
                    //     html: 'Please wait while we are generating shipping labels for this order.',
                    //     allowOutsideClick: false,
                    //     didOpen: () => {
                    //         Swal.showLoading()
                    //     },
                    // });
                    // axios.post(`{{ route('shipping.generate_cn_multiple') }}`, {
                    //     order_id : order_id,
                    //     courier_id : courier_id,
                    //     cn_data : arr_data
                    // })
                    // .then(response => {
                    //     if (response.data.status == 'error') {
                    //         Swal.fire({
                    //             title: 'Error!',
                    //             text: response.data.message,
                    //             icon: 'error',
                    //             confirmButtonText: 'OK'
                    //         })
                    //         return;
                    //     }
                    //     else if (response.data.status == 'success'){
                    //         Swal.fire({
                    //             title: 'Success!',
                    //             text: response.data.message,
                    //             icon: 'success',
                    //             confirmButtonText: 'Download CN',
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 axios({
                    //                         url: '/api/download-consignment-note',
                    //                         method: 'POST',
                    //                         responseType: 'json', // important
                    //                         data: {
                    //                             order_ids: [order_id],
                    //                         }
                    //                     })
                    //                     .then(function(res) {
                    //                         // redirect
                    //                         const fileName = String(res.data.download_url).split("/").pop();
                    //                         let a = document.createElement('a');
                    //                         a.target = '_blank';
                    //                         a.download = fileName;
                    //                         a.href = res.data.download_url;
                    //                         a.click();
                    //                         // window.location.href = res.data.download_url;
                    //                         Swal.fire({
                    //                             icon: 'success',
                    //                             title: 'Success',
                    //                             html: `<div>Download Request CN Successful.</div>
                //                             <div>Click <a href="${res.data.download_url}" target="_blank" download="${fileName}">here</a> if CN not downloaded.</div>`,
                    //                             footer: '<small class="text-danger">Please enable popup if required</small>',
                    //                             allowOutsideClick: false
                    //                         }).then((result) => {
                    //                             location.reload();
                    //                         })

                    //                     }).catch(() => {
                    //                         Swal.fire({
                    //                             title: 'Success!',
                    //                             html: `Failed to generate pdf`,
                    //                             allowOutsideClick: false,
                    //                             icon: 'error',
                    //                         });

                    //                     })
                    //             }
                    //         })
                    //     }
                    // })
                    // .catch(error => {
                    //     Swal.fire({
                    //         title: 'Error!',
                    //         text: error.response.data.message,
                    //         icon: 'error',
                    //         confirmButtonText: 'OK'
                    //     })
                    // });
                }
            }

        }
</script>
@endpush
