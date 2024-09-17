<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryListController extends Controller
{
    public function index()
    {
        $countries = Country::paginate(10);

        return view('country_list/index', [
            'title' => 'Country List',
            'countries' => $countries,
        ]);
    }
}
