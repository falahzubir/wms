<div class="modal-body position-relative">
    <button type="button" class="btn-close position-absolute bg-light rounded-circle"
        style="top: -3%;right:-3%;color:white" data-bs-dismiss="modal" aria-label="Close"></button>
    <h3 class="text-center" style="font-size: 1.35rem"><strong>Packing List</strong></h3>
    <div class="my-3">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span>Kopi Ala Kazim</span></td>
                    <td><span>1</span></td>
                    <td><span>RM XX.XX</span></td>
                    <td><span>RM XX.XX</span></td>
                </tr>
                <tr>
                    <td><span>Olive Tin</span></td>
                    <td><span>1</span></td>
                    <td><span>RM XX.XX</span></td>
                    <td><span>RM XX.XX</span></td>
                </tr>
                <tr>
                    <td><span>Neloco</span></td>
                    <td><span>1</span></td>
                    <td><span>RM XX.XX</span></td>
                    <td><span>RM XX.XX</span></td>
                </tr>
                <tr>
                    <td><span>Shaker(FOC)</span></td>
                    <td><span>1</span></td>
                    <td><span>RM XX.XX</span></td>
                    <td><span>RM XX.XX</span></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td style="font-weight: 700"><span>Qty Total : 4</span></td>
                    <td style="font-weight: 700" colspan="2"><span>Total</span></td>
                    <td style="font-weight: 700"><span>RM XX.XX</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div>
        <h4 class="text-center"><strong id="modal-preview-header-box"></strong></h4>
        <div class="text-center my-5" id="qr-code-box">
            <span class="position-absolute" id="first"></span>
            <span class="position-absolute" id="second"></span>
            <img width="150" height="150" src="{{ asset('assets/img/qr_code_2.png') }}" alt="">
            <span class="position-absolute" id="third"></span>
            <span class="position-absolute" id="fourth"></span>
        </div>
        <div class="" id="modal-preview-desc-box" style="font-size: 0.7rem;">

        </div>
    </div>
</div>
