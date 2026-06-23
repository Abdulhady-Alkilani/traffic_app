<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Models\Scopes\DepartmentScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = [
        'citizen_id',
        'vehicle_id',
        'reported_vehicle_plate',
        'assigned_department',
        'report_type',
        'description',
        'latitude',
        'longitude',
        'location_text',
        'image_url',
        'video_url',
        'status',
        'ai_detected_plate',
        'ai_incident_type',
        'ai_severity_score',
        'ai_damage_assessment',
        'ai_summary',
        'ai_is_duplicate',
        'ai_duplicate_of',
        'ai_analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'assigned_department' => Department::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'ai_is_duplicate' => 'boolean',
            'ai_severity_score' => 'integer',
            'ai_analyzed_at' => 'datetime',
        ];
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(CitizenData::class, 'citizen_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(TrafficViolation::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'ai_duplicate_of');
    }
}
