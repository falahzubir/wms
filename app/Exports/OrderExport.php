<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class OrderExport implements FromView
{
    use Exportable;

    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return \Illuminate\Support\View
     */
    public function view(): View
    {
        return view('exports.poslaju', [
            'orders' => $this->orders,
        ]);
    }
}
