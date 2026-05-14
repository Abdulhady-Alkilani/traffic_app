<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'citizen_id',
        'plate_number',
        'vehicle_type',
        'make',
        'model_year',
        'color',
    ];

    public function citizen()
    {
        return $this->belongsTo(CitizenData::class, 'citizen_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function violations()
    {
        return $this->hasMany(TrafficViolation::class);
    }
}