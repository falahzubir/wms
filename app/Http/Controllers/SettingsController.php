<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\OperationalModel;
use App\Services\SettingsService;
use LaravelQRCode\Facades\QRCode;
use Illuminate\Support\Facades\Storage;
use App\Models\ShippingDocumentTemplate;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct()
    {
        $this->settingsService = new SettingsService();
    }

    public function getSetting($key, $default = null)
    {
        return $this->settingsService->getSetting($key, $default);
    }

    private function setSetting($key, $value)
    {
        $this->settingsService->setSetting($key, $value);
    }

    public function getAllSettings()
    {
        return $this->settingsService->getAllSettings();
    }

    public function index()
    {
        $settings = $this->getAllSettings();
        $title = 'Settings';
        return view('settings.index', compact('settings', 'title'));
    }

    public function update(Request $request)
    {
        foreach ($request->setting as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function view_shipping_doc_desc()
    {
        return view('settings.shipping_doc_desc_list', [
            'title' => 'Shipping Document Description',
        ]);
    }

    public function init_shipping_doc_desc_page_data() // initialize data IN THE FORM
    {
        $operationalModels = OperationalModel::with('company')->get();
        $platform = PaymentType::where('payment_type_status', 1)
            ->select('id', 'payment_type_name')->get();
        return response()->json([
            'operationalModels' => $operationalModels,
            'platforms' => $platform
        ]);
    }

    public function init_sdd_table() // initialize the table data shipping document description page
{
    $shippingDocumentTemplates = ShippingDocumentTemplate::paginate(10); //pagination
    return response()->json([
        'data' => $shippingDocumentTemplates->items(),
        'meta' => [
            'current_page' => $shippingDocumentTemplates->currentPage(),
            'last_page' => $shippingDocumentTemplates->lastPage(),
            'prev_page_url' => $shippingDocumentTemplates->previousPageUrl(),
            'next_page_url' => $shippingDocumentTemplates->nextPageUrl(),
            // You can include more pagination metadata as needed
        ],
        'message' => 'Fetch complete'
    ]);
}

    public function delete_sdd_table(ShippingDocumentTemplate $template){
        $template->delete();

        return response([
            'message' => 'success delete'
        ],200);
    }
    public function edit_sdd_table(ShippingDocumentTemplate $templateId){
        // $template->delete();

        return response([
            'message' => 'success fetch for editing',
            'data'=> $templateId
        ],200);
    }

    public function sdd_form()
    {
        return view('settings.shipping_doc_desc_form', [
            'title' => 'Shipping Document Description',
        ]);
    }
    public function add_sdd(Request $request)
    {
        $file = [];
        if ($request->hasFile('promotional_link_upload_file') && $request['promotional_attachment_type'] == 'photo') {
            //store file
            $fileName = 'img/' . time() . '.' . $request->promotional_link_upload_file->extension();
            $filePath = public_path($fileName);
            $request->promotional_link_upload_file->move(public_path('img'), $fileName);
            $file = [
                'content_path' => $fileName,
                'additional_detail' => null
            ];
        }else if($request->hasFile('promotional_link_upload_file') == false && $request['promotional_attachment_type'] == 'qr'){
            $fileName = 'img/qr_code_' . time() . '.png';
            $filePath = public_path($fileName);
            QRCode::url($request->input('at_qr_code_promo_link_field'))->setOutfile($filePath)->setSize(10)->setMargin(1)->png();
            $file = [
                'additional_detail' => json_encode($request['at_qr_code_order_details_check'] ?? null),
                'content_path' => $fileName
            ];
        }

        $ShippingDocumentTemplate = ShippingDocumentTemplate::create([
            'promotional_title' => $request->input('promotional_title_field'),
            'start_date' => $request->input('start_date_field'),
            'end_date' => $request->input('end_date_field'),
            'operational_model_id' => $request->input('operational_model_field') != null ? implode(', ', $request->input('operational_model_field')) : null,
            'platform' => $request->input('platform_field') != null ? implode(', ', $request->input('platform_field')) : null,
            'link_type' => $request->input('promotional_attachment_type') == 'photo'? 2:1,
            'promotion_header' => $request->input('at_qr_code_promo_header_field') ?? null,
            'description' => $request->input('text_editor_description') ?? null,
            ...$file
        ]);

        if(!$ShippingDocumentTemplate){
            return response([
                'error'=>'error',
                'message' => 'fail'
            ],400);
        }

        // Optionally, you can return a response
        return response()->json(['message' => 'Data added successfully', 'data' => $request->all()], 201);
    }

    public function update_sdd(Request $request,$form_id)
    {

        $ShippingDocumentTemplate = ShippingDocumentTemplate::find($form_id);

        if (!$ShippingDocumentTemplate) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $file = [];
        if ($request->hasFile('promotional_link_upload_file') && $request['promotional_attachment_type'] == 'photo') {
            $filePath = Storage::put('/public/img', $request->promotional_link_upload_file);
            $fileName = Storage::url($filePath);
            $file = [
                'content_path' => $filePath,
                'additional_detail' => null
            ];
        } else if ($request->hasFile('promotional_link_upload_file') == false && $request['promotional_attachment_type'] == 'qr') {
            $file = [
                'additional_detail' => json_encode($request['at_qr_code_order_details_check'] ?? null),
                'content_path' => $request->input('at_qr_code_promo_link_field') ?? null
            ];
        }

        $ShippingDocumentTemplate->update([
            'promotional_title' => $request->input('promotional_title_field'),
            'start_date' => $request->input('start_date_field'),
            'end_date' => $request->input('end_date_field'),
            'operational_model_id' => $request->input('operational_model_field') != null ? implode(', ', $request->input('operational_model_field')) : null,
            'platform' => $request->input('platform_field') != null ? implode(', ', $request->input('platform_field')) : null,
            'link_type' => $request->input('promotional_attachment_type') == 'photo' ? 1 : 2,
            'promotion_header' => $request->input('at_qr_code_promo_header_field') ?? null,
            'description' => $request->input('text_editor_description') ?? null,
            'updated_at' => Carbon::now(),
            ...$file
        ]);

        if (!$ShippingDocumentTemplate->save()) {
            return response()->json(['error' => 'Error updating'], 500);
        }

        return response()->json(['message' => 'Data updated successfully', 'data' => $ShippingDocumentTemplate], 200);
    }

    //To be used when download cn
    public function sdd_template_view(){
        return view('pdf_template.shipping_description_document_template');
    }
}
