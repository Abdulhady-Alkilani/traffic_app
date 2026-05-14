<?php

namespace App\Models;

use App\Enums\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliceData extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function violations()
    {
        return $this->hasMany(TrafficViolation::class, 'police_id');
    }
}
