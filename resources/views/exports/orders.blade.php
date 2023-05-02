<table>
    <thead>
        <tr>
            <td></td>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->customer->address }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ sprintf('%05d', $order->customer->postcode) }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->customer->phone }}</td>
                <td></td>
                <td></td>
                <td>{{ get_shipping_remarks($order,,true) }}</td>
                <td></td>
                <td>{{ $order->total_price / 100 }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ MY_STATES[$order->customer->state] }}</td>
                <td>0.00</td>
                <td>{{ $order->sales_id }}</td>
                <td></td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->shipping_remarks }}</td>
                <td>
                    @switch($order->purchase_type)
                        @case(1)
                            COD
                        @break

                        @case(2)
                            Paid
                        @break

                        @default
                            Installment
                    @endswitch
                </td>
                <td>{{ $order->customer->city }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
