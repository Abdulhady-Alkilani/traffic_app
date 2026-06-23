<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an activity, automatically resolving the actor from the authenticated user.
     *
     * @param  string  $action  create|update|delete|view|payment|status_change
     * @param  string  $table  Target table name
     * @param  string  $description  Human-readable description
     * @param  User|null  $user  Override the actor (defaults to authenticated user)
     */
    public function log(string $action, string $table, string $description, ?User $user = null): void
    {
        $user = $user ?? Auth::user();

        $data = [
            'action_type' => $action,
            'target_table' => $table,
            'description' => $description,
            'actor_type' => $this->resolveActorType($user),
            'actor_name' => $this->resolveActorName($user),
        ];

        $adminId = $this->resolveAdminId($user);
        if ($adminId !== null) {
            $data['admin_id'] = $adminId;
        }

        try {
            ActivityLog::create($data);
        } catch (\Exception $e) {
            logger()->error('ActivityLogger failed', ['error' => $e->getMessage(), 'action' => $action]);
        }
    }

    /**
     * Log a system-initiated action (no user actor).
     */
    public function system(string $action, string $table, string $description): void
    {
        try {
            ActivityLog::create([
                'action_type' => $action,
                'target_table' => $table,
                'description' => $description,
                'actor_type' => 'system',
                'actor_name' => 'النظام',
            ]);
        } catch (\Exception $e) {
            logger()->error('ActivityLogger system failed', ['error' => $e->getMessage()]);
        }
    }

    private function resolveActorType(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        return match (true) {
            $user->isAdmin() => 'admin',
            $user->isPolice() => 'police',
            $user->isCitizen() => 'citizen',
            default => null,
        };
    }

    private function resolveActorName(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        try {
            return $user->getFilamentName();
        } catch (\Exception) {
            return $user->username;
        }
    }

    private function resolveAdminId(?User $user): ?int
    {
        if (!$user || !$user->isAdmin()) {
            return null;
        }

        return $user->adminData?->id;
    }
}
