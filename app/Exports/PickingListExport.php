<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PickingListExport implements FromView
{
    private $products;
    private $total_products;
    private $total_parcels;

    /**
     * Constructor method
     */
    public function __construct($products, $total_products, $total_parcels)
    {
        $this->products = $products;
        $this->total_products = $total_products;
        $this->total_parcels = $total_parcels;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $products = $this->products;
        $total_products = $this->total_products;
        $total_parcels = $this->total_parcels;
        return view('exports.picking_list', compact('products', 'total_products', 'total_parcels'));
    }
}
