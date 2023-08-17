import './bootstrap';

import Swal from 'sweetalert2';

window.Swal = Swal;

import moment from 'moment';

window.moment = moment;

import {DataTable} from "simple-datatables";

window.DataTable = DataTable;

function pagePagination(data) {
    let pages = [];
    try {
        const numShown = Math.min(3, data.last_page);
        let first = data.current_page - Math.floor(numShown / 2);
        first = Math.max(first, 1);
        first = Math.min(first, data.last_page - numShown + 1);
        pages = [...Array(numShown)].map((k, i) => i + first);

    } catch (error) {
        console.log(error);
    }

    let html = `<ul class="pagination pagination-sm">
    <li class="page-item ${data.current_page == 1 ? "disabled":""}">
        <a class="page-link" href="javascript:void(0)" aria-label="Previous" onclick="backwalk()">
            <span aria-hidden="true">&laquo;</span>
        </a>
    </li>`
    for (const page of pages) {
        html += `<li class="page-item ${data.current_page == page ? "active":""}">
        <a class="page-link" href="javascript:void(0)" onclick="setPage(${page})">${page}</a>
        </li>`
    }

    html += `<li class="page-item">
    <a class="page-link" href="javascript:void(0)" aria-label="Next" onclick="forward()">
    <span aria-hidden="true">&raquo;</span>
    </a>
    </li>
    </ul>`


    const el = document.querySelector("#page-pagination");

    if (el != null) {
        el.innerHTML = "";
        el.insertAdjacentHTML('beforeend', html)
    }
}
