<?php

namespace Database\Seeders;

use App\Models\PoliceData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PoliceDataSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $officersData = [
        [
            'username' => 'officer_highway_patrol',
            'email' => 'officer_highway_patrol@traffic.com',
            'badge_number' => 'HI4521',
            'full_name' => 'Major Tariq Al-Zoubi',
            'rank' => 'Major',
            'department' => 'highway_patrol',
        ],
        [
            'username' => 'officer_traffic_police',
            'email' => 'officer_traffic_police@traffic.com',
            'badge_number' => 'TR7832',
            'full_name' => 'Captain Lina Al-Sharif',
            'rank' => 'Captain',
            'department' => 'traffic_police',
        ],
        [
            'username' => 'officer_local_police',
            'email' => 'officer_local_police@traffic.com',
            'badge_number' => 'LO3196',
            'full_name' => 'Lieutenant Zaid Al-Ramahi',
            'rank' => 'Lieutenant',
            'department' => 'local_police',
        ],
    ];

    public function run(int $policeRoleId): array
    {
        $officers = [];

        foreach ($this->officersData as $data) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role_id' => $policeRoleId,
                'is_active' => true,
            ]);

            $officers[] = PoliceData::create([
                'user_id' => $user->id,
                'badge_number' => $data['badge_number'],
                'full_name' => $data['full_name'],
                'rank' => $data['rank'],
                'department' => $data['department'],
            ]);
        }

        return $officers;
    }
}
