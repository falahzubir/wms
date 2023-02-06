@props(['label', 'name', 'id','class' => ''])
<div class="{{ $class }}">
    <!-- filter select box -->
        <label for="{{ $id }}" class="font-weight-bold mb-1">{{ $label }}</label>
        <select class="form-control tomsel" id="{{ $id }}" name="{{ $name }}[]" multiple  placeholder="All " autocomplete="off" style="padding:0;">
            {{ $slot }}
        </select>
</div>
