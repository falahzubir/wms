<x-layout :title="$title">

    <section class="section">

        <div class="card p-4">
            <form method="POST" action="{{ route('template_setting.update') }}">
                @csrf
               
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-success" id="save" type="submit">Update</button>
                </div>
                <table class="table is-fullwidth border mb-2">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td class="text-center">#</td>
                            <td>Column Name</td>
                            <td>Display Name</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td valign="middle" class="text-center">{{ $loop->index + 1 }}</td>
                                <td valign="middle">{{ $row->column_name }}</td>
                                <td>
                                    <input class="form-control" type="text" value="{{ $row->column_display_name }}" name="column_display_name[]">
                                    <input type="hidden" name="column_id[]" value="{{ $row->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>

    </section>
    <x-slot name="script">
        <script>
            
        </script>
    </x-slot>

</x-layout>
