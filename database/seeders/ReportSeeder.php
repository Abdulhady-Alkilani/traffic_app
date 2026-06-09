<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $reportsData = [
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'حادث تصادم بين سيارتين على أوتستراد دمشق-حمص بالقرب من مدخل جرمانا. إصابات طفيفة.', 'latitude' => '33.4850000', 'longitude' => '36.4120000', 'location_text' => 'أوتستراد دمشق-حمص، قرب جرمانا', 'status' => 'resolved'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'حفرة كبيرة في المسار الأوسط تسبب انحراف المركبات بشكل خطير.', 'latitude' => '33.5020000', 'longitude' => '36.2950000', 'location_text' => 'شارع بغداد، دمشق', 'status' => 'in_progress'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'مركبة مشبوهة متروكة قرب المنطقة المدرسية بأضواء التحذير مضاء.', 'latitude' => '36.1950000', 'longitude' => '37.1600000', 'location_text' => 'حي الجميلية، حلب', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'اختناق مروري شديد لمدة تزيد عن 45 دقيقة بسبب أعمال البناء.', 'latitude' => '33.5100000', 'longitude' => '36.2850000', 'location_text' => 'شارع الثورة، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'شاحنة مقلوبة على كتف الطريق تسد المسار الأيمن جزئياً على أوتستراد حلب.', 'latitude' => '35.2800000', 'longitude' => '36.7500000', 'location_text' => 'أوتستراد حلب-حماة، قرب خناصر', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'غصن شجرة كبيرة ساقط يسد نصف الطريق السكني.', 'latitude' => '33.5200000', 'longitude' => '36.3000000', 'location_text' => 'حي المالكي، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'حادث اصطدام خلفي عند إشارة ضوئية. أضرار طفيفة في كلتا المركبتين.', 'latitude' => '33.5050000', 'longitude' => '36.2900000', 'location_text' => 'دوار كفر سوسة، دمشق', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'ازدحام شديد بسبب مهرجان في المنطقة المجاورة.', 'latitude' => '33.4900000', 'longitude' => '36.3200000', 'location_text' => 'طريق مطار دمشق الدولي', 'status' => 'rejected'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'مجموعة أشخاص يسدون الطريق ويسببون فوضى عند الدوار.', 'latitude' => '34.7300000', 'longitude' => '36.7150000', 'location_text' => 'دوار الدعلول، حمص', 'status' => 'in_progress'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'حطام دراجة نارية متناثر على الطريق بعد حادث بسيط.', 'latitude' => '35.5250000', 'longitude' => '35.7900000', 'location_text' => 'كورنيش البحر، اللاذقية', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'مرور متوقف لمدة 30 دقيقة قرب مفترق طريق حمص-طرطوس.', 'latitude' => '34.6500000', 'longitude' => '36.4500000', 'location_text' => 'مفترق طرطوس، أوتستراد حمص', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'assigned_department' => 'local_police', 'report_type' => 'accident', 'description' => 'دهس مشاة من قبل سيارة نقل عند ممر المشاة. تم استدعاء الإسعاف.', 'latitude' => '33.5150000', 'longitude' => '36.2750000', 'location_text' => 'حي البرامكة، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'تسرب زيت على سطح الطريق قرب محطة الوقود يجعله زلقاً جداً.', 'latitude' => '33.5080000', 'longitude' => '36.2980000', 'location_text' => 'شارع المكتبي، دمشق', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'حادث تصادم متعدد المركبات على الأوتستراد وقت الذروة. ثلاث سيارات متورطة.', 'latitude' => '35.3500000', 'longitude' => '36.8500000', 'location_text' => 'أوتستراد حلب-حماة، كم 25', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'عطل في إشارة المرور يسبب اختناقاً عند تقاطع رئيسي.', 'latitude' => '33.5000000', 'longitude' => '36.3100000', 'location_text' => 'تقاطع adornment، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'حقيبة مهملة متروكة عند محطة الباصات قرب الجامعة.', 'latitude' => '33.5200000', 'longitude' => '36.3050000', 'location_text' => 'باب توما، دمشق القديمة', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'زجاج مكسر متناثر على عدة مسارات بعد حادث بسيط.', 'latitude' => '36.2000000', 'longitude' => '37.1550000', 'location_text' => 'شارع النيل، حلب', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'سيارة اصطدمت بحاجز الحماية وتعطلت على كتف الأوتستراد.', 'latitude' => '33.4800000', 'longitude' => '36.3500000', 'location_text' => 'أوتستراد دمشق-حمص، كم 15', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'تضيق الطريق بسبب أعمال تمديدات يسبب تأخيراً كبيراً.', 'latitude' => '33.5120000', 'longitude' => '36.2880000', 'location_text' => 'ساحة العباسيين، دمشق', 'status' => 'new'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'عمود إنارة يميل بخطورة فوق الطريق بعد رياح قوية.', 'latitude' => '34.8900000', 'longitude' => '35.8850000', 'location_text' => 'شارع الحبيب، طرطوس', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'حادث تصادم بين حافلة وتاكسي عند تقاطع. بدون إصابات خطيرة.', 'latitude' => '33.5030000', 'longitude' => '36.2920000', 'location_text' => 'دوار فلسطين، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'صخور كبيرة سقطت من التلة على مسار الأوتستراد.', 'latitude' => '34.7000000', 'longitude' => '36.5000000', 'location_text' => 'طريق حمص-طرطوس الجبلي', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'شجار بين سائقين يسد الشارع بالكامل.', 'latitude' => '33.5180000', 'longitude' => '36.2800000', 'location_text' => 'شارع القوتلي، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'ازدحام المنطقة المدرسية أثناء ساعات الصباح.', 'latitude' => '34.7350000', 'longitude' => '36.7200000', 'location_text' => 'حي الورشة، حمص', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'مواشي على الأوتستراد سببت حادث تصادم بسيط. سيارة واحدة متضررة.', 'latitude' => '35.1300000', 'longitude' => '36.7500000', 'location_text' => 'أوتستراد حماة-حلب، القسم الشمالي', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'انبثق أنبوب مياه يغرق الطريق بمياه بعمق الكاحل.', 'latitude' => '33.5100000', 'longitude' => '36.3000000', 'location_text' => 'شارع فلسطين، دمشق', 'status' => 'in_progress'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'تخريب مركبات متوقفة في المنطقة التجارية.', 'latitude' => '36.2050000', 'longitude' => '37.1650000', 'location_text' => 'سوق المدينة، حلب', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'انفجار إطار سبب دوران السيارة. بدون إصابات لكن يسد المسار.', 'latitude' => '33.4750000', 'longitude' => '36.3800000', 'location_text' => 'أوتستراد دمشق-حمص، كم 30', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'تأخير كبير بسبب مرور موكب رسمي في المنطقة.', 'latitude' => '33.5070000', 'longitude' => '36.2930000', 'location_text' => 'ساحة الأمويين، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'غطاء بالوعة مفقود على ممر مشاة مزدحم.', 'latitude' => '33.5130000', 'longitude' => '36.2770000', 'location_text' => 'القيمة، دمشق', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => 7, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'مركبة انقلبت على منعطف. السائق مصاب ويحتاج خدمات طوارئ.', 'latitude' => '33.4700000', 'longitude' => '36.2500000', 'location_text' => 'منعطف طريق المطار، دمشق', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'ازدحام مباراة كرة قدم يسبب تأخيراً شديداً قرب الملعب.', 'latitude' => '33.5050000', 'longitude' => '36.2880000', 'location_text' => 'ملعب العباسيين، دمشق', 'status' => 'new'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'سيارة نوافذها مكسورة موجودة متروكة في حي سكني.', 'latitude' => '33.5220000', 'longitude' => '36.3100000', 'location_text' => 'حي الروضة، دمشق', 'status' => 'in_progress'],
        ['citizen_index' => 1, 'vehicle_index' => 4, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'حطام بناء متروك على الطريق بعد ساعات العمل.', 'latitude' => '33.5000000', 'longitude' => '36.2800000', 'location_text' => 'شارع خالد بن الوليد، حمص', 'status' => 'new'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'حادث تصادم بين شاحنة وسيارة ركوب قرب نقطة الجباية.', 'latitude' => '35.2500000', 'longitude' => '36.8000000', 'location_text' => 'أوتستراد حلب، نقطة جباية الكم 45', 'status' => 'in_progress'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'شاحنة معطلة تسد التقاطع وتسبب تأخيراً هائلاً.', 'latitude' => '33.5080000', 'longitude' => '36.2950000', 'location_text' => 'دوار كفر سوسة، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 4, 'vehicle_index' => 9, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'تشكل جليد على الطريق خلال ليلة باردة يسبب انزلاقاً.', 'latitude' => '33.5300000', 'longitude' => '36.2700000', 'location_text' => 'طريق بلودان، ريف دمشق', 'status' => 'resolved'],
        ['citizen_index' => 0, 'vehicle_index' => null, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'ضباب يقلل الرؤية لأقل من 50 متر على الأوتستراد.', 'latitude' => '34.7000000', 'longitude' => '36.4000000', 'location_text' => 'أوتستراد دمشق-حمص، القسم الجنوبي', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 3, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'سيارة نقل اصطدمت بسيارة متوقفة. السائق فر من المكان.', 'latitude' => '34.7400000', 'longitude' => '36.7100000', 'location_text' => 'حي النزهة، حمص', 'status' => 'in_progress'],
        ['citizen_index' => 2, 'vehicle_index' => 6, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'سباق شوارع غير قانوني على طرقات فارغة في وقت متأخر من الليل.', 'latitude' => '35.5300000', 'longitude' => '35.7950000', 'location_text' => 'شارع 8 آذار، اللاذقية', 'status' => 'new'],
        ['citizen_index' => 3, 'vehicle_index' => null, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'صهريج مياه معطل في وسط الطريق وقت الذروة.', 'latitude' => '33.5120000', 'longitude' => '36.2820000', 'location_text' => 'شارع النصر، دمشق', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => 10, 'assigned_department' => 'highway_patrol', 'report_type' => 'accident', 'description' => 'حافلة فقدت السيطرة وخرجت عن الطريق قرب مخرج الأوتستراد.', 'latitude' => '33.4650000', 'longitude' => '36.2400000', 'location_text' => 'طريق مطار دمشق، المخرج 7', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 0, 'assigned_department' => 'traffic_police', 'report_type' => 'hazard', 'description' => 'حصى مفككة على الطريق المبني حديثاً تسبب ضعف التماسك.', 'latitude' => '33.5250000', 'longitude' => '36.3150000', 'location_text' => 'شارع المهاجرين، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 1, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'security_threat', 'description' => 'حاجز طريق غير مصرح به من قبل متظاهرين قرب المبنى الحكومي.', 'latitude' => '33.5070000', 'longitude' => '36.3000000', 'location_text' => 'المرابطة، دمشق', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => 5, 'assigned_department' => 'highway_patrol', 'report_type' => 'traffic_jam', 'description' => 'زحام العطلة يسبب تأخيراً طويلاً على طريق دمشق-حمص.', 'latitude' => '34.6800000', 'longitude' => '36.4200000', 'location_text' => 'أوتستراد دمشق-حمص، جنوب حمص', 'status' => 'rejected'],
        ['citizen_index' => 3, 'vehicle_index' => 8, 'assigned_department' => 'traffic_police', 'report_type' => 'accident', 'description' => 'دراجة نارية انزلقت على طريق مبلل واصطدمت بحاجز مروري. السائق واعي.', 'latitude' => '33.5000000', 'longitude' => '36.2900000', 'location_text' => 'دوار المكتبي، دمشق', 'status' => 'new'],
        ['citizen_index' => 4, 'vehicle_index' => 11, 'assigned_department' => 'local_police', 'report_type' => 'hazard', 'description' => 'حيوانات ضالة على الطريق تسبب تباطؤاً كبيراً في المرور.', 'latitude' => '35.1300000', 'longitude' => '36.7400000', 'location_text' => 'حي الصناعة، حماة', 'status' => 'in_progress'],
        ['citizen_index' => 0, 'vehicle_index' => 1, 'assigned_department' => 'traffic_police', 'report_type' => 'traffic_jam', 'description' => 'ركن مزدوج على جانبي الطريق يضيق الشارع لمسار واحد.', 'latitude' => '33.4980000', 'longitude' => '36.3200000', 'location_text' => 'حي دمر، دمشق', 'status' => 'new'],
        ['citizen_index' => 1, 'vehicle_index' => 2, 'assigned_department' => 'highway_patrol', 'report_type' => 'hazard', 'description' => 'تساقط بضائع من شاحنة تغطي الأوتستراد بالخضروات والفواكه.', 'latitude' => '35.3000000', 'longitude' => '36.8200000', 'location_text' => 'أوتستراد حلب، كم 60', 'status' => 'resolved'],
        ['citizen_index' => 2, 'vehicle_index' => null, 'assigned_department' => 'local_police', 'report_type' => 'accident', 'description' => 'طفل على دراجة هوائية دهسته سيارة في حي سكني. إصابات طفيفة.', 'latitude' => '33.5150000', 'longitude' => '36.3050000', 'location_text' => 'حي المزرعة، دمشق', 'status' => 'new'],
    ];

    public function run(array $citizens, array $vehicles): array
    {
        $reports = [];

        foreach ($this->reportsData as $data) {
            $vehicleId = null;
            if ($data['vehicle_index'] !== null && isset($vehicles[$data['vehicle_index']])) {
                $vehicleId = $vehicles[$data['vehicle_index']]->id;
            }

            $reports[] = Report::create([
                'citizen_id' => $citizens[$data['citizen_index']]->id,
                'vehicle_id' => $vehicleId,
                'assigned_department' => $data['assigned_department'],
                'report_type' => $data['report_type'],
                'description' => $data['description'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'location_text' => $data['location_text'],
                'status' => $data['status'],
            ]);
        }

        return $reports;
    }
}
