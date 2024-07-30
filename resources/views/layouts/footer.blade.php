@props(['script'])
<footer id="footer" class="footer">
    <x-credits />
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

<!-- dselect -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<!-- Template Main JS File -->
<script src="{{ asset('assets/js/main.js?=v0.02') }}"></script>
<script src="{{ asset('assets/js/custom.js?=v0.03') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

{{-- <script src="//www.tracking.my/track-button.js"></script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        init_validate_func();
    })

    function init_validate_func() {
        document.querySelectorAll('[validate-type]').forEach((elem) => {
            const newDivHTML = `<div class='validate-box'>${elem.outerHTML}</div>`;
            elem.insertAdjacentHTML('beforebegin', newDivHTML);
            elem.remove();
        })
    }

    function input_validator(form_elem, elem, validate_type) {
        let result;
        let validate_box = elem.closest('.validate-box');

        // Remove existing validate-guide-text elements
        validate_box.querySelectorAll('.validate-guide-text').forEach(el => el.remove());

        let validate_guide_element = document.createElement('span');
        validate_guide_element.className = 'validate-guide-text';

        if (validate_type === 'radio') {
            let extract_name_attr = elem.getAttribute('name');
            let extract_form_id = document.querySelector(form_elem).getAttribute('id');
            let checkedRadio = document.querySelector(`#${extract_form_id} [name='${extract_name_attr}']:checked`);

            if (!checkedRadio) {
                validate_guide_element.textContent = 'Please Check One';
                validate_guide_element.style.color = 'red';
                elem.style.borderColor = 'red';
                result = false;
            } else {
                validate_guide_element.style.color = '#CED4DA';
                elem.style.borderColor = '#CED4DA';
                result = true;
            }
        } else if (validate_type === 'select' || validate_type === 'selectpicker' || validate_type ===
            'selectpicker-single') {
            let options = elem.value;

            if (options !== null && (validate_type !== 'select' || options !== '0')) {
                validate_guide_element.style.color = '#CED4DA';
                elem.style.borderColor = '#CED4DA';
                result = true;
            } else {
                validate_guide_element.textContent = (validate_type === 'select') ? 'Please Select' : 'Required';
                validate_guide_element.style.color = 'red';
                elem.style.borderColor = 'red';
                result = false;
            }
        } else if (validate_type === 'date') {
            let enteredDate = new Date(elem.value);

            if (!isNaN(enteredDate) && enteredDate.toISOString().split('T')[0] === elem.value) {
                // Valid date format
                validate_guide_element.style.color = '#CED4DA';
                elem.style.borderColor = '#CED4DA';
                result = true;
            } else {
                validate_guide_element.textContent = 'Invalid date format';
                validate_guide_element.style.color = 'red';
                elem.style.borderColor = 'red';
                result = false;
            }
        } else {
            if (elem.value !== '' && elem.value.length > 0) {
                validate_guide_element.style.color = '#CED4DA';
                elem.style.borderColor = '#CED4DA';
                result = true;
            } else {
                validate_guide_element.textContent = 'Required';
                validate_guide_element.style.color = 'red';
                elem.style.borderColor = 'red';
                result = false;
            }
        }

        validate_box.appendChild(validate_guide_element);
        return result;
    }
</script>
{!! $script !!}

</body>

</html>
