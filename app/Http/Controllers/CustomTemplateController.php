<?php

namespace App\Http\Controllers;

use App\Models\ColumnMain;
use App\Models\TemplateMain;
use App\Models\TemplateColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomTemplateController extends Controller
{
    public function index()
    {
        $columnMain = ColumnMain::all();
        // $templateMain = TemplateMain::paginate(10);

        $templateMain = TemplateMain::join('template_columns', 'template_mains.id', '=', 'template_columns.template_main_id')
            ->where('template_mains.delete_status', '!=', 1)
            ->select('template_mains.*')
            ->distinct()
            ->paginate(10);
        
        return view('custom_template_setting.index', [
            'title' => 'Custom Template Setting',
            'columnMain' => $columnMain,
            'templateMain' => $templateMain
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

        // Save the columns to the template_columns table
        foreach ($request->input('column_order') as $order => $columnId) {
            $templateColumn = new TemplateColumn();
            $templateColumn->template_main_id = $template->id;
            $templateColumn->column_main_id = $columnId;
            $templateColumn->column_position = $order + 1;
            $templateColumn->created_at = now()->timezone('Asia/Kuala_Lumpur');
            $templateColumn->save();
        }

        // Step 3: Return a response
        return response()->json(['message' => 'Template saved successfully']);
    }

    public function getColumns($id)
    {
        $data = TemplateColumn::where('template_main_id', $id)->get();
    
        // Extract column_main_id from each record in the collection
        $columnMainIds = $data->pluck('column_main_id');

        // Fetch corresponding data from column_mains table
        $columnsData = ColumnMain::whereIn('id', $columnMainIds)->select('id', 'column_display_name')->get();

        return response()->json(['columns' => $columnsData]);
    }

    public function updateTemplate(Request $request)
    {
        // Step 1: Validate the input
        $request->validate([
            'template_id' => 'required|exists:template_mains,id',
            'template_name' => 'required|string|max:255',
            'template_type' => 'required|string|max:255',
            'template_header' => 'required|string',
            'column_order' => 'array', // Assuming column_order is an array
        ]);

        $templateId = $request->input('template_id');

        if (!$templateId) {
            throw new \Exception('Template ID is null or empty');
        }

        // Step 2: Process the data within a transaction
        return DB::transaction(function () use ($request, $templateId) {
            // Delete existing columns for the specified template
            TemplateColumn::where('template_main_id', $templateId)->delete();

            // Update template information
            $template = TemplateMain::findOrFail($templateId);
            $template->update([
                'template_name' => $request->input('template_name'),
                'template_type' => $request->input('template_type'),
                'template_header' => $request->input('template_header'),
                'updated_at' => now()->timezone('Asia/Kuala_Lumpur'),
            ]);

            // Insert new columns based on the provided order
            $columnOrder = $request->input('column_order');

            if (!$templateId) {
                throw new \Exception('Template ID is null or empty');
            }

            if ($columnOrder && is_array($columnOrder)) {
                foreach ($columnOrder as $order => $columnId) {
                    $templateColumn = TemplateColumn::updateOrCreate(
                        ['template_main_id' => $templateId, 'column_main_id' => $columnId],
                        [
                            'column_position' => $order + 1,
                            'updated_at' => now()->timezone('Asia/Kuala_Lumpur'),
                        ]
                    );
                }
            }

            // Step 3: Return a response
            return response()->json(['message' => 'Template updated successfully']);
        });
    }


    public function deleteTemplate(Request $request)
    {
        $template = TemplateMain::findOrFail($request->input('template_id'));
        $template->delete_status = 1;
        $template->update();
    }
}
