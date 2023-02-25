<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="mt-3">
                        @can('user.create')
                            <!-- modal button -->
                            <a class="btn btn-info" id="add-user" href="/register">
                                <i class="bi bi-plus"></i>
                            </a>
                        @endcan
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->staff_id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                {{ $role->name }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @can('user.edit')
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                    class="btn btn-warning btn-sm">Update</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('register') }}" method="POST" id="add-user-form" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="staff_id" id="staff_id" class="form-control"
                                    placeholder="Staff ID" value="{{ old('staff_id') }}">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Name" value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Password">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Confirm Password">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="add-user-submit-btn">Add User</button>
                    </div>
                </div>
            </div>
        </div>
        </div>


    </section>

    <x-slot name="script">
        <script>
            document.querySelector('#add-user-submit-btn').addEventListener('click', function() {
                document.querySelector('#add-user-form').submit();
            });
        </script>
    </x-slot>

</x-layout>
