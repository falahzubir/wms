<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ColumnMain;
use Illuminate\Support\Carbon;

class TemplateSettingController extends Controller
{
    public function index()
    {
        $data = ColumnMain::where('deleted_at', null)->get();

        return view('template_setting/index', [
            'title' => 'CSV Template Setting',
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'column_id' => 'required|array',
            'column_display_name' => 'required|array',
            'new_column_name.*' => 'nullable|string',
            'new_column_display_name.*' => 'nullable|string',
        ]);

        try {

            $columnIds = $request->input('column_id');
            $displayNames = $request->input('column_display_name');
            $newColumnNames = $request->input('new_column_name');
            $newColumnDisplayName = $request->input('new_column_display_name');

            // Loop through the data and update the corresponding records
            foreach ($columnIds as $index => $columnId) {
                $record = ColumnMain::find($columnId);
                $record->column_display_name = $displayNames[$index];
                $record->updated_at = now()->timezone('Asia/Kuala_Lumpur');
                $record->save();
            }

            // Create new records for the new columns
            if (!empty($newColumnNames)) {
                foreach ($newColumnNames as $index => $newColumnName) {
                    ColumnMain::create([
                        'column_name' => $newColumnName,
                        'column_display_name' => $newColumnDisplayName[$index],
                        'created_at' => now()->timezone('Asia/Kuala_Lumpur'),
                    ]);
                }
            }

            // Redirect back
            return redirect()->back()->with('success', 'Courier Template Setting Updated');

        } catch (\Exception $e) {
            // Redirect back
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
