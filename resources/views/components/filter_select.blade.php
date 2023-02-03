@props(['label', 'name', 'id','class' => ''])
<div class="{{ $class }}">
    <!-- filter select box -->
        <label for="{{ $id }}" class="font-weight-bold">{{ $label }}</label>
        <select class="form-control tomsel" id="{{ $id }}" name="{{ $name }}[]" multiple  placeholder="All " autocomplete="off" style="padding:0;">
            {{ $slot }}
        </select>
</div>
