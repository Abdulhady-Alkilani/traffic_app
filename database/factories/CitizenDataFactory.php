<?php

namespace Database\Factories;

use App\Models\CitizenData;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitizenDataFactory extends Factory
{
    protected $model = CitizenData::class;

    public function definition(): array
    {
        return [
            'national_id' => fake()->unique()->numerify('##########'),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
        ];
    }
}
