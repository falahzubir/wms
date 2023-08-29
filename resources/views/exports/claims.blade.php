<table>
    <thead>
        <tr>
            <td>Order</td>
            <td>Company</td>
            <td>Sales ID</td>
            <td>Ref No</td>
            <td>Batch No</td>
            <td>Product</td>
            <td>Quantity</td>
            <td>Claimant</td>
            <td>Status</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($claims as $claim)
            @foreach ($claim->items as $item)
            <tr>
                <td>{{ order_num_format($claim->order) }}</td>
                <td>{{ $claim->order->company->name }}</td>
                <td>{{ $claim->order->sales_id }}</td>
                <td>{{ $claim->ref_no }}</td>
                <td>{{ implode(", ", json_decode($item->batch_no)) }}</td>
                <td>{{ $item->order_item->product->code }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    @if($claim->claimant == 1)
                        {{ $claim->order->courier->name }}
                    @endif

                    @if($claim->claimant == 2)
                        @if($item->order_item->product->detail != null)
                            {{ $item->order_item->product->detail->owner->name }}
                        @else
                            EMZI HEALTH SCIENCE SDN. BHD.
                        @endif
                    @endif
                </td>
                <td>{{ $claim->status ? 'COMPLETED' : 'PENDING' }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
