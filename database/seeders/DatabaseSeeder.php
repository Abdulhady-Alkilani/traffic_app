<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Models\AdminData;
use App\Models\CitizenData;
use App\Models\PoliceData;
use App\Models\Report;
use App\Models\Role;
use App\Models\TrafficViolation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $citizenRole = Role::create(['name' => 'Citizen', 'slug' => 'citizen']);
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $policeRole = Role::create(['name' => 'Police', 'slug' => 'police']);

        $adminUser = User::create([
            'username' => 'superadmin',
            'email' => 'admin@traffic.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
        AdminData::create([
            'user_id' => $adminUser->id,
            'full_name' => 'Super Admin',
        ]);

        $departments = Department::cases();
        foreach ($departments as $dept) {
            $policeUser = User::create([
                'username' => 'officer_' . $dept->value,
                'email' => 'officer_' . $dept->value . '@traffic.com',
                'password' => Hash::make('password'),
                'role_id' => $policeRole->id,
                'is_active' => true,
            ]);
            PoliceData::create([
                'user_id' => $policeUser->id,
                'badge_number' => strtoupper(substr($dept->value, 0, 2)) . rand(1000, 9999),
                'full_name' => fake()->name(),
                'rank' => 'Officer',
                'department' => $dept->value,
            ]);
        }

        $citizens = [];
        for ($i = 0; $i < 5; $i++) {
            $citizenUser = User::create([
                'username' => fake()->userName(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role_id' => $citizenRole->id,
                'is_active' => true,
            ]);
            $citizenData = CitizenData::create([
                'user_id' => $citizenUser->id,
                'national_id' => fake()->unique()->numerify('##########'),
                'full_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            ]);
            $citizens[] = $citizenData;
        }

        $vehicles = [];
        foreach ($citizens as $citizen) {
            $count = rand(1, 3);
            for ($j = 0; $j < $count; $j++) {
                $vehicles[] = Vehicle::create([
                    'citizen_id' => $citizen->id,
                    'plate_number' => fake()->unique()->numerify('ABC ####'),
                    'vehicle_type' => fake()->randomElement(['sedan', 'suv', 'truck', 'motorcycle', 'van']),
                    'make' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Hyundai']),
                    'model_year' => fake()->numberBetween(2010, 2026),
                    'color' => fake()->randomElement(['white', 'black', 'silver', 'red', 'blue']),
                ]);
            }
        }

        for ($i = 0; $i < 50; $i++) {
            $citizen = fake()->randomElement($citizens);
            Report::create([
                'citizen_id' => $citizen->id,
                'vehicle_id' => fake()->optional()->randomElement($vehicles)?->id,
                'assigned_department' => fake()->randomElement(Department::cases()),
                'report_type' => fake()->randomElement(['accident', 'hazard', 'traffic_jam', 'security_threat']),
                'description' => fake()->paragraph(),
                'latitude' => fake()->latitude(24.0, 32.0),
                'longitude' => fake()->longitude(34.0, 42.0),
                'location_text' => fake()->address(),
                'status' => fake()->randomElement(\App\Enums\ReportStatus::cases()),
            ]);
        }

        $policeOfficers = PoliceData::all();
        $violationTypes = ['speeding', 'reckless_driving', 'red_light', 'illegal_parking', 'no_seatbelt', 'using_phone'];

        for ($i = 0; $i < 25; $i++) {
            $citizen = fake()->randomElement($citizens);
            $vehicle = fake()->optional(0.7)->randomElement($citizen->vehicles->toArray());
            TrafficViolation::create([
                'citizen_id' => $citizen->id,
                'vehicle_id' => $vehicle['id'] ?? null,
                'police_id' => fake()->randomElement($policeOfficers)->id,
                'report_id' => fake()->optional(0.3)->randomElement(Report::pluck('id')->toArray()),
                'violation_type' => fake()->randomElement($violationTypes),
                'description' => fake()->optional()->sentence(),
                'fine_amount' => fake()->randomElement([100, 150, 300, 500, 1000, 2000]),
                'due_date' => fake()->dateTimeBetween('now', '+90 days')->format('Y-m-d'),
                'status' => fake()->randomElement(['unpaid', 'unpaid', 'unpaid', 'paid', 'canceled']),
                'issued_at' => fake()->dateTimeBetween('-60 days', 'now'),
            ]);
        }
    }
}
