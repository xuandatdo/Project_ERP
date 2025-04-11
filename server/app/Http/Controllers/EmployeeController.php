<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
                'birth_date' => 'required|date',
                'profile_image' => 'required|image|max:2048|dimensions:max_width=1024,max_height=1024',
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
                'work_experience' => 'required|string|max:255', // Thêm validation cho kinh nghiệm làm việc
                'education_level' => 'required|in:THPT,Cao đẳng,Đại học,Thạc sĩ,Tiến sĩ', // Thêm validation cho trình độ
            ]);

            // Xử lý upload ảnh
            $imagePath = $request->file('profile_image')->store('employees', 'public');

            // Tạo nhân viên mới
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'birth_date' => $request->birth_date,
                'profile_image' => $imagePath,
                'address' => $request->address,
                'phone' => $request->phone,
                'work_experience' => $request->work_experience,
                'education_level' => $request->education_level,
                'gender' => $request->gender,
                'position' => $request->position,
                'department' => $request->department,
                'workplace' => $request->workplace,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'probation_end' => $request->probation_end,
                'work_hours' => $request->work_hours,
                'salary_structure' => $request->salary_structure,
                'supervisor' => $request->supervisor,
                'salary_type' => $request->salary_type,
                'salary_amount' => $request->salary_amount,

            ]);

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
            'birth_date' => 'required|date',
            'profile_image' => 'nullable|image|max:2048|dimensions:max_width=1024,max_height=1024',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^\d{10}$/',
            'work_experience' => 'required|string|max:255',
            'education_level' => 'required|in:THPT,Cao đẳng,Đại học,Thạc sĩ',
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

        // Xử lý upload ảnh nếu có
        $data = $request->all();
        if ($request->hasFile('profile_image')) {
            // Xóa ảnh cũ nếu cần
            if ($employee->profile_image && File::exists(public_path('storage/' . $employee->profile_image))) {
                File::delete(public_path('storage/' . $employee->profile_image));
            }
            $imagePath = $request->file('profile_image')->store('employees', 'public');
            $data['profile_image'] = $imagePath;
        }

        // Cập nhật dữ liệu, bao gồm hai trường mới
        $employee->update($data);

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

        // Xóa ảnh nếu tồn tại
        if ($employee->profile_image && File::exists(public_path('storage/' . $employee->profile_image))) {
            File::delete(public_path('storage/' . $employee->profile_image));
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully'], 204);
    }
}
