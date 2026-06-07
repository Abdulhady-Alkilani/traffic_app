<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): array
    {
        $roles = [];

        $roles['citizen'] = Role::create([
            'name' => 'Citizen',
            'slug' => 'citizen',
        ]);

        $roles['admin'] = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $roles['police'] = Role::create([
            'name' => 'Police',
            'slug' => 'police',
        ]);

        return $roles;
    }
}
