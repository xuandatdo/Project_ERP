<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Get employee list with attendance status for the specified date
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Using eager loading to fetch attendance status along with employee data
        $employees = Employee::with(['attendanceRecords' => function ($query) use ($date) {
            $query->where('attendance_date', '=', $date);
        }])->get(['id', 'name']);

        // Mapping attendance status to each employee
        $employees = $employees->map(function ($employee) use ($date) {
            $attendance = $employee->attendanceRecords->first();
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'status' => $attendance ? $attendance->status : null,
            ];
        });

        return response()->json($employees);
    }

    // Store or update attendance records
    public function store(Request $request)
{
    // Log the request for debugging
    \Log::info($request->all());

    $request->validate([
        'attendance' => 'required|array',
        'attendance.*.employee_id' => 'required|exists:employees,id',
        'attendance.*.attendance_date' => 'required|date',
        'attendance.*.status' => 'nullable|in:present,absent',
    ]);

    try {
        foreach ($request->attendance as $record) {
            AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'attendance_date' => $record['attendance_date']
                ],
                ['status' => $record['status']]
            );
        }

        return response()->json([
            'message' => 'Attendance saved successfully',
            'data' => $request->attendance
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error saving attendance',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
