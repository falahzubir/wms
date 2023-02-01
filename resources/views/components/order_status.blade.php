@props(['order'])
<div>
    @switch($order->status)
        @case(ORDER_STATUS_PENDING)
            <span class="badge bg-secondary text-light">Pending</span>
        @break

        @case(ORDER_STATUS_PENDING_ON_BUCKET)
            <span class="badge bg-warning text-light">Processing</span>
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

@if ($order->bucket != null)
    <div class="text-nowrap very-small-text">[{{ $order->bucket->name }}]</div>
@endif
@if ($order->batch != null)
    <div class="text-nowrap very-small-text">[{{ get_picking_batch($order) }}]</div>
@endif
