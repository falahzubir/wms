<x-layout :title="$title">

    <section class="section">

        <div class="card p-3">
            <form id="settings" method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                <div class="accordion mb-3" id="accordionExample">
                    @foreach ($settings as $setting)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-heading{{$setting->id}}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#setting{{$setting->id}}" aria-expanded="false" aria-controls="flush-collapse{{$setting->id}}">
                                    {{ $setting->key }}
                                </button>
                            </h2>
                            <div id="setting{{$setting->id}}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{$setting->id}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table is-fullwidth border mb-2">
                                        <thead>
                                            <tr>
                                                <td>Key</td>
                                                <td>Value</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($setting->children as $child)
                                                <tr>
                                                    <td valign="middle">
                                                        {{ ucwords(str_replace('_', ' ', $child->key)) }}
                                                    </td>
                                                    <td>
                                                        @if($child->key == 'detect_by_product')
                                                        <select class="form-select" name="setting[{{$child->key}}]">
                                                            <option value="NONE" {{ $child->value == 'NONE' ? 'selected' : '' }}>NONE</option>
                                                            <option value="ANY" {{ $child->value == 'ANY' ? 'selected' : '' }}>ANY</option>
                                                            <option value="ALL" {{ $child->value == 'ALL' ? 'selected' : '' }}>ALL</option>
                                                        @elseif($child->key == 'detect_operation_type')
                                                        <select class="form-select" name="setting[{{$child->key}}]">
                                                            <option value="AND" {{ $child->value == 'AND' ? 'selected' : '' }}>AND</option>
                                                            <option value="OR" {{ $child->value == 'OR' ? 'selected' : '' }}>OR</option>
                                                        @else
                                                        <input class="form-control" type="text" value="{{ $child->value }}"
                                                            name="setting[{{$child->key}}]">
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success" id="save" type="button">Save</button>
                </div>
            </form>
        </div>

    </section>
    <x-slot name="script">
        <script>
            document.getElementById('save').addEventListener('click', function() {
                //check if there are empty fields
                let empty = [];
                let inputs = document.querySelectorAll('input');
                inputs.forEach(function(input) {
                    if (input.value == '') {
                        empty.push(input.name);
                    }
                });

                Swal.fire({
                    title: 'Are you sure?',
                    html: empty.length > 0 ? `There are ${empty.length} empty fields. <small>${empty.join('<br>')}</small>` : '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Saving...',
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            },
                        });
                        document.getElementById('settings').submit();
                    }
                })
            });
        </script>
    </x-slot>

</x-layout>
