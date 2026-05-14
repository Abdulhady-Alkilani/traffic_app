<?php

namespace Database\Factories;

use App\Models\AdminData;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminDataFactory extends Factory
{
    protected $model = AdminData::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
        ];
    }
}
