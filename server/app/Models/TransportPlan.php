<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'weight',
        'expected_time',
        'status',
        'delivery_location',
        'pickup_location',
        'driver_name',
        'driver_phone',
        'quantity',
        'cargo_details',
        'notes'
    ];

    protected $casts = [
        'expected_time' => 'datetime',
        'weight' => 'decimal:2',
        'quantity' => 'integer'
    ];
} 