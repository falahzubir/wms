<?php

use App\Models\User;
use App\Models\State;
use App\Models\OrderLog;
use App\Models\OrderItem;
use App\Models\PaymentType;
use App\Models\OperationalModel;

if (!function_exists('currency')) {
    /**
     * Convert cents to ringgit, add true to show currency symbol
     *
     * @param  int  $amount, boolean $currency
     * @return string
     */
    function currency($amount, $currency = false)
    {

        //convert cents to ringgit
        if ($currency == true) {
            if (config('app.currency_position') == 'before') {
                return config('app.currency_symbol') . number_format($amount / 100, 2);
            }
            return number_format($amount / 100, 2) . config('app.currency_symbol');
        } else {
            return number_format($amount / 100, 2);
        }
    }
}


if (!function_exists('order_num_format')) {
    /**
     * Get order number format, use object if order is already loaded, do not use int if page has multiple orders
     *
     * @param object $order or int $order_id
     * @return string
     */
    function order_num_format($order)
    {
        if (is_object($order)) {
            return "SO" . $order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id);
        }
        $order = \App\Models\Order::with(['company'])->find($order);
        return "SO" . $order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id);
    }
}

if (!function_exists('shipment_num_format')) {
    /**
     * Get shipment number format
     *
     * @param object  $shipment
     * @return string
     */
    function shipment_num_format($order)
    {
        return DHL_PREFIX[$order->company_id] . $order->company->code . "-"
            . ($order->operationalModel->short_name ?? "UNK")
            . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id) . "-"
            . date("ymd", strtotime($order->batch->created_at)) . "-"
            . sprintf('%03d', $order->batch->batch_id);
    }
}

if (!function_exists('shipment_num_format_mult')) {
    /**
     * Get shipment number format
     *
     * @param object  $shipment
     * @return string
     */
    function shipment_num_format_mult($order, $no)
    {
        return DHL_PREFIX[$order->company_id] . $order->company->code . "-"
            . ($order->operationalModel->short_name ?? "UNK")
            . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id) . "-"
            . sprintf('%03d', ($no+1)) . "-" //no. of consignment
            . date("ymd", strtotime($order->batch->created_at)) . "-"
            . sprintf('%03d', $order->batch->batch_id);

    }
}

if (!function_exists('set_order_status')) {
    /**
     * Update order status
     *
     * @param  object $order, int $status
     * @return void
     */
    function set_order_status($order, $status, $remarks = null, $user_id = 1)
    {
        $order->status = $status;
        $order->save();

        OrderLog::create([
            'order_id' => $order->id,
            'order_status_id' => $status,
            'remarks' => $remarks ?? 'Order status updated to ' . $status,
            'created_by' => auth()->user()->id ?? $user_id,
        ]);

        return true;
    }
}

if(!function_exists('set_order_status_bulk')){
    /**
     * Update order status bulk
     *
     * @param  object $orders, int $status
     * @return void
     */
    function set_order_status_bulk($orders, $status, $remarks = null)
    {
        foreach($orders as $order){
            set_order_status($order, $status, $remarks . ". Bulk update status");
        }

        return true;
    }
}

if (!function_exists('order_num_id')) {
    /**
     * Get order id from order number
     *
     * @param  string $order_num
     * @return int
     */
    function order_num_id($order_num)
    {
        return (int)substr($order_num, ORDER_NUMBER_LENGTH * -1);
    }
}

if (!function_exists('get_couriers')) {
    /**
     * Get all couriers
     *
     * @return json
     */
    function get_couriers()
    {
        return \App\Models\Courier::where('status', 1)->get();
    }
}

if (!function_exists('get_picking_batch')) {
    /**
     * Get courier by id
     *
     * @param  Object $order or int $batch, string $delimiter
     * @return json
     */
    function get_picking_batch($order_or_batch, $delimiter = "-")
    {
        if (is_object($order_or_batch)) {
            return date("ymd", strtotime($order_or_batch->batch->created_at)) . $delimiter . sprintf("%03d", $order_or_batch->batch->batch_id);
        }
        $batch = \App\Models\BucketBatch::find($order_or_batch);
        return date("ymd", strtotime($batch->created_at)) . $delimiter . sprintf("%03d", $batch->batch_id);
    }
}

if (!function_exists('number_formatter')) {
    /**
     * Format number
     *
     * @param  int $number
     * @return string
     */
    function number_formatter($number)
    {
        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
        return  $numberFormatter->format($number);
    }
}

if (!function_exists('split_order_num')) {
    /**
     * Split order number to get sales id
     *
     * @param  string $order_num
     * @return int
     */
    function split_order_num($order_num)
    {
        $order['company'] = substr($order_num, 2, 2);
        preg_match('/\d+/', $order_num, $matches);
        $order['sales_id'] = $matches[0];
    }
}

