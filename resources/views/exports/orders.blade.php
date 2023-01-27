<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Sales_ID</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->sales_id }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
