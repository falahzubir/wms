<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">
    <style>
        .ts-control {
            border: none;
        }

        .customBtnSave {
            background-color: #7166e0;
        }
    </style>
    <section class="section">
        <div class="card p-3">
            <section id="searchForm" class="mb-3">
                <form action="" method="get">
                    <div class="d-flex gap-2">
                        <input type="text" class="flex-grow-1 form-control" name="search" placeholder="Search"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </section>
            <section id="addStateGroup" class="mb-3">
                <div>
                    @can('state_group.create')
                    <button class="btn btn-primary" onclick="addStateGroup()"><strong>+</strong></button>
                    @endcan
                </div>
            </section>
            <section id="stateGroupList">
                <table class="table">
                    <thead>
                        <tr class="text-center align-middle">
                            <th id="number-text">#</th>
                            <th>Action</th>
                            <th>State Group</th>
                            <th>State</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stateGroups as $stateGroup)
                            <tr class="text-center align-middle" id="stateGroup-{{ $stateGroup->id }}">
                                <td>
                                    {{ $loop->iteration + $stateGroups->firstItem() - 1 }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('state_group.delete')
                                        <button class="btn btn-danger p-1 px-2"
                                            onclick="deleteStateGroup({{ $stateGroup->id }})"><i
                                                class="bi bi-trash"></i></button>
                                        @endcan
                                        @can('state_group.edit')
                                        <button class="btn btn-warning p-1 px-2"
                                            onclick="editStateGroup('{{ $stateGroup->id }}','{{ $stateGroup->name }}', '{{ json_encode($stateGroup->group_state_lists->pluck('state_id')) }}')"><i
                                                class="bi bi-pencil"></i></button>
                                        @endcan
                                    </div>
                                </td>
                                <td>{{ $stateGroup->name }}</td>
                                <td>
                                    {{ $stateGroup->group_state_lists->pluck('state_name')->join(', ') }}
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center align-middle">
                                <td colspan="4">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex">
                    {{ $stateGroups->links() }}
                </div>
            </section>
        </div>
    </section>

    <div class="modal fade" id="addStateGroupModal" tabindex="-1" aria-labelledby="addStateGroupModalLabel"
        aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="storeGroup()" class="btn btn-primary customBtnSave">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStateGroupModal" tabindex="-1" aria-labelledby="updateStateGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5" id="updateStateGroupModalLabel"><strong>Update State Group</strong>
                    </h1>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="updateGroup()" class="btn btn-primary customBtnSave">Save</button>
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

            function storeGroup() {
                let addGroupModal = $('#addStateGroupModal');
                let name = addGroupModal.find('#stateGroupName').val();
                let states = addGroupModal.find('#state-filter').val();

                axios.post('/api/state-group/store', {
                        name: name,
                        states: states,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            addGroupModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'State Group created successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors);
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response.data.message,
                                'error'
                            )
                        }
                    });
            }

            const states = @json($states);

            //onload
            document.addEventListener('DOMContentLoaded', function() {
                initite_tomsel();
            });

            function initite_tomsel(cls = 'tomsel') {
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
                    title: 'Delete State Group?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#a5a5a5',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        axios.post(`/api/state-group/delete/${id}`, {
                                _token: '{{ csrf_token() }}'
                            })
                            .then(response => {
                                if (response.data.status == 'success') {
                                    document.getElementById(`stateGroup-${id}`).remove();
                                    //rebuild the page number

                                    Swal.fire(
                                        'State Group deleted successfully!',
                                        '',
                                        'success'
                                    )
                                }
                            })
                            .catch(error => {
                                if (error.response.status === 422) {
                                    let errors = error.response.data.errors;
                                    displayErrors(errors);
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        error.response.data.message,
                                        'error'
                                    )
                                }
                            });
                    }
                })
            }

            function editStateGroup(id, name, state_ids) {
                let stateGroupName = name;
                let stateGroupStates = JSON.parse(state_ids);

                let html = `<div>
                        <input type="hidden" id="updateStateGroupId" value="${id}">
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

            function updateGroup()
            {
                let updateGroupModal = $('#updateStateGroupModal');
                let id = updateGroupModal.find('#updateStateGroupId').val();
                let name = updateGroupModal.find('#updateStateGroupName').val();
                let states = updateGroupModal.find('#updateStates').val();

                axios.post('/api/state-group/update', {
                        id: id,
                        name: name,
                        states: states,
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            updateGroupModal.modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'State Group updated successfully!',
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            let errors = error.response.data.errors;
                            displayErrors(errors);
                        } else {
                            Swal.fire(
                                'Error!',
                                error.response.data.message,
                                'error'
                            )
                        }
                    });
            }

            const displayErrors = (errors) => {
                let message = [];
                for (let field in errors) {
                    let errorMessage = errors[field][0];
                    message.push(errorMessage);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: message.join('<br>'),
                })
            }
        </script>
    </x-slot>

</x-layout>
