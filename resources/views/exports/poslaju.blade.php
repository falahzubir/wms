<table>
    <thead>
        <tr>
            @foreach ($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $key => $order)
            <tr>
                @foreach ($columnName as $column)
                    @if ($column->column_name == "blank")
                        <td>{{ $key + 1 }}</td>
                    @elseif ($column->column_name == "customers_name")
                        <td>{{ $order->customer->name }}</td>
                    @elseif ($column->column_name == "customers_phone")
                        <td>="{{ $order->customer->phone }}"</td>
                    @elseif ($column->column_name == "customers_address")
                        <td>{{ $order->customer->address }}</td>
                    @elseif ($column->column_name == "customers_postcode")
                        <td>="{{ $order->customer->postcode }}"</td>
                    @elseif ($column->column_name == "shipping_remarks")
                        <td>{{ get_shipping_remarks($order)}}</td>
                    @elseif ($column->column_name == "total_price")
                        <td>{{ $order->purchase_type == PURCHASE_TYPE_COD ? $order->total_price/100 : 0 }}</td>
                    @else
                        <td>{{ $order->{$column->column_name} ?? '' }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
