<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoliceData extends Model
{
    protected $table = 'police_data';

    protected $fillable = [
        'user_id',
        'badge_number',
        'full_name',
        'rank',
        'department',
    ];

    protected function casts(): array
    {
        return [
            'department' => Department::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(TrafficViolation::class, 'police_id');
    }
}
