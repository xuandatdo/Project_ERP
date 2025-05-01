<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'code',
        'name',
        'total_orders',
        'total_amount',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'partner_code', 'code');
    }
}