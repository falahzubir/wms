<?php

namespace App\Imports;

use App\Models\Shipping;
use Maatwebsite\Excel\Concerns\ToModel;

class ShippingsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Shipping([
            'order_id' => order_num_id($row[0]),
            'tracking_number' => $row[1],
            'courier' => $row[2],
            'created_by' => auth()->user()->id ?? 1,
        ]);
    }
}
