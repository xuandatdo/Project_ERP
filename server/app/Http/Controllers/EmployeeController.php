<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Lấy danh sách nhân viên
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    // Thêm nhân viên mới
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10}$/',
                'gender' => 'required|in:Nam,Nữ,Khác',
                'position' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'workplace' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'probation_end' => 'nullable|date',
                'work_hours' => 'required|string|max:255',
                'salary_structure' => 'required|string|max:255',
                'supervisor' => 'required|string|max:255',
                'salary_type' => 'required|in:Lương tháng,Lương ngày,Lương giờ',
                'salary_amount' => 'required|numeric|min:0',
            ]);

            $employee = Employee::create($request->all());
            return response()->json([
                'message' => 'Employee created successfully',
                'employee' => $employee
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating employee', 'error' => $e->getMessage()], 400);
        }
    }

    // Lấy thông tin một nhân viên
    public function show($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee);
    }

    // Cập nhật thông tin nhân viên
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^\d{10}$/',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'workplace' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'probation_end' => 'nullable|date',
            'work_hours' => 'required|string|max:255',
            'salary_structure' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'salary_type' => 'required|in:Lương tháng,Lương ngày,Lương giờ',
            'salary_amount' => 'required|numeric|min:0',
        ]);

        $employee->update($request->all());
        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ]);
    }

    // Xóa nhân viên
    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully'], 204);
    }
}
