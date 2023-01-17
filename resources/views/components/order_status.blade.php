@props(['status', 'bucket' => null])
<div>
    @switch($status)
        @case(1)
            <span class="badge bg-secondary text-light">Pending</span>
        @break

        @case(2)
            <span class="badge bg-secondary text-light">Pending</span>
            {{-- <span class="badge bg-primary text-light">On Bucket</span> --}}
        @break

        @case(3)
            <span class="badge bg-primary text-light">Packing</span>
        @break

        @case(4)
            <span class="badge bg-primary text-light">Ready To Ship</span>
        @break

        @case(5)
            <span class="badge bg-info text-light">Shipping</span>
        @break

        @case(6)
            <span class="badge bg-warning text-light">Delivered</span>
        @break

        @case(7)
            <span class="badge bg-danger text-light">Returned</span>
        @break

        @case(8)
            <span class="badge bg-success text-light">Completed</span>
        @break

        @default
    @endswitch
</div>

@if ($bucket != null)
    <div class="text-nowrap very-small-text">[{{ $bucket->name }}]</div>
@endif
