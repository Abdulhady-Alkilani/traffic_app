<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'admin_id',
        'actor_type',
        'actor_name',
        'action_type',
        'target_table',
        'description',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminData::class, 'admin_id');
    }
}
