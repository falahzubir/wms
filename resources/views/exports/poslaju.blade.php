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

                    @elseif ($column->column_name == "companies_name")
                        <td>{{ $order->company->name }}</td>
                     @elseif ($column->column_name == "companies_phone")
                        <td>{{ $order->company->phone }}</td>
                    @elseif ($column->column_name == "companies_address")
                        <td>{{ $order->company->address }}</td>
                    @elseif ($column->column_name == "companies_postcode")
                        <td>{{ $order->company->postcode }}</td>
                    @elseif ($column->column_name == "companies_city")
                        <td>{{ $order->company->city }}</td>
                    @elseif ($column->column_name == "companies_state")
                        <td>{{ $order->company->state }}</td>
                    @elseif ($column->column_name == "companies_country")
                        <td>{{ $order->company->country }}</td>

                    @elseif ($column->column_name == "customers_name")
                        <td>{{ $order->customer->name }}</td>
                    @elseif ($column->column_name == "customers_phone")
                        <td>="{{ $order->customer->phone }}"</td>
                    @elseif ($column->column_name == "customers_phone_2")
                        <td>="{{ $order->customer->phone_2 }}"</td>
                    @elseif ($column->column_name == "customers_address")
                        <td>{{ $order->customer->address }}</td>
                    @elseif ($column->column_name == "customers_postcode")
                        <td>="{{ $order->customer->postcode }}"</td>
                    @elseif ($column->column_name == "customers_city")
                        <td>="{{ $order->customer->city }}"</td>
                    @elseif ($column->column_name == "customers_state")
                        <td>="{{ $order->customer->state }}"</td>
                    @elseif ($column->column_name == "customers_country")
                        <td>="{{ $order->customer->country }}"</td>

                    @elseif ($column->column_name == "payment_type_name")
                        <td>="{{ $order->paymentType->payment_type_name }}"</td>

                    @elseif ($column->column_name == "operational_models_name")
                        <td>{{ $order->operationalModel->name }}</td>

                    @elseif ($column->column_name == "couriers_name")
                        <td>{{ $order->courier->name }}</td>

                    @elseif ($column->column_name == "shipping_remarks")
                        <td>{{ get_shipping_remarks($order)}}</td>

                    @elseif ($column->column_name == "total_price")
                        <td>{{ $order->purchase_type == PURCHASE_TYPE_COD ? $order->total_price/100 : 0 }}</td>

                    @elseif ($column->column_name == "products_name")
                        <td>{{ $order->items->name }}</td>

                    {{-- @elseif ($column->column_name == "quantity")
                        <td>{{ $order->items->name }}</td> --}}

                    @elseif ($column->column_name == "weight")
                        <td>{{ $order->items->weight }}</td>

                    @elseif ($column->column_name == "item_description")
                        <td>{{ $order->items->description }}</td>

                    @else
                        <td>{{ $order->{$column->column_name} ?? '' }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
