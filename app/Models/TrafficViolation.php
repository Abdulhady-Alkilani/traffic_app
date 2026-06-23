<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ViolationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrafficViolation extends Model
{
    protected $fillable = [
        'citizen_id',
        'vehicle_id',
        'police_id',
        'report_id',
        'violation_type',
        'description',
        'fine_amount',
        'status',
        'issued_at',
        'due_date',
        'payment_receipt_path',
    ];

    protected function casts(): array
    {
        return [
            'status' => ViolationStatus::class,
            'fine_amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_date' => 'date',
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

    public function police(): BelongsTo
    {
        return $this->belongsTo(PoliceData::class, 'police_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
