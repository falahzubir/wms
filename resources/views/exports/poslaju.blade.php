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
                        <td>{{ optional($order->company)->name }}</td>
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
                        <td>{{ get_state($order->customer->state)}}</td>
                    @elseif ($column->column_name == "customers_country")
                        <td>
                            @switch($order->customer->country)
                                @case('1')
                                    MY
                                    @break

                                @case('2')
                                    ID
                                    @break

                                @case('3')
                                    SG
                                    @break
                            @endswitch
                        </td>

                    @elseif ($column->column_name == "purchase_type")
                        <td>
                            @switch($order->purchase_type)
                                @case('1')
                                    COD
                                    @break

                                @case('2')
                                    Paid
                                    @break

                                @case('3')
                                    Installment
                                    @break
                            @endswitch
                        </td>

                    @elseif ($column->column_name == "operational_models_name")
                        <td>{{ get_operational_details($order->operational_model_id) }}</td>

                    @elseif ($column->column_name == "payment_type_name")
                        <td>{{ get_payment_name($order->payment_type) }}</td>

                    @elseif ($column->column_name == "couriers_name")
                        <td>{{ $order->courier->name }}</td>

                    @elseif ($column->column_name == "total_price")
                        <td>{{ $order->total_price/100 }}</td>

                    @elseif ($column->column_name == "quantity")
                        <td>{{ get_order_items($order->id)['sumQuantity'] }}</td>

                    @elseif ($column->column_name == "weight")
                        <td>{{ get_order_items($order->id)['sumWeight'] }}g</td>

                    @elseif ($column->column_name == "item_description")
                        <td>{{ get_shipping_remarks($order)}}</td>
                    @elseif ($column->column_name == "date_insert")
                        <td>{{ $order->created_at }}</td>
                    @elseif ($column->column_name == "shipping_date")
                        @if ($order->shippings->isNotEmpty())
                            <td>{{ $order->shippings()->latest()->first()->created_at }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @elseif ($column->column_name == "scan_date")
                        @if ($order->shippings->isNotEmpty())
                            <td>{{ $order->shippings()->latest()->first()->scanned_at }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @elseif ($column->column_name == "pic_scan")
                        @if ($order->shippings->isNotEmpty())
                            <td>{{ get_pic($order->shippings()->latest()->first()->scanned_by) }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @elseif ($column->column_name == "delivered_date")
                        @if ($order->logs->isNotEmpty())
                            @php
                                $deliveredLog = $order->logs->where('order_status_id', 6)->first();
                            @endphp
                            @if ($deliveredLog)
                                <td>{{ $deliveredLog->created_at }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @else
                            <td>-</td>
                        @endif
                    @elseif ($column->column_name == "order_pic")
                        <td>
                            @php
                            $staffNames = collect(json_decode($staffMain))->pluck('staff_name')->implode(', ');
                            @endphp
                            {{ !empty($staffNames) ? $staffNames : '-' }}
                        </td>
                    @else
                        <td>{{ $order->{$column->column_name} ?? '' }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
