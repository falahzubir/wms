<table>
    <thead>
        <tr>
            <td>Sender Name</td>
            <td>Sender Email</td>
            <td>Sender Contact No</td>
            <td>Sender Address</td>
            <td>Sender Postcode</td>
            <td>Receiver Name</td>
            <td>Receiver Email</td>
            <td>Receiver Contact No</td>
            <td>Receiver Address</td>
            <td>Receiver Postcode</td>
            <td>Item Weight (kg)</td>
            <td>Item Width (cm)</td>
            <td>Item Length (cm)</td>
            <td>Item Height (cm)</td>
            <td>Category</td>
            <td>Sender Ref No</td>
            <td>Item Description</td>
            <td>Parcel Notes</td>
            <td>COD Amount</td>
            <td>Insurance (MYR)</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $order->customer->name }}</td>
                <td></td>
                <td>="{{ $order->customer->phone }}"</td>
                <td>{{ $order->customer->address }}</td>
                <td>="{{ $order->customer->postcode }}"</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $order->sales_id }}</td>
                <td>{{ get_shipping_remarks($order)}}</td>
                <td>{{ $order->sales_remarks }}</td>
                <td>{{ $order->purchase_type == PURCHASE_TYPE_COD ? $order->total_price/100 : 0 }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
