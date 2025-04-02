<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $table = 'attendance_records'; // Explicitly define the table name (optional if it matches convention)

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'status',
    ];

    // Define relationships if necessary
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
