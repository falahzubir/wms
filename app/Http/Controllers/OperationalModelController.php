<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\OperationalModel;
use Doctrine\DBAL\Schema\View;
use Illuminate\Http\Request;

class OperationalModelController extends Controller
{
    public function index()
    {
        return view('operational_model.index', [
            'title' => 'Operational Model',
            'op_models' => OperationalModel::with('company')->get(),
            'companies' => Company::all()
        ]);
    }

    public function show ($opmodel_id) {
        $op = OperationalModel::find($opmodel_id);
        return response()->json($op, 200);
    }

    public function update($opmodel_id, Request $request)
    {
        $request->validate([
            'short_name' => 'required',
            'default_company' => 'required'
        ]);

        $op = OperationalModel::find($opmodel_id);
        $op->short_name = $request->short_name;
        $op->default_company_id = $request->default_company != 0 ? $request->default_company : null ;
        $op->save();
        return response()->json($op, 200);
    }
}
