<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Exception;

class DepartmentController extends Controller
{
    // Lấy danh sách phòng ban
    public function index()
    {
        try {
            $departments = Department::with('positions')->get();
            return response()->json($departments);
        } catch (Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách phòng ban', 'error' => $e->getMessage()], 500);
        }
    }

    // Thêm phòng ban mới
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:departments,name',
                'description' => 'nullable|string|max:255',
            ]);

            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Phòng ban đã được tạo thành công',
                'department' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi tạo phòng ban', 'error' => $e->getMessage()], 400);
        }
    }

    // Lấy thông tin một phòng ban
    public function show($id)
    {
        $department = Department::with('positions')->find($id);
        if (!$department) {
            return response()->json(['message' => 'Không tìm thấy phòng ban'], 404);
        }
        return response()->json($department);
    }

    // Cập nhật thông tin phòng ban
    public function update(Request $request, $id)
    {
        try {
            $department = Department::find($id);
            if (!$department) {
                return response()->json(['message' => 'Không tìm thấy phòng ban'], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:departments,name,' . $id,
                'description' => 'nullable|string|max:255',
            ]);

            $department->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Phòng ban đã được cập nhật thành công',
                'department' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi cập nhật phòng ban', 'error' => $e->getMessage()], 400);
        }
    }

    // Xóa phòng ban
    public function destroy($id)
    {
        try {
            $department = Department::find($id);
            if (!$department) {
                return response()->json(['message' => 'Không tìm thấy phòng ban'], 404);
            }

            // Kiểm tra xem phòng ban có nhân viên không
            if ($department->employees()->count() > 0) {
                return response()->json(['message' => 'Không thể xóa phòng ban vì có nhân viên đang thuộc phòng ban này'], 400);
            }

            $department->delete();
            return response()->json(['message' => 'Phòng ban đã được xóa thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xóa phòng ban', 'error' => $e->getMessage()], 500);
        }
    }
}