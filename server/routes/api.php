<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TaskController;

Route::resource('employees', EmployeeController::class)->except(['create', 'edit']);
// Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::post('/attendance', [AttendanceController::class, 'store']);

// Routes cho Department
Route::resource('departments', DepartmentController::class)->except(['create', 'edit']);

// Routes cho Position
Route::resource('positions', PositionController::class)->except(['create', 'edit']);
Route::get('/positions/department/{departmentId}', [PositionController::class, 'getByDepartment']);

// Routes cho Task
Route::resource('tasks', TaskController::class)->except(['create', 'edit']);
Route::delete('/task-attachments/{id}', [TaskController::class, 'deleteAttachment']);
