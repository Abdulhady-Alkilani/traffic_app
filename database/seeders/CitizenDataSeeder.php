<?php

namespace Database\Seeders;

use App\Models\CitizenData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CitizenDataSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $citizensData = [
        [
            'username' => 'ahmad_khaled',
            'email' => 'ahmad.khaled@example.com',
            'national_id' => '9012345678',
            'full_name' => 'Ahmad Khaled Al-Omari',
            'phone' => '+962790123456',
            'blood_type' => 'A+',
        ],
        [
            'username' => 'sara_mohammad',
            'email' => 'sara.mohammad@example.com',
            'national_id' => '9023456789',
            'full_name' => 'Sara Mohammad Al-Hussein',
            'phone' => '+962791234567',
            'blood_type' => 'B+',
        ],
        [
            'username' => 'omar_yousef',
            'email' => 'omar.yousef@example.com',
            'national_id' => '9034567890',
            'full_name' => 'Omar Yousef Al-Masri',
            'phone' => '+962792345678',
            'blood_type' => 'O+',
        ],
        [
            'username' => 'layla_ibrahim',
            'email' => 'layla.ibrahim@example.com',
            'national_id' => '9045678901',
            'full_name' => 'Layla Ibrahim Al-Nasser',
            'phone' => '+962793456789',
            'blood_type' => 'AB+',
        ],
        [
            'username' => 'rami_hasan',
            'email' => 'rami.hasan@example.com',
            'national_id' => '9056789012',
            'full_name' => 'Rami Hasan Al-Tamimi',
            'phone' => '+962794567890',
            'blood_type' => 'O-',
        ],
    ];

    public function run(int $citizenRoleId): array
    {
        $citizens = [];

        foreach ($this->citizensData as $data) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role_id' => $citizenRoleId,
                'is_active' => true,
            ]);

            $citizens[] = CitizenData::create([
                'user_id' => $user->id,
                'national_id' => $data['national_id'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'blood_type' => $data['blood_type'],
            ]);
        }

        return $citizens;
    }
}
