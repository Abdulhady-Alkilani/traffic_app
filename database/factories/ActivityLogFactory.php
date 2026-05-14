<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'action_type' => fake()->randomElement(['create', 'update', 'delete', 'view']),
            'target_table' => fake()->randomElement(['users', 'reports', 'vehicles', 'citizens_data']),
            'description' => fake()->sentence(),
        ];
    }
}
