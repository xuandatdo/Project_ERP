<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Hiển thị danh sách công việc
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $tasks = Task::with('employee')->paginate($perPage);
        
        // Thêm tên nhân viên vào kết quả
        $tasks->getCollection()->transform(function ($task) {
            $task->employee_name = $task->employee ? $task->employee->name : 'Chưa phân công';
            return $task;
        });
        
        return response()->json($tasks);
    }

    /**
     * Lưu công việc mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:Cao,Trung bình,Thấp',
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:Chưa bắt đầu,Đang thực hiện,Hoàn thành,Tạm dừng',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Tạo công việc mới
            $task = Task::create([
                'name' => $request->name,
                'employee_id' => $request->employee_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority,
                'progress' => $request->progress,
                'status' => $request->status,
                'description' => $request->description,
                'notes' => $request->notes,
            ]);

            // Xử lý file đính kèm
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('task_attachments', $fileName, 'public');
                    
                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Tạo công việc thành công', 'task' => $task], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hiển thị thông tin chi tiết công việc
     */
    public function show($id)
    {
        $task = Task::with(['employee', 'attachments'])->findOrFail($id);
        return response()->json($task);
    }

    /**
     * Cập nhật thông tin công việc
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:Cao,Trung bình,Thấp',
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:Chưa bắt đầu,Đang thực hiện,Hoàn thành,Tạm dừng',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);
            
            // Cập nhật thông tin công việc
            $task->update([
                'name' => $request->name,
                'employee_id' => $request->employee_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority,
                'progress' => $request->progress,
                'status' => $request->status,
                'description' => $request->description,
                'notes' => $request->notes,
            ]);

            // Xử lý file đính kèm mới
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('task_attachments', $fileName, 'public');
                    
                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Cập nhật công việc thành công', 'task' => $task]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xóa công việc
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);
            
            // Xóa các file đính kèm
            foreach ($task->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
            
            // Xóa công việc
            $task->delete();
            
            DB::commit();
            return response()->json(['message' => 'Xóa công việc thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xóa file đính kèm
     */
    public function deleteAttachment($id)
    {
        try {
            $attachment = TaskAttachment::findOrFail($id);
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            
            return response()->json(['message' => 'Xóa file đính kèm thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}