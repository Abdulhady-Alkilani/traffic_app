<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CitizenData extends Model
{
    protected $table = 'citizens_data';

    protected $fillable = [
        'user_id',
        'national_id',
        'full_name',
        'phone',
        'blood_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'citizen_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'citizen_id');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(TrafficViolation::class, 'citizen_id');
    }
}