if (!function_exists('get_shipping_remarks')) {
    /**
     * Get order remarks
     *
     * @param  obj $order_id
     * @return string
     */
    function get_shipping_remarks($order, $mult_cn = [], $full = false)
    {
        // return null;
        $remark = '';
        foreach ($order->items as $item) :
            if(empty($mult_cn)){
                $quantity = $item->quantity;
            }
            else{
                $quantity = collect($mult_cn)
                        ->whereIn('order_item_id', $item['id'])
                        ->pluck('quantity')
                        ->values()
                        ->implode(',');
            }

            if($quantity > 0){
                $remark .= $full ? $item->product->name: $item->product->code;
                $remark .= '[';
                $remark .= $quantity;
                $remark .= ']';
            }
        endforeach;

        if(strlen($remark) > 50){
            $remark = str_replace(' ', '', $remark);
        }
        if(strlen($remark) > 50){
            $remark = str_replace('FOC', 'F', $remark);
        }
        if(strlen($remark) > 50){
            $remark = str_replace('[', '', $remark);
            $remark = str_replace(']', ',', $remark);
            $remark = rtrim($remark, ',');
        }
        return $remark;
    }
}

if (!function_exists('get_order_weight')) {
    /**
     * Get order weight
     *
     * @param  obj $order_id
     * @return string
     */
    function get_order_weight($order, $mult_cn = [])
    {
        $weight = 0;
        foreach ($order->items as $item) :
            if(empty($mult_cn)){
                $quantity = $item->quantity;
            }
            else{
                $quantity = collect($mult_cn)
                        ->whereIn('order_item_id', $item['id'])
                        ->pluck('quantity')
                        ->values()
                        ->implode(',');
            }
            $weight += ($item->product->weight ?? 200) * $quantity;
        endforeach;

        return $weight;
    }
}

if (!function_exists('get_order_quantity')) {
    /**
     * Get order quantity
     *
     * @param  obj $order_id
     * @return string
     */
    function get_order_quantity($order)
    {
        $quantity = 0;
        foreach ($order->items as $item) :
            $quantity += $item->quantity;
        endforeach;

        return $quantity;
    }
}

if(!function_exists('last_two_digits_zero')){
    /**
     * Check if last two digits are the zero
     *
     * @param string $postcode
     * @return bool
     */
    function last_two_digits_zero($postcode)
    {
        return substr($postcode, -2) == '00';
    }
}

if(!function_exists('is_digit_count')){
    /**
     * Check count of digits
     *
     * @param  int $number, int $divisor
     * @return bool
     */
    function is_digit_count($number, $count)
    {
        return strlen((string)$number) == $count;
    }
}

if(!function_exists('hash_url_encode')){
    /**
     * Encode url
     *
     * @param  string $url
     * @return string
     */
    function hash_url_encode($id)
    {
        $url_hash_key = 'Gr0b0xT3cH@URL===';
        return urlencode(base64_encode($url_hash_key.$id));
    }
}

if(!function_exists('hash_url_decode')){
    /**
     * Decode url
     *
     * @param  string $url
     * @return string
     */
    function hash_url_decode($decoded_id)
    {
        $x = base64_decode(urldecode($decoded_id));
        $arr_x = explode("===", $x);
        $data_return = isset($arr_x[1]) ? $arr_x[1] : '';
        return $data_return;
    }
}

if(!function_exists('check_order_status')){
    /**
     * Check order status
     *
     * @param  int $order_id, int $status
     * @return bool
     */
    function check_order_status($order_id)
    {
        return \App\Models\Order::where('id', $order_id)->first()->status;
    }
}

if (!function_exists('get_state')) {
    function get_state($state_id)
    {
        $result = State::find($state_id);

        if ($result) {
            return $result->name;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_operational_details')) {
    function get_operational_details($operational_model_id)
    {
        $result = OperationalModel::find($operational_model_id);

        if ($result) {
            return $result->name;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_order_items')) {
    function get_order_items($order_id)
    {
        // Join the OrderItem table with the products table
        $orderItems = OrderItem::where('order_id', $order_id)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.weight as weight')
            ->get();

        // Calculate the sum of quantities and weights
        $sumQuantity = $orderItems->sum('quantity');
        $sumWeight = $orderItems->sum('weight');

        // Prepare an array with detailed information
        $result = [
            'sumQuantity' => $sumQuantity,
            'sumWeight' => $sumWeight,
        ];

        return $result;
    }
}

if (!function_exists('get_payment_name')) {
    function get_payment_name($payment_type)
    {
        $result = PaymentType::find($payment_type);

        if ($result) {
            return $result->payment_type_name;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_pic')) {
    function get_pic($id)
    {
        $result = User::find($id);

        if ($result) {
            return $result->name;
        } else {
            return null;
        }
    }
}
