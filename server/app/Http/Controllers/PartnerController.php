<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        return response()->json(Partner::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:partners,code',
            'name' => 'required',
            'total_orders' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        // Proceed with storing the partner
        Partner::create($request->all());

        return response()->json(['message' => 'Partner created successfully!'], 201);
    }

    public function show($id)
    {
        $partner = Partner::findOrFail($id);
        return response()->json($partner);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:partners,code,' . $id,
            'name' => 'required',
            'total_orders' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        // Proceed with updating the partner
        $partner = Partner::findOrFail($id);
        $partner->update($request->all());

        return response()->json(['message' => 'Partner updated successfully!']);
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();
        return response()->json(['message' => 'Partner deleted successfully']);
    }

    public function checkDuplicate(Request $request)
    {
        $code = $request->query('code');

        // Check if the code exists in the database
        $exists = \App\Models\Partner::where('code', $code)->exists();

        return response()->json(['exists' => $exists]);
    }
}