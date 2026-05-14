<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'plate_number' => fake()->unique()->numerify('ABC ####'),
            'vehicle_type' => fake()->randomElement(['sedan', 'suv', 'truck', 'motorcycle', 'van']),
            'make' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Hyundai', 'Nissan']),
            'model_year' => fake()->numberBetween(2010, 2026),
            'color' => fake()->randomElement(['white', 'black', 'silver', 'red', 'blue', 'gray', 'green']),
        ];
    }
}
