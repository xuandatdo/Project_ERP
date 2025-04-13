<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Exception;

class PositionController extends Controller
{
    // Lấy danh sách vị trí
    public function index()
    {
        try {
            $positions = Position::with('department')->get();
            return response()->json($positions);
        } catch (Exception $e) {
            return response()->json(['message' => 'Lỗi khi lấy danh sách vị trí', 'error' => $e->getMessage()], 500);
        }
    }

    // Thêm vị trí mới
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
            ]);

            $position = Position::create([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
            ]);

            return response()->json([
                'message' => 'Vị trí đã được tạo thành công',
                'position' => $position
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi tạo vị trí', 'error' => $e->getMessage()], 400);
        }
    }

    // Lấy thông tin một vị trí
    public function show($id)
    {
        $position = Position::with('department')->find($id);
        if (!$position) {
            return response()->json(['message' => 'Không tìm thấy vị trí'], 404);
        }
        return response()->json($position);
    }

    // Cập nhật thông tin vị trí
    public function update(Request $request, $id)
    {
        try {
            $position = Position::find($id);
            if (!$position) {
                return response()->json(['message' => 'Không tìm thấy vị trí'], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
            ]);

            $position->update([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
            ]);

            return response()->json([
                'message' => 'Vị trí đã được cập nhật thành công',
                'position' => $position
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi cập nhật vị trí', 'error' => $e->getMessage()], 400);
        }
    }

    // Xóa vị trí
    public function destroy($id)
    {
        try {
            $position = Position::find($id);
            if (!$position) {
                return response()->json(['message' => 'Không tìm thấy vị trí'], 404);
            }

            // Kiểm tra xem vị trí có nhân viên không
            if ($position->employees()->count() > 0) {
                return response()->json(['message' => 'Không thể xóa vị trí vì có nhân viên đang giữ vị trí này'], 400);
            }

            $position->delete();
            return response()->json(['message' => 'Vị trí đã được xóa thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xóa vị trí', 'error' => $e->getMessage()], 500);
        }
    }

    // Lấy danh sách vị trí theo phòng ban
    public function getByDepartment($departmentId)
    {
        $positions = Position::where('department_id', $departmentId)->get();
        return response()->json($positions);
    }
}