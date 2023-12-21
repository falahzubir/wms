<?php

namespace App\Http\Controllers;

use App\Models\ColumnMain;
use App\Models\TemplateMain;
use Illuminate\Http\Request;

class CustomTemplateController extends Controller
{
    public function index()
    {
        $data = ColumnMain::all();
        
        return view('custom_template_setting.index', [
            'title' => 'Custom Template Setting',
            'dataFromDB' => $data
        ]);
    }

    public function saveTemplate(Request $request)
    {
        // Step 1: Validate the input
        $request->validate([
            'template_name' => 'required|string|max:255',
            'template_type' => 'required|string|max:255',
            'template_header' => 'required|string',
            'columns' => 'required|array',
        ]);

        // Step 2: Process the data
        $template = new TemplateMain();
        $template->template_name = $request->input('template_name');
        $template->template_type = $request->input('template_type');
        $template->template_header = $request->input('template_header');
        $template->created_at = now()->timezone('Asia/Kuala_Lumpur');

        // Save the template to get an ID
        $template->save();

        // Now, attach columns to the template
        $columns = $request->input('columns');

        foreach ($columns as $columnName) {
            // Assuming you have a pivot table for the template_columns
            $template->columns()->attach($columnName);
        }

        // Step 3: Return a response
        return response()->json(['message' => 'Template saved successfully']);
    }
}
