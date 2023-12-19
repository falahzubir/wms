<!-- ====================== Use this template for new page ====================== -->
<!-- ================== Delete This comment after done copying ================== -->
<x-layout :title="$title">

    <section class="section">

        <div class="card p-5">
            <form id="settings" method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')
                <div class="p-5 border rounded">
                <table class="table-responsive table-bordered w-100">
                    @if($settings->count() > 0)
                        @foreach ($settings as $setting)
                        <tr>
                            <td width="50%" class="p-1">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                {{-- description --}}
                                <i class="bi bi-question-circle-fill" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="{{ $setting->description }}"></i>
                            </td>
                            <td class="p-1 px-2">
                            @switch($setting->data_type)
                                @case(SETTING_DATA_TYPE_BOOLEAN)
                                    <input type="radio" id="setting[{{$setting->key}}]-yes" name="setting[{{$setting->key}}]" value="1" {{ $setting->value == 1 ? 'checked' : '' }}><label for="setting[{{$setting->key}}]-yes" class="mx-2">Yes</label>
                                    <input type="radio" id="setting[{{$setting->key}}]-no" name="setting[{{$setting->key}}]" value="0" {{ $setting->value == 0 ? 'checked' : '' }}><label for="setting[{{$setting->key}}]-no" class="mx-2">No</label>
                                    @break
                                @case(SETTING_DATA_TYPE_INTEGER)
                                    <input class="form-control" type="number" value="{{ $setting->value }}" min="0"
                                        name="setting[{{$setting->key}}]">
                                    @break
                                @default
                                    <input class="form-control" type="text" value="{{ $setting->value }}"
                                        name="setting[{{$setting->key}}]">
                            @endswitch
                            </td>
                            @endforeach
                    @else
                        No setting found
                    @endif
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-success" id="save" type="submit">Save</button>
                    </div>
                </div>
            </form>
        </div>

    </section>

    <x-slot name="script">
        <script>
            // Replace this with script for individual page
            console.log('Replace this with script for individual page');
        </script>
    </x-slot>

</x-layout>

