<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ColumnMain;

class TemplateSettingController extends Controller
{
    public function index()
    {
        $data = ColumnMain::all();

        return view('template_setting/index', [
            'title' => 'CSV Template Setting',
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        try {

            $columnIds = $request->input('column_id');
            $displayNames = $request->input('column_display_name');

            // Loop through the data and update the corresponding records
            foreach ($columnIds as $index => $columnId) {

                $record = ColumnMain::find($columnId);
                $record->column_display_name = $displayNames[$index];
                $record->save();
            }

            // Redirect back
            return redirect()->back()->with('success', 'Courier Template Setting Updated');

        } catch (\Exception $e) {
    
            // Redirect back
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
