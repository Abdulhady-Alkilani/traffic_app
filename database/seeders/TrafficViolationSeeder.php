<?php

namespace Database\Seeders;

use App\Models\TrafficViolation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrafficViolationSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $violationsData = [
        ['citizen_index' => 0, 'vehicle_index' => 0, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'تجاوز السرعة المحددة بـ 30 كم/س على أوتستراد دمشق-حمص.', 'fine_amount' => '50000.00', 'status' => 'unpaid', 'issued_at' => '2026-04-20 10:30:00', 'due_date' => '2026-07-20'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'police_index' => 1, 'report_index' => 1, 'violation_type' => 'reckless_driving', 'description' => 'قيادة متهورة وتغيير مسار بشكل مفاجئ على شارع بغداد بدمشق.', 'fine_amount' => '100000.00', 'status' => 'paid', 'issued_at' => '2026-03-15 14:45:00', 'due_date' => '2026-06-15'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'تجاوز الإشارة الحمراء عند تقاطع ساحة العباسيين بدمشق.', 'fine_amount' => '75000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-01 08:15:00', 'due_date' => '2026-08-01'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'police_index' => 0, 'report_index' => 11, 'violation_type' => 'illegal_parking', 'description' => 'ركن المركبة في منطقة ممنوعة أمام مستشفى المواس بحمص.', 'fine_amount' => '20000.00', 'status' => 'unpaid', 'issued_at' => '2026-04-28 16:20:00', 'due_date' => '2026-07-28'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'police_index' => 1, 'report_index' => 4, 'violation_type' => 'speeding', 'description' => 'شاحنة تتجاوز السرعة المحددة في منطقة أعمال على أوتستراد حلب.', 'fine_amount' => '75000.00', 'status' => 'unpaid', 'issued_at' => '2026-02-10 09:00:00', 'due_date' => '2026-05-10'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'السائق لم يرتدِ حزام الأمان على شارع المكتبي بدمشق.', 'fine_amount' => '10000.00', 'status' => 'paid', 'issued_at' => '2026-04-05 11:30:00', 'due_date' => '2026-07-05'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'police_index' => 0, 'report_index' => 6, 'violation_type' => 'using_phone', 'description' => 'استخدام الهاتف المحمول أثناء القيادة عند جسر الرئيس بدمشق.', 'fine_amount' => '25000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-10 13:00:00', 'due_date' => '2026-08-10'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'تجاوز السرعة في منطقة سكنية قرب المدارس بحي المزة بدمشق.', 'fine_amount' => '75000.00', 'status' => 'unpaid', 'issued_at' => '2026-04-22 07:45:00', 'due_date' => '2026-07-22'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'ركن مزدوج في المنطقة التجارية بسوق الحميدية بدمشق.', 'fine_amount' => '20000.00', 'status' => 'paid', 'issued_at' => '2026-03-30 18:10:00', 'due_date' => '2026-06-30'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'police_index' => 0, 'report_index' => 9, 'violation_type' => 'reckless_driving', 'description' => 'قيادة دراجة نارية متهورة وأداء حركات خطرة على كورنيش اللاذقية.', 'fine_amount' => '100000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-12 20:30:00', 'due_date' => '2026-08-12'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'السائق بدون حزام أمان عند نقطة تفتيش على طريق مطار دمشق.', 'fine_amount' => '10000.00', 'status' => 'paid', 'issued_at' => '2026-01-20 09:30:00', 'due_date' => '2026-04-20'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'using_phone', 'description' => 'استخدام الهاتف أثناء التوقف عند إشارة ضوئية في حلب.', 'fine_amount' => '25000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-08 17:00:00', 'due_date' => '2026-08-08'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'تجاوز الإشارة الحمراء عند دوار كفر سوسة بدمشق في أوقات الذروة.', 'fine_amount' => '75000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-15 08:00:00', 'due_date' => '2026-08-15'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'سيارة نقل تتجاوز السرعة بـ 45 كم/س في منطقة مدرسية بحمص.', 'fine_amount' => '75000.00', 'status' => 'paid', 'issued_at' => '2026-04-10 07:30:00', 'due_date' => '2026-07-10'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'شاحنة مركونة على الرصيف تعيق مرور المشاة في حماة.', 'fine_amount' => '25000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-18 15:30:00', 'due_date' => '2026-08-18'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'no_seatbelt', 'description' => 'ركاب المقعد الخلفي بدون حزام أمان خلال توقف روتيني في طرطوس.', 'fine_amount' => '10000.00', 'status' => 'unpaid', 'issued_at' => '2026-03-25 12:00:00', 'due_date' => '2026-06-25'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'using_phone', 'description' => 'كتابة رسالة نصية أثناء القيادة على شارع الثورة بحمص.', 'fine_amount' => '25000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-20 16:45:00', 'due_date' => '2026-08-20'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'police_index' => 1, 'report_index' => 13, 'violation_type' => 'reckless_driving', 'description' => 'تجاوز خطير على منعطف عمياني على طريق دمشق-زبداني.', 'fine_amount' => '100000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-22 10:15:00', 'due_date' => '2026-08-22'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'police_index' => 2, 'report_index' => null, 'violation_type' => 'red_light', 'description' => 'تجاوز الإشارة الحمراء عند الانعطاف يساراً في ساحة سعد الله الجابري بحلب.', 'fine_amount' => '75000.00', 'status' => 'paid', 'issued_at' => '2026-04-15 09:30:00', 'due_date' => '2026-07-15'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'speeding', 'description' => 'تجاوز السرعة المحددة بـ 20 كم/س على شارع فلسطين بدمشق.', 'fine_amount' => '30000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-25 14:00:00', 'due_date' => '2026-08-25'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'police_index' => 1, 'report_index' => null, 'violation_type' => 'illegal_parking', 'description' => 'ركن في موقف ذوي الاحتياجات الخاصة بدون تصريح أمام مشفى الأسد بحلب.', 'fine_amount' => '50000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-28 19:00:00', 'due_date' => '2026-08-28'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'police_index' => 0, 'report_index' => null, 'violation_type' => 'reckless_driving', 'description' => 'تفحيط على طريق مبلل بالقرب من منطقة سكنية ليلاً في اللاذقية.', 'fine_amount' => '150000.00', 'status' => 'unpaid', 'issued_at' => '2026-05-30 23:00:00', 'due_date' => '2026-08-30'],
    ];

    public function run(array $citizens, array $vehicles, array $officers, array $reports): array
    {
        $violations = [];

        foreach ($this->violationsData as $data) {
            $vehicleId = null;
            if ($data['vehicle_index'] !== null && isset($vehicles[$data['vehicle_index']])) {
                $vehicleId = $vehicles[$data['vehicle_index']]->id;
            }

            $reportId = null;
            if ($data['report_index'] !== null && isset($reports[$data['report_index']])) {
                $reportId = $reports[$data['report_index']]->id;
            }

            $violations[] = TrafficViolation::create([
                'citizen_id' => $citizens[$data['citizen_index']]->id,
                'vehicle_id' => $vehicleId,
                'police_id' => $officers[$data['police_index']]->id,
                'report_id' => $reportId,
                'violation_type' => $data['violation_type'],
                'description' => $data['description'],
                'fine_amount' => $data['fine_amount'],
                'status' => $data['status'],
                'issued_at' => $data['issued_at'],
                'due_date' => $data['due_date'],
            ]);
        }

        return $violations;
    }
}
