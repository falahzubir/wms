@props(['label', 'name', 'id','class' => ''])
<div class="{{ $class }}">
    <!-- filter select box -->
        <label for="{{ $id }}" class="font-weight-bold">{{ $label }}</label>
        {{-- <input id="{{ $id }}"> --}}
        <select class="form-control tomsel" id="{{ $id }}" name="{{ $name }}" multiple  placeholder="All " autocomplete="off">
            <option value="pending">Pending</option>
            <option value="packing">Packing</option>
            <option value="ready-to-ship">Ready To Ship</option>
            <option value="shipping">Shipping</option>
            <option value="delivered">Delivered</option>
            <option value="returned">Returned</option>
            <option value="completed">Completed</option>
        </select>
</div>
