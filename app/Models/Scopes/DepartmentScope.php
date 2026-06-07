<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Enums\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class DepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user && $user->isPolice() && $user->policeData) {
            $builder->where('assigned_department', $user->policeData->department->value);
        }
    }
}
