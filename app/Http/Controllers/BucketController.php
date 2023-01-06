<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;

class BucketController extends Controller
{
    public function index()
    {
        $buckets = Bucket::where('status', ACTIVE)->get();
        return view('buckets.index', [
            'title' => 'List Buckets',
            'buckets' => $buckets,
        ]);
    }

    /**
     * Show bucket detail
     * @param $id
     * @return json
     */
    public function show($id)
    {
        $bucket = Bucket::find($id);
        return response()->json($bucket);

    }

    /**
     * Create new bucket
     * @param Request $request
     */

    public function store(Request $request)
    {
        $bucket = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $bucket['created_by'] = 1;

        Bucket::create($bucket);

        return redirect()->route('buckets.index')->with('success', 'Bucket created successfully.');
    }

    /**
     * Edit bucket
     * @param $id
     * @return view
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $bucket = Bucket::find($id);
        $bucket->name = $request->name;
        $bucket->description = $request->description;
        $bucket->save();

        return redirect()->route('buckets.index')->with('success', 'Bucket updated successfully.');
    }
}
