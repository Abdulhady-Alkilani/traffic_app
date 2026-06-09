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
            'description' => 'تم إنشاء حساب ضابط شرطة جديد: officer_highway_patrol',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'تم إنشاء حساب ضابط شرطة جديد: officer_traffic_police',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'تم إنشاء حساب ضابط شرطة جديد: officer_local_police',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'تم تحديث حالة البلاغ إلى "تم الحل" لبلاغ حادث على أوتستراد دمشق-حمص.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'تم إسناد البلاغ إلى قسم دورية الأوتستراد.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'تم تسجيل مخالفة تجاوز السرعة لأحمد خالد العمري.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'traffic_violations',
            'description' => 'تم تأشير المخالفة كمدفوعة لمخالفة القيادة المتهورة.',
        ],
        [
            'action_type' => 'delete',
            'target_table' => 'reports',
            'description' => 'تم حذف بلاغ مكرر مقدّم لنفس الحادثة.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'users',
            'description' => 'تم تعطيل حساب مستخدم بسبب عدم النشاط لمدة 90 يوماً.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'تم تسجيل مخالفة تجاوز إشارة حمراء لسارة محمد الحموي.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'تم تغيير حالة البلاغ من "جديد" إلى "قيد التنفيذ" لبلاغ التهديد الأمني.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'users',
            'description' => 'تم إنشاء حساب مواطن جديد: layla_ibrahim',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'traffic_violations',
            'description' => 'تم إلغاء مخالفة تجاوز السرعة للشاحنة بسبب خطأ في معايرة رادار السرعة.',
        ],
        [
            'action_type' => 'create',
            'target_table' => 'traffic_violations',
            'description' => 'تم تسجيل مخالفة استخدام الهاتف لليلى إبراهيم الشامي.',
        ],
        [
            'action_type' => 'update',
            'target_table' => 'reports',
            'description' => 'تم رفض بلاغ الاختناق المروري بسبب عدم كفاية الأدلة.',
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
