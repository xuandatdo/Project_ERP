<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Danh sách các trường được phép gán hàng loạt
    protected $fillable = [
        'name',
        'email',
        'birth_date',
        'profile_image',
        'address',
        'phone',
        'work_experience',
        'education_level',
        'gender',
        'department_id',
        'position_id',
        'department_name',
        'position_name',
        'workplace',
        'start_date',
        'end_date',
        'probation_end',
        'work_hours',
        'salary_structure',
        'supervisor',
        'salary_type',
        'salary_amount',
    ];

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
    
    // Quan hệ nhiều-một với Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    // Quan hệ nhiều-một với Position
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
