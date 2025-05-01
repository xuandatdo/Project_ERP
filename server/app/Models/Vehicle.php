<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['partner_code', 'name', 'max_load', 'maintenance_date'];

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_code', 'code');
    }
}
