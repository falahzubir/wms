@props(['status', 'bucket' => null])
<div>
    @switch($status)
        @case(ORDER_STATUS_PENDING)
            <span class="badge bg-secondary text-light">Pending</span>
        @break

        @case(ORDER_STATUS_PENDING_ON_BUCKET)
            <span class="badge bg-secondary text-light">Pending</span>
            {{-- <span class="badge bg-primary text-light">On Bucket</span> --}}
        @break

        @case(ORDER_STATUS_PACKING)
            <span class="badge bg-primary text-light">Packing</span>
        @break

        @case(ORDER_STATUS_READY_TO_SHIP)
            <span class="badge bg-primary text-light">Ready To Ship</span>
        @break

        @case(ORDER_STATUS_SHIPPING)
            <span class="badge bg-info text-light">Shipping</span>
        @break

        @case(ORDER_STATUS_DELIVERED)
            <span class="badge bg-warning text-light">Delivered</span>
        @break

        @case(ORDER_STATUS_RETURNED)
            <span class="badge bg-danger text-light">Returned</span>
        @break

        @case(ORDER_STATUS_COMPLETED)
            <span class="badge bg-success text-light">Completed</span>
        @break

        @default
    @endswitch
</div>

@if ($bucket != null)
    <div class="text-nowrap very-small-text">[{{ $bucket->name }}]</div>
@endif
