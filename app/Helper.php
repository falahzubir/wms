<?php

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
     * Get order number format
     *
     * @param object  $order
     * @return string
     */
    function order_num_format($order)
    {
        return "SO".$order->company->code . sprintf('%' . ORDER_NUMBER_LENGTH . 'd', $order->id);
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
