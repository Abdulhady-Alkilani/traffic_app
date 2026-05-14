<?php

namespace App\Models;

use App\Enums\ViolationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficViolation extends Model
{
    use HasFactory;

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

    public function citizen()
    {
        return $this->belongsTo(CitizenData::class, 'citizen_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function police()
    {
        return $this->belongsTo(PoliceData::class, 'police_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
