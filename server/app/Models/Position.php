<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'department_id'
    ];

    // Quan hệ nhiều-một với Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Quan hệ một-nhiều với Employee
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}