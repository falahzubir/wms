<x-layout :title="$title">
    <style>
        .table tbody tr td {
            vertical-align: middle;
        }
    </style>

    <section class="section">

        <div class="row">

            <div class="card" style="font-size:0.8rem" id="sdd-table">
                <div class="card-body">
                    <div class="card-title text-end">
                        <a type="button" class="btn btn-primary" href="{{ route('settings.sdd_form') }}"><i
                                class="bi bi-plus"></i></a>
                    </div>
                    <!-- Default Table -->
                    <table class="table">
                        <thead class="text-center" class="bg-secondary">
                            <tr class="align-middle">
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Status</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center" id="shiping-doc-desc-tbody">
                        </tbody>
                    </table>
                    <div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination-links">
                                <!-- Pagination links will be dynamically populated here -->
                            </ul>
                        </nav>
                    </div>
                    <!-- End Default Table Example -->
                </div>
            </div>
        </div>
    </section>

    @include('orders.multiple_cn_modal')
    <x-slot name="script">
        <script>
            const shipping_doc_desc_table_elem = document.getElementById("shiping-doc-desc-tbody");
            const pagination_links_elem = document.getElementById("pagination-links");
            const per_page = 10;

            document.addEventListener('DOMContentLoaded', () => {
                init_page(1);
            });

            function init_page(page) {
                const table_overlay_state = 'show';
                loading_table_overlay(shipping_doc_desc_table_elem, 6, table_overlay_state);
                axios.get(`/api/settings/init_sdd_table?page=${page}&perPage=${per_page}`)
                    .then(response => {
                        console.log(response.data);
                        const {
                            data,
                            meta
                        } = response.data;
                        loading_table_overlay(shipping_doc_desc_table_elem, 6, 'hide');
                        render_shipping_doc_table(data, meta);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }

            function execute_editing(id) {
                location.assign(`/settings/ship_doc_desc/form/${id}`);
            }

            function execute_deletion(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/api/settings/init_sdd_table/${id}`)
                            .then(response => {
                                console.log(response.data);
                                init_page();
                            })
                            .catch(error => {
                                console.log(error);
                            });
                    }
                });
            }

            function render_shipping_doc_table(data, meta) {
                shipping_doc_desc_table_elem.innerHTML = "";
                let html_template = '';

                if (data.length > 0) {
                    const start_index = (meta.current_page - 1) * per_page;
                    data.forEach((table_data, index) => {
                        const current_index = start_index + index + 1;

                        const current_date = new Date();
                        const start_date = new Date(table_data.start_date);
                        const end_date = new Date(table_data.end_date);

                        let status = '';
                        if (current_date >= start_date && current_date <= end_date) {
                            status = `<span class="badge bg-success px-3 py-1 rounded-pill">Active</span>`
                        } else {
                            status = `<span class="badge bg-secondary px-3 py-1 rounded-pill">Inactive</span>`
                        }

                        const template = `<tr>
                            <td>${current_index}</td>
                            <td>${table_data.promotional_title}</td>
                            <td>${status}</td>
                            <td>${table_data.start_date}</td>
                            <td>${table_data.end_date}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm m-1 edit-sdd" onclick="execute_editing(${table_data.id})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm m-1 edit-sdd" onclick="execute_deletion(${table_data.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                        html_template += template;
                    });
                } else {
                    html_template += `<tr><td colspan="6">No Data</td></tr>`;
                }

                shipping_doc_desc_table_elem.insertAdjacentHTML('beforeend', html_template);
                render_pagination_links(meta);
            }

            function loading_table_overlay(table_body_elem, colspan_val, state) {
                const table_overlay_html = state === 'show' ?
                    `<tr><td class="text-center" colspan="${colspan_val}"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></td></tr>` :
                    '';
                table_body_elem.innerHTML = table_overlay_html;
            }

            function render_pagination_links(meta) {
                pagination_links_elem.innerHTML = '';

                for (let i = 1; i <= meta.last_page; i++) {
                    const li = document.createElement('li');
                    li.classList.add('page-item');
                    if (i === meta.current_page) {
                        li.classList.add('active');
                    }
                    const link = document.createElement('a');
                    link.classList.add('page-link');
                    link.href = '#';
                    link.textContent = i;
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        init_page(i);
                    });
                    li.appendChild(link);
                    pagination_links_elem.appendChild(li);
                }
            }
        </script>
    </x-slot>
    @stack('orders.multiple_cn_modal')
</x-layout>
