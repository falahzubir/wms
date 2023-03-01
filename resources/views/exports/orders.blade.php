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
            <td>{{ $order->shippings[0]->tracking_number }}</td>
            <td>{{ $order->company->code }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
