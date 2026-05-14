<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'admin_id',
        'action_type',
        'target_table',
        'description',
    ];

    public function admin()
    {
        return $this->belongsTo(AdminData::class, 'admin_id');
    }
}
