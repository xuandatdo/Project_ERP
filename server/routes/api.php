<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TransportPlanController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\VehicleController;

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

//Route cho Statistics
Route::get('/statistics', [AttendanceController::class, 'getEmployeeStatistics']);
//Route cho TransportPlan
Route::apiResource('transport-plans', TransportPlanController::class);
//Route cho Partner
Route::apiResource('partners', PartnerController::class);
Route::get('/partners', [PartnerController::class, 'index']);
Route::post('/partners', [PartnerController::class, 'store']);
Route::get('/partners/{id}', [PartnerController::class, 'show']);
Route::put('/partners/{id}', [PartnerController::class, 'update']);
Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);
Route::get('/partners/check-duplicate', [PartnerController::class, 'checkDuplicate']);

//Route cho Vehicle
Route::apiResource('vehicles', VehicleController::class);
Route::get('/vehicles', [VehicleController::class, 'index']);
Route::post('/vehicles', [VehicleController::class, 'store']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);