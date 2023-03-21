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
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Bal</th>
                                    <th class="text-center">CN</th>
                                </tr>
                            </thead>
                            <tbody id="multiple-cn-modal-table-footer-body">

                            </tbody>
                        </table>


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
        let html = `<div class="card mb-3 multiple-cn-card" id="row-${id}">
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
                <td class="text-center">${item.quantity}</td>
                <td class="text-center">${item.cn_qty ?? 0}</td>`
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

    function generateMultipleCN(){
        // validate
        const errors = [];

      for (const [i,card] of document.querySelectorAll(".multiple-cn-card").entries()) {
        let total = 0;

        for (const input of card.querySelectorAll(".multiple-cn-input")) {
            const val = parseInt(input.value);

            if(Number.isNaN(val)){
                continue;
            }

            if(val <1){
                continue;
            }

            total += val;
        }

        if(total == 0){
            errors.push(`CN ${(i+1)} all qty can't be empty`)
        }
      }

      if(errors.length >0){
        // has Error;
      }
    }
</script>
@endpush