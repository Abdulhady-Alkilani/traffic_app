<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Department;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function update(User $user, Report $report): bool
    {
        $policeData = $user->policeData;

        if (!$policeData) {
            return false;
        }

        return $policeData->department === $report->assigned_department;
    }
}
