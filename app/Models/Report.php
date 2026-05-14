<?php

namespace App\Models;

use App\Enums\Department;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'citizen_id',
        'vehicle_id',
        'assigned_department',
        'report_type',
        'description',
        'latitude',
        'longitude',
        'location_text',
        'image_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'assigned_department' => Department::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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

    public function violations()
    {
        return $this->hasMany(TrafficViolation::class);
    }
}
