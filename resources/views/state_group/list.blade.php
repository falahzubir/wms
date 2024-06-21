<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <style>
        .ts-control {
            border:none;
        }
    </style>
    <section class="section">
        <div class="card p-3">
            <section id="searchForm" class="mb-3">
                <form action="" method="get">
                    <div class="d-flex gap-2">
                        <input type="text" class="flex-grow-1 form-control" name="search" placeholder="Search">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </section>
            <section id="addStateGroup" class="mb-3">
                <div>
                    <button class="btn btn-primary" onclick="addStateGroup()"><strong>+</strong></button>
                </div>
            </section>
            <section id="stateGroupList">
                <table class="table">
                    <thead>
                        <tr class="text-center align-middle">
                            <th>#</th>
                            <th>Action</th>
                            <th>State Group</th>
                            <th>State</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($stateGroups as $stateGroup) --}}
                        <tr class="text-center align-middle">
                            <td>1</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-danger p-1 px-2" onclick="deleteStateGroup(1)"><i class="bi bi-trash"></i></button>
                                    <button class="btn btn-warning p-1 px-2" onclick="editStateGroup(1)"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                            <td>Penisular Malaysia</td>
                            <td>
                                Johor Johor, Kedah Kedah, Kelantan Kelantan, Melaka Melaka, Pahang Pahang, Perak Negeri
                                Perak, Perlis Perlis, Pulau Pinang Pulau Pinang, Selangor Selangor, Negeri Sembilan
                                Negeri Sembilan, Terengganu Terengganu
                            </td>
                        </tr>
                        {{-- @endforeach --}}
                    </tbody>
                </table>
            </section>
        </div>
    </section>

    <div class="modal fade" id="addStateGroupModal" tabindex="-1" aria-labelledby="addStateGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="addStateGroupModalLabel"><strong>Create State Group</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label for="stateGroupName" class="form-label fs-6"><strong>State Group Name:</strong></label>
                        <input type="text" class="form-control" id="stateGroupName" name="stateGroupName">
                    </div>
                    <div>
                        <label for="stateGroupStates" class="form-label fs-6"><strong>State:</strong></label>
                        <x-filter_select name="states" id="state-filter" class="col-12 form-control">
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">
                                    {{ $state->name }}</option>
                            @endforeach
                        </x-filter_select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStateGroupModal" tabindex="-1" aria-labelledby="updateStateGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="updateStateGroupModalLabel"><strong>Update State Group</strong></h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            function addStateGroup() {
                const modal = new bootstrap.Modal(document.getElementById('addStateGroupModal'), {
                    keyboard: false
                });
                modal.show();
            }

            const states = @json($states);

            //onload
            document.addEventListener('DOMContentLoaded', function () {
                initite_tomsel();
            });

            function initite_tomsel(cls = 'tomsel'){
                document.querySelectorAll('.' + cls).forEach(el => {
                    let settings = {
                        plugins: {
                            remove_button: {
                                title: 'Remove this item',
                            }
                        },
                        hidePlaceholder: true,
                    };
                    new TomSelect(el, settings);
                });
            }

            function deleteStateGroup(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                })
            }

            function editStateGroup(id) {

                let stateGroupName = 'Penisular Malaysia';
                let stateGroupStates = [1,2,3,4];

                let html = `<div>
                        <label for="stateGroupName" class="form-label fs-6"><strong>State Group Name:</strong></label>
                        <input type="text" class="form-control" id="updateStateGroupName" name="stateGroupName" value="${stateGroupName}">
                    </div>
                    <div>
                        <label for="stateGroupStates" class="form-label fs-6"><strong>State:</strong></label>
                        <div class="form-control">
                        <select id="updateStates" name="states[]" multiple placeholder="All" autocomplete="off" class="form-control col-12 tomsel2" style="padding:0;">
                            ${states.map(state => `<option value="${state.id}" ${stateGroupStates.includes(state.id) ? 'selected' : ''}>${state.name}</option>`).join('')}
                        </select>
                        </div>
                    </div>`;
                document.querySelector('#updateStateGroupModal .modal-body').innerHTML = html;

                initite_tomsel('tomsel2');

                const modal = new bootstrap.Modal(document.getElementById('updateStateGroupModal'), {
                    keyboard: false
                });
                modal.show();
            }

        </script>
    </x-slot>

</x-layout>
