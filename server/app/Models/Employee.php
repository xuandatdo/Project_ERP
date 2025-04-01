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
        'address',
        'phone',
        'gender',
        'position',
        'department',
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
}
