<table>
    <thead>
    <tr>
        <th>Sales_ID</th>
        <th>Tracking_Number</th>
        <th>Company_Code</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->sales_id }}</td>
            @if($order->shippings->count() > 0)
            <td>{{ $order->shippings[0]->tracking_number }}</td>
            @else
            <td>-</td>
            @endif
            <td>{{ $order->company->code }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
