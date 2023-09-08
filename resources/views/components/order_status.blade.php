@props(['order'])
<div>
    @switch($order->status)
        @case(ORDER_STATUS_PENDING)
            <span class="badge bg-secondary text-light">Pending</span>
        @break

        @case(ORDER_STATUS_PROCESSING)
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
            <span class="badge bg-info text-light">In Transit</span>
        @break

        @case(ORDER_STATUS_DELIVERED)
            <span class="badge bg-success text-light">Delivered</span>
        @break

        @case(ORDER_STATUS_RETURN_SHIPPING)
            <span class="badge bg-warning text-light">Return: In Transit</span>
        @break

        @case(ORDER_STATUS_RETURN_PENDING)
            <span class="badge bg-danger text-light">Return: Pending</span>
        @break

        @case(ORDER_STATUS_RETURNED)
            <span class="badge text-light" style="background-color:#DA6A00">Return Pending</span>
        @break

        @case(ORDER_STATUS_RETURN_COMPLETED)
            <span class="badge text-light" style="background-color:#da00a0">Return Completed</span>
        @break

        @case(ORDER_STATUS_REJECTED)
            <span class="badge bg-danger text-light">Rejected</span>
        @break

        @default
    @endswitch
</div>

@if ($order->bucket != null)
    <div class="text-nowrap small-text">[{{ $order->bucket->name }}]</div>
@endif
@if ($order->batch != null)
    <div class="text-nowrap small-text"><a href="/orders/bucket-batch/{{ $order->bucket_batch_id }}">[{{ get_picking_batch($order) }}]</a></div>
@endif
