<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = (new RoleSeeder())->run();

        $admin = (new AdminDataSeeder())->run($roles['admin']->id);

        $officers = (new PoliceDataSeeder())->run($roles['police']->id);

        $citizens = (new CitizenDataSeeder())->run($roles['citizen']->id);

        $vehicles = (new VehicleSeeder())->run($citizens);

        $reports = (new ReportSeeder())->run($citizens, $vehicles);

        (new TrafficViolationSeeder())->run($citizens, $vehicles, $officers, $reports);

        (new ActivityLogSeeder())->run($admin->id);
    }
}
