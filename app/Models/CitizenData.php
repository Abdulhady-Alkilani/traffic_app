<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenData extends Model
{
    use HasFactory;

    protected $table = 'citizens_data';

    protected $fillable = [
        'user_id',
        'national_id',
        'full_name',
        'phone',
        'blood_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'citizen_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'citizen_id');
    }

    public function violations()
    {
        return $this->hasMany(TrafficViolation::class, 'citizen_id');
    }
}
