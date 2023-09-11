<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ClaimExport implements FromView
{
    use Exportable;

    protected $claims;

    public function __construct($claims)
    {
        $this->claims = $claims;
    }

    public function view(): View
    {
        return view('exports.claims', [
            'claims' => $this->claims,
        ]);
    }
}
