<x-layout :title="$title">

    <section class="section">

        <div class="card p-3">
            <form id="settings" method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')
                @foreach ($settings as $setting)
                    <table class="table is-fullwidth border mb-2">
                        <thead>
                            <tr>
                                <th colspan="2">{{ $setting->key }}</th>
                            </tr>
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
                                        <input class="form-control" type="text" value="{{ $child->value }}"
                                            name="setting[{{$child->key}}]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success" id="save" type="submit">Save</button>
                </div>
            </form>
        </div>

    </section>
    <x-slot name="script">
        <script>

        </script>
    </x-slot>

</x-layout>
