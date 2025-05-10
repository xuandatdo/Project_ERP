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
                'late_count' => $attendance ? $attendance->late_count : 0, // Include late_count
                'note' => $attendance ? $attendance->note : '', // Include note
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
        'attendance.*.status' => 'nullable|in:present,absent_with_permission,absent_without_permission,late',
        'attendance.*.note' => 'nullable|string|max:255',
    ]);

    try {
        foreach ($request->attendance as $record) {
            $attendanceRecord  = AttendanceRecord::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'attendance_date' => $record['attendance_date']
                ]);

                // Handle late count logic
                if ($record['status'] === 'late') {
                    if (!$attendanceRecord->exists || $attendanceRecord->status !== 'late') {
                        // Increment late count only if it's the first late check of the day
                        $attendanceRecord->late_count = ($attendanceRecord->late_count ?? 0) + 1;
                    }
                } elseif ($attendanceRecord->status === 'late' && $record['status'] !== 'late') {
                    // Decrement late count if the status changes from "late" to something else
                    $attendanceRecord->late_count = max(($attendanceRecord->late_count ?? 0) - 1, 0);
                }

                $attendanceRecord->status = $record['status'];
                $attendanceRecord->note = $record['note'] ?? null;
                $attendanceRecord->save();
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
    public function getEmployeeStatistics()
    {
        $employees = Employee::with(['attendanceRecords'])->get();

        $statistics = $employees->map(function ($employee) {
            $absentDates = $employee->attendanceRecords
                ->whereIn('status', ['absent_with_permission', 'absent_without_permission'])
                ->groupBy('attendance_date')
                ->map(function ($records, $date) {
                    return [
                        'date' => $date,
                        'count' => $records->count(),
                    ];
                })
                ->values();
    
            $calendarData = $employee->attendanceRecords
                ->whereIn('status', ['absent_with_permission', 'absent_without_permission'])
                ->groupBy('attendance_date')
                ->mapWithKeys(function ($records, $date) {
                    return [$date => $records->count()];
                });
    
            $totalAbsentWithPermission = $employee->attendanceRecords
                ->where('status', 'absent_with_permission')
                ->count();
    
            $totalAbsentWithoutPermission = $employee->attendanceRecords
                ->where('status', 'absent_without_permission')
                ->count();
    
            $totalAbsentDays = $totalAbsentWithPermission + $totalAbsentWithoutPermission;
    
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'absent_dates' => $absentDates,
                'calendar_data' => $calendarData, // Calendar-friendly data
                'total_absent_with_permission' => $totalAbsentWithPermission,
                'total_absent_without_permission' => $totalAbsentWithoutPermission,
                'total_absent_days' => $totalAbsentDays,
            ];
        });

    return response()->json($statistics);
    }
}
