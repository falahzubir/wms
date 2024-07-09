<?php

namespace App\Http\Controllers;

use App\Models\PickingSequence;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingListSettingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pickingSequences = PickingSequence::whereNull('deleted_at')->orderBy('sequence')->get();
            return response()->json(['pickingSequences' => $pickingSequences]);
        }
    
        $products = Product::active()->get();
        $pickingSequences = PickingSequence::whereNull('deleted_at')->orderBy('sequence')->get();
    
        return view('picking_list_setting.index', [
            'title' => 'Picking List Product Sequence',
            'products' => $products,
            'pickingSequences' => $pickingSequences,
        ]);
    }
    
    public function update(Request $request) {

        $columnOrder = $request->input('column_order');

        if (!$columnOrder) {
            return response()->json(['message' => 'No sequence provided'], 400);
        }

        DB::transaction(function () use ($columnOrder) {
            // Get existing picking sequence records
            $existingPickingSequence = PickingSequence::whereNull('deleted_at')->pluck('product_id')->toArray();

            // Update or create new records
            foreach ($columnOrder as $index => $productId) {
                $pickingSequence = PickingSequence::where('product_id', $productId)->first();

                if ($pickingSequence) {
                    // Update existing record
                    $pickingSequence->sequence = $index + 1;
                    $pickingSequence->updated_at = now();
                    $pickingSequence->deleted_at = null;
                    $pickingSequence->save();
                } else {
                    // Add new record
                    PickingSequence::create([
                        'product_id' => $productId,
                        'sequence' => $index + 1,
                        'created_at' => now(),
                    ]);
                }
            }
            // Identify removed products and update deleted_at in picking_sequence table
            $removedProducts = array_diff($existingPickingSequence, $columnOrder);

            if (!empty($removedProducts)) {
                PickingSequence::whereIn('product_id', $removedProducts)
                    ->update(['deleted_at' => now()]);
            }
        });

        // Fetch updated picking sequence
        $updatedPickingSequences = PickingSequence::whereNull('deleted_at')->orderBy('sequence')->get();

        return response()->json(['message' => 'Picking sequence updated successfully!', 'pickingSequences' => $updatedPickingSequences]);
    }
}
