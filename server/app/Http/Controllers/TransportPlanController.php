<?php

namespace App\Http\Controllers;

use App\Models\TransportPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransportPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = TransportPlan::query();

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('delivery_location', 'like', "%{$search}%")
                  ->orWhere('pickup_location', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Lọc theo ngày
        if ($date = $request->input('date')) {
            $query->whereDate('expected_time', $date);
        }

        // Phân trang
        $perPage = $request->input('per_page', 10);
        $transportPlans = $query->latest()->paginate($perPage);

        return response()->json($transportPlans);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'expected_time' => 'required|date',
            'status' => ['required', Rule::in(['preparing', 'in_transit', 'completed', 'delayed'])],
            'delivery_location' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'quantity' => 'required|integer|min:1',
            'cargo_details' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transportPlan = TransportPlan::create($request->all());

        return response()->json($transportPlan, 201);
    }

    public function show(TransportPlan $transportPlan)
    {
        return response()->json($transportPlan);
    }

    public function update(Request $request, TransportPlan $transportPlan)
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'expected_time' => 'required|date',
            'status' => ['required', Rule::in(['preparing', 'in_transit', 'completed', 'delayed'])],
            'delivery_location' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'quantity' => 'required|integer|min:1',
            'cargo_details' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transportPlan->update($request->all());

        return response()->json($transportPlan);
    }

    public function destroy(TransportPlan $transportPlan)
    {
        $transportPlan->delete();
        return response()->json(null, 204);
    }
} 