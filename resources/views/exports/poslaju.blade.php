<table>
    <thead>
        <tr>
            @foreach ($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $globalCounter = 1;
        @endphp
        @foreach ($orders as $key => $order)
            @if ($order->shippings->isEmpty())
                <tr>
                    @foreach ($columnName as $column)
                        @if ($column->column_name == "blank")
                            <td>{{ $globalCounter++ }}</td>
                        @elseif ($column->column_name == "order_id")
                            <td>{{ $order->id }}</td>
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
                            <td>{{ $order->customer->phone }}</td>
                        @elseif ($column->column_name == "customers_phone_2")
                            <td>{{ $order->customer->phone_2 }}</td>
                        @elseif ($column->column_name == "customers_address")
                            <td>{{ $order->customer->address }}</td>
                        @elseif ($column->column_name == "customers_postcode")
                            <td>{{ $order->customer->postcode }}</td>
                        @elseif ($column->column_name == "customers_city")
                            <td>{{ $order->customer->city }}</td>
                        @elseif ($column->column_name == "customers_state")
                            <td>{{ get_state($order->customer->state) }}</td>
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
                            <td>{{ $order->total_price / 100 }}</td>
                        @elseif ($column->column_name == "quantity")
                            <td>{{ get_order_items($order->id)['sumQuantity'] }}</td>
                        @elseif ($column->column_name == "weight")
                            <td>{{ get_order_items($order->id)['sumWeight'] }}g</td>
                        @elseif ($column->column_name == "item_description")
                            <td>{{ get_shipping_remarks($order) }}</td>
                        @elseif ($column->column_name == "date_insert")
                            <td>{{ $order->created_at }}</td>
                        @elseif ($column->column_name == "shipping_date")
                            <td>-</td>
                        @elseif ($column->column_name == "tracking_number")
                            <td>-</td>
                        @elseif ($column->column_name == "scan_date")
                            <td>-</td>
                        @elseif ($column->column_name == "pic_scan")
                            <td>-</td>
                        @elseif ($column->column_name == "delivered_date")
                            <td>-</td>
                        @elseif ($column->column_name == "order_pic")
                            <td>
                                @php
                                    $salesId = $order->sales_id;
                                    $staffNames = json_decode($staffMain, true);
                                @endphp
                                @if (!empty($staffNames))
                                    @foreach ($staffNames as $staff)
                                        @if ($staff['sales_id'] == $salesId)
                                            {{ $staff['staff_name'] }}
                                            @php $found = true; @endphp
                                        @endif
                                    @endforeach
                                    @if (!isset($found))
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        @elseif ($column->column_name == "state_group")
                            <td>-</td>
                        @elseif ($column->column_name == "total_weight")
                            <td>-</td>
                        @elseif ($column->column_name == "weight_category")
                            <td>-</td>
                        @elseif ($column->column_name == "shipping_cost")
                            <td>-</td>
                        @elseif ($column->column_name == "shipping_cost_product_quantity")
                            <td>-</td>
                        @else
                            <td>{{ $order->{$column->column_name} ?? '' }}</td>
                        @endif
                    @endforeach
                </tr>
        @else
            @foreach ($order->shippings as $shippingIndex => $shipping)
                <tr>
                    @foreach ($columnName as $column)
                        @if ($column->column_name == "blank")
                            <td>{{ $globalCounter++ }}</td>
                        @elseif ($column->column_name == "order_id")
                            @if ($shippingIndex === 0)
                                <td rowspan="{{ $order->shippings->count() }}">{{ $order->id }}</td>
                            @endif
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
                            <td>{{ $order->customer->phone }}</td>
                        @elseif ($column->column_name == "customers_phone_2")
                            <td>{{ $order->customer->phone_2 }}</td>
                        @elseif ($column->column_name == "customers_address")
                            <td>{{ $order->customer->address }}</td>
                        @elseif ($column->column_name == "customers_postcode")
                            <td>{{ $order->customer->postcode }}</td>
                        @elseif ($column->column_name == "customers_city")
                            <td>{{ $order->customer->city }}</td>
                        @elseif ($column->column_name == "customers_state")
                            <td>{{ get_state($order->customer->state) }}</td>
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
                            <td>{{ $order->total_price / 100 }}</td>
                        @elseif ($column->column_name == "quantity")
                            <td>{{ get_order_items($order->id)['sumQuantity'] }}</td>
                        @elseif ($column->column_name == "weight")
                            <td>{{ get_order_items($order->id)['sumWeight'] }}g</td>
                        @elseif ($column->column_name == "item_description")
                            <td>{{ get_shipping_remarks($order) }}</td>
                        @elseif ($column->column_name == "date_insert")
                            <td>{{ $order->created_at }}</td>
                        @elseif ($column->column_name == "shipping_date")
                            <td>{{ $shipping->created_at }}</td>
                        @elseif ($column->column_name == "tracking_number")
                            @if (is_numeric($shipping->tracking_number))
                                <td>'{{ $shipping->tracking_number }}</td>
                            @else
                                <td>{{ $shipping->tracking_number }}</td>
                            @endif
                        @elseif ($column->column_name == "scan_date")
                            <td>{{ $shipping->scanned_at }}</td>
                        @elseif ($column->column_name == "pic_scan")
                            <td>{{ get_pic($shipping->scanned_by) }}</td>
                        @elseif ($column->column_name == "delivered_date")
                            @php
                                $deliveredLog = $order->logs->where('order_status_id', 6)->first();
                            @endphp
                            @if ($deliveredLog)
                                <td>{{ $deliveredLog->created_at }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @elseif ($column->column_name == "order_pic")
                            <td>
                                @php
                                    $salesId = $order->sales_id;
                                    $staffNames = json_decode($staffMain, true);
                                @endphp
                                @if (!empty($staffNames))
                                    @foreach ($staffNames as $staff)
                                        @if ($staff['sales_id'] == $salesId)
                                            {{ $staff['staff_name'] }}
                                            @php $found = true; @endphp
                                        @endif
                                    @endforeach
                                    @if (!isset($found))
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        @elseif ($column->column_name == "state_group")
                            @if (isset($shipping->shipping_cost->state_groups))
                                <td>{{ $shipping->shipping_cost->state_groups->name }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @elseif ($column->column_name == "total_weight")
                            @if (isset($shipping->total_weight))
                                <td>{{ $shipping->total_weight }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @elseif ($column->column_name == "weight_category")
                            @if (isset($shipping->shipping_cost->weight_category))
                                <td>{{ $shipping->shipping_cost->weight_category->name }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @elseif ($column->column_name == "shipping_cost")
                            @if (isset($shipping->shipping_cost))
                                <td>{{ $shipping->shipping_cost->price / 100 }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @elseif ($column->column_name == "shipping_cost_product_quantity")
                            @if (isset($shipping->shipping_product))
                                <td>{{ $shipping->shipping_product->pluck('quantity')->sum() }}</td>
                            @else
                                <td>-</td>
                            @endif
                        @else
                            <td>{{ $order->{$column->column_name} ?? '' }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        @endif
        @endforeach
    </tbody>
</table>
