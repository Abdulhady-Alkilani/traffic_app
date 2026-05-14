<?php

namespace Database\Factories;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'assigned_department' => fake()->randomElement(Department::cases()),
            'report_type' => fake()->randomElement(['accident', 'hazard', 'traffic_jam', 'security_threat']),
            'description' => fake()->paragraph(),
            'latitude' => fake()->latitude(24.0, 32.0),
            'longitude' => fake()->longitude(34.0, 42.0),
            'location_text' => fake()->address(),
            'image_url' => null,
            'status' => fake()->randomElement(ReportStatus::cases()),
        ];
    }
}
