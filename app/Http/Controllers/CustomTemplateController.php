<?php

namespace App\Http\Controllers;

use App\Models\ColumnMain;
use App\Models\TemplateMain;
use App\Models\TemplateColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomTemplateController extends Controller
{
    public function index()
    {
        $columnMain = ColumnMain::all();

        $templateMain = TemplateMain::where('delete_status', '!=', 1)
            ->paginate(10);

        $templateMain->load('templateColumns');

        return view('custom_template_setting.index', [
            'title' => 'Custom Template Setting',
            'columnMain' => $columnMain,
            'templateMain' => $templateMain
        ]);
    }

    public function saveTemplate(Request $request)
    {
        // Validate the input
        $request->validate([
            'template_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('template_mains')->where(function ($query) {
                    return $query->where('delete_status', 0);
                }),
            ],
            'template_type' => 'required|string|max:255',
            'template_header' => 'required|string',
            'columns' => 'required|array',
        ]);

        // Process the data
        $template = new TemplateMain();
        $template->template_name = $request->input('template_name');
        $template->template_type = $request->input('template_type');
        $template->template_header = $request->input('template_header');
        $template->created_at = now()->timezone('Asia/Kuala_Lumpur');
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

        // Return a response
        return response()->json(['message' => 'Template saved successfully']);
    }

    public function getColumns($id)
    {
        $data = TemplateColumn::where('template_main_id', $id)->where('deleted_at', null)->get();
    
        // Extract column_main_id from each record in the collection
        $columnMainIds = $data->pluck('column_main_id');

        // Fetch corresponding data from column_mains table
        $columnsData = ColumnMain::whereIn('id', $columnMainIds)->select('id', 'column_display_name')->get();

        return response()->json(['columns' => $columnsData]);
    }

    public function updateTemplate(Request $request)
    {
        $templateId = $request->input('template_id');
        
        // Validate the input
        $request->validate([
            'template_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('template_mains')->where(function ($query) use ($templateId) {
                    return $query->where('id', '!=', $templateId)
                                ->where('delete_status', 0); // Assuming delete_status is a column to mark deleted templates
                }),
            ],
            'template_type' => 'required|string|max:255',
            'template_header' => 'required|string',
            'columns' => 'required|array',
        ]);

        if (!$templateId) {
            throw new \Exception('Template ID is null or empty');
        }

        // Process the data within a transaction
        return DB::transaction(function () use ($request, $templateId) {
            // Retrieve existing columns before updating the template
            $existingColumns = TemplateColumn::where('template_main_id', $templateId)->get()->pluck('column_main_id')->toArray();

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

            if ($columnOrder && is_array($columnOrder)) {
                foreach ($columnOrder as $order => $columnId) {
                    $templateColumn = TemplateColumn::whereNull('deleted_at')->updateOrCreate(
                        ['template_main_id' => $templateId, 'column_main_id' => $columnId],
                        [
                            'column_position' => $order + 1,
                            'updated_at' => now()->timezone('Asia/Kuala_Lumpur'),
                        ]
                    );
                }
            }

            // Identify removed columns and update deleted_at in template_columns table
            $removedColumns = array_diff($existingColumns, $columnOrder);

            if (!empty($removedColumns)) {
                TemplateColumn::where('template_main_id', $templateId)
                    ->whereIn('column_main_id', $removedColumns)
                    ->update(['deleted_at' => now()->timezone('Asia/Kuala_Lumpur')]);
            }

            // Return a response
            return response()->json(['message' => 'Template updated successfully']);
        });
    }


    public function deleteTemplate(Request $request)
    {
        $template_id = $request->input('template_id');

        // Update TemplateMain
        $templateMain = TemplateMain::findOrFail($template_id);
        $templateMain->delete_status = 1;
        $templateMain->deleted_at = now()->timezone('Asia/Kuala_Lumpur');
        $templateMain->update();

        // Update all TemplateColumn records with the specified template_main_id
        TemplateColumn::where('template_main_id', $template_id)
            ->update([
                'deleted_at' => now()->timezone('Asia/Kuala_Lumpur'),
            ]);
    }
}
