<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row pt-4">
                        @foreach ($permissions as $permission)
                            <div class="col-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch-{{ $permission->id }}"
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                        data-permission="{{ $permission->name }}">
                                    <label class="form-check-label d-flex"
                                        for="switch-{{ $permission->id }}">{{ $permission->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </section>

    <x-slot name="script">
        <script>
            document.querySelectorAll('.form-check-input').forEach((element) => {
                element.addEventListener('change', (e) => {
                    let permission = e.target.dataset.permission;
                    let url = "{{ route('permissions.update', $role->id) }}";
                    let checked = e.target.checked;
                    let data = {
                        _token: "{{ csrf_token() }}",
                        permission: permission,
                        checked: checked,
                    };
                    axios.post(url, data)
                        .then((response) => {
                            if (response.data.success != undefined) {
                                let label = document.querySelector(`label[for="${e.target.id}"]`);
                                label.insertAdjacentHTML('beforeend', `<div class="ms-2">
                                        <div class="circle-border"></div>
                                                <div class="circle">
                                                    <div class="success"></div>
                                                    </div>
                                                </div>
                                            </div>`);
                                setTimeout(() => {
                                     document.querySelector(`label[for="${e.target.id}"] div`).remove();
                                }, 2000);
                            }

                        })
                        .catch((error) => {
                            console.log(error);
                        });
                });
            });
        </script>
    </x-slot>

</x-layout>
