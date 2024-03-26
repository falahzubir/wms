<x-layout :title="$title">
    <style>
        .btn {
            --bs-btn-font-size: 0.8rem;
        }

        #filter-body .card-body * {
            font-size: 0.9rem;
        }

        .outline-good,
        .outline-defect {
            position: relative;
        }

        .outline-good {
            border: 1px solid #00ff00 !important;
        }

        .outline-defect {
            border: 1px solid #ff0000 !important;
        }

        .outline-good::before {
            content: "";
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            left: 5px;
            border: 1px solid #00ff00;
            box-shadow: 0 0 0 5px #CDF7E3;
        }

        .outline-defect::before {
            content: "";
            position: absolute;
            top: 5px;
            right: 5px;
            bottom: 5px;
            left: 5px;
            border: 1px solid #ff0000;
            box-shadow: 0 0 0 5px #F9C2C2;
        }

        #returnModal table {
            font-size: 0.8rem;
        }

        .img-50 {
            max-width: 50px;
            max-height: 50px;
        }

        .btn-purple {
            background-color: purple;
            color: white;
            /* Set the text color to white or another contrasting color */
        }

        .btn-purple:hover {
            background-color: #4b2b6b;
            color: white;
            /* Set the text color to white or another contrasting color */
        }

        .bg-susu {
            background-color: #FF8244;
        }

        .swal2-styled.swal2-custom {
            border: 0;
            border-radius: .25em;
            /* background: initial; */
            font-size: 1em;
            border: 1px solid #cecece;
            box-shadow: 1px 1px 0px 0px #cecece;
        }

        .swal2-dhl-ecommerce {
            background-color: #FFCC00;
            color: #D40510;
        }

        .swal2-posmalaysia {
            background-color: #fff;
            color: #FF0000;
        }

        .swal2-tiktok {
            background-color: #000;
            color: #fff;
        }

        .swal2-shopee {
            background-color: #E74A2B;
            color: #fff;
        }

        .swal2-styled.swal2-custom {
            border: 0;
            border-radius: .25em;
            /* background: initial; */
            font-size: 1em;
            border: 1px solid #cecece;
            box-shadow: 1px 1px 0px 0px #cecece;
        }

        .swal2-dhl-ecommerce {
            background-color: #FFCC00;
            color: #D40510;
        }

        .swal2-posmalaysia {
            background-color: #fff;
            color: #FF0000;
        }

        .swal2-tiktok {
            background-color: #000;
            color: #fff;
        }

        .swal2-shopee {
            background-color: #E74A2B;
            color: #fff;
        }

        .bg-purple {
            background-color: purple;
        }

        .btn-teal {
            background-color: #3B8C9E;
            color: #fff;
        }

        .btn-teal:hover {
            background-color: #2d6a75;
            color: #fff;
        }

        .small-check-box {
            margin: 4px 0 0;
            line-height: normal;
            width: 14px;
            height: 14px;
        }

        .modal-body {
            max-height: calc(100vh) !important;
            overflow: unset !important;
        }

        .custom-btn-width {
            width: 100%;
            /* Adjust this value as needed */
        }

        #attachment-type-file {
            margin: 8px 0px;
        }

        #upload-box {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #d4d4d4;
            cursor: pointer;
            /* padding: 20px; */
            color: black;
            min-height: 15vh;
            border-radius: 5px;
            text-align: center;
            background-color: #f6f6f6;
            transition: all 0.25s ease;
        }

        #upload-box:hover {
            background-color: #d5d1d1;
        }

        #promotional-attachment-preview .table thead>tr>th {
            font-size: 0.8rem;
            font-weight: 700;
        }

        #promotional-attachment-preview .table tbody>tr>td,
        #promotional-attachment-preview .table tfoot>tr>td {
            font-size: 0.75rem
        }

        .ck-editor__editable_inline {
            min-height: 100px;
        }

        #qr-code-box {
            position: relative;
        }

        #qr-code-box #first::before,
        #qr-code-box #second::before,
        #qr-code-box #third::before,
        #qr-code-box #fourth::before {
            content: "";
            position: absolute;
            width: 4rem;
            height: 3px;
            background-color: black;
        }

        #qr-code-box #first::after,
        #qr-code-box #second::after,
        #qr-code-box #third::after,
        #qr-code-box #fourth::after {
            content: "";
            position: absolute;
            width: 3px;
            height: 4rem;
            background-color: black;
        }

        #qr-code-box #first::before {
            top: -0.5rem;
            left: -0.5rem;
        }

        #qr-code-box #first::after {
            top: -0.5rem;
            left: -0.5rem;
        }

        #qr-code-box #second::before {
            bottom: -9.75rem;
            left: -0.5rem;
        }

        #qr-code-box #second::after {
            bottom: -9.75rem;
            left: -0.5rem;
        }

        #qr-code-box #third::before {
            top: -0.5rem;
            right: -0.5rem;
        }

        #qr-code-box #third::after {
            top: -0.5rem;
            right: -0.5rem;
        }

        #qr-code-box #fourth::before {
            bottom: -9.75rem;
            right: -0.5rem;
        }

        #qr-code-box #fourth::after {
            bottom: -9.75rem;
            right: -0.5rem;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" style="font-size:0.8rem" id="sdd-form">
                <div class="card-body">
                    <form id="shipping-doc-form" enctype="multipart/form-data">
                        <div class="card-title border-bottom mb-3 pb-0">
                            General Setting
                        </div>
                        <div>
                            <div class="row m-0">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="promotional-title-field"
                                            class="form-label"><strong>Title</strong></label>
                                        <input validate-type type="text" class="form-control form-control-sm"
                                            id="promotional-title-field" name="promotional_title_field"
                                            placeholder="Promotional Title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row m-0">
                                        <div class="col-md-6 ps-md-0">
                                            <div class="mb-3">
                                                <label for="start-date-field" class="form-label"><strong>Start
                                                        Date</strong></label>
                                                <input validate-type type="date" class="form-control form-control-sm"
                                                    id="start-date-field" name="start_date_field"
                                                    placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                        <div class="col-md-6 pe-md-0">
                                            <div class="mb-3">
                                                <label for="end-date-field" class="form-label"><strong>End
                                                        Date</strong></label>
                                                <input validate-type type="date" class="form-control form-control-sm"
                                                    id="end-date-field" name="end_date_field" placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operational-model-field" class="form-label"><strong>Operational
                                                Model</strong></label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="operational-model-field" name="operational_model_field[]"
                                            placeholder="Operational Model">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="platform-field" class="form-label"><strong>Platform</strong></label>
                                        <input type="text" class="form-control form-control-sm" id="platform-field"
                                            name="platform_field[]" placeholder="Platform">
                                    </div>
                                </div>
                                <div class="col-12 px-0 border-bottom">
                                    <p class="fs-6 card-title">PROMOTION & INFORMATION</p>
                                </div>
                                <div class="col-12 my-2 d-flex justify-content-between">
                                    <span class="d-inline-block">Promotional Link</span>
                                    <button type="button" class="btn btn-secondary"
                                        onclick="display_preview()">Preview</button>
                                </div>
                                <div class="col-12">
                                    <div id="attachment-type-header-section">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" value="qr"
                                                name="promotional_attachment_type" id="generate-link-qr"
                                                onchange="promotional_attachment(this)">
                                            <label class="form-check-label" for="generate-link-qr">
                                                Generate link QR
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" value="photo"
                                                onchange="promotional_attachment(this)"
                                                name="promotional_attachment_type" id="upload-photo-field" checked>
                                            <label class="form-check-label" for="upload-photo-field">
                                                Upload Photo
                                            </label>
                                        </div>
                                    </div>
                                    <div id="attachment-type-content-section">
                                        <div class="" id="attachment-type-file">
                                            <div class="">
                                                <div id="upload-box" onclick="triggerUpload()">
                                                    <div class="upload-guide">
                                                        <p class="mb-0 fs-2"><i class="bi bi-file-earmark-image"></i>
                                                        </p>
                                                        <p class="mb-0 d-inline-block"
                                                            style="border-bottom: 1px solid black">Click to Upload File
                                                        </p>
                                                    </div>

                                                </div>
                                                <div class="d-flex mt-1 justify-content-between">
                                                    <small class="text-muted">JPG, JPEG and PNG only</small>
                                                    <div>
                                                        <small class="text-muted d-block">100 MB maximum file
                                                            size</small>
                                                        <small class="text-muted d-block text-end">1 : 1 Image
                                                            Ratio</small>
                                                    </div>
                                                </div>
                                                <input type="file" id="fileInput"
                                                    name="promotional_link_upload_file" accept=".jpg, .jpeg, .png"
                                                    style="display: none;" onchange="handleFileChange()">
                                            </div>
                                        </div>
                                        <div class="d-none" id="attachment-type-qr">
                                            <div>
                                                <div class="mb-3">
                                                    <label for="at-qr-code-promo-link-field"
                                                        class="form-label">Promotional
                                                        Link (qr
                                                        code link)</label>
                                                    <input type="text" validate-type
                                                        class="form-control form-control-sm"
                                                        id="at-qr-code-promo-link-field"
                                                        name='at_qr_code_promo_link_field'
                                                        placeholder="Example: https://wms.grobook.com.my/xxx/xxx/xxx">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Include Order
                                                        Details :</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="1" id="at-qr-code-order-details-check-1"
                                                            name="at_qr_code_order_details_check[]">
                                                        <label class="form-check-label"
                                                            for="at-qr-code-order-details-check-1">
                                                            Order Id
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="2" id="at-qr-code-order-details-check-2"
                                                            name="at_qr_code_order_details_check[]">
                                                        <label class="form-check-label"
                                                            for="at-qr-code-order-details-check-2">
                                                            Tracking Number
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="3" id="at-qr-code-order-details-check-3"
                                                            name="at_qr_code_order_details_check[]">
                                                        <label class="form-check-label"
                                                            for="at-qr-code-order-details-check-3">
                                                            Phone Number
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <p><strong>Text Editing</strong></p>
                                    </div>
                                    <div class="p-2">
                                        <div>
                                            <div class="mb-3">
                                                <label for="at-qr-code-promo-header-field"
                                                    class="form-label">Header</label>
                                                <input type="text" validate-type
                                                    class="form-control form-control-sm"
                                                    id="at-qr-code-promo-header-field"
                                                    name='at_qr_code_promo_header_field'
                                                    placeholder="PLACE YOUR TEXT HEADER PROMOTION HERE">
                                                <small class="text-muted">*Limit 100 characters</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <div class="editor-box position-relative">
                                                    <div id="editor"></div>
                                                </div>
                                                <small class="text-muted">*limit 200 characters</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-secondary me-2">Clear</button>
                        <button class="btn btn-primary ms-2" onclick="execute_submit_save()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="promotional-attachment-preview" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
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
                            <img width="150" height="150" src="{{ asset('assets/img/qr_code_2.png') }}"
                                alt="">
                            <span class="position-absolute" id="third"></span>
                            <span class="position-absolute" id="fourth"></span>
                        </div>
                        <div class="" id="modal-preview-desc-box" style="font-size: 0.7rem;">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="true" class="bg-dark position-relative bd-example-toasts">
        <div class="toast-container position-fixed p-3" style="top: 0;right: 0;" id="toastPlacement">
            <div class="toast" id="alert-toast">
                <div class="toast-header">
                    {{-- <img src="..." class="rounded me-2" alt="..."> --}}
                    <strong class="me-auto">Alert</strong>
                    {{-- <small>11 mins ago</small> --}}
                </div>
                <div class="toast-body">
                    <span class="d-inline-block text-center"><i class="fa fa-spinner fa-spin"></i></span>
                    <span class="ms-2">Loading Core Data</span>
                </div>
            </div>
        </div>
    </div>

    @include('orders.multiple_cn_modal')
    <x-slot name="script">
        <script>
            const baseURL = "{{ asset('') }}";
            let desc_editor;
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'underline',
                            'strikethrough',
                            '|',
                            'undo',
                            'redo'
                        ]
                    }
                })
                .then(editor => {
                    desc_editor = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            // let editorData = ClassicEditor.instances[0].getData()


            // ELEMENT LIST \/
            const pa_photo = document.getElementById('attachment-type-file'); // promotional attachment 
            const pa_qr = document.getElementById('attachment-type-qr');
            const preview_modal_elem = document.getElementById('promotional-attachment-preview');
            const shopping_doc_form_elem = document.getElementById('shipping-doc-form');
            const alert_toast_elem = document.getElementById('alert-toast');
            // ELEMENT LIST /\

            let operational_model_tom_select;
            let platform_tomSelect;
            let alertToast;
            let form_id;

            let preview_modal;
            document.addEventListener('DOMContentLoaded', () => {

                operational_model_tom_select = new TomSelect('#operational-model-field', {
                    create: true
                });
                platform_tomSelect = new TomSelect('#platform-field', {
                    create: true
                }); // create tomselect
                preview_modal = new bootstrap.Modal(preview_modal_elem); // create modal instance
                // Modals State
                preview_modal_elem.addEventListener('hidden.bs.modal', function(event) {
                    // preview_modal.show();
                })
                preview_modal_elem.addEventListener('show.bs.modal', function(event) {
                    let promotion_header_field = document.querySelector(
                        "[name='at_qr_code_promo_header_field']");
                    let first_ckeditor_val = desc_editor.getData(); // promotion_description_field

                    const validateAndScroll = (form_src, fieldName, type, for_type) => {
                        console.log(for_type);
                        if (for_type == 'normal_field') {
                            const fieldValue = document.querySelector(`[name="${fieldName}"]`).value;
                            console.log(fieldValue);
                            // if (fieldValue.length > 0 && fieldValue != '') {
                            const result = input_validator(document.querySelector(`#${form_src}`), document
                                .querySelector(
                                    `[name="${fieldName}"]`), `${type}`);

                            return result;
                            // }

                            return true;
                        } else {
                            let validation_section = document.querySelector('.editor-box');
                            if (validation_section.querySelector('.validate-popup')) {
                                validation_section.querySelector('.validate-popup').remove();
                            }
                            if (first_ckeditor_val != '') {
                                return true;
                            } else {
                                validation_section.insertAdjacentHTML('beforeend',
                                    '<span class="validate-popup position-absolute" style="font-size:0.85rem;color:red;bottom:10%;right:2%">Invalid</span>'
                                );
                                return false;
                            }


                        }
                    };

                    const promotion_header_result = validateAndScroll('shipping-doc-form',
                        'at_qr_code_promo_header_field',
                        'text', 'normal_field'
                    ); //the parameter are [<form tag id>,<targetted field name attr>,<field type>,<determine for normal field or ckeditor>]
                    const promotion_desc_result = validateAndScroll(_, _, _,
                        'ckeditor'
                    ); //the parameter are [<no need>,<no need>,<no need>,<determine for normal field or ckeditor>] since there is only one ckeditor

                    if (promotion_header_result && promotion_desc_result) {
                        display_form_data_to_preview();
                    }



                })

                // Toast 
                alertToast = new bootstrap.Toast(alert_toast_elem, {
                    autohide: false
                }); // Returns a Bootstrap toast instance

                init_data();
            });

            function init_data() {
                form_id = `{{ Request::segment(4) ?? '' }}`
                alertToast.show();

                // GET DATA FOR TOM SELECT VALUE
                axios.get('/api/settings/init_shipping_doc_desc_data')
                    .then(function(response) {
                        sanitize_for_tomSelect(operational_model_tom_select, response.data.operationalModels, {
                            id: 'id',
                            name: 'name'
                        }); //set the data into the tomselect
                        // sanitize_for_tomSelect(platform_tomSelect, [{id:'22','name'}], {id: 'id',name: 'payment_type_name'}); //set the data into the tomselect
                        platform_tomSelect.addOption({
                            value: 22,
                            text: 'Shopee'
                        });
                        platform_tomSelect.addOption({
                            value: 23,
                            text: 'Tik Tok'
                        });
                        if (form_id == '') alert_toast_elem.querySelector('.toast-body').innerHTML =
                            '<span>Loading Complete</span>'; // change the text in the toast
                        setTimeout(() => {
                            if (form_id == '') alertToast.hide()
                        }, 3500); // hide the toast 

                        //IF the URL contain sdd id or entering editing phase
                        if (form_id != '') {
                            axios.get(`/api/settings/edit_sdd/form/${form_id}`)
                                .then(function(response) {
                                    let sdd_edit_data = response.data.data;
                                    alert_toast_elem.querySelector('.toast-body').innerHTML =
                                        '<span>Loading Complete</span>';

                                    document.getElementById('promotional-title-field').value = sdd_edit_data
                                        .promotional_title //title input field
                                    document.getElementById('start-date-field').value = sdd_edit_data
                                        .start_date; //start date input field
                                    document.getElementById('end-date-field').value = sdd_edit_data
                                        .end_date; //end date input field
                                    operational_model_tom_select.setValue(sdd_edit_data.operational_model_id.split(
                                        ',')); // set the data for op_model tomselect
                                    platform_tomSelect.setValue(sdd_edit_data.platform.split(
                                        ',')); // set the data for platform tomselect
                                    let link_type_radio = document.querySelector(
                                        `[name="promotional_attachment_type"][value='${sdd_edit_data.link_type == '1' ? 'qr' : 'photo'}']`
                                    ) //link type/promotional_attachment_type input field
                                    link_type_radio.checked = true;
                                    link_type_radio.dispatchEvent(new Event('change'));

                                    if (sdd_edit_data.link_type == '1') {
                                        document.getElementById('at-qr-code-promo-link-field').value = sdd_edit_data
                                            .content_path; //promo link (QR CODE SELECTED) date input field
                                        if (sdd_edit_data.additional_detail != null) {
                                            sdd_edit_data.additional_detail.forEach((detail_select) => {
                                                document.getElementById(
                                                        `at-qr-code-order-details-check-${detail_select}`)
                                                    .checked = true;
                                            });
                                        }
                                    } else {
                                        // Uploading the file
                                        // let reader = new FileReader();
                                        // // Set up event listeners to handle file reading
                                        // reader.onload = function(event) {
                                        //     // Set the file content to the file input element
                                        //     document.getElementById('fileInput').files = event.target.result;
                                        // };
                                        //     // Read the file as a data URL
                                        //     reader.readAsDataURL(sdd_edit_data.content_path);
                                    }

                                    document.getElementById('at-qr-code-promo-header-field').value = sdd_edit_data
                                        .promotion_header; //header input field

                                    desc_editor.setData(sdd_edit_data.description) //description (CKEDITOR)input field

                                    setTimeout(() => {
                                        alertToast.hide();
                                    }, 3500);
                                })
                                .catch(function(error) {
                                    alert_toast_elem.querySelector('.toast-body').innerHTML =
                                        '<span>Loading Data For Editing Failed</span>';
                                    console.log(error);
                                });
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });


            }

            function display_form_data_to_preview() {
                let promotion_header_field = document.querySelector("[name='at_qr_code_promo_header_field']");
                let first_ckeditor_val = desc_editor.getData(); // promotion_description_field

                let modal_preview_header_box = document.getElementById('modal-preview-header-box');
                let modal_preview_desc_box = document.getElementById("modal-preview-desc-box");

                modal_preview_desc_box.innerHTML = first_ckeditor_val;
                modal_preview_header_box.innerHTML = promotion_header_field.value;

            }

            function execute_submit_save() {
                let first_ckeditor_val = desc_editor.getData(); // promotion_description_field
                function ckeditor_validation() {
                    let validation_section = document.querySelector('.editor-box');
                    if (validation_section.querySelector('.validate-popup')) {
                        validation_section.querySelector('.validate-popup').remove();
                    }
                    if (first_ckeditor_val != '') {
                        return true;
                    } else {
                        validation_section.insertAdjacentHTML('beforeend',
                            '<span class="validate-popup position-absolute" style="font-size:0.85rem;color:red;bottom:10%;right:2%">Invalid</span>'
                        );
                        return false;
                    }
                };
                // HINT
                // gs- general setting section
                // te- text editing section 
                let gs_title_result = input_validator(shopping_doc_form_elem, document.querySelector(
                    `[name="promotional_title_field"]`), 'text');
                let gs_start_date_result = input_validator(shopping_doc_form_elem, document.querySelector(
                    `[name="start_date_field`), 'date');
                let gs_end_date_result = input_validator(shopping_doc_form_elem, document.querySelector(
                    `[name="end_date_field`), 'date');
                let promotional_attachment_type = document.querySelector('[name="promotional_attachment_type"]:checked');
                let promotional_link_result = true;

                if (promotional_attachment_type.value == 'qr') {
                    te_promo_link_result = input_validator(shopping_doc_form_elem, document.querySelector(
                            `[name="at_qr_code_promo_link_field`),
                        'text');
                }

                let te_header_result = input_validator(shopping_doc_form_elem, document.querySelector(
                        `[name="at_qr_code_promo_header_field`),
                    'text');
                let te_promotion_desc_result = ckeditor_validation();
                // let gs_op_result = input_validator(shopping_doc_form_elem, document.querySelector(`[name="operational_model_field`), 'tom-select');
                // let gs_platform_result = input_validator(shopping_doc_form_elem, document.querySelector(`[name="platform_field`), 'tom-select');
                if (gs_title_result && gs_start_date_result && gs_end_date_result && te_header_result &&
                    te_promotion_desc_result) {
                    let serializedFormData = new FormData(shopping_doc_form_elem);
                    serializedFormData.append('text_editor_description', desc_editor.getData());

                    if (form_id != '') { // if form_id exist in the segment or url = editing phase
                        axios({
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            method: 'post',
                            url: `/api/settings/shipping_doc_desc/form/update/${form_id}`, // Use the update route
                            data: serializedFormData
                        }).then(function(response) {
                            success_alert();
                            // console.log(response);
                        }).catch(function(error) {
                            console.log(error);
                        });
                    } else { // add new shipping document descripton phase
                        axios({
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            method: 'post',
                            url: '/api/settings/shipping_doc_desc/form/add',
                            data: serializedFormData
                        }).then(function(response) {
                            success_alert();
                            // console.log(response);
                        }).catch(function(error) {
                            console.log(error)
                        });
                    }

                    function success_alert() {
                        Swal.fire({
                            title: "Success?",
                            text: "Promotion & Information Saved!",
                            icon: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#7066E0",
                            confirmButtonText: "Okay"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.replace(`${baseURL}settings/ship_doc_desc`);
                            }
                        });
                    }
                }

            }


            function triggerUpload() {
                document.getElementById('fileInput').click();
            }

            function handleFileChange() {
                const fileInput = document.getElementById('fileInput');
                const uploadDiv = document.getElementById('upload-box');

                if (fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name;
                    uploadDiv.innerHTML = `<p>File Selected: ${fileName}</p>`;
                }
            }

            function promotional_attachment(event) {
                let get_promotional_event_value = event.value;

                switch (get_promotional_event_value) {
                    case 'qr':
                        pa_qr.classList.remove('d-none');
                        pa_photo.classList.add('d-none');
                        break;
                    case 'photo':
                        pa_qr.classList.add('d-none');
                        pa_photo.classList.remove('d-none');
                        break;
                }
            }

            function display_preview() {
                preview_modal.show();
            }

            function serializeForm(form, type) {
                let formData = new FormData(form);
                let serializedData = [];
                let filesData = [];

                for (let [name, value] of formData.entries()) {
                    if (Array.isArray(value)) {
                        // Handle multiple values for the same field
                        value.forEach(item => {
                            serializedData.push({
                                name: name,
                                value: item
                            });
                        });
                    } else if (value instanceof File) {
                        // Handle file input separately
                        filesData.push({
                            name: name,
                            value: value
                        });
                    } else {
                        serializedData.push({
                            name: name,
                            value: value
                        });
                    }
                }

                if (type === 'string') {
                    // Serialize data into query string format
                    serializedData = serializedData.map(pair => encodeURIComponent(pair.name) + '=' + encodeURIComponent(pair
                            .value))
                        .join('&');
                }

                return {
                    formData: serializedData,
                    filesData: filesData
                };
            }

            function sanitize_for_tomSelect(tomselect_target, items, param) {
                items.forEach(item => {
                    tomselect_target.addOption({
                        value: item[param.id],
                        text: item[param.name]
                    });
                });
            }
        </script>

    </x-slot>
    @stack('orders.multiple_cn_modal')
</x-layout>
