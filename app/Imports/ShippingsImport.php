<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Shipping;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShippingsImport implements ToModel, WithStartRow
{

    protected $company_id;

    /**
     *  constructor.
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $order = Order::where('sales_id', $row[0])->where('company_id', $this->company_id)->first();
        logger($row[0]);
        return new Shipping([
            'order_id' => Order::where('sales_id', $row[0])->where('company_id', $this->company_id)->first()->id,
            'tracking_number' => $row[1],
            // 'customer_name' => $row[2],
            'receiver_name' => $row[3],
            'receiver_phone_1' => $row[4],
            'receiver_phone_2' => $row[5],
            'created_by' => auth()->user()->id ?? 1,
        ]);
    }
}
