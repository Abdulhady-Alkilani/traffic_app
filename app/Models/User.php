<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function citizenData(): HasOne
    {
        return $this->hasOne(CitizenData::class);
    }

    public function policeData(): HasOne
    {
        return $this->hasOne(PoliceData::class);
    }

    public function adminData(): HasOne
    {
        return $this->hasOne(AdminData::class);
    }

    public function violations(): HasManyThrough
    {
        return $this->hasManyThrough(TrafficViolation::class, CitizenData::class, 'user_id', 'citizen_id');
    }

    public function reports(): HasManyThrough
    {
        return $this->hasManyThrough(Report::class, CitizenData::class, 'user_id', 'citizen_id');
    }

    public function vehicles(): HasManyThrough
    {
        return $this->hasManyThrough(Vehicle::class, CitizenData::class, 'user_id', 'citizen_id');
    }

    public function isCitizen(): bool
    {
        return $this->role?->slug === 'citizen';
    }

    public function isPolice(): bool
    {
        return $this->role?->slug === 'police';
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'police' => $this->isPolice(),
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        if ($this->isAdmin()) {
            return $this->adminData->full_name ?? $this->username;
        } elseif ($this->isPolice()) {
            return $this->policeData->full_name ?? $this->username;
        } elseif ($this->isCitizen()) {
            return $this->citizenData->full_name ?? $this->username;
        }
        return $this->username ?? 'User';
    }
}
