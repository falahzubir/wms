<?php

use App\Models\OrderLog;

if (! function_exists('currency')) {
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
            if(config('app.currency_position') == 'before'){
                return config('app.currency_symbol') . number_format($amount/100, 2);
            }
            return number_format($amount/100, 2) . config('app.currency_symbol');
        }
        else{
            return number_format($amount/100, 2);
        }

    }
}


if (! function_exists('order_num_format')) {
    /**
     * Get order number format, use object if order is already loaded, do not use int if page has multiple orders
     *
     * @param object $order or int $order_id
     * @return string
     */
    function order_num_format($order)
    {
        if(is_object($order)){
            return "SO".$order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id);
        }
        $order = \App\Models\Order::with(['company'])->find($order);
        return "SO".$order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->sales_id);
    }
}

if (! function_exists('shipment_num_format')) {
    /**
     * Get shipment number format
     *
     * @param object  $shipment
     * @return string
     */
    function shipment_num_format($order)
    {
        // return "MYAAH".$order->company->code."00".$order->id;
        return "MYAAH".$order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->id);
    }
}

if (! function_exists('set_order_status')) {
    /**
     * Update order status
     *
     * @param  object $order, string $status
     * @return void
     */
    function set_order_status($order, $status)
    {
        $order->status = $status;
        $order->save();

        OrderLog::create([
            'order_id' => $order->id,
            'order_status_id' => $status,
            'remarks' => 'Order status updated to ' . $status,
            'created_by' => auth()->user()->id ?? 1,
        ]);

        return true;
    }
}

if(! function_exists('order_num_id')){
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

if(! function_exists('get_couriers')){
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
