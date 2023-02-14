<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a company listing of the resource.
     *
     * @return view
     */
    public function index()
    {
        return view('companies.index', [
            'title' => 'Companies',
            'companies' => Company::all()
        ]);
    }

    /**
     * Edit the company.
     *
     * @return view
     */
    public function edit(Company $company)
    {
        return view('companies.edit', [
            'title' => $company->name,
            'company' => $company
        ]);
    }

    /**
     * Update the company.
     *
     * @return view
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|min:3',
            'code' => 'required|min:2|max:3',
            'address' => 'required',
            'address2' => 'required',
            'email' => 'nullable|email',
            'state' => 'required|in:'.implode(',', MY_STATES),
            'city' => 'required',
            'postcode' => 'required|numeric|digits:5|regex:/^[0-9]+$/|not_in:00000',
            'country' => 'required|in:'.implode(',', array_keys(COUNTRIES)),
            'contact_person' => 'required',
            'phone' => 'required',

        ]);
        $company->update($request->all());

        return redirect()->route('companies.index')->with('success', 'Company updated successfully');
    }


}
