<?php

namespace Database\Seeders;

use App\Models\AdminData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(int $adminRoleId): AdminData
    {
        $adminUser = User::create([
            'username' => 'superadmin',
            'email' => 'admin@traffic.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'is_active' => true,
        ]);

        return AdminData::create([
            'user_id' => $adminUser->id,
            'full_name' => 'Super Admin',
        ]);
    }
}
