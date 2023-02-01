<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PickingListExport implements FromView
{
    private $products;
    private $total_products;

    /**
     * Constructor method
     */
    public function __construct($products, $total_products)
    {
        $this->products = $products;
        $this->total_products = $total_products;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $products = $this->products;
        $total_products = $this->total_products;
        return view('exports.picking_list', compact('products', 'total_products'));
    }
}
