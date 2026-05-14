<?php

namespace Database\Factories;

use App\Enums\Department;
use App\Models\PoliceData;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoliceDataFactory extends Factory
{
    protected $model = PoliceData::class;

    public function definition(): array
    {
        return [
            'badge_number' => fake()->unique()->numerify('PN####'),
            'full_name' => fake()->name(),
            'rank' => fake()->randomElement(['Officer', 'Sergeant', 'Lieutenant', 'Captain']),
            'department' => fake()->randomElement(Department::cases()),
        ];
    }
}
