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
    protected $headers;
    protected $columnName;
    protected $staffMain;

    public function __construct($orders, $headers, $columnName, $staffMain)
    {
        $this->orders = $orders;
        $this->headers = $headers;
        $this->columnName = $columnName;
        $this->staffMain = $staffMain;
    }

    /**
     * @return \Illuminate\Support\View
     */
    public function view(): View
    {
        return view('exports.poslaju', [
            'orders' => $this->orders,
            'headers' => $this->headers,
            'columnName' => $this->columnName,
            'staffMain' => $this->staffMain,
        ]);
    }
}
