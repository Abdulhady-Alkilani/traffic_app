<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function citizenData()
    {
        return $this->hasOne(CitizenData::class);
    }

    public function policeData()
    {
        return $this->hasOne(PoliceData::class);
    }

    public function adminData()
    {
        return $this->hasOne(AdminData::class);
    }

    public function violations()
    {
        return $this->hasManyThrough(TrafficViolation::class, CitizenData::class, 'user_id', 'citizen_id');
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
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'police' => $this->isPolice(),
            default => false,
        };
    }
}