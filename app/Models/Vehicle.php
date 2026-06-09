<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'citizen_id',
        'plate_number',
        'vehicle_type',
        'make',
        'model_name',
        'model_year',
        'chassis_number',
        'engine_number',
        'color',
        'registration_expiry',
        'insurance_status',
    ];

    protected $casts = [
        'registration_expiry' => 'date',
    ];

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(CitizenData::class, 'citizen_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(TrafficViolation::class);
    }
}
