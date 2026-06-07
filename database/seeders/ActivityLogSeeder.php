<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $logsData = [
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'Created new police officer account: officer_highway_patrol',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'Created new police officer account: officer_traffic_police',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'Created new police officer account: officer_local_police',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'Updated report status to "resolved" for highway accident report.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'Assigned report to highway_patrol department.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'Issued speeding violation to Ahmad Khaled Al-Omari.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'traffic_violations',
            'description' => 'Marked violation as paid for reckless driving offense.',
        ],
        [
            'action_type' => 'delete',
            'target_table' => 'reports',
            'description' => 'Deleted duplicate report submitted for the same incident.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'users',
            'description' => 'Deactivated user account due to inactivity for 90 days.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'Issued red light violation to Sara Mohammad Al-Hussein.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'Changed report status from "new" to "in_progress" for security threat report.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'Created new citizen account: layla_ibrahim',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'traffic_violations',
            'description' => 'Canceled truck speeding violation due to speed radar calibration error.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'Issued phone usage violation to Layla Ibrahim Al-Nasser.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'Rejected traffic jam report due to insufficient evidence.',
        ],
    ];

    public function run(int $adminId): array
    {
        $logs = [];

        foreach ($this->logsData as $data) {
            $logs[] = ActivityLog::create([
                'admin_id' => $adminId,
                'action_type' => $data['action_type'],
                'target_table' => $data['target_table'],
                'description' => $data['description'],
            ]);
        }

        return $logs;
    }
}
